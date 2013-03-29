interlace
=========

This is a markup plugin that tries to do quite a lot regarding markup and an API for markup. I still use a few of it's features although to be fair this could be so much more than it is at present.

My hope with this project is to move the mark-up to seperate plugins and provide a framework for adding them to installs.


API
===

interlace impliments two new events into the Plugin API

Interlace_markup :: This passes the mark up pattern array

  $myPattern[] = array("pattern" => "[hr]", "target" => "&lt;hr />");
  
Provides the HR BB-Code 


Interlace_allmarkup :: This passes the mark up patern array and the interwiki link array

  $myInterwiki[] = array("interwiki" => "WORD", "url" => "http://example.com/tag/\\1", "add" => " rel='tag' ");
  
Allows [[WORD:text]] to make text into a link to example.com/tag/text

