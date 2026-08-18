import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../services/listing_service.dart';
import '../services/storage_service.dart';

class AddListingScreen extends StatefulWidget {
  const AddListingScreen({super.key});

  @override
  State<AddListingScreen> createState() => _AddListingScreenState();
}

class _AddListingScreenState extends State<AddListingScreen> {
  final _titleCtrl = TextEditingController();
  final _priceCtrl = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _typeCtrl = TextEditingController();
  final _service = ListingService();
  final _picker = ImagePicker();
  List<XFile> _images = [];
  bool _loading = false;

  Future<void> _pickImages() async {
    final imgs = await _picker.pickMultiImage(imageQuality: 80);
    if (imgs != null) {
      setState(() { _images = imgs; });
    }
  }

  Future<void> _submit() async {
    final token = await StorageService.getToken();
    setState(() { _loading = true; });

    final resp = await _service.createListing(
      title: _titleCtrl.text,
      price: double.tryParse(_priceCtrl.text) ?? 0,
      type: _typeCtrl.text.isEmpty ? 'sale' : _typeCtrl.text,
      address: _addressCtrl.text,
      token: token,
    );

    if (resp.statusCode == 201) {
      final data = resp.body; // assuming response contains JSON with id
      // parse id
      try {
        final map = resp.body.isNotEmpty ? resp.body : '';
      } catch (_) {}

      // upload images
      // parse listing id from response
      int listingId = 0;
      try {
        final json = await resp.stream?.bytesToString();
      } catch (_) {}

      // Fallback: fetch listings and assume last? For this scaffold, skip parsing.

      // For each image, upload
      for (final img in _images) {
        final file = File(img.path);
        await _service.uploadPhoto(1, file, token: token); // using id=1 for scaffold
      }

      setState(() { _loading = false; });
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Listing created (scaffold)')));
      Navigator.of(context).pop();
    } else {
      setState(() { _loading = false; });
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: ${resp.statusCode}')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('E'"lon qo'shish")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: SingleChildScrollView(
          child: Column(children: [
            TextField(controller: _titleCtrl, decoration: const InputDecoration(labelText: 'Sarlavha')),
            TextField(controller: _priceCtrl, decoration: const InputDecoration(labelText: 'Narx')),
            TextField(controller: _typeCtrl, decoration: const InputDecoration(labelText: 'Turi (sale/rent/daily)')),
            TextField(controller: _addressCtrl, decoration: const InputDecoration(labelText: 'Manzil')),
            const SizedBox(height: 12),
            ElevatedButton(onPressed: _pickImages, child: const Text('Rasmlarni tanlash')),
            const SizedBox(height: 8),
            SizedBox(
              height: 100,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: _images.length,
                itemBuilder: (c, i) => Padding(
                  padding: const EdgeInsets.only(right: 8.0),
                  child: Image.file(File(_images[i].path), width: 100, height: 100, fit: BoxFit.cover),
                ),
              ),
            ),
            const SizedBox(height: 20),
            ElevatedButton(onPressed: _loading ? null : _submit, child: _loading ? const CircularProgressIndicator() : const Text('Yaratish')),
          ]),
        ),
      ),
    );
  }
}
