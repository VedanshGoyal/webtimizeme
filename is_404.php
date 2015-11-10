<?php
//Custom function for checking whether a URL returns 404 or not
function is_404($link) {
$headers = get_headers($link);
 if(strpos($headers[0],'404') !== FALSE or !isset($headers[0])) {
 return true;
 }
 else {
 return false;
 }
}

// Demo Time
/*
if(!is_404('http://www.facebook.com/')) {
echo "yes";
}
*/
?>