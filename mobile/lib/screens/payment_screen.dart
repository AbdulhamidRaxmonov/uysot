import 'dart:convert';
import 'package:flutter/material.dart';
import '../services/payment_service.dart';
import 'checkout_webview.dart';

class PaymentScreen extends StatefulWidget {
  final int listingId;
  final String returnUrl;
  const PaymentScreen({super.key, required this.listingId, required this.returnUrl});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  final _amountCtrl = TextEditingController();
  final _service = PaymentService();
  bool _loading = false;
  String _result = '';
  int? _txId;

  void _startPayme() async {
    final amount = double.tryParse(_amountCtrl.text) ?? 0;
    setState(() { _loading = true; _result = ''; });
    final resp = await _service.createPayme(widget.listingId, amount, 'UZS');
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      final data = json.decode(resp.body);
      setState(() {
        _txId = data['transaction'] != null ? data['transaction']['id'] : null;
      });
      final checkout = data['payme']?['checkout_url'] ?? data['payme']?['payload']?['response']?['checkout_url'];
      if (checkout != null && checkout is String && checkout.isNotEmpty) {
        final result = await Navigator.of(context).push(MaterialPageRoute(builder: (_) => CheckoutWebView(url: checkout, txId: _txId ?? 0, returnUrl: widget.returnUrl)));
        if (result == true) {
          await _pollStatus();
        }
      } else {
        setState(() { _result = 'Checkout URL not returned'; });
      }
    } else {
      setState(() { _result = 'Error: ${resp.body}'; });
    }
  }

  void _startClick() async {
    final amount = double.tryParse(_amountCtrl.text) ?? 0;
    setState(() { _loading = true; _result = ''; });
    final resp = await _service.createClick(widget.listingId, amount, 'UZS');
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      final data = json.decode(resp.body);
      setState(() {
        _txId = data['transaction'] != null ? data['transaction']['id'] : null;
      });
      final checkout = data['click']?['checkout_url'] ?? data['click']?['payload']?['payment_url'];
      if (checkout != null && checkout is String && checkout.isNotEmpty) {
        final result = await Navigator.of(context).push(MaterialPageRoute(builder: (_) => CheckoutWebView(url: checkout, txId: _txId ?? 0, returnUrl: widget.returnUrl)));
        if (result == true) {
          await _pollStatus();
        }
      } else {
        setState(() { _result = 'Checkout URL not returned'; });
      }
    } else {
      setState(() { _result = 'Error: ${resp.body}'; });
    }
  }

  Future<void> _pollStatus() async {
    if (_txId == null) return;
    for (int i = 0; i < 8; i++) {
      await Future.delayed(const Duration(seconds: 1));
      final resp = await _service.getTransaction(_txId!);
      if (resp.statusCode == 200) {
        final data = json.decode(resp.body);
        final status = data['transaction']?['status'];
        setState(() { _result = 'Transaction status: $status'; });
        if (status == 'paid' || status == 'failed') return;
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('To\'lov')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(children: [
          TextField(controller: _amountCtrl, decoration: const InputDecoration(labelText: 'Summa (masalan 100000)')),
          const SizedBox(height: 12),
          Row(children: [
            ElevatedButton(onPressed: _loading ? null : _startPayme, child: const Text('Payme')),
            const SizedBox(width: 12),
            ElevatedButton(onPressed: _loading ? null : _startClick, child: const Text('Click')),
          ]),
          const SizedBox(height: 20),
          if (_loading) const CircularProgressIndicator(),
          if (_result.isNotEmpty) Text(_result),
        ]),
      ),
    );
  }
}
