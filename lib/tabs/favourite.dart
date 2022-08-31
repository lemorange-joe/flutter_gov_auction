import 'package:flutter/material.dart';
import '../generated/l10n.dart';

class FavouriteTab extends StatelessWidget {
  const FavouriteTab({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Text(S.of(context).myFavourite);
  }
}
