=== FPM Media Cleaner ===
Contributors: soerenbalke
Donate link: https://fokuspokus-media.de
Tags: media,cleaner
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
Requires PHP: 8.1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Scanned database for non linked media files. This plugin scanned the tables: `wp_postmeta`, `wp_options` and `wp_posts`.



== Frequently Asked Questions ==

= is this plugin epic? =

jep

== Upgrade Notice == 
Silence is golden


== Changelog ==

= v1.0.X = 
* init of plugin with some bugfixing :)



== Screenshots ==
1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png


== Language Compile ==

```
$ # create pot file:
$ wp i18n make-pot --path=../../ . ./languages/fpm-media-cleaner.pot
$ cd ./languages
$ for file in `find . -name "*.po"` ; do msgfmt -o ${file/.po/.mo} $file ; done
```