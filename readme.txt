=== Plugin Name ===
Contributors: chriscarson
Tags: twitter, tweet, embedded tweets, twitter api, oembed, shortcode
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.0.1

Adds 'tweet' shortcode for embedding tweets using Twitter's shortcode format.

== Description ==

This plugin adds a 'tweet' shortcode to embed tweets using Twitter's shortcode format,
for example:

[tweet https://twitter.com/OnionSports/status/145262716104351747 ]

1. The plugin uses Twitter's statuses/oembed API endpoint to retrieve embedded tweets identified by the id at the end of the url in the shortcode parameter
1. It caches retrieved tweets on the server to minimize API usage.
1. Optionally, it adds the necessary javascript from Twitter in document `<head>`.
1. Allows you to control the width of the embedded tweet.
1. Fixes a `clear:both;` issue in Twitter's CSS.
1. Tweet functionality (e.g., retweet and follow buttons) can be displayed in multiple languages.

== Installation ==

1. Upload the `modern-media-tweet-shortcode` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Tweak your settings in Options > Embedded Tweets

== Screenshots ==

1. The shortcode in action
2. Admin Panel


== Changelog ==

= 1.0.1 =
* Fixed faulty directory layout which put the plugin in a subdirectory, breaking the plugin installation when installed from the Plugins > Add Plugin panel

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
Fixed faulty directory layout when the plugin is installed automatically from wordpress.org


