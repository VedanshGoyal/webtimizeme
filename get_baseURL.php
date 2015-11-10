<?
// Include the HTML Parser
include('simple_html_dom.php');
function get_baseURL($url) {

//Start HTML Parser
$html = new simple_html_dom();

// Load URL
$html->load_file($url);

//Get the Links
$base_href = $html->find('base');

//Close HTML Parser
 $html->clear();
 unset($html);

}
/*
$parse_url = parse_url('http://www.webconfs.com/seo-tutorial/example.html');
if (strpos($parse_url['path'],'.')) {
  echo 'True';
}
  */
?>