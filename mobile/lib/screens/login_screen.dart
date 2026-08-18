import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _phoneCtrl = TextEditingController();
  final _auth = AuthService();
  bool _loading = false;

  void _sendCode() async {
    final phone = _phoneCtrl.text.trim();
    if (phone.isEmpty) return;
    setState(() { _loading = true; });
    final resp = await _auth.sendCode(phone);
    setState(() { _loading = false; });
    if (resp.statusCode == 200) {
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => OtpScreen(phone: phone)));
    } else {
      final msg = resp.body.isNotEmpty ? resp.body : 'Xatolik';
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Telefon bilan kirish')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(controller: _phoneCtrl, decoration: const InputDecoration(labelText: 'Telefon (masalan +998901234567)')),
            const SizedBox(height: 16),
            ElevatedButton(onPressed: _loading ? null : _sendCode, child: _loading ? const CircularProgressIndicator() : const Text('Kod yuborish'))
          ],
        ),
      ),
    );
  }
}
