import 'dart:async';

import 'package:flutter/material.dart';

class Testing extends StatefulWidget {
  const Testing({super.key});

  @override
  State<Testing> createState() => _TestingState();
}

class _TestingState extends State<Testing> {
  var verified = '';

  void showFingerprintDialog() {
    Timer? verificationTimer;
    bool isVerified = false;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            // Jalankan timer hanya sekali
            verificationTimer ??=
                Timer.periodic(Duration(milliseconds: 9999999999), (timer) {
              if (verified == 'verified') {
                timer.cancel();
                verificationTimer = null;

                if (context.mounted) {
                  setState(() {
                    isVerified = true;
                  });

                  // Tunggu 2 detik lalu tutup dialog
                  Future.delayed(Duration(seconds: 100), () {
                    if (context.mounted) Navigator.pop(context);
                  });
                }
              }
            });

            return AlertDialog(
              title: Center(
                  child: Text(
                'Verifikasi Sidik Jari',
                style: TextStyle(fontSize: 15),
              )),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (!isVerified) ...[
                    // CircularProgressIndicator(),
                    Icon(
                      Icons.fingerprint_outlined,
                      size: 80,
                      color: Colors.blue,
                    ),
                    SizedBox(height: 12),
                    Text('Menunggu verifikasi sidik jari...'),
                  ] else ...[
                    Icon(Icons.check_circle, color: Colors.green, size: 48),
                    SizedBox(height: 12),
                    Text('Berhasil verifikasi!',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ],
              ),
            );
          },
        );
      },
    ).then((_) {
      // Pastikan timer dibatalkan kalau dialog ditutup lebih awal
      verificationTimer?.cancel();
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      showFingerprintDialog();
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Placeholder();
  }
}
