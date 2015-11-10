<?php

//Function Checks for Broken Links from $links['url'] Array; Returns brokenLinks Array
function get_brokenLinks($links) {

  //Set the Main Array
  global $brokenLinks;
  $brokenLinks = array('url' => array(),'source' => array());

  //Loop through Each Link
  for ($i=0;$i<count($links['url']);$i++) {

  $headers = @get_headers($links['url'][$i]);
  $responce_header = @$headers[0];

  //Check for 404 Bad Link
  if(strpos($headers[0],'404') !== FALSE or !isset($headers[0])) {

  //Add the 404 Link to Array
  $brokenLinks['url'][] = $links['url'][$i];
  $brokenLinks['source'][] = $links['source'][$i];
  
  //$brokenLinks['text'][] = $links['text'][$i];

  //Remove the 404 Link from Main Array
  unset($links['url'][$i]);
  }
 }
}
/*
// DEMO time ;-)
get_brokenLinks($links);

echo "<h1>Broken Links</h1>";

for ($i=0;$i<count($brokenLinks['url']);$i++) {

echo $brokenLinks['url'][$i];

}
*/

?>