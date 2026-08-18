import 'dart:async';

import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

import '../services/payment_service.dart';

class CheckoutWebView extends StatefulWidget {
  final String url;
  final int txId;
  final String returnUrl; // when webview navigates to this URL, we consider payment finished

  const CheckoutWebView({super.key, required this.url, required this.txId, required this.returnUrl});

  @override
  State<CheckoutWebView> createState() => _CheckoutWebViewState();
}

class _CheckoutWebViewState extends State<CheckoutWebView> {
  late final WebViewController _controller;
  bool _loading = true;
  final _paymentService = PaymentService();

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(NavigationDelegate(
        onPageStarted: (url) {
          if (url.startsWith(widget.returnUrl)) {
            // Close webview and let caller poll transaction
            if (mounted) Navigator.of(context).pop(true);
          }
        },
        onPageFinished: (url) {
          setState(() { _loading = false; });
        },
      ))
      ..loadRequest(Uri.parse(widget.url));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('To\'lov sahifasi')),
      body: Stack(children: [
        WebViewWidget(controller: _controller),
        if (_loading) const Center(child: CircularProgressIndicator()),
      ]),
    );
  }
}
