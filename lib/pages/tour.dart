import 'package:flutter/material.dart';
import '../generated/l10n.dart';
import '../helpers/hive_helper.dart';

class TourPage extends StatefulWidget {
  const TourPage(this.popPage, {Key? key}) : super(key: key);

  final bool popPage;

  @override
  State<TourPage> createState() => _TourPageState();
}

class _TourPageState extends State<TourPage> {
  final List<TourImage> _tourImages = <TourImage>[];
  final PageController _controller = PageController();
  int _page = 0;
  bool _finished = false;

  @override
  void initState() {
    super.initState();

    _tourImages.add(TourImage(S.current.tourTitle1, S.current.tourContent1, 'tour01.jpg'));
    _tourImages.add(TourImage(S.current.tourTitle2, S.current.tourContent2, 'tour02.jpg'));
    _tourImages.add(TourImage(S.current.tourTitle3, S.current.tourContent3, 'tour03.jpg'));
  }

  @override
  void dispose() {
    _controller.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: <Widget>[
          PageView.builder(
            onPageChanged: (int index) {
              setState(() {
                _page = index;
                _finished = _finished || _page >= _tourImages.length - 1;
              });
            },
            controller: _controller,
            itemCount: _tourImages.length,
            itemBuilder: (BuildContext context, int i) {
              return pageBuilder(context, _tourImages[i]);
            },
          ),
          if (!_finished)
            Align(
              alignment: Alignment.topRight,
              child: Padding(
                padding: const EdgeInsets.only(right: 10.0, top: 40.0),
                child: TextButton(
                  onPressed: () {
                    HiveHelper().writeFirstLaunch(false);
                    if (widget.popPage) {
                      Navigator.pop(context);
                    } else {
                      Navigator.pushReplacementNamed(context, 'home');
                    }
                  },
                  child: Text(
                    S.of(context).skip,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 16.0,
                    ),
                  ),
                ),
              ),
            ),
          Align(
            alignment: Alignment.bottomCenter,
            child: Container(
              height: 60.0,
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              width: MediaQuery.of(context).size.width,
              color: Colors.black,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: <Widget>[
                  getBullets(),
                  TextButton(
                    onPressed: () {
                      if (_page < _tourImages.length - 1) {
                        setState(() {
                          _controller.animateToPage(
                            _page + 1,
                            duration: const Duration(milliseconds: 300),
                            curve: Curves.easeIn,
                          );
                        });
                      } else {
                        HiveHelper().writeFirstLaunch(false);
                        if (widget.popPage) {
                          Navigator.pop(context);
                        } else {
                          Navigator.pushReplacementNamed(context, 'home');
                        }
                      }
                    },
                    child: Text(
                      _page == _tourImages.length - 1 ? S.of(context).done : S.of(context).next,
                      style: TextStyle(
                        fontSize: 16.0,
                        color: _page == _tourImages.length - 1 ? Colors.blue : Colors.yellow[600],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget getBullets() {
    return Row(
        children: _tourImages.asMap().entries.map((MapEntry<int, TourImage> entry) {
      final int i = entry.key;
      return Padding(
        padding: const EdgeInsets.only(right: 12.0),
        child: Container(
          width: 8.0,
          height: 8.0,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(8.0),
            color: i == _page ? Colors.white : Colors.grey,
          ),
        ),
      );
    }).toList());
  }

  Widget pageBuilder(BuildContext contex, TourImage tourImage) {
    return SizedBox(
      width: double.infinity,
      height: double.infinity,
      child: Stack(
        fit: StackFit.expand,
        children: <Widget>[
          ClipRect(
            child: FittedBox(
              fit: BoxFit.cover,
              child: Image(
                image: AssetImage('assets/images/${tourImage.imageAsset}'),
              ),
            ),
          ),
          Positioned(
            bottom: 60.0, // the height of the button row at the bottom
            child: Column(
              children: <Widget>[
                Container(
                  width: MediaQuery.of(context).size.width,
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(
                      colors: <Color>[
                        Color.fromARGB(30, 0, 0, 0),
                        Color.fromARGB(255, 0, 0, 0),
                      ],
                      begin: FractionalOffset(0.0, 0.4),
                      end: FractionalOffset.bottomLeft,
                      stops: <double>[0.0, 1.0],
                    ),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12.0, 6.0, 12.0, 6.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        Text(
                          tourImage.title,
                          style: const TextStyle(
                            fontSize: 24.0,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 15.0),
                        Text(
                          tourImage.content,
                          style: const TextStyle(
                            fontSize: 18.0,
                            color: Colors.white,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class TourImage {
  TourImage(this.title, this.content, this.imageAsset);

  final String title;
  final String content;
  final String imageAsset;
}
