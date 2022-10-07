import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

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
    return SizedBox(
      height: 28.0,
      child: OutlinedButton(
        onPressed: () {
          _makePhoneCall(trimTel);
        },
        style: OutlinedButton.styleFrom(
          padding: const EdgeInsets.symmetric(horizontal: 8.0),
        ),
        child: Text(trimTel.length == 8 ? '${trimTel.substring(0, 4)} ${trimTel.substring(4, 8)}' : trimTel),
      ),
    );
  }
}
