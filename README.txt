=== FPM Media Cleaner ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://fokuspokus-media.de
Tags: 
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Removed unused media files.

== Language Compile ==

```
$ # create pot file:
$ wp i18n make-pot . languages/fpm-media-cleaner.pot
$ 
$ cd ./languages
$ for file in `find . -name "*.po"` ; do msgfmt -o ${file/.po/.mo} $file ; done
```