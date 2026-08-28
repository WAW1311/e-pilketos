import 'package:epilketos/widget/votingcode.dart';
import 'package:flutter/material.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _scaleAnim;
  late Animation<double> _opacityAnim;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    )..forward();
    _scaleAnim =
        CurvedAnimation(parent: _controller, curve: Curves.easeOutBack);
    _opacityAnim = CurvedAnimation(parent: _controller, curve: Curves.easeIn);

    // Tunggu splash selesai lalu pindah ke halaman kode.
    Future.delayed(const Duration(seconds: 3), () {
      if (mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const VotingCode()),
        );
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  /// Skala ukuran berdasarkan lebar layar (base 400px, clamp 0.8–1.6).
  double s(double v, double w) => v * (w / 400).clamp(0.8, 1.6);

  @override
  Widget build(BuildContext context) {
    final w = MediaQuery.of(context).size.width;

    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF00B6EE), Color(0xFF0175B8)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // ----- Logo (code-based, bukan gambar) -----
              ScaleTransition(
                scale: _scaleAnim,
                child: Container(
                  width: s(104, w),
                  height: s(104, w),
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.25),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Icon(
                    Icons.how_to_vote,
                    size: s(52, w),
                    color: const Color(0xFF0175B8),
                  ),
                ),
              ),
              SizedBox(height: s(24, w)),

              // ----- Title -----
              FadeTransition(
                opacity: _opacityAnim,
                child: Column(
                  children: [
                    Text(
                      'E-Voting',
                      style: TextStyle(
                        fontSize: s(34, w),
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        letterSpacing: 1.2,
                      ),
                    ),
                    SizedBox(height: s(8, w)),
                    Text(
                      'Pilketos SMA Negeri 1 Ulujami',
                      style: TextStyle(
                        fontSize: s(15, w),
                        fontWeight: FontWeight.w500,
                        color: Colors.white.withValues(alpha: 0.9),
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),

              SizedBox(height: s(56, w)),

              // ----- Loading indicator -----
              SizedBox(
                width: s(46, w),
                height: s(46, w),
                child: const CircularProgressIndicator(
                  strokeWidth: 3,
                  color: Colors.white,
                  backgroundColor: Colors.white24,
                ),
              ),
              SizedBox(height: s(16, w)),
              Text(
                'Menyiapkan aplikasi...',
                style: TextStyle(
                  fontSize: s(14, w),
                  color: Colors.white.withValues(alpha: 0.85),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
