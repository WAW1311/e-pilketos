import 'package:evoting_pilketos/service/votepapperservice.dart';
import 'package:flutter/material.dart';

class VotingCode extends StatefulWidget {
  const VotingCode({super.key});

  @override
  State<VotingCode> createState() => _VotingCodeState();
}

class _VotingCodeState extends State<VotingCode> {
  final TextEditingController _kodeController = TextEditingController();
  final VotePaperService votePaperService = VotePaperService();
  bool isLoading = false;
  void findVoterCode(context) async {
    String kode = _kodeController.text.trim();
    if (kode.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Kode voting tidak boleh kosong')),
      );
      return;
    }
    setState(() {
      isLoading = true;
    });
    Future.delayed(const Duration(seconds: 2), () async {
      var data = await votePaperService.getVotePapers(kode);
      setState(() {
        isLoading = false;
      });
      if (data['status'] == false) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('kode tidak valid, coba lagi')),
        );
        return;
      }
      Navigator.pushReplacementNamed(context, '/home', arguments: data['data']);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: DecoratedBox(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Color(0xFF00B6EE),
              Color(0xFF0175B8),
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: Center(
          child: Container(
            padding: const EdgeInsets.all(16),
            margin: const EdgeInsets.symmetric(horizontal: 20),
            decoration: BoxDecoration(
              color: Color.fromRGBO(255, 255, 255, 1),
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  blurRadius: 10,
                  color: Colors.black12,
                )
              ],
            ),
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text(
                    'Silahkan masukkan kode voting',
                    style: TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF00B6EE),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(16),
                        child: Image.asset(
                          'assets/voting_ilustration.jpg',
                          width: 120,
                          height: 100,
                          fit: BoxFit.cover,
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          children: [
                            const SizedBox(height: 10),
                            TextField(
                              controller: _kodeController,
                              decoration: InputDecoration(
                                border: OutlineInputBorder(),
                                labelText: 'Kode Voting',
                                hintText: 'Code Voting Here',
                              ),
                            ),
                            const SizedBox(height: 10),
                            ElevatedButton(
                              onPressed: () {
                                findVoterCode(context);
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.blue,
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 20, vertical: 10),
                              ),
                              child: isLoading
                                  ? const CircularProgressIndicator(
                                      color: Colors.white,
                                    )
                                  : const Text('Masuk',
                                      style: TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: Colors.white)),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
