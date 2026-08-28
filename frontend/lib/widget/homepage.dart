import 'dart:async';

import 'package:epilketos/service/votepapperservice.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
// import 'package:flutter_dotenv/flutter_dotenv.dart';

enum VotingSessionStatus { waiting, active, ended, unavailable }

class HomePage extends StatefulWidget {
  final Map data;
  const HomePage({super.key, required this.data});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final VotePaperService votePaperService = VotePaperService();
  final url = "https://epilketos.wawtech.id";
  // final url = dotenv.env['API_URL'];
  bool nisDialogShown = false;
  String? verifiedNis;
  String? _votingToken;
  late final DateTime? _startsAt;
  late final DateTime? _endsAt;
  late DateTime _now;
  Timer? _sessionTimer;
  late final ValueNotifier<DateTime> _sessionTick;
  bool _sessionDialogVisible = false;
  bool _isLeavingToVotingCode = false;
  Future<void>? _sessionDialogFuture;

  // ── Responsive helpers ──────────────────────────────────────────
  double _s(double v, double w) => v * (w / 400).clamp(0.85, 1.55);
  double get _screenW => MediaQuery.of(context).size.width;

  // ── Palet teks dialog (kontras tinggi, mudah dibaca) ────────────
  static const Color _dTitle = Color(0xFF111827); // judul — hampir hitam
  static const Color _dBody = Color(0xFF374151); // isi/subjudul — abu gelap
  static const Color _dMuted = Color(0xFF6B7280); // caption — abu sedang

  @override
  void initState() {
    super.initState();
    _startsAt = _parseScheduleTime(widget.data['data']['dimulai']);
    _endsAt = _parseScheduleTime(widget.data['data']['berakhir']);
    _now = DateTime.now();
    _sessionTick = ValueNotifier<DateTime>(_now);
    _sessionTimer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (!mounted) return;
      final wasActive = _sessionStatus == VotingSessionStatus.active;
      final now = DateTime.now();
      setState(() => _now = now);
      _sessionTick.value = now;

      if (_sessionStatus == VotingSessionStatus.active) {
        if (!wasActive) {
          _dismissSessionDialog().then((_) {
            if (mounted) _showNisDialogIfNeeded();
          });
        }
      } else {
        _showSessionDialog();
      }
    });

    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_sessionStatus == VotingSessionStatus.active) {
        _showNisDialogIfNeeded();
      } else {
        _showSessionDialog();
      }
    });
  }

  @override
  void dispose() {
    _sessionTimer?.cancel();
    _sessionTick.dispose();
    super.dispose();
  }

  DateTime? _parseScheduleTime(dynamic value) {
    if (value is! String || value.trim().isEmpty) return null;
    return DateTime.tryParse(value)?.toLocal();
  }

  VotingSessionStatus get _sessionStatus {
    final startsAt = _startsAt;
    final endsAt = _endsAt;
    if (startsAt == null || endsAt == null || !endsAt.isAfter(startsAt)) {
      return VotingSessionStatus.unavailable;
    }
    if (_now.isBefore(startsAt)) return VotingSessionStatus.waiting;
    if (!_now.isBefore(endsAt)) return VotingSessionStatus.ended;
    return VotingSessionStatus.active;
  }

  bool get _isVotingActive => _sessionStatus == VotingSessionStatus.active;

  String _formatCountdown(Duration duration) {
    final totalSeconds = duration.isNegative ? 0 : duration.inSeconds;
    final hours = totalSeconds ~/ 3600;
    final minutes = (totalSeconds % 3600) ~/ 60;
    final seconds = totalSeconds % 60;
    return '${hours.toString().padLeft(2, '0')}:${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}';
  }

  String _formatScheduleTime(DateTime time) {
    return '${time.day.toString().padLeft(2, '0')}/${time.month.toString().padLeft(2, '0')}/${time.year} '
        '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
  }

  String get _remainingTime {
    switch (_sessionStatus) {
      case VotingSessionStatus.waiting:
        return _formatCountdown(_startsAt!.difference(_now));
      case VotingSessionStatus.active:
        return _formatCountdown(_endsAt!.difference(_now));
      case VotingSessionStatus.ended:
      case VotingSessionStatus.unavailable:
        return '00:00:00';
    }
  }

  void _showNisDialogIfNeeded() {
    if (!nisDialogShown && mounted && _isVotingActive) {
      nisDialogShown = true;
      showNisDialog();
    }
  }

  Future<void> _dismissSessionDialog() async {
    if (!_sessionDialogVisible || !mounted) return;
    Navigator.of(context, rootNavigator: true).pop();
    await _sessionDialogFuture;
  }

  void _returnToVotingCode() {
    if (_isLeavingToVotingCode || !mounted) return;
    _isLeavingToVotingCode = true;
    _sessionTimer?.cancel();
    Navigator.pop(context);
    Navigator.of(context).pushNamed('/code');
  }

  // ═══════════════════════════════════════════════════════════════
  //  SESSION DIALOG
  // ═══════════════════════════════════════════════════════════════
  void _showSessionDialog() {
    if (!mounted ||
        _isVotingActive ||
        _sessionDialogVisible ||
        _isLeavingToVotingCode) {
      return;
    }

    _sessionDialogVisible = true;
    final w = _screenW;
    final dialogFuture = showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return PopScope(
          canPop: false,
          child: ValueListenableBuilder<DateTime>(
            valueListenable: _sessionTick,
            builder: (context, _, __) {
              final status = _sessionStatus;
              final isWaiting = status == VotingSessionStatus.waiting;
              final isEnded = status == VotingSessionStatus.ended;
              final title = isWaiting
                  ? 'Voting Belum Dimulai'
                  : isEnded
                      ? 'Voting Telah Berakhir'
                      : 'Jadwal Voting Tidak Tersedia';
              final message = isWaiting
                  ? 'Sesi voting akan dimulai dalam:'
                  : isEnded
                      ? 'Sesi voting sudah ditutup.'
                      : 'Waktu mulai atau berakhir belum valid.';
              final iconData = isWaiting
                  ? Icons.schedule_rounded
                  : isEnded
                      ? Icons.lock_clock_rounded
                      : Icons.error_outline_rounded;
              final iconColor =
                  isWaiting ? const Color(0xFFF59E0B) : const Color(0xFFEF4444);

              return Dialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(_s(20, w))),
                child: Padding(
                  padding: EdgeInsets.all(_s(28, w)),
                  child: ConstrainedBox(
                    constraints: BoxConstraints(maxWidth: 400),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // icon circle
                        Container(
                          width: _s(72, w),
                          height: _s(72, w),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: iconColor.withValues(alpha: 0.12),
                          ),
                          child:
                              Icon(iconData, size: _s(36, w), color: iconColor),
                        ),
                        SizedBox(height: _s(16, w)),
                        Text(title,
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontSize: _s(18, w),
                                fontWeight: FontWeight.w700,
                                color: _dTitle)),
                        SizedBox(height: _s(10, w)),
                        Text(message,
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontSize: _s(14, w),
                                fontWeight: FontWeight.w500,
                                color: _dBody)),
                        if (isWaiting) ...[
                          SizedBox(height: _s(18, w)),
                          Container(
                            padding: EdgeInsets.symmetric(
                                horizontal: _s(20, w), vertical: _s(12, w)),
                            decoration: BoxDecoration(
                              color: const Color(0xFFEEF2FF),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              children: [
                                Text(_remainingTime,
                                    style: TextStyle(
                                        fontSize: _s(30, w),
                                        fontWeight: FontWeight.w800,
                                        color: const Color(0xFF3B82F6),
                                        letterSpacing: 2)),
                                SizedBox(height: _s(4, w)),
                                Text(
                                  'Dimulai: ${_formatScheduleTime(_startsAt!)}',
                                  style: TextStyle(
                                      fontSize: _s(12, w),
                                      fontWeight: FontWeight.w500,
                                      color: _dBody),
                                ),
                              ],
                            ),
                          ),
                        ],
                        if (isEnded) ...[
                          SizedBox(height: _s(20, w)),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF00B6EE),
                                padding:
                                    EdgeInsets.symmetric(vertical: _s(14, w)),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12)),
                              ),
                              onPressed: _returnToVotingCode,
                              icon: Icon(Icons.refresh_rounded,
                                  color: Colors.white, size: _s(18, w)),
                              label: Text('Kembali ke Kode Voting',
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w700,
                                      fontSize: _s(14, w))),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      },
    );
    _sessionDialogFuture = dialogFuture;
    dialogFuture.whenComplete(() {
      _sessionDialogVisible = false;
      _sessionDialogFuture = null;
      if (mounted && !_isVotingActive && !_isLeavingToVotingCode) {
        WidgetsBinding.instance
            .addPostFrameCallback((_) => _showSessionDialog());
      }
    });
  }

  // ═══════════════════════════════════════════════════════════════
  //  SUCCESS DIALOG
  // ═══════════════════════════════════════════════════════════════
  void showSuccessDialog(int noCandidate) {
    int countdown = 5;
    late StateSetter dialogSetState;

    Timer.periodic(const Duration(seconds: 1), (timer) {
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

    final w = _screenW;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            dialogSetState = setState;
            return Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(_s(20, w))),
              child: Padding(
                padding: EdgeInsets.all(_s(24, w)),
                child: ConstrainedBox(
                  constraints: BoxConstraints(maxWidth: 400),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Paksa lebar dialog memenuhi maxWidth (samakan dgn dialog konfirmasi)
                      const SizedBox(width: double.infinity),
                      Container(
                        width: _s(64, w),
                        height: _s(64, w),
                        decoration: const BoxDecoration(
                          shape: BoxShape.circle,
                          color: Color(0xFFECFDF5),
                        ),
                        child: Icon(Icons.check_circle_rounded,
                            size: _s(32, w), color: const Color(0xFF10B981)),
                      ),
                      SizedBox(height: _s(14, w)),
                      Text('Berhasil!',
                          style: TextStyle(
                              fontSize: _s(18, w),
                              fontWeight: FontWeight.w700,
                              color: _dTitle)),
                      SizedBox(height: _s(8, w)),
                      Text(
                        'Berhasil memilih kandidat $noCandidate',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                            fontSize: _s(14, w),
                            fontWeight: FontWeight.w500,
                            color: _dBody),
                      ),
                      SizedBox(height: _s(6, w)),
                      Text('Halaman akan direfresh dalam :',
                          style: TextStyle(
                              fontSize: _s(12, w),
                              fontWeight: FontWeight.w500,
                              color: _dMuted)),
                      SizedBox(height: _s(14, w)),
                      Container(
                        padding: EdgeInsets.symmetric(
                            horizontal: _s(16, w), vertical: _s(10, w)),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF0F4F8),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          '$countdown detik',
                          style: TextStyle(
                              fontSize: _s(26, w),
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF0175B8)),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  // ═══════════════════════════════════════════════════════════════
  //  CODE CONFIRMATION DIALOG (exit)
  // ═══════════════════════════════════════════════════════════════
  Future<bool> showCodeConfirmationDialog() async {
    final TextEditingController codeController = TextEditingController();
    bool confirmed = false;
    final w = _screenW;

    await showDialog(
      context: context,
      barrierDismissible: true,
      builder: (context) {
        return Dialog(
          shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(_s(20, w))),
          child: Padding(
            padding: EdgeInsets.all(_s(24, w)),
            child: ConstrainedBox(
              constraints: BoxConstraints(maxWidth: 400),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: _s(56, w),
                    height: _s(56, w),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: const Color(0xFFFEF2F2),
                    ),
                    child: Icon(Icons.logout_rounded,
                        size: _s(28, w), color: const Color(0xFFEF4444)),
                  ),
                  SizedBox(height: _s(14, w)),
                  Text('Konfirmasi Keluar',
                      style: TextStyle(
                          fontSize: _s(18, w),
                          fontWeight: FontWeight.w700,
                          color: _dTitle)),
                  SizedBox(height: _s(8, w)),
                  Text('Masukkan kode voting untuk keluar',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                          fontSize: _s(13, w),
                          fontWeight: FontWeight.w500,
                          color: _dBody)),
                  SizedBox(height: _s(18, w)),
                  TextField(
                    controller: codeController,
                    style: TextStyle(fontSize: _s(14, w)),
                    decoration: InputDecoration(
                      labelText: 'Kode Voting',
                      prefixIcon: const Icon(Icons.vpn_key_outlined),
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
                  SizedBox(height: _s(20, w)),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          style: OutlinedButton.styleFrom(
                            padding: EdgeInsets.symmetric(vertical: _s(12, w)),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12)),
                            side: const BorderSide(color: Color(0xFFEF4444)),
                          ),
                          onPressed: () => Navigator.pop(context),
                          child: Text('Batal',
                              style: TextStyle(
                                  color: const Color(0xFFEF4444),
                                  fontWeight: FontWeight.w700,
                                  fontSize: _s(14, w))),
                        ),
                      ),
                      SizedBox(width: _s(10, w)),
                      Expanded(
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF00B6EE),
                            padding: EdgeInsets.symmetric(vertical: _s(12, w)),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12)),
                          ),
                          onPressed: () {
                            final inputCode = codeController.text.trim();
                            final correctCode = widget.data['data']['vote_id'];
                            if (inputCode == correctCode) {
                              confirmed = true;
                              Navigator.pop(context);
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                    content: Text('Kode salah. Coba lagi.')),
                              );
                            }
                          },
                          child: Text('Konfirmasi',
                              style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w700,
                                  fontSize: _s(14, w))),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
    return confirmed;
  }

  /// Validasi format NIS di sisi klien sebelum memanggil API.
  String? _validateNis(String nis) {
    if (nis.isEmpty) return 'NIS tidak boleh kosong';
    if (!RegExp(r'^\d+$').hasMatch(nis)) return 'NIS hanya boleh berisi angka';
    if (nis.length < 4 || nis.length > 20) return 'Panjang NIS tidak valid';
    return null;
  }

  // ═══════════════════════════════════════════════════════════════
  //  NIS DIALOG
  // ═══════════════════════════════════════════════════════════════
  void showNisDialog() {
    if (!_isVotingActive) {
      _showSessionDialog();
      return;
    }

    final TextEditingController nisController = TextEditingController();
    bool isLoading = false;
    String? errorText;
    final w = _screenW;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            Future<void> verify() async {
              if (!_isVotingActive) {
                Navigator.pop(context);
                _showSessionDialog();
                return;
              }
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
                _votingToken = res['token'];
                Navigator.pop(context);
              } else {
                setState(() {
                  isLoading = false;
                  errorText = res['message'] ?? 'Verifikasi gagal';
                });
              }
            }

            return Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(_s(20, w))),
              child: Padding(
                padding: EdgeInsets.all(_s(24, w)),
                child: ConstrainedBox(
                  constraints: BoxConstraints(maxWidth: 400),
                  child: SingleChildScrollView(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: _s(64, w),
                          height: _s(64, w),
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            color: Color(0xFFEEF2FF),
                          ),
                          child: Icon(Icons.badge_rounded,
                              size: _s(32, w), color: const Color(0xFF3B82F6)),
                        ),
                        SizedBox(height: _s(14, w)),
                        Text('Verifikasi NIS',
                            style: TextStyle(
                                fontSize: _s(18, w),
                                fontWeight: FontWeight.w700,
                                color: _dTitle)),
                        SizedBox(height: _s(6, w)),
                        Text('Masukkan NIS untuk memulai voting',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontSize: _s(13, w),
                                fontWeight: FontWeight.w500,
                                color: _dBody)),
                        SizedBox(height: _s(20, w)),
                        TextField(
                          controller: nisController,
                          enabled: !isLoading,
                          keyboardType: TextInputType.number,
                          inputFormatters: [
                            FilteringTextInputFormatter.digitsOnly
                          ],
                          style: TextStyle(fontSize: _s(15, w)),
                          decoration: InputDecoration(
                            labelText: 'NIS',
                            hintText: 'Contoh: 12345',
                            prefixIcon: const Icon(Icons.person_outline_rounded,
                                color: Color(0xFF3B82F6)),
                            filled: true,
                            fillColor: const Color(0xFFF0F4F8),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide.none,
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(
                                  color: Color(0xFF3B82F6), width: 2),
                            ),
                          ),
                        ),
                        if (errorText != null) ...[
                          SizedBox(height: _s(8, w)),
                          Text(errorText!,
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                  color: const Color(0xFFDC2626),
                                  fontWeight: FontWeight.w600,
                                  fontSize: _s(12.5, w))),
                        ],
                        SizedBox(height: _s(20, w)),
                        Row(
                          children: [
                            // Expanded(
                            //   child: OutlinedButton(
                            //     style: OutlinedButton.styleFrom(
                            //       padding: EdgeInsets.symmetric(
                            //           vertical: _s(12, w)),
                            //       shape: RoundedRectangleBorder(
                            //           borderRadius: BorderRadius.circular(12)),
                            //       side: const BorderSide(
                            //           color: Color(0xFFEF4444)),
                            //     ),
                            //     onPressed: () => Navigator.pop(context),
                            //     child: Text('Batal',
                            //         style: TextStyle(
                            //             color: const Color(0xFFEF4444),
                            //             fontWeight: FontWeight.w700,
                            //             fontSize: _s(14, w))),
                            //   ),
                            // ),
                            // SizedBox(width: _s(10, w)),
                            Expanded(
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFF00B6EE),
                                  padding:
                                      EdgeInsets.symmetric(vertical: _s(12, w)),
                                  shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(12)),
                                ),
                                onPressed: isLoading ? null : verify,
                                child: isLoading
                                    ? SizedBox(
                                        width: _s(18, w),
                                        height: _s(18, w),
                                        child: const CircularProgressIndicator(
                                            color: Colors.white,
                                            strokeWidth: 2),
                                      )
                                    : Text('Verifikasi',
                                        style: TextStyle(
                                            color: Colors.white,
                                            fontWeight: FontWeight.w700,
                                            fontSize: _s(14, w))),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  // ═══════════════════════════════════════════════════════════════
  //  CONFIRMATION DIALOG (vote)
  // ═══════════════════════════════════════════════════════════════
  Future<void> showConfirmationDialog(BuildContext context, noCandidate) async {
    if (!_isVotingActive) {
      _showSessionDialog();
      return;
    }
    if (verifiedNis == null) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Verifikasi NIS terlebih dahulu')),
      );
      showNisDialog();
      return;
    }

    final w = _screenW;
    await showDialog<bool>(
      context: context,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(_s(20, w))),
        child: Padding(
          padding: EdgeInsets.all(_s(24, w)),
          child: ConstrainedBox(
            constraints: BoxConstraints(maxWidth: 400),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: _s(64, w),
                  height: _s(64, w),
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    color: Color(0xFFFEF9C3),
                  ),
                  child: Icon(Icons.help_outline_rounded,
                      size: _s(32, w), color: const Color(0xFFF59E0B)),
                ),
                SizedBox(height: _s(14, w)),
                Text('Konfirmasi Pilihan',
                    style: TextStyle(
                        fontSize: _s(18, w),
                        fontWeight: FontWeight.w700,
                        color: _dTitle)),
                SizedBox(height: _s(8, w)),
                Text(
                  'Kamu yakin memilih\nkandidat $noCandidate?',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                      fontSize: _s(14, w),
                      fontWeight: FontWeight.w500,
                      color: _dBody),
                ),
                SizedBox(height: _s(20, w)),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        style: OutlinedButton.styleFrom(
                          padding: EdgeInsets.symmetric(vertical: _s(12, w)),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                          side: const BorderSide(color: Color(0xFFEF4444)),
                        ),
                        onPressed: () => Navigator.pop(context, false),
                        child: Text('Tidak',
                            style: TextStyle(
                                color: const Color(0xFFEF4444),
                                fontWeight: FontWeight.w700,
                                fontSize: _s(14, w))),
                      ),
                    ),
                    SizedBox(width: _s(10, w)),
                    Expanded(
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF10B981),
                          padding: EdgeInsets.symmetric(vertical: _s(12, w)),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                        ),
                        onPressed: () async {
                          final result = await votePaperService.submitVote(
                            widget.data['data']['vote_id'],
                            widget.data['data'][
                                    'paslon_${noCandidate == 1 ? 'first' : noCandidate == 2 ? 'second' : 'third'}']
                                ['paslon_id'],
                            _votingToken,
                          );
                          if (!context.mounted) return;
                          if (result) {
                            showSuccessDialog(noCandidate);
                          } else {
                            Navigator.pop(context);
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                  content: Text('Gagal mengirim suara')),
                            );
                          }
                        },
                        child: Text('Ya, Pilih!',
                            style: TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700,
                                fontSize: _s(14, w))),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
  }

  // ═══════════════════════════════════════════════════════════════
  //  BUILD
  // ═══════════════════════════════════════════════════════════════
  @override
  Widget build(BuildContext context) {
    final w = _screenW;

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, _) async {
        if (didPop) return;
        final confirmed = await showCodeConfirmationDialog();
        if (confirmed && mounted) _returnToVotingCode();
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        body: Column(
          children: [
            // ── HEADER (code-based, no image) ──
            _headerBanner(w),
            // ── BODY ──
            Expanded(
              child: SingleChildScrollView(
                padding: EdgeInsets.symmetric(
                    horizontal: _s(16, w), vertical: _s(16, w)),
                child: Center(
                  child: ConstrainedBox(
                    constraints: BoxConstraints(maxWidth: w >= 600 ? 880 : 700),
                    child: Column(
                      children: [
                        // instruksi
                        Text(
                          'Pilih salah satu kandidat di bawah ini',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: _s(13, w),
                            fontWeight: FontWeight.w600,
                            color: Colors.grey.shade600,
                          ),
                        ),
                        SizedBox(height: _s(12, w)),
                        // grid kandidat
                        _kandidatGrid(w),
                        SizedBox(height: _s(24, w)),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ──────────────────────────────────────────────────────────────
  //  HEADER BANNER — red-white drapery + logos (code-based)
  // ──────────────────────────────────────────────────────────────
  Widget _headerBanner(double w) {
    final periode = widget.data['data']['periode'];
    return Stack(
      children: [
        // Background putih
        Container(
          width: double.infinity,
          height: _s(220, w),
          color: Colors.white,
        ),
        // Drapery merah-putih
        CustomPaint(
          size: Size(double.infinity, _s(100, w)),
          painter: _DraperyPainter(),
        ),
        // Content
        SafeArea(
          bottom: false,
          child: Padding(
            padding:
                EdgeInsets.fromLTRB(_s(16, w), _s(12, w), _s(16, w), _s(16, w)),
            child: Column(
              children: [
                // Top row: tombol Keluar + badge (+ countdown di bawahnya)
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    GestureDetector(
                      onTap: () async {
                        final confirmed = await showCodeConfirmationDialog();
                        if (confirmed && mounted) _returnToVotingCode();
                      },
                      child: Container(
                        padding: EdgeInsets.symmetric(
                            horizontal: _s(14, w), vertical: _s(8, w)),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF0F4F8),
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.08),
                              blurRadius: 4,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.logout_rounded,
                                color: const Color(0xFFDC2626),
                                size: _s(16, w)),
                            SizedBox(width: _s(6, w)),
                            Text(
                              'Keluar',
                              style: TextStyle(
                                color: const Color(0xFFDC2626),
                                fontWeight: FontWeight.w700,
                                fontSize: _s(13, w),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const Spacer(),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        _sessionBadge(w),
                        if (_isVotingActive) ...[
                          SizedBox(height: _s(8, w)),
                          _headerCountdown(w),
                        ],
                      ],
                    ),
                  ],
                ),
                SizedBox(height: _s(14, w)),
                // Logo row (4 logos)
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _logoCircle('assets/logo_provinsi.png', w),
                    _logoCircle('assets/logo_sekolah.png', w),
                    _logoCircle('assets/logo_osis.png', w),
                    _logoCircle('assets/logo_kpu.png', w),
                  ],
                ),
                SizedBox(height: _s(14, w)),
                // Judul
                Text(
                  'SURAT SUARA',
                  style: TextStyle(
                    fontSize: _s(22, w),
                    fontWeight: FontWeight.w800,
                    color: const Color(0xFFDC2626),
                    letterSpacing: 2,
                  ),
                ),
                SizedBox(height: _s(4, w)),
                Text(
                  'Pemilihan Ketua & Wakil OSIS',
                  style: TextStyle(
                    fontSize: _s(13, w),
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF1F2937),
                  ),
                ),
                SizedBox(height: _s(2, w)),
                Text(
                  'SMA Negeri 1 Ulujami',
                  style: TextStyle(
                    fontSize: _s(14, w),
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFFDC2626),
                  ),
                ),
                SizedBox(height: _s(2, w)),
                Text(
                  'Periode $periode',
                  style: TextStyle(
                    fontSize: _s(12, w),
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF6B7280),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _logoCircle(String assetPath, double w) {
    return Container(
      width: _s(48, w),
      height: _s(48, w),
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      padding: EdgeInsets.all(_s(6, w)),
      child: Image.asset(
        assetPath,
        fit: BoxFit.contain,
        errorBuilder: (_, __, ___) => Icon(
          Icons.account_balance_rounded,
          size: _s(24, w),
          color: const Color(0xFF9CA3AF),
        ),
      ),
    );
  }

  // badge status sesi di pojok kanan atas header
  Widget _sessionBadge(double w) {
    Color bg;
    Color fg;
    String label;
    IconData icon;

    switch (_sessionStatus) {
      case VotingSessionStatus.active:
        bg = const Color(0xFF10B981).withValues(alpha: 0.2);
        fg = Colors.white;
        label = 'Berlangsung';
        icon = Icons.play_circle_outline_rounded;
        break;
      case VotingSessionStatus.waiting:
        bg = const Color(0xFFF59E0B).withValues(alpha: 0.2);
        fg = Colors.white;
        label = 'Menunggu';
        icon = Icons.schedule_rounded;
        break;
      case VotingSessionStatus.ended:
        bg = const Color(0xFFEF4444).withValues(alpha: 0.2);
        fg = Colors.white;
        label = 'Selesai';
        icon = Icons.lock_rounded;
        break;
      case VotingSessionStatus.unavailable:
        bg = Colors.white.withValues(alpha: 0.2);
        fg = Colors.white;
        label = 'Tidak Tersedia';
        icon = Icons.error_outline_rounded;
        break;
    }

    return Container(
      padding: EdgeInsets.symmetric(horizontal: _s(12, w), vertical: _s(6, w)),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: _s(14, w), color: fg),
          SizedBox(width: _s(4, w)),
          Text(label,
              style: TextStyle(
                  fontSize: _s(11, w), fontWeight: FontWeight.w600, color: fg)),
        ],
      ),
    );
  }

  // ──────────────────────────────────────────────────────────────
  //  COUNTDOWN CHIP (di header kanan atas, di bawah badge)
  // ──────────────────────────────────────────────────────────────
  Widget _headerCountdown(double w) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: _s(12, w), vertical: _s(7, w)),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.12),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.timer_outlined,
              color: const Color(0xFFDC2626), size: _s(16, w)),
          SizedBox(width: _s(6, w)),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Berakhir dalam',
                style: TextStyle(
                    fontSize: _s(9.5, w),
                    fontWeight: FontWeight.w600,
                    color: _dMuted),
              ),
              Text(
                _remainingTime,
                style: TextStyle(
                  fontSize: _s(15, w),
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFFDC2626),
                  letterSpacing: 1,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ──────────────────────────────────────────────────────────────
  //  KANDIDAT GRID — selalu 3 kolom sejajar, card menyesuaikan lebar
  // ──────────────────────────────────────────────────────────────
  Widget _kandidatGrid(double w) {
    final kandidatList = [
      {
        'no': '1',
        'paslonKey': 'paslon_first',
        'imageKey': 'paslon1',
      },
      {
        'no': '2',
        'paslonKey': 'paslon_second',
        'imageKey': 'paslon2',
      },
      {
        'no': '3',
        'paslonKey': 'paslon_third',
        'imageKey': 'paslon3',
      },
    ];

    // Layar sedang/besar diberi ruang lebih lebar; mobile tetap seperti semula.
    final isBig = w >= 600;
    final maxContent = isBig ? 880.0 : 700.0;

    // Lebar konten sebenarnya (dibatasi maxWidth & padding body 16).
    final contentW = (w.clamp(0, maxContent).toDouble()) - _s(16, w) * 2;
    // Jarak antar card: lebih lega di layar sedang/besar, tetap rapat di mobile.
    final gap = isBig
        ? (contentW * 0.06).clamp(28.0, 52.0)
        : (contentW * 0.045).clamp(10.0, 22.0);
    // Lebar tiap card setelah dikurangi 2 gap.
    final cardW = (contentW - gap * 2) / 3;

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          for (int i = 0; i < kandidatList.length; i++) ...[
            if (i > 0) SizedBox(width: gap),
            Expanded(
              child: _kandidatCard(
                no: kandidatList[i]['no']!,
                imagePath:
                    '$url${widget.data['assets'][kandidatList[i]['imageKey']]}',
                nama1:
                    '${widget.data['data'][kandidatList[i]['paslonKey']]['ketua']['nama']}',
                nama2:
                    '${widget.data['data'][kandidatList[i]['paslonKey']]['wakil']['nama']}',
                cardW: cardW,
              ),
            ),
          ],
        ],
      ),
    );
  }

  // ──────────────────────────────────────────────────────────────
  //  KANDIDAT CARD — skala mengikuti lebar card (bukan lebar layar)
  // ──────────────────────────────────────────────────────────────
  Widget _kandidatCard({
    required String no,
    required String imagePath,
    required String nama1,
    required String nama2,
    required double cardW,
  }) {
    final isActive = _isVotingActive;
    // Skala relatif terhadap lebar card ideal (~170px). Rentang aman dari
    // layar kecil (3 card sempit) sampai tablet/layar besar.
    double c(double v) => v * (cardW / 170).clamp(0.62, 1.6);

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(c(16)),
      elevation: 0,
      child: InkWell(
        onTap: isActive
            ? () => showConfirmationDialog(context, int.parse(no))
            : null,
        borderRadius: BorderRadius.circular(c(16)),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: EdgeInsets.all(c(12)),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(c(16)),
            border: isActive
                ? Border.all(color: const Color(0xFF0175B8), width: 1.2)
                : Border.all(color: Colors.grey.shade200),
            // Shadow tipis agar card terangkat dari background putih.
            boxShadow: [
              BoxShadow(
                color: const Color.fromARGB(255, 71, 71, 71)
                    .withValues(alpha: 0.4),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Number badge
              Container(
                width: c(34),
                height: c(34),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF00B6EE), Color(0xFF0175B8)],
                  ),
                  borderRadius: BorderRadius.circular(c(10)),
                ),
                alignment: Alignment.center,
                child: Text(
                  no,
                  style: TextStyle(
                    fontSize: c(16),
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                  ),
                ),
              ),
              SizedBox(height: c(10)),
              // Foto kandidat (rasio tetap, diberi border)
              Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(c(12)),
                  border:
                      Border.all(color: const Color(0xFF0175B8), width: 1.2),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(c(11)),
                  child: AspectRatio(
                    aspectRatio: 1,
                    child: Image.network(
                      imagePath,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        color: const Color(0xFFF0F4F8),
                        child: Icon(Icons.person_rounded,
                            size: c(46), color: Colors.grey.shade400),
                      ),
                    ),
                  ),
                ),
              ),
              SizedBox(height: c(10)),
              // Nama ketua
              Text(
                nama1,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontSize: c(13),
                  height: 1.15,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF1F2937),
                ),
              ),
              SizedBox(height: c(2)),
              Text(
                'Ketua',
                style: TextStyle(
                  fontSize: c(10.5),
                  color: Colors.grey.shade500,
                ),
              ),
              SizedBox(height: c(6)),
              // Nama wakil
              Text(
                nama2,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontSize: c(13),
                  height: 1.15,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF1F2937),
                ),
              ),
              SizedBox(height: c(2)),
              Text(
                'Wakil',
                style: TextStyle(
                  fontSize: c(10.5),
                  color: Colors.grey.shade500,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Drapery merah-putih (kelambu) di bagian atas header — full code-based.
/// Meniru gaya kain lipat merah dengan lengkungan di bawahnya.
class _DraperyPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final w = size.width;
    final h = size.height;

    // Jumlah lipatan kain menyesuaikan lebar layar (agar tetap proporsional).
    final foldCount = (w / 55).round().clamp(6, 14);
    final foldWidth = w / foldCount;

    final redPaint = Paint()..style = PaintingStyle.fill;

    for (int i = 0; i < foldCount; i++) {
      final startX = i * foldWidth;
      final midX = startX + foldWidth / 2;
      final endX = startX + foldWidth;

      // Warna selang-seling agar lipatan terlihat (merah tua & merah terang).
      redPaint.color =
          i.isEven ? const Color(0xFFDC2626) : const Color(0xFFB91C1C);

      // Panjang jatuh kain: yang genap lebih panjang, membentuk gerigi.
      final dropShort = h * 0.55;
      final dropLong = h * 0.9;

      final path = Path()
        ..moveTo(startX, 0)
        ..lineTo(endX, 0)
        ..lineTo(endX, dropShort)
        // lengkung ke ujung bawah lipatan
        ..quadraticBezierTo(midX, dropLong, startX, dropShort)
        ..close();
      canvas.drawPath(path, redPaint);
    }

    // Garis dasar merah solid di paling atas agar rapi.
    final topBar = Paint()..color = const Color(0xFFDC2626);
    canvas.drawRect(Rect.fromLTWH(0, 0, w, h * 0.18), topBar);
  }

  @override
  bool shouldRepaint(covariant _DraperyPainter oldDelegate) => false;
}
