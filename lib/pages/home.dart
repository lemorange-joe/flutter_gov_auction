import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:persistent_bottom_nav_bar_v2/persistent-tab-view.dart';
import '../generated/l10n.dart';
import '../tabs/favourite.dart';
import '../tabs/home.dart';
import '../tabs/settings.dart';

class HomePage extends StatefulWidget {
  const HomePage(this.version, {Key? key}) : super(key: key);

  final String version;

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  late PersistentTabController _tabController;

  @override
  void initState() {
    super.initState();

    _tabController = PersistentTabController(initialIndex: 1);
  }

  List<Widget> _buildTabs() {
    return <Widget>[
      const FavouriteTab(),
      const HomeTab(),
      const SettingsTab(),
    ];
  }

  List<PersistentBottomNavBarItem> _navBarsItems() {
    return <PersistentBottomNavBarItem>[
      PersistentBottomNavBarItem(
        contentPadding: MediaQuery.of(context).textScaleFactor > 1 ? 0.0 : 5.0,
        icon: const Icon(MdiIcons.heart),
        iconSize: 26.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
        title: S.of(context).myFavourite,
        textStyle: TextStyle(
          fontSize: 15.0 * MediaQuery.of(context).textScaleFactor,
          height: 1.25,
        ),
        activeColorPrimary: Colors.red[300]!,
        activeColorSecondary: Colors.white,
        inactiveColorPrimary: Colors.grey[400],
      ),
      PersistentBottomNavBarItem(
        contentPadding: MediaQuery.of(context).textScaleFactor > 1 ? 0.0 : 5.0,
        icon: const Icon(MdiIcons.home),
        iconSize: 26.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
        title: S.of(context).home,
        textStyle: TextStyle(
          fontSize: 15.0 * MediaQuery.of(context).textScaleFactor,
          height: 1.25,
        ),
        activeColorPrimary: Theme.of(context).primaryColor,
        activeColorSecondary: Colors.white,
        inactiveColorPrimary: Colors.grey[400],
      ),
      PersistentBottomNavBarItem(
        contentPadding: MediaQuery.of(context).textScaleFactor > 1 ? 0.0 : 5.0,
        icon: const Icon(MdiIcons.cog),
        iconSize: 26.0 * (1 + (MediaQuery.of(context).textScaleFactor - 1) * 0.5),
        title: S.of(context).settings,
        textStyle: TextStyle(
          fontSize: 15.0 * MediaQuery.of(context).textScaleFactor,
          height: 1.25,
        ),
        activeColorPrimary: Colors.grey[800]!,
        activeColorSecondary: Colors.white,
        inactiveColorPrimary: Colors.grey[400],
      ),
    ];
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
      body: PersistentTabView(
        context,
        controller: _tabController,
        screens: _buildTabs(),
        items: _navBarsItems(),
        navBarStyle: NavBarStyle.style7,
        backgroundColor: Theme.of(context).backgroundColor,
        decoration: NavBarDecoration(
          borderRadius: BorderRadius.circular(10.0),
          colorBehindNavBar: Colors.white,
        ),
      ),
    );
  }
}
