import 'dart:async';

import 'package:evoting_pilketos/service/mqttservice.dart';
// import 'package:evoting_pilketos/service/reverb_service.dart';
import 'package:evoting_pilketos/service/votepapperservice.dart';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
// import 'package:simple_flutter_reverb/simple_flutter_reverb.dart';
// import 'package:simple_flutter_reverb/simple_flutter_reverb_options.dart';

class HomePage extends StatefulWidget {
  final Map data;
  const HomePage({super.key, required this.data});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final VotePaperService votePaperService = VotePaperService();
  TextEditingController verified = TextEditingController();
  final url = dotenv.env['API_URL'];
  final mqtt = MqttService();
  bool showFingerprint = false;
  late int idFp = 0;
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

  void showFingerprintDialog() {
    Timer? verificationTimer;
    bool isVerified = false;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            verificationTimer ??=
                Timer.periodic(Duration(milliseconds: 500), (timer) {
              if (verified.text == 'matched') {
                timer.cancel();
                verificationTimer = null;

                if (context.mounted) {
                  setState(() {
                    isVerified = true;
                  });

                  Future.delayed(Duration(seconds: 2), () {
                    if (context.mounted) Navigator.pop(context);
                  });
                }
              } else {}
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
                    SizedBox(height: 12),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                      ),
                      onPressed: () async {
                        bool confirmed = await showCodeConfirmationDialog();
                        if (confirmed) {
                          Navigator.pushReplacementNamed(context, '/code');
                        } else {
                          Navigator.pop(context);
                          showFingerprintDialog();
                        }
                      },
                      child:
                          Text('Keluar', style: TextStyle(color: Colors.white)),
                    )
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
      verificationTimer?.cancel();
    });
  }

  Future<void> showConfirmationDialog(BuildContext context, noCandidate) async {
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
                    idFp,
                  );
                  print(result);
                  if (result) {
                    showSuccessDialog(noCandidate);
                  } else {
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
  void initState() {
    super.initState();
    mqtt.connect();
    mqtt.onMessageReceived = (message) {
      verified.text = message;
    };
    // connectReverb();
  }

  // Future<void> connectReverb() async {
  //   final options = SimpleFlutterReverbOptions(
  //     scheme: dotenv.env['REVERB_SCHEME']!,
  //     host: dotenv.env['DOMAIN']!,
  //     port: dotenv.env['REVERB_PORT']!,
  //     appKey: dotenv.env['REVERB_APP_KEY']!,
  //     authUrl: "",
  //     authToken: "",
  //     privatePrefix: "private-",
  //     usePrefix: dotenv.env['REVERB_IS_PRIVATE'] == 'true',
  //   );
  //   var reverbService = SimpleFlutterReverb(options: options);
  //   listenMessages(reverbService);
  // }

  // void listenMessages(SimpleFlutterReverb reverbService) {
  //   reverbService.listen(
  //     (message) {
  //       print("🔥 EVENT MASUK");
  //       print("  Event: ${message.event}");
  //       print("  Data : ${message.data}");

  //       if (message.event.contains("pusher_internal")) {
  //         print("ℹ️ Internal event diterima, dilewati...");
  //         return;
  //       }

  //       if (message.data == null || message.data.isEmpty) {
  //         print("⚠️ Tidak ada payload data dari backend");
  //         return;
  //       }

  //       if (message.data['data'] == null) {
  //         print("⚠️ Format data tidak sesuai, payload:");
  //         print(message.data);
  //         return;
  //       }
  //       verified.text = message.data['data']['decision'];
  //       setState(() {
  //         idFp = message.data['data']['id_fp'];
  //       });
  //     },
  //     "fp.${widget.data['data']['vote_id']}",
  //     isPrivate: false,
  //   );
  // }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!showFingerprint) {
      showFingerprint = true;
      WidgetsBinding.instance.addPostFrameCallback((_) {
        showFingerprintDialog();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
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
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      InkWell(
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
                      InkWell(
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
                          )),
                      InkWell(
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
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 40),
          ],
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
    print(imagePath);
    return Container(
      width: MediaQuery.of(context).size.width / 3 - 24,
      height: 242,
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
            height: 30,
            width: double.infinity,
            alignment: Alignment.center,
            child: Text(
              no,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
          ),
          Container(
            height: 100,
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
          Padding(
            padding: const EdgeInsets.all(4),
            child: Column(
              children: [
                Text(
                  '$nama1 (ketua)',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                      fontSize: 10, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 4),
                Text(
                  '$nama2 (wakil)',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                      fontSize: 10, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          )
        ],
      ),
    );
  }
}
