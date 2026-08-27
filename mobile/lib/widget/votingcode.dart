import 'package:epilketos/service/votepapperservice.dart';
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

  @override
  void dispose() {
    _kodeController.dispose();
    super.dispose();
  }

  double s(double v, double w) => v * (w / 400).clamp(0.85, 1.6);

  void findVoterCode() async {
    final kode = _kodeController.text.trim();
    if (kode.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Kode voting tidak boleh kosong')),
      );
      return;
    }
    setState(() => isLoading = true);
    await Future.delayed(const Duration(seconds: 2));
    if (!mounted) return;
    final data = await votePaperService.getVotePapers(kode);
    if (!mounted) return;
    setState(() => isLoading = false);
    if (data['status'] == false) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Kode tidak valid, coba lagi')),
      );
      return;
    }
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, '/home', arguments: data['data']);
  }

  @override
  Widget build(BuildContext context) {
    final w = MediaQuery.of(context).size.width;

    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF00B6EE), Color(0xFF0175B8)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: EdgeInsets.symmetric(horizontal: s(24, w)),
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 480),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // ---- Logo code-based ----
                    Container(
                      width: s(80, w),
                      height: s(80, w),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.white,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.2),
                            blurRadius: 16,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Icon(
                        Icons.how_to_vote,
                        size: s(40, w),
                        color: const Color(0xFF0175B8),
                      ),
                    ),
                    SizedBox(height: s(16, w)),
                    Text(
                      'E-Voting Pilketos',
                      style: TextStyle(
                        fontSize: s(22, w),
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        letterSpacing: 0.8,
                      ),
                    ),
                    SizedBox(height: s(4, w)),
                    Text(
                      'SMA Negeri 1 Ulujami',
                      style: TextStyle(
                        fontSize: s(13, w),
                        color: Colors.white.withValues(alpha: 0.85),
                      ),
                    ),
                    SizedBox(height: s(32, w)),

                    // ---- Card form ----
                    Container(
                      width: double.infinity,
                      padding: EdgeInsets.all(s(24, w)),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.15),
                            blurRadius: 20,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Masukkan Kode Voting',
                            style: TextStyle(
                              fontSize: s(17, w),
                              fontWeight: FontWeight.w700,
                              color: const Color(0xFF0175B8),
                            ),
                          ),
                          SizedBox(height: s(6, w)),
                          Text(
                            'Kode voting telah diberikan oleh panitia.',
                            style: TextStyle(
                              fontSize: s(13, w),
                              color: Colors.grey.shade600,
                            ),
                          ),
                          SizedBox(height: s(20, w)),

                          // ---- Text field ----
                          TextField(
                            controller: _kodeController,
                            enabled: !isLoading,
                            style: TextStyle(fontSize: s(15, w)),
                            decoration: InputDecoration(
                              labelText: 'Kode Voting',
                              hintText: 'Masukkan kode di sini',
                              prefixIcon: const Icon(Icons.vpn_key_outlined,
                                  color: Color(0xFF0175B8)),
                              filled: true,
                              fillColor: const Color(0xFFF0F4F8),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide.none,
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: const BorderSide(
                                    color: Color(0xFF00B6EE), width: 2),
                              ),
                            ),
                          ),
                          SizedBox(height: s(20, w)),

                          // ---- Tombol Masuk ----
                          SizedBox(
                            width: double.infinity,
                            height: s(50, w),
                            child: DecoratedBox(
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [
                                    Color(0xFF00B6EE),
                                    Color(0xFF0175B8),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(12),
                                boxShadow: [
                                  BoxShadow(
                                    color: const Color(0xFF0175B8)
                                        .withValues(alpha: 0.4),
                                    blurRadius: 8,
                                    offset: const Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: ElevatedButton.icon(
                                onPressed:
                                    isLoading ? null : () => findVoterCode(),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.transparent,
                                  shadowColor: Colors.transparent,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                ),
                                icon: isLoading
                                    ? const SizedBox(
                                        width: 18,
                                        height: 18,
                                        child: CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 2,
                                        ),
                                      )
                                    : const Icon(Icons.login,
                                        color: Colors.white),
                                label: Text(
                                  isLoading ? 'Memverifikasi...' : 'Masuk',
                                  style: TextStyle(
                                    fontSize: s(15, w),
                                    fontWeight: FontWeight.w700,
                                    color: Colors.white,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    SizedBox(height: s(24, w)),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
