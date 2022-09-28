import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import '../class/auction.dart';

class FeaturedPageView extends StatelessWidget {
  const FeaturedPageView(this.auction, {super.key});

  final Auction auction;

  Widget _buildFeaturedCard(int i) {
    return SizedBox(
      width: 60.0,
      height: 120.0,
      child: Card(
        child: Text(i.toString()),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Text('Featured ${auction.id}'),
        SizedBox(
          width: 380.0,
          height: 100.0,
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: <Widget>[
                _buildFeaturedCard(1),
                _buildFeaturedCard(2),
                _buildFeaturedCard(3),
                _buildFeaturedCard(4),
                _buildFeaturedCard(5),
                _buildFeaturedCard(6),
                _buildFeaturedCard(7),
                _buildFeaturedCard(8),
                _buildFeaturedCard(9),
                _buildFeaturedCard(10),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
