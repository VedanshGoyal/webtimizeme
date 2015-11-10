<?php
//Function for Getting Long Title Tags From an Array
function get_longTitleTags($metaTagsArray) {

 //Set the Environment First
 define("GOOD_TITLE_LENGTH", 70);
 global $longTitleTags;
 $longTitleTags = array('title' => array(), 'src' => array());

 //Check for Each Title Tag From Main Array
 for($i=0;$i<count($metaTagsArray['title']);$i++) {
  
  //First Replace HTML Entity Names, if any, with Empty Strings
  $html_entity_names = array('&quot;','&amp;','&lt;','&gt;','&amp;','&nbsp;','&copy;','&laquo;','&raquo;','&#8216;','&#8217;','&#8220;','&#8221;');
  $metaTagsArray['title'][$i] = str_replace($html_entity_names,'',$metaTagsArray['title'][$i]);

  if(strlen($metaTagsArray['title'][$i]) > GOOD_TITLE_LENGTH) {
   $longTitleTags['title'][] = $metaTagsArray['title'][$i];
   //Also Append Source URL With The Title
   $longTitleTags['src'][] = $metaTagsArray['src'][$i];
  }

 }

}

?>