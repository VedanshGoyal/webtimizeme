<?php

set_time_limit(0);

// Include the HTML Parser
require_once('simple_html_dom.php');
require_once('checkURL.php');

//Function for Checking Google Authorship... Requires Several Internal Links
function is_googleAuthorship($links) {

 //Set the Global Variable googleAuthorship as False for Now
 global $googleAuthorship;
 $googleAuthorship = false;

 //Count the Number of Internal Links Given in Function
 $count = count($links);

 //Check for Google Authorship on a Maximum of 20 links...
 for($i=0;$i<21;$i++) {

  //Keep Checking These 20 Internal Links Until Google Authorship on Any One of Them is Verified
  if($i < $count && $googleAuthorship == false) {

  //Proceed only if Link is Reachable
  $url = $links[$i];
   if(checkURL($url)) {

   // Create DOM from URL
   $html = file_get_html($url);

   //Check If It Is An Object
   if(is_object($html)) {

   //3 Tricks Used for checking Google Authorship on a Webpage...
    
    //(1) If rel="author" is present in any <link> tag

     //Get all <link> tags
     $linkTags = $html->find('link');
     //Search for rel="author" and Proceed Accordingly
     foreach ($linkTags as $linkTag) {
     if ($linkTag->rel == 'author') {
     $googleAuthorship = true;
      }
     }

    //(2) If rel="author" is present in any <a> tag... (3) If ?rel=author is present in HREF of any <a> tag

     //Proceed Only if Method 1 Produced No Result
     if($googleAuthorship == false) {
     //Get all <a> tags
     $aTags = $html->find('a');
     //Search for rel="author"
     foreach ($aTags as $aTag) {
     if($googleAuthorship == false) {
      if ($aTag->rel == 'author') {
      $googleAuthorship = true;
       }
      //Or Search for ?rel=author in HREF
      else if (!(strpos($aTag->href, '?rel=author') === false)) {
      $googleAuthorship = true;
      }
     }
     }
    }
   }
  }
 }
}
}

//Demo Time
/*
require_once('get_internalLinks.php');
$homepage_url = 'http://mashable.com/';
get_internalLinks($homepage_url);
is_googleAuthorship($internalLinks['url']);
if ($googleAuthorship == true) { echo "true"; } else { echo "false" ; }
*/
?>