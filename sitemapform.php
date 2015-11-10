<?php
// Include the HTML Parser
include('get_internalLinks.php');
include('get_brokenLinks.php');

//Check if User Has Put Homepage URl
if (isset($_POST['url_home'])) {

 //Check if User Has Submitted URLs for Sitemap
 if (isset($_POST['url'])) {

 function get_sitemapXML($links) {

 //Set the Header First
 header('Content-type: text/xml');

 //Use SimpleXMLElement for Creating XML File
 $xml = new SimpleXMLElement('<xml/>');

 //Set URLSET Element
 $urlset_child = $xml->addChild('urlset');
 $urlset_child->addAttribute('xlmns','http://www.sitemaps.org/schemas/sitemap/0.9');

 foreach ($links as $link) {

 //Set URL Element
 $url_child = $xml->addChild('url');

 //Set Location Element under URL Element
 $url_child->addChild('loc',$link);
 }

 //Save & Return Sitemap XML
 $sitemapXML = $xml->asXML();

 return $sitemapXML;

 }

 echo get_sitemapXML($_POST['url']);

 } else {
 //Run Functions for Internal Links
 $url = $_POST['url_home'];
 get_internalLinks($url);

 //Check for Broken Links as It Also Removes the Broken Links From Given Array
 get_brokenLinks($internalLinks);
?>
<!DOCTYPE html>
<html>
<body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php
foreach ($internalLinks['url'] as $url) {
echo "<input type=\"checkbox\" name=\"url[]\" value=\"$url\" checked>$url<br />";
echo "<input type=\"hidden\" name=\"url_home\" value=\"".$_POST['url_home']."\">";
}
?>
  <input type="submit" value="Submit">
</form>

</body>
</html>
<?php
 }
} else {
?>
<!DOCTYPE html>
<html>
<body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="text" name="url_home">
<input type="submit" value="Submit">
</form>

</body>
</html>
<?php
}
?>