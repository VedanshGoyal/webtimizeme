<?php
//Function for Checking If GZIP Compression is Enabled on a Webpage
function is_compressed($a_url) {

   //Use cURL to Get Headers from a Webpage...
   $ch = curl_init();
   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_URL, $a_url);
   curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);

   //Set "User Agent" if Needs Be: curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11');


   //Must Include This Header in the Request, Otherwise You Can't Evaluate About Website Compression Status
   curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip'));

   //Only calling the head
   curl_setopt($ch, CURLOPT_HEADER, true); // Header will be in Output
   //curl_setopt($ch, CURLOPT_NOBODY, true); // Don't Get Page Body


   $headers = curl_exec($ch);

   curl_close ($ch);

  //Set the Global Variable
  global $isCompressed;

  //Check if "Content-Encoding: Gzip" Header is Present Which Means Compression is Enabled
  if(substr_count($headers,'Content-Encoding: gzip')) {
   $isCompressed = 1;
  }
  else {
   $isCompressed = 0;
  }


}
/*
//Demo Test
$url = 'http://www.winshosting.com/';
is_compressed($url);
if ($isCompressed){ echo "true"; } else {echo "false";}
     */
?>