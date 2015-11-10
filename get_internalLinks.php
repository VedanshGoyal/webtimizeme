<?php
// Include the HTML Parser
require_once('simple_html_dom.php');
require_once('get_cleanURLs.php');

//Function Scraps All Internal Links from a URL
function get_internalLinks($url) {

//Parse URL into Components
$url_parsed = parse_url($url);


//Get Various Formatted URLs
$furl = $url_parsed['host'];
$baseurl = $url_parsed['scheme'].'://'.$url_parsed['host'];
$dirurl = dirname($url);
$dirurl_upto2 = dirname($dirurl);

//For Homepage URL like 'http://example.com/, dirname funtion outputs directory name as 'http' .. So Correct It !!!
if($dirurl == 'http:' || $dirurl == 'https:' || $dirurl_upto2 == 'http:' || $dirurl_upto2 == 'https:') {
$dirurl = $baseurl;
$dirurl_upto2 = $baseurl;
}


//Check Whether URL is Like 'http://example.com/example/' or 'http://example.com/example.html' ... Proceed Accordingly
if (!empty($url_parsed['path']) && strpos($url_parsed['path'],'.') === FALSE) {
$dirurl = $url;
}

//Set Pattern for Checking Whether a Link is Internal or Not
$pattern1 = '/^(http:\/\/|https:\/\/)(www.)?'.$furl.'/';

//Set Pattern for Checking Relative Links
$pattern2 = '/^'.'(http:\/\/|https:\/\/)'.'/';

//Create DOM from URL or file
/////////////VERSION 2////////////////////
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$curl_response_res = curl_exec ($ch);
curl_close ($ch);

$html= str_get_html($curl_response_res);

/////////////VERSION 2////////////////////

//Check If It Is An Object
if(is_object($html)) {

//Before Proceeding any Further, Check for BASE HREF in URL
$base_href = $html->find('base');


 //If Found Any BASE HREF, Make Changes Accordingly
 if (!empty($base_href[0]->href)) {

 //As BASE HREF Is Declared, All Relative Links Will Ammend It; Now Don't Discriminate example.com or ../example.com
//$baseurl = $base_href[0]->href;
 $dirurl =  $base_href[0]->href;
 $dirurl_upto2 = $base_href[0]->href;
 }



//Get the Links
$links = $html->find('a');

//Set the Main Array
global $internalLinks;
if (!is_array($internalLinks)) {
$internalLinks = array('url' => array(),'source' => array(),'text' => array());
}

//Loop Through Each Link
foreach ($links as $element) {
$linkHref = $element->href;

//Remove the White Spaces in URL i.e. ' example.com/ exam/' => 'example.com/%20exam/'
$linkHref = trim($linkHref);
$linkHref = str_replace(' ', '%20', $linkHref);

//Avoid Bad Links Also
 if (strpos($linkHref, '#') === FALSE && strpos($linkHref, 'sitemap.xml') === FALSE && empty($linkHref) === FALSE) {

  //Check for Complete Internal Link
  if (preg_match($pattern1, $linkHref)) {

    //Avoid Duplication
    if (!in_array($linkHref,$internalLinks['url'])) {
    $internalLinks['url'][] = $linkHref;
    $internalLinks['source'][] = $url;
    $internalLinks['text'][] = $linkHref;
   }
  }
  
  //Or Check for Relative Internal Link ... Avoid Bad Links like "mail:example@domain.com" etc.
    else if (!preg_match($pattern2, $linkHref) && strpos($linkHref, ':') === FALSE && strpos($linkHref, '//') === FALSE) {
    
    //Proceed ONLY For Links Like "../example.html"
     if(preg_match('/'.'^\.\.\/'.'/',$linkHref)) {
     
     //Avoid Duplication
      if (!in_array($dirurl_upto2."/".$linkHref,$internalLinks['url'])) {

      //Put Formated Link in Array
      $internalLinks['url'][] = $dirurl_upto2."/".$linkHref;
      $internalLinks['source'][] = $url;
      $internalLinks['text'][] = $linkHref;
     }
    }
    
    //OR Proceed For Links Like "/example.html" or "example.html"
     else {

       //For "/example.html"
       if (preg_match("/"."^\/"."/",$linkHref)) {
      
      //Avoid Duplication
      if (!in_array($baseurl.$linkHref,$internalLinks['url'])) {
      
      //Put Formated Link in Array
      $internalLinks['url'][] = $baseurl.$linkHref;
      $internalLinks['source'][] = $url;
      $internalLinks['text'][] = $linkHref;
     }
    }

     //For "example.html"
      else {

      //Avoid Duplication
      if (!in_array($dirurl."/".$linkHref,$internalLinks['url'])) {

      //Put Formated Link in Array
      $internalLinks['url'][] = $dirurl."/".$linkHref;
      $internalLinks['source'][] = $url;
      $internalLinks['text'][] = $linkHref;
      }
     }
    }
   }
  }
 }
}
}






/*
// Demo Time ... //

$homepage_url = 'http://web.mit.edu/newsoffice/2014/how-the-matthew-effect-helps-some-scientific-papers-gain-popularity-0127.html';
get_internalLinks($homepage_url);




$x = count($internalLinks['url']);
for ($i=0;$i<$x;$i++) {
  if(count($internalLinks['url']) < 100) {
  get_internalLinks($internalLinks['url'][$i]);
 }
}

$y = count($internalLinks['url']);
for ($i=$x;$i<$y;$i++) {
  if(count($internalLinks['url']) < 100) {
  get_internalLinks($internalLinks['url'][$i]);
 }
}



get_cleanURLs($internalLinks);

// Echo Em ... ;-)
echo "<h1>HomePage Internal Links Href</h1>";
for ($i=0;$i<count($cleanURLs['url']);$i++) {
echo $i."<br />"."  <b>HREF:</b>".$cleanURLs['url'][$i]."<br />".'  <b>Source:</b>'.$cleanURLs['source'][$i]."<br />".'   <b>Text:</b>'.$cleanURLs['text'][$i]."<br /><br /><br />";
}

include('get_externalLinks.php');
$links_array = $internalLinks['url'];


$x = count($internalLinks['url']);
for ($i=0;$i<count($internalLinks['url']);$i++) {
  if(count($internalLinks['url']) < 500) {
  get_internalLinks($internalLinks['url'][$i]);
 }
}

for ($i=0;$i<count($internalLinks['url']);$i++) {
  if(count($internalLinks['url']) < 500 && $i > $x) {
  get_internalLinks($internalLinks['url'][$i]);
 }
}





get_externalLinks($links_array);
// Echo Em ... ;-)
echo "<h1>External Links </h1>";
for ($i=0;$i<count($externalLinks['url']);$i++) {
echo $i."<br />"."  <b>HREF:</b>".$externalLinks['url'][$i]."<br />".'  <b>Source:</b>'.$externalLinks['url'][$i]."<br />"."<br /><br /><br />";
}

//Function Checks for Broken Links from $links['url'] Array; Returns brokenLinks Array
function get_brokenLinks($links) {

  //Set the Main Array
  global $brokenLinks;
  $brokenLinks = array('url' => array(),'source' => array());

  //Loop through Each Link
  for ($i=0;$i<count($links['url']);$i++) {

  $headers = get_headers($links['url'][$i]);
  $responce_header = $headers[0];

  if(strpos($responce_header,'404') ==! false) {
  $brokenLinks['url'][] = $links['url'][$i];
  $brokenLinks['source'][] = $links['source'][$i];
  }
 }
}

// DEMO time ;-)
get_brokenLinks($internalLinks);

echo "<h1>Broken Links</h1>";

for ($i=0;$i<count($brokenLinks['url']);$i++) {

echo $brokenLinks['url'][$i]."  <b>Link From</b> ".$brokenLinks['source'][$i]."<br />";

}


function get_more_internal_links($links, $x=FALSE, $y=0, $z=2) {

 while ($y < $z ) {
 get_more_internal_links($links,$x);
 $y++;
 }

  if ($x == FALSE) {
  global $x;
  $x = count($links);

  for ($i=0;$i<count($links);$i++) {
   get_internalLinks($links[$i]);
  }

 } else {

  for ($i=0;$i<count($links);$i++) {
   if ($i > $x) {
    get_internalLinks($links[$i]);

   }
  }

 }

}

*/
?>