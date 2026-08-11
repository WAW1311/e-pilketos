import 'dart:async';

import 'package:evoting_pilketos/service/votepapperservice.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';

class HomePage extends StatefulWidget {
  final Map data;
  const HomePage({super.key, required this.data});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final VotePaperService votePaperService = VotePaperService();
  final url = dotenv.env['API_URL'];
  bool nisDialogShown = false;
  String? verifiedNis;

  void showSuccessDialog(int noCandidate) {
    int countdown = 5;
    late StateSetter dialogSetState;

    Timer.periodic(Duration(seconds: 1), (timer) {
      if (countdown == 1) {
        timer.cancel();
        if (context.mounted) {
          Navigator.of(context).pop();
          Navigator.pushReplacementNamed(context, '/home',
              arguments: widget.data);
        }
      } else {
        countdown--;
        dialogSetState(() {});
      }
    });

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            dialogSetState = setState;
            return AlertDialog(
              title: Center(child: Text('Berhasil!')),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text('Berhasil memilih kandidat $noCandidate'),
                  SizedBox(height: 10),
                  Text('Halaman akan direfresh dalam:'),
                  SizedBox(height: 10),
                  Text(
                    '$countdown detik',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  Future<bool> showCodeConfirmationDialog() async {
    TextEditingController codeController = TextEditingController();
    bool confirmed = false;

    await showDialog(
      context: context,
      barrierDismissible: true,
      builder: (context) {
        return AlertDialog(
          title: Center(
              child: Text(
            'Konfirmasi Keluar',
            style: TextStyle(fontSize: 18),
          )),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text('Masukkan kode voting untuk keluar'),
                SizedBox(height: 10),
                TextField(
                  controller: codeController,
                  decoration: InputDecoration(
                    labelText: 'Kode Voting',
                    border: OutlineInputBorder(),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red,
                  ),
                  onPressed: () {
                    Navigator.pop(context);
                  },
                  child: Text(
                    'Batal',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blue,
                  ),
                  onPressed: () {
                    String inputCode = codeController.text.trim();
                    String correctCode = widget.data['data']['vote_id'];

                    if (inputCode == correctCode) {
                      print(
                          {'inputCode': inputCode, 'correctCode': correctCode});
                      confirmed = true;
                      Navigator.pop(context);
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('Kode salah. Coba lagi.')),
                      );
                    }
                  },
                  child: Text(
                    'Konfirmasi',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
              ],
            ),
          ],
        );
      },
    );

    return confirmed;
  }

  /// Validasi format NIS di sisi klien sebelum memanggil API.
  /// Mengembalikan pesan error, atau null jika valid.
  String? _validateNis(String nis) {
    if (nis.isEmpty) {
      return 'NIS tidak boleh kosong';
    }
    if (!RegExp(r'^\d+$').hasMatch(nis)) {
      return 'NIS hanya boleh berisi angka';
    }
    if (nis.length < 4 || nis.length > 20) {
      return 'Panjang NIS tidak valid';
    }
    return null;
  }

  void showNisDialog() {
    final TextEditingController nisController = TextEditingController();
    bool isLoading = false;
    String? errorText;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            Future<void> verify() async {
              final nis = nisController.text.trim();
              final validationError = _validateNis(nis);
              if (validationError != null) {
                setState(() => errorText = validationError);
                return;
              }

              setState(() {
                isLoading = true;
                errorText = null;
              });

              final res = await votePaperService.verifyNis(
                widget.data['data']['vote_id'],
                nis,
              );

              if (!context.mounted) return;

              if (res['status'] == true && res['decision'] == 'matched') {
                verifiedNis = nis;
                Navigator.pop(context);
              } else {
                setState(() {
                  isLoading = false;
                  errorText = res['message'] ?? 'Verifikasi gagal';
                });
              }
            }

            return AlertDialog(
              title: Center(
                  child: Text(
                'Verifikasi Pemilih',
                style: TextStyle(fontSize: 16),
              )),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      Icons.badge_outlined,
                      size: 72,
                      color: Colors.blue,
                    ),
                    SizedBox(height: 12),
                    Text(
                      'Masukkan NIS untuk memverifikasi identitas Anda',
                      textAlign: TextAlign.center,
                    ),
                    SizedBox(height: 12),
                    TextField(
                      controller: nisController,
                      keyboardType: TextInputType.number,
                      inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                      enabled: !isLoading,
                      decoration: InputDecoration(
                        labelText: 'NIS',
                        border: OutlineInputBorder(),
                        errorText: errorText,
                      ),
                      onSubmitted: (_) => verify(),
                    ),
                    SizedBox(height: 12),
                    if (isLoading) CircularProgressIndicator(),
                  ],
                ),
              ),
              actions: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                      ),
                      onPressed: isLoading
                          ? null
                          : () async {
                              bool confirmed =
                                  await showCodeConfirmationDialog();
                              if (!context.mounted) return;
                              if (confirmed) {
                                Navigator.pop(context);
                                Navigator.pushReplacementNamed(context, '/code');
                              }
                            },
                      child: Text('Keluar',
                          style: TextStyle(color: Colors.white)),
                    ),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                      ),
                      onPressed: isLoading ? null : verify,
                      child: Text('Verifikasi',
                          style: TextStyle(color: Colors.white)),
                    ),
                  ],
                ),
              ],
            );
          },
        );
      },
    );
  }

  Future<void> showConfirmationDialog(BuildContext context, noCandidate) async {
    if (verifiedNis == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Verifikasi NIS terlebih dahulu')),
      );
      showNisDialog();
      return;
    }
    await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Center(child: Text('Konfirmasi')),
        content: Text('Kamu yakin memilih kandidat $noCandidate?'),
        actions: <Widget>[
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                ),
                onPressed: () => Navigator.pop(context, false),
                child: Text('Tidak', style: TextStyle(color: Colors.white)),
              ),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                ),
                onPressed: () async {
                  final result = await votePaperService.submitVote(
                    widget.data['data']['vote_id'],
                    widget.data['data'][
                            'paslon_${noCandidate == 1 ? 'first' : noCandidate == 2 ? 'second' : 'third'}']
                        ['paslon_id'],
                    verifiedNis,
                  );
                  print(result);
                  if (!context.mounted) return;
                  if (result) {
                    showSuccessDialog(noCandidate);
                  } else {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Gagal mengirim suara')),
                    );
                  }
                },
                child: Text('Ya', style: TextStyle(color: Colors.white)),
              ),
            ],
          ),
        ],
      ),
    );
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!nisDialogShown) {
      nisDialogShown = true;
      WidgetsBinding.instance.addPostFrameCallback((_) {
        showNisDialog();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color.fromARGB(255, 255, 255, 255),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 700),
          child: SingleChildScrollView(
            child: Column(
              children: [
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: Image.asset(
                'assets/header_rmbg.png',
                fit: BoxFit.cover,
              ),
            ),
            Stack(
              children: <Widget>[
                Text(
                  'SURAT SUARA',
                  style: TextStyle(
                    fontSize: 30,
                    fontWeight: FontWeight.bold,
                    foreground: Paint()
                      ..style = PaintingStyle.stroke
                      ..strokeWidth = 6
                      ..color = const Color.fromARGB(255, 255, 255, 255),
                  ),
                ),
                const Text(
                  'SURAT SUARA',
                  style: TextStyle(
                    fontSize: 30,
                    fontWeight: FontWeight.bold,
                    color: Colors.red,
                  ),
                ),
              ],
            ),
            const Text(
              'Pemilihan Ketua dan Wakil OSIS',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const Text(
              'SMA NEGERI 1 ULUJAMI',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Colors.red,
              ),
            ),
            Text(
              'Periode ${widget.data['data']['periode']}',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 20),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              child: Column(
                children: [
                  Text(
                    'Klik pada salah satu kandidat :',
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                        fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 10),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: InkWell(
                          onTap: () {
                            showConfirmationDialog(context, 1);
                          },
                          child: kandidatBox(
                            no: '1',
                            imagePath:
                                "${url!}${widget.data['assets']['paslon1']}",
                            nama1:
                                '${widget.data['data']['paslon_first']['ketua']['nama']}',
                            nama2:
                                '${widget.data['data']['paslon_first']['wakil']['nama']}',
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: InkWell(
                          onTap: () {
                            showConfirmationDialog(context, 2);
                          },
                          child: kandidatBox(
                            no: '2',
                            imagePath:
                                "${url!}${widget.data['assets']['paslon2']}",
                            nama1:
                                '${widget.data['data']['paslon_second']['ketua']['nama']}',
                            nama2:
                                '${widget.data['data']['paslon_second']['wakil']['nama']}',
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: InkWell(
                          onTap: () {
                            showConfirmationDialog(context, 3);
                          },
                          child: kandidatBox(
                            no: '3',
                            imagePath:
                                "${url!}${widget.data['assets']['paslon3']}",
                            nama1:
                                '${widget.data['data']['paslon_third']['ketua']['nama']}',
                            nama2:
                                '${widget.data['data']['paslon_third']['wakil']['nama']}',
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget kandidatBox({
    required String no,
    required String imagePath,
    required String nama1,
    required String nama2,
  }) {
    final isTablet = MediaQuery.of(context).size.shortestSide >= 600;
    final boxHeight = isTablet ? 320.0 : 242.0;
    final imageHeight = isTablet ? 150.0 : 100.0;
    final headerHeight = isTablet ? 40.0 : 30.0;
    final noSize = isTablet ? 24.0 : 18.0;
    final nameSize = isTablet ? 14.0 : 10.0;

    return Container(
      height: boxHeight,
      margin: const EdgeInsets.all(6),
      decoration: BoxDecoration(
        border: Border.all(
          color: Colors.black,
          width: 2,
        ),
        color: Colors.white,
      ),
      child: Column(
        children: [
          Container(
            color: Colors.white,
            height: headerHeight,
            width: double.infinity,
            alignment: Alignment.center,
            child: Text(
              no,
              style:
                  TextStyle(fontSize: noSize, fontWeight: FontWeight.bold),
            ),
          ),
          Container(
            height: imageHeight,
            width: double.infinity,
            decoration: BoxDecoration(
              border: Border(
                top: BorderSide(color: Colors.black, width: 2),
                bottom: BorderSide(color: Colors.black, width: 2),
                left: BorderSide(color: Colors.black, width: 1),
                right: BorderSide(color: Colors.black, width: 1),
              ),
            ),
            child: Image.network(
              imagePath,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) {
                return Center(child: Text('Gagal load gambar'));
              },
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(4),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    '$nama1 (ketua)',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                        fontSize: nameSize, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '$nama2 (wakil)',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                        fontSize: nameSize, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }
}
