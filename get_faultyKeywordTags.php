<?php
//Function for Checking Keywords Tags Stuffing and Length from a Meta Tags Array
function get_faultyKeywordTags($metaTagsArray) {

 //Set the Environment First
 define("GOOD_KEYWORDS_LIMIT", 10);
 define("KEYWORDS_REDUNDANCY_LIMIT", 1);

 //Variables for Faulty Keyword Tags
 global $longKeywordTags;
 $longKeywordTags = array('keywords' => array(), 'src' => array());
 global $redundantKeywordTags;
 $redundantKeywordTags = array('redundantKeywords' => array(), 'src' => array());


 //Check for Each Keyword Tag From Main Array
 for($i=0;$i<count($metaTagsArray['keywords']);$i++) {

  //Check & Proceed for Maximum Words Limit in Keywords Tag
   $keywordsArray = explode(',',$metaTagsArray['keywords'][$i]);
   if(count($keywordsArray) > GOOD_KEYWORDS_LIMIT) {
     $longKeywordTags['keywords'][] = $metaTagsArray['keywords'][$i];
     //Also Append Source URL With The Title
     $longKeywordTags['src'][] = $metaTagsArray['src'][$i];
    }

  //Check & Proceed for Keywords Tag Stuffing
   //Find Redundant Keywords... Start by assuming there are No Redundant Keywords
   $redundantWords = "";

   //Convert "apple, iphone, apple company" into "apple  iphone  apple company"
   $plainKeywords = str_replace(',',' ',$metaTagsArray['keywords'][$i]);
   //Get Seperate Words
   $plainKeywords = explode(' ',$plainKeywords);
   //Count Each Keyword Repetition & Proceed Accordingly
   $plainKeywordsCount = array_count_values($plainKeywords);
   foreach($plainKeywordsCount as $key => $value) {
    if($value > KEYWORDS_REDUNDANCY_LIMIT && !empty($key)) {
    $redundantWords .= " | $key ";
    }
   }

   //Enter Redundant Keywords, If Any, Into The Main Array
   if(!empty($redundantWords)) {
    $redundantKeywordTags['redundantKeywords'][] = $redundantWords;
    //Also Append Source URL With The Title
    $redundantKeywordTags['src'][] = $metaTagsArray['src'][$i];
   }

 }

}

?>