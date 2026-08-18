import 'dart:convert';
import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'home_screen.dart';

class OtpScreen extends StatefulWidget {
  final String phone;
  const OtpScreen({super.key, required this.phone});

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final _codeCtrl = TextEditingController();
  final _auth = AuthService();
  bool _loading = false;

  void _verify() async {
    final code = _codeCtrl.text.trim();
    if (code.isEmpty) return;
    setState(() { _loading = true; });
    final resp = await _auth.verifyCode(widget.phone, code);
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      final data = json.decode(resp.body);
      final token = data['token'];
      // TODO: persist token securely
      Navigator.of(context).pushAndRemoveUntil(MaterialPageRoute(builder: (_) => const HomeScreen()), (r) => false);
    } else {
      final msg = resp.body.isNotEmpty ? resp.body : 'Xatolik';
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Kodni tasdiqlash')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Text('Kod telefon raqamiga yuborildi: ${widget.phone}'),
            TextField(controller: _codeCtrl, decoration: const InputDecoration(labelText: 'Kod')),
            const SizedBox(height: 16),
            ElevatedButton(onPressed: _loading ? null : _verify, child: _loading ? const CircularProgressIndicator() : const Text('Tasdiqlash'))
          ],
        ),
      ),
    );
  }
}
