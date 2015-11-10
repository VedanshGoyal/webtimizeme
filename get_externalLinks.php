<?php
require_once('get_internalLinks.php');
require_once('checkURL.php');

//Function Scraps All External Links an Internal Links URL Array; Returns $externalLinks Array
function get_externalLinks($urlarray) {

//Set the Environment Before Looping from Array
 //Parse URL into Components
 $any_Url_From_Main_Array = $urlarray[0];
 $url_parsed = parse_url($any_Url_From_Main_Array);

 //Get Various Formatted URLs
 $furl = $url_parsed['host'];

 //Pattern of an Internal Link (Non-Relative)
 $pattern1 = '/^(http:\/\/|https:\/\/)(www.)?'.$furl.'/';

 //Pattern of an External Link
 $pattern2 = '/^'.'(http:\/\/|https:\/\/)(www.)?'.'/';

 //Set the Main Array to Return
 global $externalLinks;
 if (!is_array($externalLinks)) {
 $externalLinks = array('url' => array(),'source' => array());
 }

 foreach ($urlarray as $url) {
 //Proceed Only If Given URL is Accesible for Parsing; Then Search For External Links
 $headers = get_headers($url);
 $responce_header = $headers[0];
 if(checkURL($url) == true && count($externalLinks['url']) < 50) {

  //Create DOM from URL or file
 $html = file_get_html($url);

  //Check If It Is An Object
  if(is_object($html)) {

  //Get the Links But Check Whether Find Works
  if(method_exists($html,"find")) {
  if($html->find('a')) {
  $links = $html->find('a');


  //Loop Through Each Link
  foreach ($links as $element) {


   $linkHref = $element->href;

   //Remove the White Spaces in URL i.e. ' example.com/ exam/' => 'example.com/%20exam/'
   $linkHref = trim($linkHref);
   $linkHref = str_replace(' ', '%20', $linkHref);

    //Check for an External Link
    if (!preg_match($pattern1, $linkHref) && preg_match($pattern2, $linkHref)) {

     //Avoid Duplication
     if (!in_array($linkHref,$externalLinks['url'])) {
     $externalLinks['url'][] = $linkHref;
     $externalLinks['source'][] = $url;

     }
   }
  }
  }
  }
  }
  $html->clear();
  unset($html);
  }
 }
}

//Demo Time ;-)
/*
get_internalLinks('http://www.redmondpie.com/');
$links_array = $internalLinks['url'];

get_externalLinks($links_array);
// Echo Em ... ;-)
echo "<h1>External Links </h1>";
for ($i=0;$i<count($externalLinks['url']);$i++) {
echo $i."<br />"."  <b>HREF:</b>".$externalLinks['url'][$i]."<br />".'  <b>Source:</b>'.$externalLinks['source'][$i]."<br />"."<br /><br /><br />";
}
*/
?>