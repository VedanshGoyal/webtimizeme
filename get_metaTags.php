<?php
// Include the HTML Parser
require_once('simple_html_dom.php');
require_once('checkURL.php');

//Function Returns META Tags from a URL Array;
function get_urlInfo($urls) {

//Set the Environment First
global $urlInfo;
$urlInfo = array('src' => array(),'title' => array(),'keywords' => array(),'description' => array());

 //Get META Tags from each URL
 foreach ($urls as $url) {
  if(count($urlInfo['src']) < 100) {


 //Load URL and Proceed Only If It's Reachable
 //if(checkURL($url)) {

 //Create DOM from URL or file
 $html = file_get_html($url);

   //Check If It Is An Object
   if(is_object($html)) {
 
 //Get the HTML Title Tag
 $titleTag = $html->find('title');
 $title = trim($titleTag[0]->innertext);

 //Get the Keywords & Description META Tags
 $metaTags = $html->find('meta');

  foreach ($metaTags as $metaTag) {
  if ($metaTag->name == 'keywords') {
  $keywords = trim($metaTag->content);
   }

  if ($metaTag->name == 'description') {
  $description = trim($metaTag->content);
   }
  }

 //Push Tags in Main Array
 $urlInfo['src'][] = $url;
 $urlInfo['title'][] = $title;
 $urlInfo['keywords'][] = $keywords;
 $urlInfo['description'][] = $description;
  
  //Close HTML Parser
 $html->clear();
 unset($html);

  }
 //}
 /*
 //If Something Went Wrong, Push Empty Element in Main Array
 if(empty($title) or !isset($title) ) {
  $title = '';
 }
 if(empty($keywords) or !isset($keywords) ) {
  $keywords = '';
 }
 if(empty($description) or !isset($description) ) {
  $description = '';
 }
   */
  }

 //Before closing the loop, set everything empty so that title, description and keywords remain unique for each webpage
 $title = "";
 $keywords = "";
 $description = "";

 }
}


//Demo Time
/*
$link_array = array('http://www.sdsdafasd','http://redmondpie.com/category/windows-8/','http://www.redmondpie.com/how-to-download-windows-8.1-iso-file-using-your-windows-8-product-key/');
get_urlInfo($link_array);
for ($i=0;$i<count($urlInfo['src']);$i++) {
echo $urlInfo['title'][$i]."<br />".$urlInfo['description'][$i]."<br />".$urlInfo['keywords'][$i]."<br />".$urlInfo['src'][$i]."<br /><br /><br />";
}
*/
?>