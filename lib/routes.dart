import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
import './class/auction.dart';
import './pages/auction_details.dart';
import './pages/auction_lot.dart';
import './pages/home.dart';

enum PageSlideDirection { up, down, left, right }

class Routes {
  PageRoute<Widget> getRoutes(RouteSettings settings) {
    switch (settings.name) {
      case 'auction_details':
        final dynamic args = settings.arguments;
        final Auction auction = (args as Map<String, dynamic>)['auction'] as Auction;
        return _buildRoute(settings, AuctionDetailsPage(auction));
      case 'auction_lot':
        final dynamic args = settings.arguments;
        final AuctionLot auctionLot = (args as Map<String, dynamic>)['auctionLot'] as AuctionLot;
        return _buildRoute(settings, AuctionLotPage(auctionLot));
      case 'home':
        return _buildRoute(settings, HomePage(FlutterConfig.get('VERSION') as String));
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
