import 'package:flutter/material.dart';
import 'screens/login_screen.dart';

void main() {
  runApp(const UysotApp());
}

class UysotApp extends StatelessWidget {
  const UysotApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Uysot',
      debugShowCheckedModeBanner: false,
      theme: ThemeData.dark().copyWith(
        scaffoldBackgroundColor: const Color(0xFF0E0E10),
        primaryColor: Colors.yellow[700],
        bottomNavigationBarTheme: BottomNavigationBarThemeData(
          backgroundColor: Colors.black,
          selectedItemColor: Colors.yellow[700],
          unselectedItemColor: Colors.white70,
        ),
      ),
      home: const LoginScreen(),
    );
  }
}
