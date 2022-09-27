import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../providers/app_info_provider.dart';

class AgreementPage extends StatefulWidget {
  const AgreementPage(this.exitPage, {Key? key}) : super(key: key);

  final String exitPage;

  @override
  State<AgreementPage> createState() => _AgreementPageState();
}

class _AgreementPageState extends State<AgreementPage> {
  int _countdown = 5;

  @override
  void initState() {
    super.initState();

    _countdown = HiveHelper().getAgreed() ? 0 : 5;

    if (_countdown > 0) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        Timer.periodic(const Duration(seconds: 1), (_) {
          if (_countdown > 0) {
            setState(() {
              --_countdown;
            });
          }
        });
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        toolbarHeight: 0.0,
        backgroundColor: Theme.of(context).primaryColor,
      ),
      body: SafeArea(
        child: Column(
          children: <Widget>[
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8.0, vertical: 20.0),
                child: Column(
                  children: <Widget>[
                    Padding(
                      padding: const EdgeInsets.only(top: 5.0, bottom: 15.0),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: <Widget>[
                          const SizedBox(width: 60.0),
                          Expanded(
                            child: Center(
                              child: Text(
                                S.of(context).agreement,
                                style: TextStyle(
                                  color: Colors.grey[800],
                                  fontSize: 30.0,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),
                          SizedBox(
                            width: 80.0,
                            child: DropdownButton<String>(
                              value: S.of(context).lang,
                              icon: const Icon(Icons.arrow_drop_down_outlined),
                              style: TextStyle(
                                color: Colors.grey[800],
                                fontSize: 26.0,
                              ),
                              items: <String>['en', 'tc', 'sc'].map<DropdownMenuItem<String>>((String val) {
                                return DropdownMenuItem<String>(
                                  value: val,
                                  child: Text(
                                    val == 'en' ? 'Eng' : (val == 'tc' ? '繁' : '简'),
                                    style: TextStyle(
                                      color: val == S.of(context).lang ? Theme.of(context).primaryColor : Theme.of(context).textTheme.bodyText2!.color,
                                      fontSize: 26.0,
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                );
                              }).toList(),
                              onChanged: (String? val) async {
                                if (val == 'en') {
                                  Provider.of<AppInfoProvider>(context, listen: false).refresh();
                                  await S.load(const Locale('en', 'US'));
                                  await HiveHelper().writeLocaleCode('en_US');
                                } else if (val == 'tc') {
                                  Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: 'tc');
                                  await S.load(const Locale('zh', 'HK'));
                                  await HiveHelper().writeLocaleCode('zh_HK');
                                } else {
                                  Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: 'sc');
                                  await S.load(const Locale('zh', 'CN'));
                                  await HiveHelper().writeLocaleCode('zh_CN');
                                }
                              },
                            ),
                          ),
                        ],
                      ),
                    ),
                    Expanded(
                      child: Container(
                        width: double.infinity,
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: Theme.of(context).textTheme.bodyText1!.color!,
                          ),
                          borderRadius: BorderRadius.circular(config.smBorderRadius),
                        ),
                        child: Scrollbar(
                          thumbVisibility: true,
                          child: Padding(
                            padding: const EdgeInsets.all(5.0),
                            child: SingleChildScrollView(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: <Widget>[
                                  Text(
                                    S.of(context).agreementParagraph1,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).agreementParagraph2,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).agreementParagraph3,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).agreementParagraph4,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 25.0),
                                  Text(
                                    S.of(context).privacyPolicy,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                          fontWeight: FontWeight.bold,
                                          decoration: TextDecoration.underline,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).privacyPolicyParagraph1,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).privacyPolicyParagraph2,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).privacyPolicyParagraph3,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).privacyPolicyParagraph4,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).privacyPolicyParagraph5,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                  const SizedBox(height: 15.0),
                                  Text(
                                    S.of(context).agreementParagraph5,
                                    textAlign: TextAlign.justify,
                                    style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                          fontSize: 20.0,
                                        ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(
              height: 5.0,
            ),
            SizedBox(
              height: 100.0,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  const SizedBox(width: 10.0),
                  SizedBox(
                    width: 120.0,
                    height: 48.0,
                    child: ElevatedButton(
                      onPressed: _countdown <= 0
                          ? () {
                              HiveHelper().writeAgreed(true);
                              if (widget.exitPage != null && widget.exitPage == 'settings') {
                                Navigator.pop(context);
                              } else {
                                Navigator.pushReplacementNamed(context, HiveHelper().getFirstLaunch() ? 'tour' : 'home');
                              }
                            }
                          : null,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: _countdown <= 0 ? Theme.of(context).primaryColor : Theme.of(context).primaryColor,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(config.mdBorderRadius),
                        ),
                      ),
                      child: Text(
                        '${S.of(context).agree}${_countdown > 0 ? ' ($_countdown)' : ''}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 22.0,
                        ),
                      ),
                    ),
                  ),
                  SizedBox(
                    width: 120.0,
                    height: 48.0,
                    child: ElevatedButton(
                      onPressed: () async {
                        await HiveHelper().writeAgreed(false);
                        exit(0);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Theme.of(context).errorColor,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(config.mdBorderRadius),
                        ),
                      ),
                      child: Text(
                        S.of(context).notAgree,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 22.0,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10.0),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
