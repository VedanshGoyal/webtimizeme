<?php
// Include the HTML Parser
require_once('simple_html_dom.php');

//Function for Checking Whether a Webpage Uses rel="canonical" Link in the HTML
function is_canonical($a_url){

 //Set the Environment
 global $isCanonical;
 $isCanonical = 0;
 
 // Create DOM from URL or file
 $html = file_get_html($a_url);

 //Check If It Is An Object
 if(is_object($html)) {
 
 //Get the Main LINK Tags
 $linkTags = $html->find('link');

 //Check for REL=Canonical Tag
 foreach ($linkTags as $linkTag) {
  if ($linkTag->rel == 'canonical') {
  $isCanonical = 1;
  }
 }
 
 }
 //Close HTML Parser
 $html->clear();
 unset($html);

}

?>