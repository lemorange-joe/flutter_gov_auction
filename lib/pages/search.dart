import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
// import 'package:logger/logger.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../generated/l10n.dart';
import '../helpers/easter_egg_helper.dart';
import '../helpers/hive_helper.dart';
import '../includes/config.dart' as config;

class SearchPage extends StatefulWidget {
  const SearchPage({super.key});

  @override
  State<SearchPage> createState() => _SearchPageState();
}

class _SearchPageState extends State<SearchPage> {
  final TextEditingController _searchController = TextEditingController();
  String _searchKeyword = '';

  void _onSubmitted(String txt) {
    if (txt.isEmpty) {
      return;
    }

    setState(() {
      _searchKeyword = txt.replaceAll(config.searchSeparatorChar, '');
    });

    if (EasterEggHelper.check(context, _searchKeyword)) {
      HiveHelper().writeDeveloper(true);
    }
    _searchController.text = '';
    HiveHelper().writeSearchHistory(_searchKeyword);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Widget _buildSearchResult() {
    return Column(
      children: <Widget>[
        Text('Search: $_searchKeyword'),
        TextButton(
          onPressed: () {
            setState(() {
              _searchKeyword = '';
            });
          },
          child: const Text('X'),
        ),
      ],
    );
  }

  Widget _buildRecentSearch() {
    return ValueListenableBuilder<Box<String>>(
      valueListenable: Hive.box<String>('search_history').listenable(),
      builder: (BuildContext context, _, __) {
        final List<String> searchHistoryList = HiveHelper().getSearchHistoryList();

        return searchHistoryList.isEmpty
            ? Text(S.of(context).noSearchHistory)
            : Column(
                children: <Widget>[
                  ...searchHistoryList
                      .map((String searchHistory) => TextButton(
                            onPressed: () {
                              setState(() {
                                _searchKeyword = searchHistory;
                              });
                            },
                            child: Text(searchHistory),
                          ))
                      .toList(),
                  TextButton(
                    onPressed: () {
                      HiveHelper().clearSearchHistory();
                    },
                    child: Text(S.of(context).clearSearchHistory),
                  ),
                ],
              );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Column(
            children: <Widget>[
              const SizedBox(height: 10.0),
              Container(
                height: 40.0 * MediaQuery.of(context).textScaleFactor,
                width: double.infinity,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(50.0),
                  color: Theme.of(context).brightness == Brightness.dark ? Theme.of(context).backgroundColor : Colors.grey[200],
                ),
                child: Row(
                  children: <Widget>[
                    IconButton(
                      onPressed: () {
                        Navigator.pop(context);
                      },
                      icon: Icon(
                        MdiIcons.arrowLeft,
                        color: Theme.of(context).textTheme.bodyText1!.color,
                        size: 24.0 * MediaQuery.of(context).textScaleFactor,
                      ),
                    ),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.only(right: 50.0),
                        child: TextField(
                          controller: _searchController,
                          onSubmitted: _onSubmitted,
                          autofocus: true,
                          decoration: InputDecoration(
                            contentPadding: const EdgeInsets.symmetric(vertical: 10.0),
                            border: InputBorder.none,
                            hintText: S.of(context).searchAuction,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 10.0),
              if (_searchKeyword.isEmpty) _buildRecentSearch() else _buildSearchResult(),
            ],
          ),
        ),
      ),
    );
  }
}
