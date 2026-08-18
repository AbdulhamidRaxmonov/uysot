import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class ListingService {
  final String baseUrl;
  final String? token;
  ListingService({this.baseUrl = 'http://10.0.2.2:8000', this.token});

  Future<http.Response> fetchListings({int page = 1, String? query}) async {
    final uri = Uri.parse('$baseUrl/api/listings?page=$page${query != null ? '&q=${Uri.encodeQueryComponent(query)}' : ''}');
    return await http.get(uri);
  }

  Future<http.Response> getListing(int id) async {
    final uri = Uri.parse('$baseUrl/api/listings/$id');
    return await http.get(uri);
  }

  Future<http.StreamedResponse> uploadPhoto(int id, File file) async {
    final uri = Uri.parse('$baseUrl/api/listings/$id/photos');

    final request = http.MultipartRequest('POST', uri);
    if (token != null) {
      request.headers['Authorization'] = 'Bearer $token';
    }

    request.files.add(await http.MultipartFile.fromPath('file', file.path));
    return await request.send();
  }
}
