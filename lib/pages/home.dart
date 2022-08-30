import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';

class HomePage extends StatefulWidget {
  const HomePage(this.version, {Key? key}) : super(key: key);

  final String version;

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _counter = 0;

  Future<void> _incrementCounter() async {
    setState(() {
      _counter++;
    });

    final dynamic result = await ApiHelper().get(S.of(context).lang, 'auction', 'list', useDemoData: true);
    Logger().d(result);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: <Widget>[
            Text('${S.current.appName} ${widget.version}'),
            TextButton(
              onPressed: () {
                Navigator.pushNamed(context, 'settings');
              },
              child: const Icon(
                MdiIcons.cog,
                color: Colors.white,
              ),
            ),
          ],
        ),
      ),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: <Widget>[
            const Text(
              'You have pushed the button this many times:',
            ),
            Text(
              '$_counter',
              style: Theme.of(context).textTheme.headline4,
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _incrementCounter,
        tooltip: 'Increment',
        child: const Icon(Icons.add),
      ),
    );
  }
}
