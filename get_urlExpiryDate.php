<?php
//Function Needs a URL & Gets its Expiry Date
function get_urlExpiryDate($url) {

global $urlExpiryDate;

//Get URL in Correct Format According to WhoIS Info Provider: WHOMSY API
///$url = preg_replace('/^(http:\/\/|https:\/\/)(www.)?/', '', $url);
  //Get Only the Domain Name from URL
  $url = trim($url);
  $url = preg_replace("/^(http:\/\/)*(www.)*/is", "", $url); 
  $url = preg_replace("/\/.*$/is" , "" ,$url);


//////////////////////WHOMSY API SERVICE IS CLOSED//////////////////////////////
/*
//Append URL into WHOMSY API URL
$urlWhoIS = 'http://whomsy.com/api/'.$url;

//Get URL WhoIS Info & Proceed Accordingly
if ($urlWhoIS = file_get_contents($urlWhoIS)) {
 $pattern = '/Expiration Date: ([[:digit:]]+\-[a-z]+\-([[:digit:]]+))/';
 preg_match($pattern, $urlWhoIS, $matches);

 global $urlExpiryDate;
 $urlExpiryDate = $matches;
 }
}
*/
/////////////USE WHO.IS INSTEAD/////////////////////
//Append URL into WHOMSY API URL
$urlWhoIS = 'http://who.is/whois/'.$url;

//Get URL WhoIS Info & Proceed Accordingly
if ($urlWhoIS = file_get_contents($urlWhoIS)) {
 $pattern = '/(Expiration|Expiry) Date: ([[:digit:]]+\-[[:digit:]]*[a-zA-z]*\-([[:digit:]]+))/';
 preg_match($pattern, $urlWhoIS, $matches);

 $urlExpiryDate = $matches;
 
//Use 2nd method if required
 if(empty($urlExpiryDate[0])) {
 
 $pattern = '/<span data-bind-domain="expiration_date" style="visibility: visible;">((.+))</';
 preg_match($pattern, $urlWhoIS, $matches2);
 
 $urlExpiryDate = $matches2; 
   } 
 
 }
//Get Expiry Year Seperately  
preg_match("/[0-9][0-9][0-9][0-9]/", $urlExpiryDate[2], $matchForYear);  
$urlExpiryDate[3] = $matchForYear[0];  
}
/*
//Demo
get_urlExpiryDate('http://mitadmissions.org');
echo $urlExpiryDate[2];
*/
?>