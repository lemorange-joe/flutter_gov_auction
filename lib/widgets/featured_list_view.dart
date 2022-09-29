import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';

class FeaturedListView extends StatelessWidget {
  const FeaturedListView(this.auction, {super.key});

  final Auction auction;

  Widget _buildFeaturedCard(String txt, int i) {
    return SizedBox(
      width: 150.0,
      child: Card(
        child: Column(
          children: <Widget>[
            Expanded(
              flex: 2,
              child: Container(
                decoration: BoxDecoration(
                  image: DecorationImage(
                    image: AssetImage('assets/images/tour0$i.jpg'),
                    fit: BoxFit.cover,
                  ),
                ),
              ),
            ),
            Expanded(
              child: Text(txt),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text(
          S.of(context).featuredItems,
          style: Theme.of(context).textTheme.bodyText1!.copyWith(
                fontSize: 16.0,
                fontWeight: FontWeight.bold,
              ),
        ),
        SizedBox(
          // width: 350.0,
          height: 250.0,
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: <Widget>[
                _buildFeaturedCard('A', 1),
                _buildFeaturedCard('B', 2),
                _buildFeaturedCard('C', 3),
                _buildFeaturedCard('D', 1),
                _buildFeaturedCard('E', 2),
                _buildFeaturedCard('A', 3),
                _buildFeaturedCard('B', 1),
                _buildFeaturedCard('C', 2),
                _buildFeaturedCard('D', 3),
                _buildFeaturedCard('E', 1),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
