<?php

//Function for Getting Clean URLs from Previously Generated internalLinks Array
function get_cleanURLs($linksArray, $isWWW = false) {

//Set the Environment First
if(empty($cleanURLs)) {
global $cleanURLs;
$cleanURLs = array('url' => array(),'source' => array(),'text' => array());
}

//Loop Through Each Link
for ($i=0;$i<count($linksArray['url']);$i++) {

 $link = $linksArray['url'][$i];

 //Trim And Append Slash At End of Each URL
 $link = trim($link);
 if(substr_count($link, '.') < 2) { 
 $link .= '/';
}

 //Remove Extra Slashes
 $link = str_replace("////","/",$link);
 $link = str_replace("///","/",$link);
 $link = str_replace("//","/",$link);
 $link = str_replace("//","/",$link);
  $link = str_replace("//","/",$link);
  
   //Above Functions Will Also Replace "http://" with "http:/" ... Make Correction
   $link = str_replace("http:/","http://",$link);
   $link = str_replace("https:/","https://",$link);


 //Remove or Append WWW In Each URL if Required
 if ($isWWW == false) {
 $link = str_replace("www.","",$link);
 }
 
 if ($isWWW == true) {
  //Replace http:// with http://www. for Each URL
  if (strpos($link,'www.') === FALSE && strpos($link,'http://') !== FALSE ) {
  $link = str_replace("http://","http://www.",$link);
  }

  //Replace https:// with https://www. for Each URL
  if (strpos($link,'www.') === FALSE && strpos($link,'https://') !== FALSE ) {
  $link = str_replace("https://","https://www.",$link);
  }
 }

 $cleanURLs['url'][] = $link;
 $cleanURLs['source'][] = $linksArray['source'][$i];
 $cleanURLs['text'][] = $linksArray['text'][$i];

 }
}

?>