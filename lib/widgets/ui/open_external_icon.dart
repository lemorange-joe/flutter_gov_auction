import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../../includes/utilities.dart' as utilities;

class OpenExternalIcon extends StatelessWidget {
  const OpenExternalIcon({super.key, this.iconSize = 16.0});

  final double iconSize ;

  @override
  Widget build(BuildContext context) {
    return Icon(
      MdiIcons.openInNew,
      color: Colors.grey[600],
      size: iconSize * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
    );
  }
}
