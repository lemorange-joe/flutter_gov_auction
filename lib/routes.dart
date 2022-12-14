import 'package:flutter/material.dart';
import './class/auction.dart';
import './pages/accessibility.dart';
import './pages/agreement.dart';
import './pages/auction_lot.dart';
import './pages/debug.dart';
import './pages/faq.dart';
import './pages/help.dart';
import './pages/home.dart';
import './pages/notice.dart';
import './pages/reminder.dart';
import './pages/saved.dart';
import './pages/search.dart';
import './pages/settings.dart';
import './pages/tour.dart';

enum PageSlideDirection { up, down, left, right }

class Routes {
  PageRoute<Widget> getRoutes(RouteSettings settings) {
    switch (settings.name) {
      case 'accessibility':
        return _buildRoute(settings, const AccessibilityPage());
      case 'auction_lot':
        final dynamic args = settings.arguments;
        final String title = (args as Map<String, dynamic>)['title'] == null ? '' : args['title'] as String;
        final String heroTagPrefix = args['heroTagPrefix'] as String;
        final int auctionId = args['auctionId'] as int;
        final String auctionNum = args['auctionNum'] == null ? '' : args['auctionNum'] as String;
        final DateTime auctionStartTime = args['auctionStartTime'] as DateTime;
        final AuctionLot auctionLot = args['auctionLot'] == null ? AuctionLot.empty() : args['auctionLot'] as AuctionLot;
        final int loadAuctionLotId = args['loadAuctionLotId'] == null ? 0 : args['loadAuctionLotId'] as int;
        return _buildRoute(settings, AuctionLotPage(title, heroTagPrefix, auctionId, auctionNum, auctionStartTime, auctionLot, loadAuctionLotId));
      case 'agreement':
        final dynamic args = settings.arguments;
        final String exitPage = (args == null || (args as Map<String, dynamic>)['exitPage'] == null) ? '' : args['exitPage'] as String;
        return _buildRoute(settings, AgreementPage(exitPage));
      case 'debug':
        return _buildRoute(settings, const DebugPage());
      case 'faq':
        return _buildRoute(settings, const FaqPage());
      case 'help':
        return _buildRoute(settings, const HelpPage());
      case 'home':
        return _buildRoute(settings, const HomePage());
      case 'noticeToParticipants':
        return _buildRoute(settings, const NoticePage());
      case 'reminder':
        return _buildRoute(settings, const ReminderPage());
      case 'saved':
        return _buildRoute(settings, const SavedPage());
      case 'search':
        return _buildRoute(settings, const SearchPage());
      case 'settings':
        final dynamic args = settings.arguments;
        final Function? changeLangCallback = (args as Map<String, dynamic>)['changeLangCallback'] == null ? null : args['changeLangCallback'] as Function;
        return _buildRoute(settings, SettingsPage(changeLangCallback));
      case 'tour':
        final dynamic args = settings.arguments;
        final bool popPage = args != null && (args as Map<String, dynamic>)['popPage'] != null && args['popPage'] as bool;
        return _buildRoute(settings, TourPage(popPage));
    }

    throw Exception('Route not found!');
  }

  PageRoute<Widget> _buildRoute(RouteSettings settings, Widget widget, {PageSlideDirection slideDirection = PageSlideDirection.up}) {
    Offset beginOffset = const Offset(0.0, 1.0); // default: up
    if (slideDirection == PageSlideDirection.left) {
      beginOffset = const Offset(1.0, 0.0);
    } else if (slideDirection == PageSlideDirection.right) {
      beginOffset = const Offset(-1.0, 0.0);
    } else if (slideDirection == PageSlideDirection.down) {
      beginOffset = const Offset(0.0, -1.0);
    }

    return PageRouteBuilder<Widget>(
      pageBuilder: (BuildContext context, Animation<double> animation, Animation<double> secondaryAnimation) => widget,
      settings: settings,
      transitionsBuilder: (BuildContext context, Animation<double> animation, Animation<double> secondaryAnimation, Widget child) {
        final Animatable<Offset> tween = Tween<Offset>(begin: beginOffset, end: Offset.zero).chain(CurveTween(curve: Curves.ease));

        return SlideTransition(
          position: animation.drive(tween),
          child: child,
        );
      },
    );
  }
}
