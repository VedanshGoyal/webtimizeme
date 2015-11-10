<?php
//Scripts for External Broken Links Check
require_once('get_brokenLinks.php');
require_once('get_externalLinks.php');
require_once('checkURL.php');

//Get Password And URL From Script
$url1 = $_GET["url1"];
$url2 = $_GET["url2"];
$password = $_GET["password"];
if($password == "talhaparacha") {
get_externalLinks(array($url1,$url2));
get_brokenLinks($externalLinks);
echo count($brokenLinks['url']);
}
else {
exit();
}
?>