<?php
require_once('get_internalLinks.php');
require_once('get_metaTags.php');


//Function Returns Duplicate Values from An Array
function get_duplicateValues($anArray){

//Set the Environment
global $duplicateValues;
$duplicateValues = array('value' => array(),'repitition' => array(),'src' => array());

//Count All Values from An Array; Loop Each to Check for Duplication
foreach (array_count_values($anArray) as $key => $value) {
 if ($value > 1 && !empty($key)) {
  //Push Faulted Value in an Array to Return
  $duplicateValues['value'][] = $key;
  $duplicateValues['repitition'][] = $value;
  }
 }


}

///// Demo Time ;-)

/*
get_internalLinks('http://www.redmondpie.com');
$urlarray = array_unique($internalLinks['url']);
get_urlInfo($urlarray);
get_duplicateValues($urlInfo['title']);

//Don't Give Just Duplicate Title Tags  ... Also Append Source URLs
for($j=0;$j<count($duplicateValues['value']);$j++){

 for($i=0;$i<count($urlInfo['title']);$i++) {

  if ($duplicateValues['value'][$j] == $urlInfo['title'][$i]) {
  $duplicateValues['src'][$j] .= "|".$urlInfo['src'][$i];

  }
 }
}

for ($i=0;$i<count($duplicateValues['value']);$i++) {
echo $duplicateValues['value'][$i].'  '.$duplicateValues['repitition'][$i].'  '.$duplicateValues['src'][$i].'<br />';
}
echo 'it worked !!! ';
*/
?>