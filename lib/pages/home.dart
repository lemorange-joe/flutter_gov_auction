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
                icon: const Icon(MdiIcons.heart),
                title: S.of(context).myFavourite,
                activeColorPrimary: Colors.red[300]!,
                activeColorSecondary: Colors.white,
                inactiveColorPrimary: Colors.grey,
            ),
            PersistentBottomNavBarItem(
                icon: const Icon(MdiIcons.home),
                title: S.of(context).home,
                activeColorPrimary: Theme.of(context).primaryColor,
                activeColorSecondary: Colors.white,
                inactiveColorPrimary: Colors.grey,
            ),
            PersistentBottomNavBarItem(
                icon: const Icon(MdiIcons.cog),
                title: S.of(context).settings,
                activeColorPrimary: Colors.grey[800]!,
                activeColorSecondary: Colors.white,
                inactiveColorPrimary: Colors.grey,
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
      ),
    );
  }
}
