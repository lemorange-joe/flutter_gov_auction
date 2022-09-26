import './app_info_provider.dart';
import './auction_provider.dart';
import './lemorange_app_provider.dart';

class InitValue {
  InitValue();

  AppInfoProvider get initAppInfo => AppInfoProvider();
  AuctionProvider get initAuction => AuctionProvider();
  LemorangeAppProvider get initLemorangeApp => LemorangeAppProvider();
}
