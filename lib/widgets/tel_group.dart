import 'package:flutter/material.dart';
import './tel.dart';
import './ui/open_external_icon.dart';

class TelGroup extends StatelessWidget {
  const TelGroup(this.strTelList, {super.key});

  final String strTelList;

  @override
  Widget build(BuildContext context) {
    final List<String> telList = strTelList.split(',');
    // final List<String> telList = '2860 2598 / 2860 2780, 2860 3485 / 2860 3488, 2860 5134 / 2860 5127'.split(','); // for testing

    return Column(
      children: telList.map((final String telPair) {
        return Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: telPair.replaceAll(RegExp(r'(\s)*\/(\s)*'), '/').split('/').map((String tel) {
            return Padding(
              padding: const EdgeInsets.only(right: 8.0),
              child: Row(
                children: <Widget>[
                  Tel(tel),
                  const OpenExternalIcon(),
                ],
              ),
            );
          }).toList(),
        );
      }).toList(),
    );
  }
}
