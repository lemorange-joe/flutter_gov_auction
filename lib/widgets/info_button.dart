import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import './common/dialog.dart';
import '../generated/l10n.dart';
import '../includes/utilities.dart' as utilities;


class InfoButton extends StatelessWidget {
  const InfoButton(this.title, this.content, {super.key, this.iconColor});

  final String title;
  final String content;
  final Color? iconColor;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 32.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      height: 32.0 * utilities.adjustedScale(MediaQuery.of(context).textScaleFactor),
      child: IconButton(
        onPressed: () {
          CommonDialog.show(
            context,
            title,
            content,
            S.of(context).close,
            () {
              Navigator.pop(context);
            },
          );
        },
        iconSize: 16.0,
        splashRadius: 18.0,
        icon: Semantics(
          label: S.of(context).semanticsMoreInfo,
          button: true,
          child: Icon(
            MdiIcons.messageTextOutline,
            color: iconColor == null ? Colors.grey[600] : iconColor!,
          ),
        ),
      ),
    );
  }
}
