import 'dart:math';
import 'package:badges/badges.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_config/flutter_config.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../class/app_info.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;
import '../includes/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../tabs/favourite.dart';
import '../tabs/home.dart';
import '../tabs/settings.dart';
import '../widgets/push_message_list.dart';

class HomePage extends StatefulWidget {
  const HomePage({Key? key}) : super(key: key);

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _tabIndex = 0;
  double _prevScrollOffset = 0;
  bool _scrollingUp = true;
  late ScrollController scrollController;
  final List<TabData> _tabs = <TabData>[];

  @override
  void initState() {
    super.initState();

    scrollController = ScrollController();

    scrollController.addListener(() {
      _scrollingUp = scrollController.offset < _prevScrollOffset;
      setState(() {
        _prevScrollOffset = scrollController.offset;
      });
    });

    _tabs
      ..add(TabData(const HomeTab(), Colors.blue, Colors.blue))
      ..add(TabData(FavouriteTab(scrollController), Colors.red[300]!, Colors.red[300]!))
      ..add(TabData(const SettingsTab(), Colors.grey[800]!, Colors.white));
  }

  @override
  void dispose() {
    scrollController.dispose();
    super.dispose();
  }

  void _onTabItemTapped(int index) {
    setState(() {
      _tabIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: buildAppBar(context),
      drawer: buildDrawer(context),
      body: IndexedStack(
        index: _tabIndex,
        children: _tabs.map((TabData t) => t.widget).toList(),
      ),
      bottomNavigationBar: AnimatedBuilder(
        animation: scrollController,
        builder: (BuildContext context, Widget? child) {
          // Logger().d(scrollController.position.userScrollDirection);
          double height = 0.0;

          if (scrollController.position.userScrollDirection == ScrollDirection.forward ||
              (scrollController.position.userScrollDirection == ScrollDirection.idle && _scrollingUp)) {
            height = 72 * MediaQuery.of(context).textScaleFactor;
          }

          return AnimatedContainer(
            duration: const Duration(milliseconds: 300),
            height: height,
            child: child,
          );
        },
        child: BottomNavigationBar(
          items: <BottomNavigationBarItem>[
            BottomNavigationBarItem(
              icon: const Icon(MdiIcons.home),
              label: S.of(context).home,
            ),
            BottomNavigationBarItem(
              icon: const Icon(MdiIcons.heart),
              label: S.of(context).myFavourite,
            ),
            BottomNavigationBarItem(
              icon: const Icon(MdiIcons.cog),
              label: S.of(context).settings,
            ),
          ],
          onTap: _onTabItemTapped,
          iconSize: 26.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
          selectedLabelStyle: TextStyle(
            fontSize: 16.0 * MediaQuery.of(context).textScaleFactor,
          ),
          unselectedLabelStyle: TextStyle(
            fontSize: 16.0 * MediaQuery.of(context).textScaleFactor,
          ),
          currentIndex: _tabIndex,
          unselectedItemColor: Colors.grey,
          selectedItemColor: Theme.of(context).brightness == Brightness.dark ? _tabs.elementAt(_tabIndex).darkColor : _tabs.elementAt(_tabIndex).lightColor,
        ),
      ),
    );
  }

  AppBar buildAppBar(BuildContext context) {
    return AppBar(
      titleSpacing: 0.0,
      toolbarHeight: 56.0 * MediaQuery.of(context).textScaleFactor,
      backgroundColor: Theme.of(context).backgroundColor,
      leading: Builder(builder: (BuildContext context) {
        return IconButton(
          onPressed: () {
            Scaffold.of(context).openDrawer();
          },
          iconSize: 24.0 * MediaQuery.of(context).textScaleFactor,
          splashRadius: 24.0 * MediaQuery.of(context).textScaleFactor,
          icon: Icon(
            MdiIcons.menu,
            color: Theme.of(context).textTheme.bodyText1!.color,
          ),
        );
      }),
      title: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: <Widget>[
          Text(
            S.of(context).appName,
            style: Theme.of(context).textTheme.bodyText1,
          ),
          Padding(
            padding: const EdgeInsets.only(right: 4.0),
            child: Consumer<AppInfoProvider>(builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
              return !appInfo.loaded
                  ? IconButton(
                      onPressed: null,
                      icon: Semantics(
                        label: S.of(context).semanticsOpenNews,
                        button: true,
                        enabled: false,
                        child: Icon(
                          MdiIcons.email,
                          color: Theme.of(context).disabledColor,
                          size: 24.0 * MediaQuery.of(context).textScaleFactor,
                        ),
                      ),
                    )
                  : ValueListenableBuilder<Box<String>>(
                      valueListenable: Hive.box<String>('history').listenable(),
                      builder: (BuildContext context, _, __) {
                        final List<int> readIdList = HiveHelper().getReadMessageList();
                        final List<int> messageIdList = appInfo.messageList.map((PushMessage message) => message.pushId).toList();
                        final int badgeCount = messageIdList.fold(0, (int val, int id) {
                          return val + (readIdList.contains(id) ? 0 : 1);
                        });

                        return Badge(
                          showBadge: badgeCount > 0,
                          badgeContent: Text(
                            badgeCount.toString(),
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 14.0 * MediaQuery.of(context).textScaleFactor,
                            ),
                          ),
                          toAnimate: false,
                          position: BadgePosition.topEnd(
                              top: 20.0 * (1.05 - MediaQuery.of(context).textScaleFactor), end: 15.0 * (1.2 - MediaQuery.of(context).textScaleFactor)),
                          child: IconButton(
                            onPressed: () {
                              openMessageList(context);
                            },
                            iconSize: 24.0 * MediaQuery.of(context).textScaleFactor,
                            splashRadius: 24.0 * MediaQuery.of(context).textScaleFactor,
                            icon: Semantics(
                              label: S.of(context).semanticsOpenNews,
                              button: true,
                              enabled: true,
                              child: Icon(
                                MdiIcons.email,
                                color: Theme.of(context).textTheme.bodyText2!.color,
                              ),
                            ),
                          ),
                        );
                      });
            }),
          ),
        ],
      ),
    );
  }

  Drawer buildDrawer(BuildContext context) {
    final Color listItemColor = Theme.of(context).textTheme.bodyText2!.color!;

    return Drawer(
      child: DefaultTextStyle(
        style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 14.0),
        child: ListView(
          children: <Widget>[
            SizedBox(
              height: 80.0,
              child: DrawerHeader(
                decoration: BoxDecoration(
                  color: Theme.of(context).backgroundColor,
                ),
                child: Text(
                  S.of(context).appName,
                  style: Theme.of(context).textTheme.bodyText1!.copyWith(fontSize: 18.0),
                ),
              ),
            ),
            getCustomListTile(MdiIcons.homeOutline, listItemColor, S.of(context).home, 'home'),
            getCustomListTile(MdiIcons.emailOutline, listItemColor, S.of(context).news, 'news'),
            const Divider(),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 4.0),
              child: Text(
                S.of(context).myItems,
                style: Theme.of(context).textTheme.bodyText2!.copyWith(fontSize: 15.0),
              ),
            ),
            getCustomListTile(MdiIcons.cardsHeartOutline, listItemColor, S.of(context).saved, 'save'),
            getCustomListTile(MdiIcons.bellOutline, listItemColor, S.of(context).reminder, 'reminder'),
            getCustomListTile(MdiIcons.cogOutline, listItemColor, S.of(context).settings, 'settings'),
            const Divider(),
            getCustomListTile(MdiIcons.bulletinBoard, listItemColor, S.of(context).noticeToUser, 'noticeToUser'),
            getCustomListTile(MdiIcons.bookOpenOutline, listItemColor, S.of(context).agreement, 'agreement'),
            getCustomListTile(MdiIcons.navigationOutline, listItemColor, S.of(context).tour, 'tour'),
            getCustomListTile(MdiIcons.frequentlyAskedQuestions, listItemColor, S.of(context).faq, 'faq'),
            getCustomListTile(MdiIcons.helpCircleOutline, listItemColor, S.of(context).help, 'help'),
            const Divider(),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    FlutterConfig.get('VERSION').toString(),
                    style: const TextStyle(fontSize: 11.0),
                  ),
                  Row(
                    children: <Widget>[
                      Consumer<AppInfoProvider>(
                        builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
                          return appInfo.loaded
                              ? Flexible(
                                  child: Text(
                                  utilities.formatDateTime(appInfo.lastUpdate, S.of(context).lang),
                                  style: const TextStyle(fontSize: 11.0),
                                ))
                              : const SizedBox(height: 20.0, width: 20.0, child: CircularProgressIndicator.adaptive());
                        },
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget getCustomListTile(IconData iconData, Color iconColor, String content, String route) {
    final double visualDensity = min((MediaQuery.of(context).textScaleFactor - 1) * 8, VisualDensity.maximumDensity);
    final bool isCurrent = ModalRoute.of(context) != null && ModalRoute.of(context)!.settings.name == route;

    return SizedBox(
      height: 48.0 * MediaQuery.of(context).textScaleFactor,
      child: ListTileTheme(
        child: ListTile(
          onTap: () {
            Navigator.pop(context);
          },
          tileColor: isCurrent ? config.green : Theme.of(context).scaffoldBackgroundColor,
          dense: true,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16.0),
          visualDensity: VisualDensity(vertical: visualDensity),
          horizontalTitleGap: (MediaQuery.of(context).textScaleFactor - 0.75) * 12,
          leading: Icon(
            iconData,
            color: isCurrent ? Colors.white : iconColor,
            size: 24.0 * MediaQuery.of(context).textScaleFactor,
          ),
          title: Text(
            content,
            style: TextStyle(
              color: isCurrent ? Colors.white : Theme.of(context).textTheme.bodyText2!.color,
              fontSize: 15.0,
              fontWeight: isCurrent ? FontWeight.w600 : FontWeight.normal,
            ),
          ),
        ),
      ),
    );
  }
}

void openMessageList(BuildContext context) {
  showDialog(
    context: context,
    barrierColor: Colors.black54,
    builder: (BuildContext context) {
      return const PushMessageList();
    },
  );
}

class TabData {
  TabData(this.widget, this.lightColor, this.darkColor);

  final Widget widget;
  final Color lightColor;
  final Color darkColor;
}
