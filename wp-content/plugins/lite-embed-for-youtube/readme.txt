=== Plugin Name ===
Contributors: seezer
Donate link:
Tags: social, youtube, video, embed
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 3.3
License: GPLv2 or later

== Description ==

"Renders faster than a sneeze." This plugin is based on [Paul Irish's script](https://github.com/paulirish/lite-youtube-embed) for embedding YouTube videos. Provide videos with a supercharged focus on visual performance. This custom element renders just like the real thing but approximately 224X faster.

== Installation ==

1. Unzip and upload plugin folder to the `/wp-content/plugins/` directory, you should end up with `/wp-content/plugins/lite-yt-embed/`.
2. Activate plugin.
3. In the text mode (not visual) of your editor (or in the Document mode - not Block, if using Gutenberg) insert the following code `<lite-youtube videoid="ogfYd705cRs"></lite-youtube>`, where `ogfYd705cRs` is the ID of the video you'd like to embed. You can find it in the URL: `https://www.youtube.com/watch?v=ogfYd705cRs`. You can use quicktag button in the WordPress post editor (Classic Editor mode) to insert the code.

== Screenshots ==

1. How to find YouTube video ID.
2. Quicktag button in Classic Editor mode.

== Changelog ==

= 3.3 =
* Update `Lite YouTube Embed` sources

= 3.2 =
* Update `Lite YouTube Embed` sources

= 2.0 =
* Update `Lite YouTube Embed` sources

= 1.2 =
* Update `Lite YouTube Embed` sources, which includes
* normalize colon usage for pseudo elements
* use youtube nocookie for enhanced privacy
* replace css with actual YT SVG

= 1.1 =
* Update `Lite YouTube Embed` sources
* Add quicktag button to the WordPress post editor (Classic Editor mode)

= 1.0 =
* Initial release.