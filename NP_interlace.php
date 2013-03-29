<?php
/**
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
Contact me : letters@lordmatt.co.uk
You may use and distribute this freely as long as you leave the copyrights intact.
*/

// Unprocesed item :: PreInterlace ::: pre any action at all
// the array :: Interlace_markup ::: this allows new interwiki to be pushed onto the array.
// the array :: Interlace_markup ::: this allows new markup patterns to be used
// fully Processed item :: PostInterlace ::: all work is compleate

// a plugin can subscribe to the plugin to add mark-up or get markup patterns (advanced).
// To see everything that will be used - use this spy event: Interlace_allmarkup
// With a little cache control any other plugin could use the markup defined by this and all supporting plugins.

/*v1.1.3 As 1.1.2 was nicely stable I added some new features     	   added: (\n\n|\r\r|\n\r\n\r)  (as opposed to /[\n\r][\n\r]/ or (\n|\r|\n\r)(\n|\r|\n\r)) 	   as a pattern two line feed to make a <br> which is inline with wikimedia markup it should	   also work with or without other <br> systems so that ALL double spaces convert but <br>'d	   spaces do not.  Add a space to the line to force the <br> not to happen	   	   added: [br] becomes <br>	   	   added: rel="tag" to the [[word]] wiki link backwards compatability code.	   	   edit: new comments	   	   added: altered the technirati tag to be lordmatt tag for personal use	   must remember to change back if I release this version
v1.1.2 Fixed: Comment System should work
       edit:  altered my own history to make it clearer (added the word "php")
v1.1.1 Restored the [[my:x]] interwiki tag for compatability
       added: php comments to show the code that I used that I didn't write       
v1.1.0 Fixed: some mark up had extra spaces (removed)
              indents to fit Extend Aware Ideals
       added: experimental: events
              hack: apply mark-up to comments  
              help text (copy is in comment below too)
v1.0.0 first release

Wp links to english wikipedia and acts as a blog tag
LordMatt links to lordmatt.co.uk/wiki as a blog tag
Google, G Links to a search on google for the linked word(s)
CheatsWiki cheatswiki.com as a tag
cPanelWiki cpanelwiki.org as a tag
Dictionary dict.org search
IMDbName imdb.com link by name
IMDbTitle imdb.com link by title
MarvelDatabase, Marvel link to marveldatabase.com/wiki as a blog tag
MeatBall usemod.com meatball wiki as a blog tag
MediaWiki mediawiki.org/wiki as a blog tag
MoinMoin moinmoin.wikiwikiweb.de as a blog tag
Wiktionary (guess) as a tag
ZZZ wiki.zzz.ee as a tag
Technorati technorati search
Tag technorati as a blog tag

[url]ww.etc.com[/url]
[url=www.etc.com]link[/url]
===== 5th level para =====
==== 4th level ====
=== 3rd level ===
== 1st level ==
---- line (the same as <hr />)
--text-- give <strike>text</strike>
[img]mydomain.com/pic.jpg[/img]
[b][i][u]blod, itallic and underline[/u][/i][/b] 

*/

if (!function_exists('sql_table'))
{
	function sql_table($name) {
		return 'nucleus_' . $name;
	}
}
 
class NP_interlace extends NucleusPlugin {
 
        function getName() { return 'Inter-Lace'; }
        function getAuthor() { return 'Lord Matt (based on some code by Auz)'; }
        function getURL()    { return 'http://lordmatt.co.uk'; }
        function getVersion() { return '1.1.2 Jimbob'; }
        function getDescription() {
                return 'Inter-Lace - the interwiki, mark-up helping, autotagging plugin allows some BB ([b], [u], [i], [url]) and some wiki mark-up (--strike--, ==headings==) as well as allowing interwiki links [[WP:Link]] (Wikipedia) and inline tags [[Tag:Link]] ...auto linking urls.  ';
        }

  function getEventList() {
    return array('PreItem', 'PreComment');
  }


	function supportsFeature($what) {
		switch($what) {
		case 'HelpPage':
			return 1;
			break;
		case 'SqlTablePrefix':
			return 1;
			break;
		  default:
			return 0;
		}
	  }
  
  
  function event_PreComment($data){
  $fred["item"] = &$data['comment']['body'];
  $this->event_PreItem(&$fred);
  }
  
  
  
  function event_PreItem($data) {
    $this->currentItem = &$data["item"];
    
		global $manager;
		$manager->notify('PreInterlace', &$this->currentItem);
    
    
		//Lots of help:
		// http://uk.php.net/preg_replace
		// http://weblogtoolscollection.com/regex/regex.php
		
		
		// INTERWIKI
		// mark the place where the unique text goes as "\\1"
		//the "add" allows an extra attribute to be added to the A tag
		// In many cases this means turning links into blog tags.
		
		//The my is here for backwards compatability but shall change in the future.
		
		//It would be nice to use an interwiki page or file at some stage
		$interwiki = array(
		array("interwiki" => "Wp", "url" => "http://en.wikipedia.org/wiki/\\1", "add" => " rel='tag' "),
		array("interwiki" => "LordMatt", "url" => "http://www.lordmatt.co.uk/wiki/index.php/\\1", "add" => " rel='tag' "),
		array("interwiki" => "my", "url" => "http://www.lordmatt.co.uk/wiki/index.php/\\1", "add" => " rel='tag' "),
		array("interwiki" => "Google", "url" => "http://www.google.co.uk/search?q=\\1", "add" => " rel='search' "),
		array("interwiki" => "G", "url" => "http://www.google.co.uk/search?q=\\1", "add" => " rel='search' "),
		array("interwiki" => "CheatsWiki", "url" => "http://www.cheatswiki.com/index.php/\\1", "add" => " rel='tag' "),
		array("interwiki" => "cPanelWiki", "url" => "http://cpanelwiki.org/index.php/\\1",  "add" => " rel='tag' "),
		array("interwiki" => "Dictionary", "url" => "http://www.dict.org/bin/Dict?Database=*&Form=Dict1&Strategy=*&Query=\\1", "add" => " rel='search' "),
		array("interwiki" => "IMDbName", "url" => "http://www.imdb.com/name/nm\\1", "add" => " "),
		array("interwiki" => "IMDbTitle", "url" => "http://www.imdb.com/title/tt\\1", "add" => " "),
		array("interwiki" => "MarvelDatabase", "url" => "http://www.marveldatabase.com/wiki/\\1", "add" => " rel='tag' "),
		array("interwiki" => "Marvel", "url" => "http://www.marveldatabase.com/wiki/\\1", "add" => " rel='tag' "),
		array("interwiki" => "MeatBall", "url" => "http://www.usemod.com/cgi-bin/mb.pl?\\1", "add" => " rel='tag' "),
		array("interwiki" => "MediaWiki", "url" => "http://www.mediawiki.org/wiki/\\1", "add" => " rel='tag' "),
		array("interwiki" => "MoinMoin", "url" => "http://moinmoin.wikiwikiweb.de/\\1", "add" => " rel='tag' "),
		array("interwiki" => "Wiktionary", "url" => "http://en.wiktionary.org/wiki/\\1", "add" => " rel='tag' "),
		array("interwiki" => "ZZZ", "url" => "http://wiki.zzz.ee/index.php/\\1", "add" => " rel='tag' "),
		array("interwiki" => "Technorati", "url" => "http://www.technorati.com/search/\\1", "add" => " rel='search' "),
		//array("interwiki" => "Tag", "url" => "http://www.technorati.com/tags/\\1", "add" => " rel='tag' ")
		
		//Lord Matt's tag version		
		array("interwiki" => "Tag", "url" => "http://lordmatt.co.uk/fact/\\1", "add" => " rel='tag' ")
		);
		
		
		global $manager;
		$manager->notify('Interlace_interwiki', &$interwiki);
		
		
		// MARK UP PATTERNS
		//[url] tags, [b], [i] & [u]
		//Auto link all urls with and without http://
		//Allows more cut n past (for forum) style codes to work.
		//The wiki mark-up allows: heading, strikeout, dividing line and 
		// Newbie Friendly.
		$basic = "[.*?^\n]"; //Any char but not a line break
		$markup = array(
		array("pattern" => "/\[url=($basic)\](.*?)\[\/url\]/", "target" => "<a href=\"\\1\">\\2</a>"),
		array("pattern" => "/\[url\]($basic)\[\/url\]/", "target" => "<a href=\"\\1\">\\1</a>"),
		array("pattern" => "/={5}($basic)={5}/", "target" => "<h5>\\1</h5>"),	//The levels must be reverse order	
		array("pattern" => "/={4}($basic)={4}/", "target" => "<h4>\\1</h4>"),
		array("pattern" => "/===($basic)===/", "target" => "<h3>\\1</h3>"),
		array("pattern" => "/==($basic)==/", "target" => "<h2>\\1</h2>"),
		//array("pattern" => "/=(.*?)=/", "target" => "<h1>\\1</h1>"),
		array("pattern" => "/----/", "target" => "<hr />"),
		array("pattern" => "/[^!^<]--($basic)--^>/", "target" => "<strike>\\1</strike>"), //This wants improving to disallow line breaks
		array("pattern" => "/\[img\](.*?)\[\/img\]/", "target" => "<img src=\"\\1\">"),
		//array("pattern" => "/((http)+(s)?:(//)|(www\.))((\w|\.|\-|_)+)(/)?(\S+)?/i",  "target" => "<a href=\"http\\3://\\5\\6\\8\\9\" target=\"_blank\" title=\"\\0\">\\5\\6</a>"),
		array("pattern" => "/\[([biu])\]/i", "target" => "<\\1>"),
		array("pattern" => "/\[\/([biu])\]/i", "target" => "</\\1>"),				array("pattern" => "/(\n\n|\r\r|\n\r\n\r)/", "target" => "<br />"),				array("pattern" => "/\[br\]/", "target" => "<br />")
		);
			
		// "/(\\S+@\\S+\\.\\w+)/", "<a href=\"mailto:\\1\">\\1</a>"
		
		$manager->notify('Interlace_markup', &$markup);
		
		
		
		$manager->notify('Interlace_allmarkup', array('markup' => $markup, 'interwiki' => $this->blog));
		
		foreach ($markup as $pp) {
			$pattern = $pp["pattern"];
			$target = $pp["target"];
			//echo ' PATTERN: '.$pattern.'<br />'.' target: '.$target.'<br /><br />';
				$this->currentItem->body = preg_replace( $pattern, $target, $this->currentItem->body );
				$this->currentItem->more = preg_replace( $pattern, $target, $this->currentItem->more );
		}
    
    
		foreach ($interwiki as $aa => $bb) {
		$interwiki = $bb["interwiki"];
		$url = $bb["url"];
		$add = $bb["add"];
			@$this->currentItem->body = preg_replace( "/\[\[".$interwiki.":(.+?)\]\]/i", "<a href=\"".$url."\" target=\"_blank\" ".$add." title=\"".$interwiki.": \\1\">\\1</a>", $this->currentItem->body );
			@$this->currentItem->more = preg_replace( "/\[\[".$interwiki.":(.+?)\]\]/i", "<a href=\"".$url."\" target=\"_blank\" ".$add." title=\"".$interwiki.": \\1\">\\1</a>", $this->currentItem->more );
		}

	// This is the code from the wikilink plugin by Auz allowing 
	// a move from the former without the two conflicting or loss of
	// functionality.  EDIT: added rel="tag"
	$this->currentItem->body = preg_replace( "/\[\[(.+?)\]\]/", "<a href=\"http://en.wikipedia.org/wiki/\\1\" target=\"_blank\" rel=\"tag\" title=\"Wikipedia: \\1\">\\1</a>", $this->currentItem->body );
	$this->currentItem->more = preg_replace( "/\[\[(.+?)\]\]/", "<a href=\"http://en.wikipedia.org/wiki/\\1\" target=\"_blank\" rel=\"tag\" title=\"Wikipedia: \\1\">\\1</a>", $this->currentItem->more );
	
	$manager->notify('PostInterlace', &$this->currentItem);
    }

// stip multi-white space out: $sample = preg_replace('/\s\s+/', ' ', $sample);
// $body = preg_replace('/\s(\w+:\/\/)(\S+)/', ' <a href="\\1\\2" target="_blank">\\1\\2</a>', $body);
// $body = preg_replace('/\s(www\.)(\S+)/', ' <a href="http://\\1\\2" target="_blank">\\1\\2</a>', $body);
}



/*
This last block of commetns is to show where the source came from.

It is the original WikiLink plugin in totality so as not to give less credit (or access) than might be desired.





<?php
class NP_WikiLink extends NucleusPlugin {

  // name of plugin
  function getName() {
    return 'WikiLink';
  }

  // author of plugin
  function getAuthor() {
    return 'Auz';
  }

  // an URL to the plugin website
  // can also be of the form mailto:foo@bar.com
  function getURL() {
    return 'http://www.auzsoft.net/';
  }

  // version of the plugin
  function getVersion() {
    return '0.2';
  }

  // a description to be shown on the installed plugins listing
  function getDescription() {
    return 'Replaces [[Wiki]] coding with links to Wikipedia';
  }

  function getEventList() {
    return array('PostAddItem', 'PreUpdateItem', 'PreDeleteItem', 'PreItem', 'PreComment');
  }

  function event_PreItem($data) {
    $this->currentItem = &$data["item"];
    
    $this->currentItem->body = preg_replace( "/\[\[(.+?)\]\]/", "<a href=\"http://en.wikipedia.org/wiki/\\1\" target=\"_blank\" title=\"Wikipedia: \\1\">\\1</a>", $this->currentItem->body );
    $this->currentItem->more = preg_replace( "/\[\[(.+?)\]\]/", "<a href=\"http://en.wikipedia.org/wiki/\\1\" target=\"_blank\" title=\"Wikipedia: \\1\">\\1</a>", $this->currentItem->more );
  }
}
?>






That's all folks

*/

?>
