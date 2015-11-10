<?php

//Function For Checking If A URL is Reachable
function checkURL($a_url) {
  $headers = @get_headers($a_url,1);
  $responce_header = @$headers[0];
  $responce_header_content_type = @$headers["Content-Type"];

  //Check for A Working HTML Link
  if(strpos($responce_header,'200') !== false && isset($responce_header) && strpos($responce_header_content_type,'html') !== false && isset($responce_header_content_type)) {
   return true;
   }
  else {
   return false;
  }
 }
?>