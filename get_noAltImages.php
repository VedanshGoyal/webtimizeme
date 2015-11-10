<?php
// Include the HTML Parser
require_once('simple_html_dom.php');
require_once('checkURL.php');

//Function Uses An Array of Links to Crop for All Images with No Alt Tags
function get_noAltImages($links) {

//Set the Environment First
global $noAltImages;
$noAltImages = array('url' => array(), 'src' => array());

foreach ($links as $link) {
if (count($noAltImages['url']) < 100 && checkURL($link) == true) {

// Create DOM from URL or file
 $html = file_get_html($link);

//Check If It Is An Object
   if(is_object($html)) {

//Crawl All Images from URL
$images = $html->find('img');

//Check Alt Text for Each Image
foreach ($images as $image) {
  
  //Proceed If Alt Text is Empty or No Alt Text is Found
   if (empty($image->alt)) {

   //Remove the White Spaces in URL i.e. ' example.com/ exam/' => 'example.com/%20exam/'
   trim($image->src);
   str_replace(' ', '%20', $image->src);

   //Send Default Image URL & Origin Source; But Beware of Duplication

   if (!in_array($image->src,$noAltImages['url']) && !empty($image->src)) {
   $noAltImages['url'][] = $image->src;
   $noAltImages['src'][] = $link;
   }
  }

////// Make Each Img Url Complete: images/example.png =>>> http://www.ex.com/images/example.png

 //Set Pattern Checking Whether An Image Link is Relative or Complete
 $pattern = '/^'.'(http:\/\/|https:\/\/)'.'/';

 //// Get Webpage Link Into Various Formats

 //Parse URL into Components
 $url_parsed = parse_url($link);

 //Get Various Formatted URLs
 $furl = $url_parsed['host'];
 $baseurl = $url_parsed['scheme'].'://'.$url_parsed['host'];
 $dirurl = dirname($link);

   //Check Whether URL is Like 'http://example.com/example/' or 'http://example.com/example.html' ... Proceed Accordingly
   if (!empty($url_parsed['path']) && strpos($url_parsed['path'],'.') === FALSE) {
   $dirurl = $link;
   }

   //For Homepage URL like 'http://example.com/, dirname funtion outputs directory name as 'http' .. So Correct It !!!
   if($dirurl == 'http:' || $dirurl == 'https:') {
   $dirurl = $baseurl;
   }

   //Before Proceeding any Further, Check for BASE HREF in URL
   $base_href = $html->find('base');

       //If Found Any BASE HREF, Make Changes Accordingly
       if (!empty($base_href[0]->href)) {

       //As BASE HREF Is Declared, All Relative Image Links Will Ammend It; Now Don't Discriminate exam.png or ../exam.png
       $baseurl = $base_href[0]->href;
       $dirurl =  $base_href[0]->href;

   }

 ////

 //If Image SRC is Relative, Make it Complete images/example.png =>>> http://www.ex.com/images/example.png

 for($i=0;$i<count($noAltImages['url']);$i++) {

  if (!preg_match($pattern, $noAltImages['url'][$i])) {

   //For "/example.png"
   if (preg_match("/"."^\/"."/",$noAltImages['url'][$i])) {
   $noAltImages['url'][$i] = $baseurl.$noAltImages['url'][$i];
   }

   //For "example.png"
   else {
   $noAltImages['url'][$i] = $dirurl."/".$noAltImages['url'][$i];
   }
  }
  
  }
  }
 //Close HTML Parser
 $html->clear();
 unset($html);
 }
 }
}
//Remove All Duplicate Images
//$noAltImages['url'] = array_unique($noAltImages['url']);


 //Only Return Images Larger Than Specific Width to Remove Icons (If Any) from Main Array
    for($i=0;$i<count($noAltImages['src']);$i++) {
    if(!empty($noAltImages['url'][$i])) {
    $image_size = @getimagesize($noAltImages['url'][$i]);
    if ($image_size[0] < 100 || empty($image_size[0])) {
    unset($noAltImages['url'][$i]);
    }
   }
  }


}

/*
Demo Time ;-)
get_noAltImages($link_array);
for ($i=0;$i<count($noAltImages['src']);$i++) {
if(!empty($noAltImages['url'][$i])) {
echo $noAltImages['url'][$i].'<br />'.$noAltImages['src'][$i].'<br /><br />';
 }
} 
*/

?>