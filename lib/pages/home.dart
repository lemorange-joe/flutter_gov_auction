import 'package:badges/badges.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../include/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../tabs/favourite.dart';
import '../tabs/home.dart';
import '../tabs/settings.dart';

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
          Consumer<AppInfoProvider>(builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
            return !appInfo.loaded
                ? Padding(
                  padding: const EdgeInsets.only(right: 12.0),
                  child: Icon(
                      MdiIcons.email,
                      color: Theme.of(context).disabledColor,
                      size: 24.0 * MediaQuery.of(context).textScaleFactor,
                    ),
                )
                : Badge(
                    badgeContent: Text(
                      appInfo.messageList.length.toString(),  // not completed yet!!!
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 14.0 * MediaQuery.of(context).textScaleFactor,
                      ),
                    ),
                    position: BadgePosition.topEnd(top: 20.0 * (1.05 - MediaQuery.of(context).textScaleFactor), end: 15.0 * (1.2 - MediaQuery.of(context).textScaleFactor)),
                    child: IconButton(
                      onPressed: () {},
                      iconSize: 24.0 * MediaQuery.of(context).textScaleFactor,
                      splashRadius: 24.0 * MediaQuery.of(context).textScaleFactor,
                      icon: Icon(
                        MdiIcons.email,
                        color: Theme.of(context).textTheme.bodyText2!.color,
                      ),
                    ),
                  );
          }),
        ],
      ),
    );
  }

  Drawer buildDrawer(BuildContext context) {
    return Drawer(
      child: ListView(
        children: <Widget>[
          DrawerHeader(
            decoration: BoxDecoration(
              color: Theme.of(context).backgroundColor,
            ),
            child: Text(
              'Drawer Header',
              style: Theme.of(context).textTheme.bodyText1,
            ),
          ),
          ListTile(
            onTap: () {
              Navigator.pop(context);
            },
            title: const Text('AAA'),
          ),
          ListTile(
            onTap: () {
              Navigator.pop(context);
            },
            title: const Text('BBB'),
          ),
          ListTile(
            onTap: () {
              Navigator.pop(context);
            },
            title: const Text('CCC'),
          ),
          Padding(
            padding: const EdgeInsets.only(left: 16.0),
            child: Consumer<AppInfoProvider>(
              builder: (BuildContext context, AppInfoProvider appInfo, Widget? _) {
                return appInfo.loaded
                    ? Text(utilities.formatDateTime(appInfo.lastUpdate, S.of(context).lang))
                    : const SizedBox(height: 20.0, width: 20.0, child: CircularProgressIndicator());
              },
            ),
          ),
        ],
      ),
    );
  }
}

class TabData {
  TabData(this.widget, this.lightColor, this.darkColor);

  final Widget widget;
  final Color lightColor;
  final Color darkColor;
}
