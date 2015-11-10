<?php
//Function Simply Get Links And Return Em In XML Form
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

?>