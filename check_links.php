<!-- put this at the top of the page -->
<?php 
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime; 
;?> 


<?php
// Include the HTML Parser
include('simple_html_dom.php');

//This Function Puts All HomePage Internal Links into $internalLinks Array;
function get_internalLinks($url) {

//Parse URL into Components
$url_parsed = parse_url($url);

//Get Various Formatted URLs
$furl = $url_parsed['host'];
$baseurl = $url_parsed['scheme']."://".$url_parsed['host'];
$dirurl = dirname($url);
$dirurl_upto2 = dirname($dirurl);

//Set Pattern for Checking Whether a Link is Internal or Not
$pattern1 = "/^(http:\/\/|https:\/\/)(www.)?".$furl."/";
//Set Pattern for Checking Relative Links
$pattern2 = "/^"."(http:\/\/|https:\/\/)"."/";

//Start HTML Parser
$html = new simple_html_dom();

// Load URL
$html->load_file($url);

//Get the Links
$links = $html->find('a');

//Set the Main Array
global $internalLinks;
if (!is_array($internalLinks)) {
$internalLinks = array('home' => array());
}
//Loop Through Each Link
foreach ($links as $element) {
$linkHref = $element->href;
if (!in_array($linkHref,$internalLinks['home']) && !in_array($dirurl_upto2."/".$linkHref,$internalLinks['home']) && !in_array($baseurl.$linkHref,$internalLinks['home']) && !in_array($baseurl."/".$linkHref,$internalLinks['home'])) {
//Avoid Bad Links
 if (strpos($linkHref, '#') === FALSE && strpos($linkHref, 'sitemap.xml') === FALSE) {
  //Check for Complete Internal Link
  if (preg_match($pattern1, $linkHref)) {


    $internalLinks['home'][] = $linkHref;

  }
  //Or Check for Relative Internal Link ... Avoid Bad Links like "mail:example@domain.com" etc.
    else if (!preg_match($pattern2, $linkHref) && strpos($linkHref, ':') === FALSE && strpos($linkHref, '//') === FALSE) {
    //Proceed ONLY For Links Like "../example.html"
     if(preg_match("/"."^\.\.\/"."/",$linkHref)) {

      //Put Formated Link in Array
      $internalLinks['home'][] = $dirurl_upto2."/".$linkHref;

    }
    //OR Proceed For Links Like "/example.html" or "example.html"
     else {
       //For "/example.html"
       if (preg_match("/"."^\/"."/",$linkHref)) {

      //Put Formated Link in Array
      $internalLinks['home'][] = $baseurl.$linkHref;

    }
     //OR Proceed ONLY For Links Like "example.html"
      else {
      //For "example.html"
      //Avoid Duplication

      //Put Formated Link in Array
      $internalLinks['home'][] = $baseurl."/".$linkHref;

     }
    }
   }
  }
 }
}
}

// Demo Time ... //
$homepage_url = 'http://winshosting.com/';
$ac = parse_url($homepage_url);
echo $ac['host'];
get_internalLinks($homepage_url);
// Echo Em ... ;-)
echo "<h1>HomePage Internal Links Href</h1>";
for ($i=0;$i<count($internalLinks['home']);$i++) {
echo $i."  <b>HrEF:</b>".$internalLinks['home'][$i]."<br />";
}

echo "<h1>All Internal Links Href</h1>";
for ($i=0;$i<35;$i++) {
get_internalLinks($internalLinks['home'][$i]);
}
for ($i=0;$i<count($internalLinks['home']);$i++) {
echo $i."  <b>HrEF:</b>".$internalLinks['home'][$i]."<br />";
}

?>




<!-- put this code at the bottom of the page -->
<?php
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = ($endtime - $starttime);
   echo "This page was created in ".$totaltime." seconds";
;?>