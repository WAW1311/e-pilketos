import 'package:evoting_pilketos/widget/testing.dart';
import 'package:evoting_pilketos/widget/votingcode.dart';
import 'package:flutter/material.dart';
import '../widget/splash_screen.dart';
import '../widget/homepage.dart';

class Routes extends StatelessWidget {
  const Routes({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'E-voting Pilketos',
      initialRoute: '/',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        scaffoldBackgroundColor: const Color.fromRGBO(250, 241, 233, 1.0),
      ),
      routes: {
        '/': (context) => const SplashScreen(),
        '/code': (context) => const VotingCode(),
        '/test': (context) => const Testing(),
        '/home': (context) =>
            HomePage(data: ModalRoute.of(context)!.settings.arguments as Map)
      },
    );
  }
}
