import 'dart:convert';
import 'package:http/http.dart' as http;

class PaymentService {
  final String baseUrl;
  final String? token;
  PaymentService({this.baseUrl = 'http://10.0.2.2:8000', this.token});

  Future<http.Response> createPayme(int listingId, double amount, String currency) async {
    final uri = Uri.parse('$baseUrl/api/payments/payme/create');
    final headers = <String, String>{};
    if (token != null) headers['Authorization'] = 'Bearer $token';
    return await http.post(uri, headers: headers, body: {
      'listing_id': listingId.toString(),
      'amount': amount.toString(),
      'currency': currency,
    });
  }

  Future<http.Response> createClick(int listingId, double amount, String currency) async {
    final uri = Uri.parse('$baseUrl/api/payments/click/create');
    final headers = <String, String>{};
    if (token != null) headers['Authorization'] = 'Bearer $token';
    return await http.post(uri, headers: headers, body: {
      'listing_id': listingId.toString(),
      'amount': amount.toString(),
      'currency': currency,
    });
  }
}
