import 'package:flutter/material.dart';
import './tel.dart';
import './ui/open_external_icon.dart';

class TelGroup extends StatelessWidget {
  const TelGroup(this.telList, {super.key});

  final String telList;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: telList.replaceAll(RegExp(r'(\s)*\/(\s)*'), '/').split('/').map((String tel) {
        return Padding(
          padding: const EdgeInsets.only(right: 6.0),
          child: Row(
            children: <Widget>[
              Tel(tel),
              const OpenExternalIcon(),
            ],
          ),
        );
      }).toList(),
    );
  }
}
