import './app_info_provider.dart';
import './lemorange_app_provider.dart';

class InitValue {
  InitValue();

  AppInfoProvider get initAppInfo => AppInfoProvider();
  LemorangeAppProvider get initLemorangeApp => LemorangeAppProvider();
}
