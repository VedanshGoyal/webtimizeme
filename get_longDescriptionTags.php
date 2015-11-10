<?php
//Function for Getting Long Description Tags From a Meta Tags Array
function get_longDescriptionTags($metaTagsArray) {

 //Set the Environment First
 define("GOOD_DESCRIPTION_LENGTH", 200);
 global $longDescriptionTags;
 $longDescriptionTags = array('description' => array(), 'src' => array());

 //Check for Each Description Tag From Main Array
 for($i=0;$i<count($metaTagsArray['description']);$i++) {
  
   if(strlen($metaTagsArray['description'][$i]) > GOOD_DESCRIPTION_LENGTH) {
   $longDescriptionTags['description'][] = $metaTagsArray['description'][$i];
   //Also Append Source URL With The Description
   $longDescriptionTags['src'][] = $metaTagsArray['src'][$i];
  }

 }

}

?>