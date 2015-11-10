<?php
// Include the HTML Parser
include('simple_html_dom.php');

//This Function Simply Returns Google Stats for a Website in googleStats Array;
function get_googleStats($url) {

//Get the URL Formatted as example.com
$url_parsed = parse_url($url);
$url = $url_parsed['host'];
$url = str_replace('www.', '', $url);

//Start HTML Parser
$html = new simple_html_dom;
$html->load_file('http://google.com.pk/search?q=site:'.$url);

//Check If It Is An Object
if(is_object($html)) {

//Find Stats DIV From Google
$stats = $html->find('div[id=resultStats]',0);

//Get the Stat Numbers
$pattern = '/'."[0-9,]+".'/';
preg_match($pattern,$stats,$match);
$match = str_replace(',', '', $match);

global $googleStats;
$googleStats = array('number' => $match[0]);
}
//Close HTML Parser
 $html->clear();
 unset($html);
}

// Demo Time ... //
/*
get_googleStats('http://www.redmondpie.com');

//Return the Desired Result
if ($googleStats['number'] < 0 || !$googleStats['number']) {
  echo "Sorry Google Has Not Indexed Your Website";
} else if ($googleStats['number'] < 100) {
  echo "Google Has Indexed Few Pages: ".$googleStats['number'];
} else if ($googleStats['number'] > 100) {
  echo "Google Has Numerous Pages Crawled: ".$googleStats['number'];
}
*/
?>