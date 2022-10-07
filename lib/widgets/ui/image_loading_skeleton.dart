import 'package:flutter/material.dart';
import 'package:shimmer_animation/shimmer_animation.dart';
import '../../includes/config.dart' as config;

class ImageLoadingSkeleton extends StatelessWidget {
  const ImageLoadingSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer(
      duration: const Duration(seconds: 2),
      interval: const Duration(milliseconds: 500),
      colorOpacity: 0.5,
      child: Container(
        decoration:  BoxDecoration(
          borderRadius: BorderRadius.circular(config.mdBorderRadius),
          color: Colors.grey[300],
        ),
      ),
    );
  }
}
