import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  final String baseUrl;
  AuthService({this.baseUrl = 'http://10.0.2.2:8000'}); // emulator -> host machine

  Future<http.Response> sendCode(String phone) async {
    final url = Uri.parse('$baseUrl/api/auth/send-code');
    return await http.post(url, body: {'phone': phone});
  }

  Future<http.Response> verifyCode(String phone, String code) async {
    final url = Uri.parse('$baseUrl/api/auth/verify-code');
    return await http.post(url, body: {'phone': phone, 'code': code});
  }
}
