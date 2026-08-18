import 'package:flutter/material.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  Widget _vipCard(String title, String price) {
    return Container(
      width: 200,
      margin: const EdgeInsets.only(right: 12),
      decoration: BoxDecoration(
        color: const Color(0xFF1B1B1D),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            height: 110,
            decoration: BoxDecoration(
              color: Colors.grey[800],
              borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(price, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                const SizedBox(height: 4),
                Text(title, style: TextStyle(color: Colors.white70)),
              ],
            ),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Row(children: [
          const CircleAvatar(backgroundColor: Colors.yellow, child: Icon(Icons.home, color: Colors.black)),
          const SizedBox(width: 8),
          const Text('JOY MEE', style: TextStyle(fontWeight: FontWeight.bold)),
        ]),
        actions: [IconButton(onPressed: (){}, icon: const Icon(Icons.favorite_border)), IconButton(onPressed: (){}, icon: const Icon(Icons.notifications_none))],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                decoration: BoxDecoration(color: const Color(0xFF1B1B1D), borderRadius: BorderRadius.circular(12)),
                child: Row(children: [const Icon(Icons.search, color: Colors.white54), const SizedBox(width: 8), Expanded(child: Text('Qidirish', style: TextStyle(color: Colors.white54))) ]),
              ),
              const SizedBox(height: 16),
              Row(children: [
                GestureDetector(
                  onTap: () {},
                  child: Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8), decoration: BoxDecoration(color: Colors.yellow[700], borderRadius: BorderRadius.circular(18)), child: const Text('Sotuv', style: TextStyle(color: Colors.black))),
                ),
                const SizedBox(width: 8),
                Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8), decoration: BoxDecoration(color: const Color(0xFF1B1B1D), borderRadius: BorderRadius.circular(18)), child: const Text('Ijara')),
                const SizedBox(width: 8),
                Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8), decoration: BoxDecoration(color: const Color(0xFF1B1B1D), borderRadius: BorderRadius.circular(18)), child: const Text('Kunlik')),
              ]),
              const SizedBox(height: 20),
              const Text('VIP E\'lonlar', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              SizedBox(
                height: 190,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  children: [
                    _vipCard('Sugdiyona mahallasi', '187 000 y.e'),
                    _vipCard('Olmazor tumani, 8-etaj', '650 y.e'),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              const Text("Topildi: 93,794", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              GridView.count(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisCount: 2,
                mainAxisSpacing: 12,
                crossAxisSpacing: 12,
                children: List.generate(4, (index) => Container(decoration: BoxDecoration(color: const Color(0xFF1B1B1D), borderRadius: BorderRadius.circular(12)), height: 140)),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Asosiy'),
          BottomNavigationBarItem(icon: Icon(Icons.people), label: 'Sotuvchilar'),
          BottomNavigationBarItem(icon: Icon(Icons.add_circle_outline), label: 'Qo\'shish'),
          BottomNavigationBarItem(icon: Icon(Icons.chat_bubble_outline), label: 'Xabarlar'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
        currentIndex: 0,
        onTap: (i) {},
      ),
    );
  }
}
