import 'dart:math';
import 'package:audioplayers/audioplayers.dart';
import 'package:confetti/confetti.dart';
import 'package:flutter/material.dart';

class EasterEgg extends StatefulWidget {
  const EasterEgg({Key? key}) : super(key: key);

  @override
  State<EasterEgg> createState() => _EasterEggState();
}

class _EasterEggState extends State<EasterEgg> {
  late ConfettiController _starConfettiController;
  late ConfettiController _rectangleConfettiController;
  late AudioPlayer _audioPlayer;

  static const int _fireworksInterval = 500; // ms
  static const int _fireworksLoop = 10; // ms

  @override
  void initState() {
    super.initState();
    _starConfettiController = ConfettiController(duration: const Duration(seconds: 10));
    _rectangleConfettiController = ConfettiController(duration: const Duration(seconds: 10));
    _audioPlayer = AudioPlayer();
    _audioPlayer.setReleaseMode(ReleaseMode.loop);

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await _audioPlayer.play(AssetSource('audio/fireworks.mp3'));
      _starConfettiController.play();
      _rectangleConfettiController.play();
    });
  }

  @override
  void dispose() {
    _audioPlayer.stop();
    _audioPlayer.dispose();

    _starConfettiController.dispose();
    _rectangleConfettiController.dispose();

    super.dispose();
  }

  /// A custom Path to paint stars.
  Path drawStar(Size size) {
    // Method to convert degree to radians
    double degToRad(double deg) => deg * (pi / 180.0);

    const int numberOfPoints = 5;
    final double halfWidth = size.width / 2;
    final double externalRadius = halfWidth;
    final double internalRadius = halfWidth / 2.5;
    final double degreesPerStep = degToRad(360 / numberOfPoints);
    final double halfDegreesPerStep = degreesPerStep / 2;
    final Path path = Path();
    final double fullAngle = degToRad(360);
    path.moveTo(size.width, halfWidth);

    for (double step = 0; step < fullAngle; step += degreesPerStep) {
      path.lineTo(halfWidth + externalRadius * cos(step), halfWidth + externalRadius * sin(step));
      path.lineTo(halfWidth + internalRadius * cos(step + halfDegreesPerStep), halfWidth + internalRadius * sin(step + halfDegreesPerStep));
    }
    path.close();
    return path;
  }

  Future<void> play() async {
    for (int i = 0; i < _fireworksLoop; i += 1) {
      await Future<void>.delayed(const Duration(milliseconds: _fireworksInterval));
    }
  }

  Widget _buildConfettiSprinkler(double top, double left, double direction,
      {int particles = 10, double minBlastForce = 5, double maxBlastForce = 20, double frequency = 0.02}) {
    return Positioned(
      top: top,
      left: left,
      child: ConfettiWidget(
        confettiController: _rectangleConfettiController,
        blastDirection: direction,
        shouldLoop: true,
        maxBlastForce: maxBlastForce,
        minBlastForce: minBlastForce,
        emissionFrequency: frequency,
        numberOfParticles: particles,
        gravity: 1,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Stack(
        children: <Widget>[
          Positioned(
            top: MediaQuery.of(context).size.height / 4,
            left: MediaQuery.of(context).size.width / 2,
            child: ConfettiWidget(
              numberOfParticles: 40,
              emissionFrequency: 0.01,
              confettiController: _starConfettiController,
              blastDirectionality: BlastDirectionality.explosive,
              shouldLoop: true,
              colors: const <Color>[Colors.green, Colors.blue, Colors.pink, Colors.orange, Colors.purple],
              createParticlePath: drawStar, // define a custom shape/path.
            ),
          ),
          _buildConfettiSprinkler(0.0, MediaQuery.of(context).size.width / 4, pi * 0.5, particles: 6, frequency: 0.03),
          _buildConfettiSprinkler(0.0, MediaQuery.of(context).size.width / 2, pi * 0.5, particles: 5, frequency: 0.04),
          _buildConfettiSprinkler(0.0, MediaQuery.of(context).size.width / 4 * 3, pi * 0.5, particles: 4, frequency: 0.05),
          _buildConfettiSprinkler(
            MediaQuery.of(context).size.height,
            0.0,
            pi * 1.55,
            particles: 5,
            minBlastForce: 300.0,
            maxBlastForce: 360.0,
            frequency: 0.05,
          ),
          _buildConfettiSprinkler(
            MediaQuery.of(context).size.height,
            MediaQuery.of(context).size.width,
            pi * 1.45,
            particles: 5,
            minBlastForce: 320.0,
            maxBlastForce: 340.0,
            frequency: 0.05,
          ),          
        ],
      ),
    );
  }
}
