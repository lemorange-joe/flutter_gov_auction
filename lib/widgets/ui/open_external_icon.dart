import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../../includes/utilities.dart' as utilities;

class OpenExternalIcon extends StatelessWidget {
  const OpenExternalIcon({super.key, this.size = 16.0, this.color});

  final double size;
  final Color? color;

  @override
  Widget build(BuildContext context) {
    return Icon(
      MdiIcons.openInNew,
      color: color ?? Colors.grey[600],
      size: size * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
    );
  }
}
