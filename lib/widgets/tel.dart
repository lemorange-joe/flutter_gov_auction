import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class Tel extends StatelessWidget {
  const Tel(this.tel, {Key? key}) : super(key: key);

  final String tel;

  Future<void> _makePhoneCall(String phoneNumber) async {
    final Uri launchUri = Uri(
      scheme: 'tel',
      path: phoneNumber,
    );
    await launchUrl(launchUri);
  }

  @override
  Widget build(BuildContext context) {
    final String trimTel = tel.replaceAll(' ', '');
    final String formattedTel = trimTel.length == 8 ? '${trimTel.substring(0, 4)} ${trimTel.substring(4, 8)}' : trimTel;

    return RichText(
      text: TextSpan(
        style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 16.0),
        children: <InlineSpan>[
          TextSpan(
            text: formattedTel,
            style: TextStyle(
              color: config.blue,
              fontSize: 16.0 * MediaQuery.of(context).textScaleFactor,
            ),
            semanticsLabel: '${S.of(context).semanticsDial}$formattedTel',
            recognizer: TapGestureRecognizer()
              ..onTap = () {
                _makePhoneCall(trimTel);
              },
          ),
        ],
      ),
    );
  }
}
