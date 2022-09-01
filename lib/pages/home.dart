import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
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
      setState((){
        _prevScrollOffset = scrollController.offset;
      });
      // Logger().i('$_scrollingUp || $_prevScrollOffset ');
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
      bottomNavigationBar: AnimatedBuilder(
        animation: scrollController,
        builder: (BuildContext context, Widget? child) {
          // Logger().d(scrollController.position.userScrollDirection);
          double height = 0.0;

          if (scrollController.position.userScrollDirection == ScrollDirection.forward || (scrollController.position.userScrollDirection == ScrollDirection.idle && _scrollingUp)) {
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
}

class TabData {
  TabData(this.widget, this.lightColor, this.darkColor);

  final Widget widget;
  final Color lightColor;
  final Color darkColor;
}
