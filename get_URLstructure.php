<?php
require_once('get_internalLinks.php');

//Function Gets an Array of URLs & Process Em for SEO; Returns Faulty URLs Array
function get_urlStructure($links) {

//Set the Environment First
define("GOOD_URL_LENGTH", 80);
global $urlStructure;
$urlStructure = array('length_error' =>array(),'noHyphen_error' =>array(),'stopWords_error' =>array(), 'cleanURL_error' =>array());

//Commonly Used Stop Words Not to be Used in Titles, Links, Anchor Texts etc.
$stopWords = array('a','the','from','as','an','it','are','to','at','be','and','in','on','of','I','me');

//Loop for Each Link
foreach ($links as $link) {

 //Check for URL Length
 
 if (strlen($link) > GOOD_URL_LENGTH) {
   $urlStructure['length_error'][] = $link;
   }

 //Check for Underscores & Spaces in URL
   //But First Convert This http://example.com/this-is-page.php?action_do=yes ==> http://example.com/this-is-page.php
   $linkf = preg_replace('/\?.+$/', '', $link);
 
 if (strpos($linkf,'_') || strpos($linkf,'%20') || strpos($linkf,'+')) {
   $urlStructure['noHyphen_error'][] = $link;
   }

 //Check for Stop Words in URL
 
 //Get a Formatted Link with Slugs Seperated by Dashes '-'
 $link_f = str_replace('%20%', '-', $link);
 $link_f = str_replace('/', '-', $link_f);
 $link_f = str_replace('_', '-', $link_f);
 $link_f = str_replace('?', '-', $link_f);
 $link_f = str_replace('=', '-', $link_f);

 $linkSlugs = explode('-',$link_f);

 foreach ($linkSlugs as $linkSlug) {
  if (in_array($linkSlug, $stopWords)) {
   $urlStructure['stopWords_error'][] = $link;
   }
  }

 //Check for '?' and '=' in URL

 if (strpos($link,'?') || strpos($link,'=')) {
   $urlStructure['cleanURL_error'][] = $link;
  }

 }
}

/* Demo Time ;-)
get_internalLinks('http://www.skidrowgames.net/');
process_urlStructure($internalLinks['url']);


echo "<h1>Length Error</h1>";
foreach ($urlStructure['length_error'] as $abc) {
echo $abc." <br/>";
}

echo "<h1>Stop Words Error</h1>";
foreach ($urlStructure['stopWords_error'] as $abc) {
echo $abc." <br/>";
}

echo "<h1>Hyphen Error</h1>";
foreach ($urlStructure['noHyphen_error'] as $abc) {
echo $abc." <br/>";
}

echo "<h1>Clean URL Error</h1>";
foreach ($urlStructure['cleanURL_error'] as $abc) {
echo $abc." <br/>";
}
*/
?>