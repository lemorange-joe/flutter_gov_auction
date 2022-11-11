import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/firebase_analytics_helper.dart';
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
  bool _allowAnalytics = false;

  @override
  void initState() {
    super.initState();

    _countdown = HiveHelper().getAgreed() ? 0 : 5;
    _allowAnalytics = HiveHelper().getAllowAnalytics();

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
              child: Column(
                children: <Widget>[
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 15.0, horizontal: 12.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: <Widget>[
                        const SizedBox(width: 65.0),
                        Expanded(
                          child: Center(
                            child: Text(
                              S.of(context).agreement,
                              style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 21.0),
                            ),
                          ),
                        ),
                        SizedBox(
                          width: 65.0,
                          child: DropdownButton<String>(
                            value: S.of(context).lang,
                            icon: const Icon(Icons.arrow_drop_down_outlined),
                            items: <String>['en', 'tc', 'sc'].map<DropdownMenuItem<String>>((String val) {
                              return DropdownMenuItem<String>(
                                value: val,
                                child: Text(
                                  val == 'en' ? 'Eng' : (val == 'tc' ? '繁' : '简'),
                                  style: TextStyle(
                                    color: val == S.of(context).lang ? config.blue : Theme.of(context).textTheme.bodyText2!.color,
                                    fontSize: 18.0,
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
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12.0),
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
                              child: DefaultTextStyle(
                                style: Theme.of(context).textTheme.bodyText1!.copyWith(
                                      fontSize: 16.0,
                                    ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: <Widget>[
                                    Text(
                                      S.of(context).agreementParagraph1,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).agreementParagraph2,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).agreementParagraph3,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).agreementParagraph4,
                                      textAlign: TextAlign.justify,
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
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).privacyPolicyParagraph2,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).privacyPolicyParagraph3,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).privacyPolicyParagraph4,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).privacyPolicyParagraph5,
                                      textAlign: TextAlign.justify,
                                    ),
                                    const SizedBox(height: 15.0),
                                    Text(
                                      S.of(context).agreementParagraph5,
                                      textAlign: TextAlign.justify,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Checkbox(
                        onChanged: (bool? val) {
                          setState(() {
                            _allowAnalytics = val!;
                          });
                        },
                        value: _allowAnalytics,
                      ),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: <Widget>[
                            const SizedBox(height: 12.0),
                            GestureDetector(
                              onTap: () {
                                setState(() {
                                  _allowAnalytics = !_allowAnalytics;
                                });
                              },
                              child: Text(S.of(context).allowAnalyticsDescription),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 12.0),
                    ],
                  ),
                ],
              ),
            ),
            SizedBox(
              height: 100.0,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: <Widget>[
                  const SizedBox(width: 10.0),
                  SizedBox(
                    width: 110.0,
                    height: 44.0,
                    child: ElevatedButton(
                      onPressed: _countdown <= 0
                          ? () {
                              HiveHelper().writeAgreed(true);
                              FirebaseAnalyticsHelper.enabled = _allowAnalytics;
                              HiveHelper().writeAllowAnalytics(_allowAnalytics);
                              if (widget.exitPage != null && widget.exitPage == 'settings') {
                                Navigator.pop(context);
                              } else {
                                Navigator.pushReplacementNamed(context, HiveHelper().getFirstLaunch() ? 'tour' : 'home');
                              }
                            }
                          : null,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: _countdown <= 0 ? config.blue : Theme.of(context).disabledColor,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(config.mdBorderRadius),
                        ),
                      ),
                      child: Text(
                        '${S.of(context).agree}${_countdown > 0 ? ' ($_countdown)' : ''}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 20.0,
                        ),
                      ),
                    ),
                  ),
                  SizedBox(
                    width: 110.0,
                    height: 44.0,
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
                          fontSize: 20.0,
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
