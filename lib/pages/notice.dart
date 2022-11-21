import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../class/app_info.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../providers/app_info_provider.dart';
import '../widgets/ui/open_external_icon.dart';

class NoticePage extends StatelessWidget {
  const NoticePage({super.key});

  @override
  Widget build(BuildContext context) {
    final List<String> paragraph2 = S.of(context).noticeParagraph2.split('[gld_website]');

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
        title: Text(S.of(context).noticeToParticipants, style: const TextStyle(color: Colors.white, fontSize: 20.0)),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
          child: DefaultTextStyle(
            style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 16.0),
            child: SingleChildScrollView(
              child: Consumer<AppInfoProvider>(builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
                return Column(
                  children: <Widget>[
                    Text(S.of(context).noticeParagraph1),
                    const SizedBox(height: 20.0),
                    RichText(
                      text: TextSpan(
                        style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 16.0),
                        children: <InlineSpan>[
                          TextSpan(text: paragraph2[0]),
                          TextSpan(
                            text: S.of(context).gldWebsite,
                            style: const TextStyle(color: config.blue),
                            semanticsLabel: '${S.of(context).semanticsOpen}${S.of(context).gldWebsite}',
                            recognizer: TapGestureRecognizer()
                              ..onTap = () {
                                launchUrl(
                                  Uri.parse(FlutterConfig.get('GLD_WEBSITE').toString().replaceAll('{lang}', S.of(context).gldWebsiteLang)),
                                  mode: LaunchMode.externalApplication,
                                );
                              },
                          ),
                          const WidgetSpan(
                            alignment: PlaceholderAlignment.top,
                            child: OpenExternalIcon(),
                          ),
                          TextSpan(text: paragraph2[1]),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12.0),
                    Row(
                      children: <Widget>[
                        Flexible(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: appInfo.catalogLocationList
                                .asMap()
                                .entries
                                .map(
                                  (MapEntry<int, CatalogLocation> locationEntry) => Padding(
                                    padding: const EdgeInsets.only(bottom: 12.0),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: <Widget>[
                                        SizedBox(width: 40.0, child: Text('${locationEntry.key + 1}.')),
                                        Semantics(
                                          label: '${locationEntry.value.address}${S.of(context).semanticsOpenInMap}',
                                          child: InkWell(
                                            onTap: () async {
                                              final String url = FlutterConfig.get('MAP_URL')
                                                  .toString()
                                                  .replaceAll('{address}', Uri.encodeComponent(locationEntry.value.mapAddress));
                                              await launchUrl(
                                                Uri.parse(url),
                                                mode: LaunchMode.externalApplication,
                                              );
                                            },
                                            child: Row(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: <Widget>[
                                                Text(locationEntry.value.address),
                                                const Padding(
                                                  padding: EdgeInsets.only(left: 8.0, top: 2.0),
                                                  child: OpenExternalIcon(),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                )
                                .toList(),
                          ),
                        ),
                      ],
                    ),
                    Row(
                      children: <Widget>[
                        Flexible(
                          child: DefaultTextStyle(
                            style: TextStyle(
                              color: Colors.blue[700],
                              decoration: TextDecoration.underline,
                              fontSize: 16.0,
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: appInfo.noticeLinkList
                                  .map(
                                    (NoticeLink noticeLink) => Padding(
                                      padding: const EdgeInsets.only(top: 12.0),
                                      child: InkWell(
                                        child: Text(noticeLink.title),
                                        onTap: () => launchUrl(Uri.parse(noticeLink.url), mode: LaunchMode.externalApplication),
                                      ),
                                    ),
                                  )
                                  .toList(),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 60.0),
                    Text(S.of(context).noticeParagraph3),
                    const SizedBox(height: 60.0),
                    Text(S.of(context).noticeParagraph4),
                    const SizedBox(height: 60.0),
                    Text(S.of(context).noticeParagraph5),
                  ],
                );
              }),
            ),
          ),
        ),
      ),
    );
  }
}
