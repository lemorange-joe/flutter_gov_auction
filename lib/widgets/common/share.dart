import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:share_plus/share_plus.dart';
import './snackbar.dart';
// import '../../generated/l10n.dart';

class CommonShare {
  const CommonShare();

  static Future<void> share(BuildContext context, String url, String subject) async {
    final Size size = MediaQuery.of(context).size; // for iPad
    Share.shareWithResult(
      url,
      subject: subject,
      sharePositionOrigin: Rect.fromLTWH(0, 0, size.width, size.height / 2),
    ).then((ShareResult result) {
      if (result.status == ShareResultStatus.success) {
        CommonSnackbar.show(context, null, Colors.transparent, 'Share completed');
      }
    });

    // Share.shareWithResult(
    //   url,
    //   subject: subject,
    //   sharePositionOrigin: Rect.fromLTWH(0, 0, size.width, size.height / 2),
    // ).then((ShareResult result) {
    //   if (result.status == ShareResultStatus.success) {
    //     CommonSnackbar.show(context, null, Colors.transparent, 'Share completed');
    //   }
    // });
  }
}
