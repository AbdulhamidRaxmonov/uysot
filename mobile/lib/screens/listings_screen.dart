import 'dart:convert';
import 'package:flutter/material.dart';
import '../services/listing_service.dart';

class ListingsScreen extends StatefulWidget {
  const ListingsScreen({super.key});

  @override
  State<ListingsScreen> createState() => _ListingsScreenState();
}

class _ListingsScreenState extends State<ListingsScreen> {
  final _service = ListingService();
  List _listings = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  void _load() async {
    final resp = await _service.fetchListings();
    if (resp.statusCode == 200) {
      final data = json.decode(resp.body);
      setState(() {
        _listings = data['data'] ?? [];
        _loading = false;
      });
    } else {
      setState(() { _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Ro\'yxat')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : GridView.builder(
              padding: const EdgeInsets.all(12),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, crossAxisSpacing: 12, mainAxisSpacing: 12, childAspectRatio: 0.8),
              itemCount: _listings.length,
              itemBuilder: (context, i) {
                final item = _listings[i];
                final photos = item['photos'] as List?;
                final thumb = (photos != null && photos.isNotEmpty) ? photos[0]['thumb_150x100'] ?? photos[0]['original'] : null;
                return GestureDetector(
                  onTap: () {},
                  child: Container(
                    decoration: BoxDecoration(color: const Color(0xFF1B1B1D), borderRadius: BorderRadius.circular(12)),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: thumb != null
                              ? ClipRRect(borderRadius: const BorderRadius.vertical(top: Radius.circular(12)), child: Image.network(thumb, width: double.infinity, fit: BoxFit.cover))
                              : Container(height: 100, color: Colors.grey[800]),
                        ),
                        Padding(
                          padding: const EdgeInsets.all(8.0),
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(item['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold)),
                            const SizedBox(height: 6),
                            Text('${item['price'] ?? ''} ${item['currency'] ?? ''}', style: const TextStyle(color: Colors.white70)),
                          ]),
                        )
                      ],
                    ),
                  ),
                );
              },
            ),
    );
  }
}
