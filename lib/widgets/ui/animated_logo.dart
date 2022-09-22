import 'package:flutter/material.dart';
import '../../includes/config.dart' as config;

class ShakeCurve extends Curve {
  @override
  double transform(double t) {
    //* pre condition
    //* t: 0 - 0.5
    double output = 0;

    if (t < 0.065) {
      // 6.5%: 0 -> -0.12
      // output = (t / 0.065) * (-0.12);

      // 6.5%: 0 -> -0.1
      // output = (t / 0.065) * (-0.1);

      // 6.5%: 0 -> -0.08
      // output = (t / 0.065) * (-0.08);

      // 6.5%: 0 -> -0.06
      output = (t / 0.065) * (-0.06);
    } else if (t < 0.185) {
      // 18.5%: -0.12 -> 0.09
      // output = ((t - 0.065) / (0.185 - 0.065)) * (0.09 - (-0.12)) + (-0.12);

      // 18.5%: -0.1 -> 0.075
      // output = ((t - 0.065) / (0.185 - 0.065)) * (0.075 - (-0.1)) + (-0.1);

      // 18.5%: -0.08 -> 0.06
      // output = ((t - 0.065) / (0.185 - 0.065)) * (0.06 - (-0.08)) + (-0.08);

      // 18.5%: -0.06 -> 0.045
      output = ((t - 0.065) / (0.185 - 0.065)) * (0.045 - (-0.06)) + (-0.06);
    } else if (t < 0.315) {
      // 31.5%: 0.09 -> -0.06
      // output = ((t - 0.185) / (0.315 - 0.185)) * ((-0.06) - 0.09) + 0.09;

      // 31.5%: 0.075 -> -0.05
      // output = ((t - 0.185) / (0.315 - 0.185)) * ((-0.05) - 0.075) + 0.075;

      // 31.5%: 0.06 -> -0.04
      // output = ((t - 0.185) / (0.315 - 0.185)) * ((-0.04) - 0.06) + 0.06;

      // 31.5%: 0.045 -> -0.03
      output = ((t - 0.185) / (0.315 - 0.185)) * ((-0.03) - 0.045) + 0.045;
    } else if (t < 0.435) {
      // 43.5%: -0.06 -> 0.03
      // output = ((t - 0.315) / (0.435 - 0.315)) * (0.03 - (-0.06)) + (-0.06);

      // 43.5%: -0.05 -> 0.025
      // output = ((t - 0.315) / (0.435 - 0.315)) * (0.025 - (-0.05)) + (-0.05);

      // 43.5%: -0.06 -> 0.03
      // output = ((t - 0.315) / (0.435 - 0.315)) * (0.02 - (-0.04)) + (-0.04);

      // 43.5%: -0.03 -> 0.015
      output = ((t - 0.315) / (0.435 - 0.315)) * (0.015 - (-0.03)) + (-0.03);
    } else if (t < 0.5) {
      // 50%: 0.03 -> 0
      // output = ((t - 0.435) / (0.5 - 0.435)) * (0 - 0.03) + 0.03;

      // 50%: 0.025 -> 0
      // output = ((t - 0.435) / (0.5 - 0.435)) * (0 - 0.025) + 0.025;

      // 50%: 0.03 -> 0
      // output = ((t - 0.435) / (0.5 - 0.435)) * (0 - 0.02) + 0.02;

      // 50%: 0.015 -> 0
      output = ((t - 0.435) / (0.5 - 0.435)) * (0 - 0.015) + 0.015;
    } else if (t == 1.0) {
      output = 1.0; 
    } else {
      output = 0;
    }

    return output;
  }
}

class LemorangeLogo extends StatefulWidget {
  LemorangeLogo({Key? key}) : super(key: key);

  final int animationDuration = 1000;
  final int visibleAnimationDuration = 500;
  final _LemorangeLogoState _lemorangeLogoState = _LemorangeLogoState();

  @override
  State<LemorangeLogo> createState() => _LemorangeLogoState();

  void animate() {
    _lemorangeLogoState.animate();
  }
}

class _LemorangeLogoState extends State<LemorangeLogo> with SingleTickerProviderStateMixin {
  // Animation<double> animation;
  late AnimationController rotationController;

  @override
  void initState() {
    super.initState();
    rotationController = AnimationController(
      duration: Duration(milliseconds: widget.animationDuration),
      vsync: this,
    );

    rotationController.addListener(() {
      setState(() {
        if (rotationController.status == AnimationStatus.completed) {
          rotationController.reset();
        }
      });
    });
  }

  @override
  void dispose() {
    rotationController.dispose();
    super.dispose();
  }

  void animate() {
    rotationController.forward();
  }

  @override
  Widget build(BuildContext context) {
    final Animation<double> curve = CurvedAnimation(parent: rotationController, curve: ShakeCurve());

    return GestureDetector(
      onTap: () {
        rotationController.forward();
      },
      child: RotationTransition(
        turns: Tween<double>(begin: 0.0, end: 0.5).animate(curve),
        child: Image.asset(
          config.env == 'dev' ? 'assets/images/office_logo.png' : 'assets/images/lemorange_logo.png',
          isAntiAlias: true,
        ),
      ),
    );
  }
}
