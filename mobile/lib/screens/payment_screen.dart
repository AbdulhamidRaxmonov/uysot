import 'dart:convert';
import 'package:flutter/material.dart';
import '../services/payment_service.dart';

class PaymentScreen extends StatefulWidget {
  final int listingId;
  const PaymentScreen({super.key, required this.listingId});

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
        _result = 'Payme checkout URL: ' + (data['payme']?['checkout_url'] ?? '');
      });
    } else {
      setState(() { _result = 'Error: ${resp.body}'; });
    }
  }

  void _simulatePayme() async {
    if (_txId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Avval to\'lov yarating')));
      return;
    }
    setState(() { _loading = true; _result = ''; });
    final resp = await _service.simulatePaymeCallback(_txId!);
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      setState(() { _result = 'Simulated Payme callback ok'; });
      _pollStatus();
    } else {
      setState(() { _result = 'Simulation error: ${resp.body}'; });
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
        _result = 'Click checkout URL: ' + (data['click']?['checkout_url'] ?? '');
      });
    } else {
      setState(() { _result = 'Error: ${resp.body}'; });
    }
  }

  void _simulateClick() async {
    if (_txId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Avval to\'lov yarating')));
      return;
    }
    setState(() { _loading = true; _result = ''; });
    final resp = await _service.simulateClickCallback(_txId!);
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      setState(() { _result = 'Simulated Click callback ok'; });
      _pollStatus();
    } else {
      setState(() { _result = 'Simulation error: ${resp.body}'; });
    }
  }

  void _pollStatus() async {
    if (_txId == null) return;
    for (int i = 0; i < 6; i++) {
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
          const SizedBox(height: 12),
          Row(children: [
            ElevatedButton(onPressed: _loading ? null : _simulatePayme, child: const Text('Simulate Payme callback')),
            const SizedBox(width: 12),
            ElevatedButton(onPressed: _loading ? null : _simulateClick, child: const Text('Simulate Click callback')),
          ]),
          const SizedBox(height: 20),
          if (_loading) const CircularProgressIndicator(),
          if (_result.isNotEmpty) Text(_result),
        ]),
      ),
    );
  }
}
