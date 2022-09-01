import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../tabs/favourite.dart';
import '../tabs/home.dart';
import '../tabs/settings.dart';

class HomePage extends StatefulWidget {
  const HomePage({Key? key}) : super(key: key);

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  // double _bottomNavHeight = 80.0;
  int _tabIndex = 0;

  final List<TabData> _tabs = <TabData>[];

  @override
  void initState() {
    super.initState();

    _tabs
      ..add(TabData(const HomeTab(), Colors.blue, Colors.blue))
      ..add(TabData(const FavouriteTab(), Colors.red[300]!, Colors.red[300]!))
      ..add(TabData(const SettingsTab(), Colors.grey[800]!, Colors.white));
  }

  void _onTabItemTapped(int index) {
    setState(() {
      _tabIndex = index;
    });
  }

  // void toggleBottomNav(bool show) {
  //   if (_bottomNavHeight == 80.0 || _bottomNavHeight == 0.0) {
  //     setState(() {
  //       _bottomNavHeight = show ? 80.0 : 0.0;
  //     });
  //   }
  // }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: <Widget>[
            Text(S.of(context).appName),
          ],
        ),
      ),
      body: IndexedStack(
        index: _tabIndex,
        children: _tabs.map((TabData t) => t.widget).toList(),
      ),
      // body: Center(child: _tabs.elementAt(_tabIndex).widget),
      bottomNavigationBar: BottomNavigationBar(
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
    );
  }
}

class TabData {
  TabData(this.widget, this.lightColor, this.darkColor);

  final Widget widget;
  final Color lightColor;
  final Color darkColor;
}
