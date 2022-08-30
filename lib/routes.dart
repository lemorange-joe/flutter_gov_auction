import 'package:flutter/material.dart';
import 'package:flutter_config/flutter_config.dart';
import './pages/home.dart';
import './pages/settings.dart';

enum PageSlideDirection { up, down, left, right }

class Routes {
  PageRoute<Widget> getRoutes(RouteSettings settings) {
    switch (settings.name) {
      case 'home':
        return _buildRoute(settings, HomePage(FlutterConfig.get('VERSION') as String));
      case 'settings':
        return _buildRoute(settings, const SettingsPage());
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
