import 'package:flutter/material.dart';
import 'package:logger/logger.dart';
import 'package:provider/provider.dart';
import '../generated/l10n.dart';
import '../helpers/api_helper.dart';
// import '../include/utilities.dart' as utilities;
import '../providers/app_info_provider.dart';
import '../widgets/ui/lemorange_loading.dart';

class HomeTab extends StatefulWidget {
  const HomeTab({Key? key}) : super(key: key);

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  int _counter = 0;

  @override
  void initState() {
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppInfoProvider>(context, listen: false).refresh(lang: S.of(context).lang);
    });
  }

  Future<void> _incrementCounter() async {
    setState(() {
      _counter++;
    });

    final dynamic result = await ApiHelper().get(S.of(context).lang, 'data', 'appinfo', useDemoData: true);
    Logger().d(result);
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          Expanded(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                const Text(
                  'You have pushed the button this many times:',
                ),
                Text(
                  '$_counter',
                  style: Theme.of(context).textTheme.headline4,
                ),
                TextButton(
                  onPressed: _incrementCounter,
                  child: const Icon(Icons.add),
                ),
                LemorangeLoading(),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
