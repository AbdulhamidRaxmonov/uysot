import 'package:flutter/material.dart';

class ListingDetailScreen extends StatelessWidget {
  final Map listing;
  const ListingDetailScreen({super.key, required this.listing});

  @override
  Widget build(BuildContext context) {
    final photos = (listing['photos'] as List?) ?? [];
    return Scaffold(
      appBar: AppBar(title: Text(listing['title'] ?? '')),
      body: SingleChildScrollView(
        child: Column(children: [
          SizedBox(
            height: 240,
            child: PageView(
              children: photos.isNotEmpty
                  ? photos.map((p) => Image.network(p['original'] ?? p['thumb_400x300'] ?? '', fit: BoxFit.cover)).toList().cast<Widget>()
                  : [Container(color: Colors.grey[800])],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(12.0),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(listing['title'] ?? '', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Text('${listing['price'] ?? ''} ${listing['currency'] ?? ''}', style: const TextStyle(color: Colors.white70)),
              const SizedBox(height: 12),
              Text(listing['description'] ?? ''),
              const SizedBox(height: 20),
              ElevatedButton(onPressed: () {}, child: const Text('To\'lov qilmoq (Payme/Click)')),
            ]),
          )
        ]),
      ),
    );
  }
}
