import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;

class HelpPage extends StatelessWidget {
  const HelpPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: config.blue,
        leading: IconButton(
          icon: Semantics(
            label: S.of(context).semanticsGoBack,
            button: true,
            enabled: true,
            child: const Icon(MdiIcons.arrowLeft, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(S.of(context).help, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
          child: DefaultTextStyle(
            style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 16.0),
            child: SingleChildScrollView(
              child: Column(
                children: <Widget>[
                  Text(S.of(context).privacyPolicyParagraph1),
                  const SizedBox(height: 60.0),
                  Text(S.of(context).privacyPolicyParagraph2),
                  const SizedBox(height: 60.0),
                  Text(S.of(context).privacyPolicyParagraph3),
                  const SizedBox(height: 60.0),
                  Text(S.of(context).privacyPolicyParagraph4),
                  const SizedBox(height: 60.0),
                  Text(S.of(context).privacyPolicyParagraph5),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
