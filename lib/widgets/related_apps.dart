import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
// import 'package:logger/logger.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../providers/lemorange_app_provider.dart';

class RelatedApps extends StatefulWidget {
  const RelatedApps({Key? key}) : super(key: key);

  @override
  State<RelatedApps> createState() => _RelatedAppsState();
}

class _RelatedAppsState extends State<RelatedApps> {
  int _selectedApp = 0;

  @override
  void initState() {
    super.initState();

    Provider.of<LemorangeAppProvider>(context, listen: false).getApps();
  }

  @override
  Widget build(BuildContext context) {
    final LemorangeAppProvider lemorangeAppProvider = Provider.of<LemorangeAppProvider>(context);

    final Locale curLocale = Localizations.localeOf(context);
    final String langCode = '${curLocale.languageCode}-${curLocale.countryCode}'.toLowerCase();

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: Text(
              S.of(context).relatedApps,
              style: Theme.of(context).textTheme.bodyText2!.copyWith(
                    fontSize: 20.0,
                    fontWeight: FontWeight.bold,
                  ),
            ),
          ),
          Consumer<LemorangeAppProvider>(
            builder: (BuildContext context, LemorangeAppProvider appModel, _) {
              // on error
              if (lemorangeAppProvider.hasError) {
                return Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(vertical: 10.0),
                    child: Column(
                      children: <Widget>[
                        Text(
                          S.of(context).networkError,
                          style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 22.0),
                        ),
                        const SizedBox(height: 10.0),
                        ElevatedButton(
                          onPressed: () async {
                            await appModel.getApps();
                          },
                          child: Text(S.of(context).reload),
                        ),
                      ],
                    ),
                  ),
                );
              }

              // on fetching
              if (lemorangeAppProvider.apps == null || lemorangeAppProvider.apps.isEmpty) {
                return Center(
                  child: Container(
                    padding: const EdgeInsets.all(50.0),
                    width: 160.0,
                    height: 160.0,
                    child: const CircularProgressIndicator.adaptive(
                      strokeWidth: 5.0,
                    ),
                  ),
                );
              }

              // on finished
              return Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  SizedBox(
                    height: 100.0,
                    width: double.infinity,
                    child: ListView.builder(
                      shrinkWrap: true,
                      scrollDirection: Axis.horizontal,
                      itemCount: lemorangeAppProvider.apps.length,
                      itemBuilder: (BuildContext ctxt, int index) {
                        return GestureDetector(
                          onTap: () {
                            setState(() {
                              _selectedApp = index;
                            });
                          },
                          child: Padding(
                            padding: const EdgeInsets.all(10.0),
                            child: SizedBox(
                              width: 80.0,
                              height: 80.0,
                              child: CachedNetworkImage(
                                placeholder: (BuildContext context, String url) => const Center(child: CircularProgressIndicator()),
                                errorWidget: (BuildContext context, String url, dynamic error) => Padding(
                                  padding: const EdgeInsets.all(12.0),
                                  child: Image.asset(
                                    'assets/images/lemorange_logo.png',
                                    isAntiAlias: true,
                                  ),
                                ),
                                imageUrl: lemorangeAppProvider.apps[index].thumbnail,
                                imageBuilder: (BuildContext context, ImageProvider imageProvider) => Container(
                                  decoration: BoxDecoration(
                                    border: _selectedApp == index
                                        ? Border.all(
                                            color: Theme.of(context).colorScheme.secondary,
                                            width: 5.0,
                                          )
                                        : null,
                                    image: DecorationImage(
                                      image: imageProvider,
                                      fit: BoxFit.cover,
                                    ),
                                    borderRadius: BorderRadius.circular(config.smBorderRadius),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(8.0, 12.0, 8.0, 8.0),
                    child: Text(
                      lemorangeAppProvider.apps[_selectedApp].title[langCode] as String,
                      style: Theme.of(context).textTheme.bodyText1!.copyWith(
                            fontSize: 18.0,
                            fontWeight: FontWeight.bold,
                          ),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 8.0),
                    child: Text(
                      lemorangeAppProvider.apps[_selectedApp].description[langCode] as String,
                      style: Theme.of(context).textTheme.bodyText2!.copyWith(
                            fontSize: 16.0,
                          ),
                    ),
                  ),
                  Row(
                    children: <Widget>[
                      Expanded(
                        child: Container(),
                      ),
                      Expanded(
                        flex: 2,
                        child: TextButton(
                          onPressed: () async {
                            final String url = lemorangeAppProvider.apps[_selectedApp].url;
                            if (await canLaunchUrl(Uri.parse(url))) {
                              await launchUrl(Uri.parse(url));
                              // FirebaseAnalyticsHelper().logEvent(
                              //   'view_related_apps',
                              //   parameters: <String, String>{'view_title': lemorangeAppProvider.apps[_selectedApp].title as String},
                              // );
                            } else {
                              throw Exception('Could not launch $url');
                            }
                          },
                          child: Text(
                            S.of(context).downloadNow,
                            style: Theme.of(context).textTheme.button!.copyWith(
                                  color: Colors.blue[800],
                                  decoration: TextDecoration.underline,
                                ),
                          ),
                        ),
                      ),
                      Expanded(
                        child: Container(),
                      ),
                    ],
                  )
                ],
              );
            },
          ),
          const SizedBox(height: 50.0),
        ],
      ),
    );
  }
}
