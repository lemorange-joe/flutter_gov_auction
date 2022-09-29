import 'package:flutter/material.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../class/auction.dart';
import '../generated/l10n.dart';
import '../includes/config.dart' as config;
import '../widgets/featured_card.dart';

class FeaturedListView extends StatelessWidget {
  const FeaturedListView(this.auction, {super.key});

  final Auction auction;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Row(
          children: <Widget>[
            Icon(
              MdiIcons.checkDecagram,
              color: config.blue,
              size: 22.0 * MediaQuery.of(context).textScaleFactor,
            ),
            const SizedBox(width: config.iconTextSpacing),
            Text(
              S.of(context).featuredItems,
              style: const TextStyle(
                color: config.blue,
                fontSize: 16.0,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        SizedBox(
          height: 250.0,
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: <Widget>[
                const FeaturedCard('Lorem ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'tour01.jpg'),
                const FeaturedCard('Curabitur scelerisque ',
                    'Curabitur scelerisque in massa quis eleifend. Curabitur in eros viverra, interdum nisl ac, dapibus turpis.', 'tour02.jpg'),
                const FeaturedCard(
                    'Nullam pharetra',
                    'Nullam pharetra varius leo et tincidunt. Suspendisse sagittis tortor quis nulla vulputate, eu fringilla odio egestas. Cras efficitur, nisi eu vehicula convallis, arcu sem congue ex, non condimentum quam lectus ut risus.',
                    'tour03.jpg'),
                const FeaturedCard(
                    'Etiam vestibulum tempor ligula',
                    'Nulla facilisi. Maecenas placerat, sapien non sollicitudin cursus, quam arcu tempor diam, eget congue eros eros egestas ex. Nullam ornare semper risus at tincidunt. Phasellus quis diam vehicula, tincidunt justo ut, fermentum eros. Ut neque justo, placerat ac laoreet at, pharetra in orci.',
                    'tour01.jpg'),
                const FeaturedCard('E', 'content', 'tour02.jpg'),
                const FeaturedCard('F', 'content', 'tour03.jpg'),
                const FeaturedCard('G', 'content', 'tour01.jpg'),
                const FeaturedCard('H', 'content', 'tour02.jpg'),
                const FeaturedCard('I', 'content', 'tour03.jpg'),
                const FeaturedCard('J', 'content', 'tour01.jpg'),
                const FeaturedCard('K', 'content', 'tour02.jpg'),
                FeaturedCard(S.of(context).agreement, S.of(context).agreementParagraph1, 'tour03.jpg'),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
