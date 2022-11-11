import 'dart:io';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilites;

class AccessibilityPage extends StatelessWidget {
  const AccessibilityPage({super.key});

  Widget _spacer() {
    return const SizedBox(height: 20.0);
  }

  @override
  Widget build(BuildContext context) {
    final String imagePlatform = Platform.isIOS ? 'ios' : 'android';
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
        title: Text(S.of(context).accessibilityDesign, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
          child: DefaultTextStyle(
            style: Theme.of(context).textTheme.bodyText1!,
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(S.of(context).accessibilityParagraph1),
                  _spacer(),
                  SizedBox(
                    width: double.infinity,
                    height: MediaQuery.of(context).size.height * 0.5 * utilites.adjustedPhotoScale(MediaQuery.of(context).textScaleFactor),
                    child: Semantics(
                      label: S.of(context).semanticsTbc,
                      child: Image(
                        image: AssetImage('assets/images/accessibility_${imagePlatform}01.png'),
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                  _spacer(),
                  _spacer(),
                  if (Platform.isIOS) Text(S.of(context).accessibilityParagraph2Ios) else Text(S.of(context).accessibilityParagraph2Android),
                  _spacer(),
                  SizedBox(
                    width: double.infinity,
                    height: MediaQuery.of(context).size.height * 0.5 * utilites.adjustedPhotoScale(MediaQuery.of(context).textScaleFactor),
                    child: Semantics(
                      label: S.of(context).semanticsTbc,
                      child: Image(
                        image: AssetImage('assets/images/accessibility_${imagePlatform}02.png'),
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                  _spacer(),
                  _spacer(),
                  RichText(
                    text: TextSpan(
                      style: Theme.of(context).textTheme.bodyText1,
                      children: <TextSpan>[
                        TextSpan(text: S.of(context).accessibilityParagraph3Before),
                        TextSpan(
                          text: ' ${config.supportEmail} ',
                          style: const TextStyle(color: config.blue),
                          semanticsLabel: '${S.of(context).semanticsEmailTo}${config.supportEmail}',
                          recognizer: TapGestureRecognizer()
                            ..onTap = () {
                              launchUrl(Uri.parse('mailto:${config.supportEmail}'), mode: LaunchMode.externalApplication);
                            },
                        ),
                        TextSpan(text: S.of(context).accessibilityParagraph3After),
                      ],
                    ),
                  ),
                  // Row(
                  //   children: <Widget>[
                  //     Text(S.of(context).accessibilityParagraph3Before),
                  //     const SizedBox(width: 3.0),
                  //     InkWell(
                  //       child: const Text(config.supportEmail),
                  //       onTap: () => launchUrl(
                  //         Uri.parse('mailto:${config.supportEmail}'),
                  //         mode: LaunchMode.externalApplication,
                  //       ),
                  //     ),
                  //     const SizedBox(width: 3.0),
                  //     Text(S.of(context).accessibilityParagraph3After),
                  //   ],
                  // ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
