import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/app_info.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../providers/app_info_provider.dart';

class PushMessageList extends StatefulWidget {
  const PushMessageList({Key? key}) : super(key: key);

  @override
  State<PushMessageList> createState() => _PushMessageListState();
}

class _PushMessageListState extends State<PushMessageList> {
  late List<int> readIdList;

  @override
  void initState() {
    super.initState();
    readIdList = HiveHelper().getReadMessageList();
  }

  @override
  Widget build(BuildContext context) {
    final List<PushMessage> messageList = Provider.of<AppInfoProvider>(context).appInfo.messageList;

    return Center(
      child: DefaultTextStyle(
        style: Theme.of(context).textTheme.bodyText1!,
        child: Container(
          padding: const EdgeInsets.all(6.0),
          height: MediaQuery.of(context).size.height * 0.6,
          width: MediaQuery.of(context).size.width * 0.8,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16.0),
            color: Theme.of(context).backgroundColor,
          ),
          // child: Container(),
          child: Scaffold(
            backgroundColor: Theme.of(context).backgroundColor,
            body: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Padding(
                    padding: const EdgeInsets.fromLTRB(15.0, 0.0, 6.0, 0.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: <Widget>[
                        Text(S.of(context).news, style: Theme.of(context).textTheme.headline6),
                        IconButton(
                          onPressed: () {
                            Navigator.pop(context);
                          },
                          icon: Semantics(
                            label: S.of(context).semanticsClose,
                            button: true,
                            enabled: true,
                            child: Icon(
                              MdiIcons.close,
                              color: Theme.of(context).textTheme.bodyText2!.color,
                              size: 24.0 * MediaQuery.of(context).textScaleFactor,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (messageList.isEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 100.0),
                      child: Center(
                        child: Text(
                          S.of(context).newsEmpty,
                          style: Theme.of(context).textTheme.bodyText1,
                        ),
                      ),
                    )
                  else
                    ...messageList
                        .map(
                          (PushMessage message) => buildMessageItem(message, readIdList.contains(message.pushId)),
                        )
                        .toList(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget buildMessageItem(PushMessage message, bool isRead) {
    return ExpansionTile(
      onExpansionChanged: (bool opened) async {
        if (opened) {
          setState(() {
            readIdList.add(message.pushId);
          });
          await HiveHelper().addReadMessage(message.pushId);
        }
      },
      collapsedBackgroundColor: Theme.of(context).backgroundColor,
      title: Row(
        children: <Widget>[
          Semantics(
            label: isRead ? S.of(context).semanticsRead : S.of(context).semanticsUnread,
            child: Icon(
              isRead ? MdiIcons.emailOpen : MdiIcons.email,
              color: Theme.of(context).textTheme.bodyText2!.color,
              size: 24.0 * MediaQuery.of(context).textScaleFactor,
            ),
          ),
          const SizedBox(width: 5.0),
          Text(message.title),
        ],
      ),
      children: <Widget>[
        ListTile(
          title: Text(
            message.body,
            style: Theme.of(context).textTheme.bodyText2,
          ),
        ),
        const SizedBox(
          height: 15.0,
        ),
      ],
    );
  }
}
