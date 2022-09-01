import 'package:flutter/material.dart';
// import 'package:flutter/rendering.dart';
import '../generated/l10n.dart';

class FavouriteTab extends StatefulWidget {
  const FavouriteTab({Key? key}) : super(key: key);

  @override
  State<FavouriteTab> createState() => _FavouriteTabState();
}

class _FavouriteTabState extends State<FavouriteTab> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();

    _scrollController.addListener(() {});
  }

  @override
  void dispose() {
    _scrollController.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // NotificationListener<UserScrollNotification>(
    // onNotification: (UserScrollNotification notification) {
    //   (widget.toggleBottomNav as Function(bool))(notification.direction == ScrollDirection.forward);
    //   return true;
    // }

    return ListView.builder(
      controller: _scrollController,
      itemCount: 500,
      itemBuilder: (BuildContext context, int i) {
        return ListTile(
          leading: const Icon(Icons.list),
          title: Text('${S.of(context).myFavourite} ${i + 1}'),
        );
      },
    );
  }
}
