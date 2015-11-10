<?php
set_time_limit(0);
ignore_user_abort(1);
error_reporting(E_ALL);

//Get The Variables
$homepage_url = $_GET['homepageurl'];
$email = $_GET['useremail'];
$name = $_GET['username'];

$parsedURL = parse_url($homepage_url);
$websiteName = $parsedURL['host'];

//Scripts For PDF Generation
require_once('fpdf.php');
require_once('fpdi.php');

//Scripts For Website SEO Analysis
require_once('get_internalLinks.php');
require_once('get_urlExpiryDate.php');
require_once('get_noAltImages.php');
require_once('get_URLstructure.php');
require_once('is_canonical.php');
require_once('get_brokenLinks.php');
require_once('get_externalLinks.php');
require_once('get_metaTags.php');
require_once('get_longTitleTags.php');
require_once('get_duplicateValues.php');
require_once('is_compressed.php');
require_once('is_googleAuthorship.php');
require_once('get_longDescriptionTags.php');
require_once('get_faultyKeywordTags.php');
require_once('is_404.php');

require_once('send_email.php');

//Constants For Page Numbers
define('noAltImages_pageNumber','4');
define('websiteExpiryDate_pageNumber','5');
define('urlStructure_pageNumber','6');
define('urlCanonicalization_pageNumber','7');
define('webpageTitle_pageNumber','8');
define('brokenLinks_pageNumber','9');
define('gzipCompression_pageNumber','10');
define('googleAuthorship_pageNumber','11');
define('metaTags_pageNumber','12');
define('sitemapXML_robotsTXT_pageNumber','13');
define('noAltImages_appendix_pageNumber','14');
define('length_appendix_pageNumber','15');
define('hyphen_appendix_pageNumber','16');
define('stopWords_appendix_pageNumber','17');
define('cleanURL_appendix_pageNumber','18');
define('urlCanonicalization_appendix_pageNumber','19');
define('internalBrokenLinks_appendix_pageNumber','20');
define('externalBrokenLinks_appendix_pageNumber','21');
define('webpageTitle_lengthError_appendix_pageNumber','22');
define('webpageTitle_duplicateError_appendix_pageNumber','23');
define('gzipCompression_appendix_pageNumber','24');
define('googleAuthorship_appendix_pageNumber','25');
define('metaTags_descriptionLengthError_appendix_pageNumber','26');
define('metaTags_keywordsLengthError_appendix_pageNumber','27');
define('metaTags_keywordsRedundancyError_appendix_pageNumber','28');

//Constants for Appendix Numbers
define('noAltImages_appendixNumber','1');
define('length_appendixNumber','2A');
define('cleanURL_appendixNumber','2D');
define('hyphen_appendixNumber','2B');
define('stopWords_appendixNumber','2C');
define('urlCanonicalization_appendixNumber','3');
define('internalBrokenLinks_appendixNumber','4A');
define('externalBrokenLinks_appendixNumber','4B');
define('webpageTitle_lengthError_appendixNumber','5A');
define('webpageTitle_duplicateError_appendixNumber','5B');
define('gzipCompression_appendixNumber','6');
define('googleAuthorship_appendixNumber','7');
define('metaTags_keywordsLengthError_appendixNumber','8B');
define('metaTags_descriptionLengthError_appendixNumber','8A');
define('metaTags_keywordsRedundancyError_appendixNumber','8C');

//Miscellenious Constants For SEO Report As Per PDF Structure
define('results_X','24');
define('results_Y','125');
define('actionsToTake_X','24');
define('actionsToTake_Y','200');
define('appendix_X','5');
define('appendix_Y','65');
define('margin','7');
define('margin_appendix','5');

//Set The Stage By Getting Internal Links From a Website
get_internalLinks($homepage_url);

//Get a Maximum of 100 Internal Links
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
  
//If www. is Present in Given URL, Append All Internal Links with www. Prefix
if(strpos($homepage_url,'www.') !== FALSE) {
get_cleanURLs($internalLinks,true);
} else {
get_cleanURLs($internalLinks);
}

$internalLinks = $cleanURLs;

$internalLinks_count = count($internalLinks['url']);



///////////////////////////////////////////////////////Start Generating PDF Report

$pdf = new FPDI();
$pdf->setSourceFile('Sample.pdf');

//Set Font and Text for Writing on PDF
  $pdf->SetFont('Helvetica', '', '14');
  $pdf->SetTextColor(0,0,0);


///////////////Import & Process Starting Page
  $pdf->AddPage();
  $page_start = $pdf->importPage(1);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_start, 0, 0, 0, 0, true);


///////////////Import & Process Pages for Table Of Contents
  $pdf->AddPage();
  $page_toc1 = $pdf->importPage(2);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_toc1, 0, 0, 0, 0, true);

$pdf->AddPage();
  $page_toc2 = $pdf->importPage(3);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_toc2, 0, 0, 0, 0, true);


///////////////Import & Process Page for "No Alt Images"
  //First Get All No Alt Images from Website URLs
  get_noAltImages($internalLinks['url']);
  $noAltImages_count = count($noAltImages['url']);

  $pdf->AddPage();
  $page_noAltImages = $pdf->importPage(noAltImages_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_noAltImages, 0, 0, 0, 0, true);

  //Write Results On The Page Accordingly
    //Define a number for the first result
    $resultNumber = 1;
    //Set position in pdf document
    $pdf->SetXY(results_X, results_Y + $resultNumber * margin);
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) $noAltImages_count image(s) found on your website without Alt Tags.");
  
//Add "Actions to Take" Headline if Needs Be
    if ($noAltImages_count > 0) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }
  
  //Add Priority Image & Actions to Take Accordingly
    //Define A Number For the First Recommended Action
    $actionNumber = 1;
    if ($noAltImages_count == 0) {
    //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
    $pdf->Image('blankscreen.png', 24, 185);
    $pdf->Image('none.png', 145, 22);

    } else if ($noAltImages_count < 15 && $noAltImages_count > 0) {
    //For Medium Priority
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Immediately add descriptive Alt Tags to your images (See Appendix ".noAltImages_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    $pdf->Image('medium.png', 145, 22);

    } else if ($noAltImages_count > 10) {
    //For Severe Priority
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Immediately add descriptive Alt Tags to your images (See Appendix ".noAltImages_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;

    $pdf->Image('severe.png', 145, 22);
    }

///////////////Import & Process Page for "Website Expiry Date"
  //Get the Expiry Date First
  get_urlExpiryDate($parsedURL['host']);

  $pdf->AddPage();
  $page_urlExpiryDate = $pdf->importPage(websiteExpiryDate_pageNumber);
  
  $urlExpiryDate[3] = $urlExpiryDate[3] - 2013;

  //Set Pointer to Page Size
  $pdf->useTemplate($page_urlExpiryDate, 0, 0, 0, 0, true);

  //Write Results On The Page Accordingly
    //Define a number for the first result
    $resultNumber = 1;

    //Set position in pdf document
    $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website domain name is expected to expire in $urlExpiryDate[2].");


   //Add "Actions to Take" Headline if Needs Be
    if ($urlExpiryDate[3] < 5) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

  //Add Priority Image & Actions To Take According to the SEO Guidelines
    //Define A Number For the First Recommended Action
    $actionNumber = 1;

    if ($urlExpiryDate[3] > 4) {
    //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
    $pdf->Image('blankscreen.png', 24, 185);

    $pdf->Image('none.png', 145, 22);

    } else if ($urlExpiryDate[3] < 5 && $urlExpiryDate[3] > 1) {

    //For Medium Priority
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Increase the age of your Website Domain Name by atleast 2 years.");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    $pdf->Image('medium.png', 145, 22);

    } else if ($urlExpiryDate[3] <= 1) {

    //For Severe Priority
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Increase the age of your Website Domain Name by atleast 4 years.");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;

    $pdf->Image('severe.png', 145, 22);
    }


///////////////Import & Process Page for "URL Structure"
  //First Analyze Website URLs
  get_urlStructure($internalLinks['url']);

  //Get The Page
  $pdf->AddPage();
  $page_urlStructure = $pdf->importPage(urlStructure_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_urlStructure, 0, 0, 0, 0, true);

  //Write Results According to Errors in URL Structure
    //Define a number for the first result
    $resultNumber = 1;

      //For Length Error

       //Set position in pdf document
       $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

       //Suppose there is no error
       $length_error = 0;

      $length_error_count = count($urlStructure['length_error']);
      if($length_error_count > 5) {
      //First parameter defines the line height
      $pdf->Write(0, "$resultNumber) $length_error_count URLs of your website have length greater than the recommended.");
      $resultNumber = $resultNumber + 1;
      //But There is an Error
      $length_error = 1;
      } else {
      $pdf->Write(0, "$resultNumber) All URLs of your website have optimal length.");
      $resultNumber = $resultNumber + 1;
      }

      //For Stop Words Error

       //Set position in pdf document
       $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

       //Suppose there is no error
       $stopWords_error = 0;

      $stopWords_error_count = count($urlStructure['stopWords_error']);
      if($stopWords_error_count > 5) {
      //First parameter defines the line height
      $pdf->Write(0, "$resultNumber) $stopWords_error_count URLs of your website contain unnecessary words(e.g. and, or, for).");
      $resultNumber = $resultNumber + 1;
      //But There is an Error
      $stopWords_error = 1;
      } else {
      $pdf->Write(0, "$resultNumber) No URL of your website contain unnecessary words.");
      $resultNumber = $resultNumber + 1;
      }

      //For Hyphen Error

       //Set position in pdf document
       $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

       //Suppose there is no error
       $hyphen_error = 0;

      $hyphen_error_count = count($urlStructure['noHyphen_error']);
      if($hyphen_error_count > 5) {
      //First parameter defines the line height
      $pdf->Write(0, "$resultNumber) $hyphen_error_count URLs of your website have keywords NOT seperated by hyphens.");
      $resultNumber = $resultNumber + 1;
      //But There is an Error
      $hyphen_error = 1;
      } else {
      $pdf->Write(0, "$resultNumber) All URLs of your website have keywords seperated by hyphens.");
      $resultNumber = $resultNumber + 1;
      }

      //For Clean URL Error
       //Set position in pdf document
       $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

       //Suppose there is no error
       $cleanURL_error = 0;

      $cleanURL_error_count = count($urlStructure['cleanURL_error']);
      if($cleanURL_error_count > 5) {
      //First parameter defines the line height
      $pdf->Write(0, "$resultNumber) Around $cleanURL_error_count URLs of your website contain query strings.");
      $resultNumber = $resultNumber + 1;
      //But There is an Error
      $cleanURL_error = 1;
      } else {
      $pdf->Write(0, "$resultNumber) No URL of your website contain query strings.");
      $resultNumber = $resultNumber + 1;
      }



   //Add "Actions to Take" Headline if Needs Be
    if ($length_error + $stopWords_error + $hyphen_error + $cleanURL_error > 0) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

   //Write Recommended Actions Accordingly
    //Define A Number For the First Recommended Action
    $actionNumber = 1;

    //Check whether an Error Exist and Write Recommened Actions Accordingly
    if ($length_error == 1) {
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Keep website URLs under 80 characters (See Appendix ".length_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    }

    if ($stopWords_error == 1) {
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Remove all the \"Stop Words\" from website URLs (See Appendix ".stopWords_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    }

    if ($hyphen_error == 1) {
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Use Hyphens for separating words in URLs (See Appendix ".hyphen_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    }

    if ($cleanURL_error == 1) {
    $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
    $pdf->Write(0, "$actionNumber) Remove query strings from your website URLs (See Appendix ".cleanURL_appendixNumber.").");
      //Increase Action Number After Each Action
      $actionNumber = $actionNumber + 1;
    }

   //Add Priority Images Accordingly
    if ($length_error + $hyphen_error + $cleanURL_error + $stopWords_error == 0) {
    //For None Priority
    $pdf->Image('none.png', 145, 22);
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);
    }
    else if ($length_error + $hyphen_error + $cleanURL_error + $stopWords_error <= 2) {
    //For Medium Priority
    $pdf->Image('medium.png', 145, 22);
    }
    else if ($length_error + $hyphen_error + $cleanURL_error + $stopWords_error >= 3) {
    //For Severe Priority
    $pdf->Image('severe.png', 145, 22);
    }


///////////////Import & Process Page for "URL Canonicalization"

  //Check for Canonicalization
  is_canonical($homepage_url);

  $pdf->AddPage();
  $page_urlCanonicalization = $pdf->importPage(urlCanonicalization_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_urlCanonicalization, 0, 0, 0, 0, true);

  //Write Results On The Page Accordingly
    //Define a number for the first result
    $resultNumber = 1;

    //Set position in pdf document
    $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

    //Check for Canonicalization and Write Results Accordingly
    if ($isCanonical == 1) {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website has NO problems with URL Canonicalization.");
    } else {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website DO has problems with URL Canonicalization.");
    }

    //Add "Actions to Take" Headline if Needs Be
    if ($isCanonical == 0) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

  //Write Actions to Take And Add Priority Image Accordingly
     //Define A Number For the First Recommended Action
     $actionNumber = 1;

     if ($isCanonical == 1) {
     //For None Priority
     $pdf->Image('none.png', 145, 22);
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);

     } else {
     //For Severe Priority
     $pdf->Image('severe.png', 145, 22);
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "$actionNumber) Immediately use URL Canonicalization on each of your webpages");
     $actionNumber = $actionNumber + 1;
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "(See Appendix ".urlCanonicalization_appendixNumber." for information about specifying a Canonical URL).");
     }


///////////////Import & Process Page for "Webpage Title Tags"
  //First Get Meta Tags
  get_urlInfo(array_unique($internalLinks['url']));
  //Now Get Links with Long Title Tags
  get_longTitleTags($urlInfo);
  $longTitleTags_count = count($longTitleTags['title']);
  //Now Get Duplicate Title Tags By Using get_duplicateValues.php
  get_duplicateValues($urlInfo['title']);
   //Don't Give Just Duplicate Title Tags  ... Also Append Source URLs
   for($j=0;$j<count($duplicateValues['value']);$j++){
    for($i=0;$i<count($urlInfo['title']);$i++) {
     if ($duplicateValues['value'][$j] == $urlInfo['title'][$i]) {
     $duplicateValues['src'][$j] .= "|".$urlInfo['src'][$i];
    }
   }
  }
  $duplicateTitleTags_count = count($duplicateValues['value']);

  //Set PDF Page
  $pdf->AddPage();
  $page_webpageTitle = $pdf->importPage(webpageTitle_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_webpageTitle, 0, 0, 0, 0, true);

  //Write Results Accordingly
   //Define a number for the first result
   $resultNumber = 1;

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Long Title Tags
   if($longTitleTags_count > 0) {
   $pdf->Write(0, "$resultNumber) $longTitleTags_count page(s) found on your website with Long Title tags.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No page is found on your website with Long Title tags.");
   $resultNumber = $resultNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Duplicate Title Tags
   if($duplicateTitleTags_count > 0) {
   $pdf->Write(0, "$resultNumber) $duplicateTitleTags_count page(s) found on your website with Duplicate Title tags.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No page is found on your website with Duplicate Title tags.");
   $resultNumber = $resultNumber + 1;
   }

  //Add "Actions to Take" Headline if Needs Be
  if ($duplicateTitleTags_count + $longTitleTags_count > 0) {
   $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
   //Also Define Actions To Take X & Y Axix for Further Use
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
  } else {
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
  }



  //Write Actions to Take Accordingly
   //Define A Number For the First Recommended Action
    $actionNumber = 1;

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Long Title Tags
   if($longTitleTags_count > 0) {
   $pdf->Write(0, "$actionNumber) Modify Lengthy Title Tags from your website pages (See Appendix ".webpageTitle_lengthError_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Duplicate Title Tags
   if($duplicateTitleTags_count > 0) {
   $pdf->Write(0, "$actionNumber) Give each webpage a unique Title Tag (See Appendix ".webpageTitle_duplicateError_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

  //Add Priority Images Accordingly
    if ($duplicateTitleTags_count == 0 && $longTitleTags_count == 0) {
    //For None Priority
    $pdf->Image('none.png', 145, 22);
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);
    }
    else if ( ($duplicateTitleTags_count > 0 && $longTitleTags_count == 0) or ($duplicateTitleTags_count == 0 && $longTitleTags_count > 0)) {
    //For Medium Priority
    $pdf->Image('medium.png', 145, 22);
    }
    else if ($duplicateTitleTags_count > 0 && $longTitleTags_count > 0) {
    //For Severe Priority
    $pdf->Image('severe.png', 145, 22);
    }

///////////////Import & Process Page for Broken Links

 //First Get All External & Internal Broken Links
  $internalLinks_copy = $internalLinks;
  get_brokenLinks($internalLinks_copy);
  $internalBrokenLinks = $brokenLinks;

  //Search For External Links From All The Internal Links on the Website
    get_externalLinks($internalLinks['url']);
    $externalLinks_count = count($externalLinks['url']);
    $externalLinks_copy = $externalLinks;
    get_brokenLinks($externalLinks_copy);
    $externalBrokenLinks = $brokenLinks;

  $internalBrokenLinks_count = count($internalBrokenLinks['url']);
  $externalBrokenLinks_count = count($externalBrokenLinks['url']);
  //$externalBrokenLinks_count = 0;

  //Set PDF Page
  $pdf->AddPage();
  $page_brokenLinks = $pdf->importPage(brokenLinks_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_brokenLinks, 0, 0, 0, 0, true);

  //Write Results Accordingly
   //Define a number for the first result
   $resultNumber = 1;

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Internal Broken Links
   if($internalBrokenLinks_count > 0) {
   $pdf->Write(0, "$resultNumber) $internalBrokenLinks_count Internal Broken link(s) found on your website.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No Internal Broken links are found on your website.");
   $resultNumber = $resultNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for External Broken Links
   if($externalBrokenLinks_count > 0) {
   $pdf->Write(0, "$resultNumber) $externalBrokenLinks_count External Broken link(s) found on your website.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No External Broken links are found on your website.");
   $resultNumber = $resultNumber + 1;
   }
   
   //Add "Actions to Take" Headline if Needs Be
    if ($internalBrokenLinks_count + $externalBrokenLinks_count > 0) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

   //Write Actions to Take Accordingly
   //Define A Number For the First Recommended Action
    $actionNumber = 1;

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Internal Broken Links
   if($internalBrokenLinks_count > 0) {
   $pdf->Write(0, "$actionNumber) Unlink Internal Broken link(s) from your website (See Appendix".internalBrokenLinks_appendixNumber.").");
   $actionNumber = $actionNumber + 1;

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
   //Write an Extra Action for Internal Broken Links
   $pdf->Write(0, "$actionNumber) Redirect Error pages on your website to the homepage (See Appendix".internalBrokenLinks_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Internal Broken Links
   if($externalBrokenLinks_count > 0) {
   $pdf->Write(0, "$actionNumber) Unlink External Broken link(s) from your website (See Appendix".externalBrokenLinks_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

   //Add Priority Image Accordingly
    //For None Priority
    if($externalBrokenLinks_count + $internalBrokenLinks_count == 0) {
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);
     $pdf->Image('none.png', 145, 22);
    }

    //For Medium Priority
    else if($externalBrokenLinks_count + $internalBrokenLinks_count < 5) {
     $pdf->Image('medium.png', 145, 22);
    }

    //For Severe Priority
    else if($externalBrokenLinks_count + $internalBrokenLinks_count > 5) {
     $pdf->Image('severe.png', 145, 22);
    }

///////////////Import & Process Page for "GZIP Compression"
  //Check for Compression
  is_compressed($homepage_url);

  $pdf->AddPage();
  $page_gzipCompression = $pdf->importPage(gzipCompression_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_gzipCompression, 0, 0, 0, 0, true);

  //Write Results On The Page Accordingly
    //Define a number for the first result
    $resultNumber = 1;

    //Set position in pdf document
    $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

    //Check for Canonicalization and Write Results Accordingly
    if ($isCompressed == 1) {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website is serving compressed pages to the browser.");
    } else {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website is NOT serving compressed pages to the browser.");
    }

    //Add "Actions to Take" Headline if Needs Be
    if ($isCompressed == 0) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

  //Write Actions to Take And Add Priority Image Accordingly
     //Define A Number For the First Recommended Action
     $actionNumber = 1;

     if ($isCompressed == 1) {
     //For None Priority
     $pdf->Image('none.png', 145, 22);

     } else {
     //For Severe Priority
     $pdf->Image('severe.png', 145, 22);
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "$actionNumber) Immediately use GZIP Compression technique on your website pages.");
     $actionNumber = $actionNumber + 1;
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "(See Appendix ".gzipCompression_appendixNumber." for information about implementing GZIP Compression).");
     }

///////////////Import & Process Page for "Google Authorship"
  //Check for Google Authorship
  is_googleAuthorship($internalLinks['url']);

  $pdf->AddPage();
  $page_googleAuthorship = $pdf->importPage(googleAuthorship_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_googleAuthorship, 0, 0, 0, 0, true);

  //Write Results On The Page Accordingly
    //Define a number for the first result
    $resultNumber = 1;

    //Set position in pdf document
    $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

    //Check for Canonicalization and Write Results Accordingly
    if ($googleAuthorship == true) {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website is utilizing Google+ Profile Authorship.");
    } else {
    //First parameter defines the line height
    $pdf->Write(0, "$resultNumber) Your website is NOT utilizing Google+ Profile Authorship.");
    }

    //Add "Actions to Take" Headline if Needs Be
    if ($googleAuthorship == false) {
     $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
     //Also Define Actions To Take X & Y Axix for Further Use
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
     } else {
     $actionsToTake_X = results_X;
     $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
    }

  //Write Actions to Take And Add Priority Image Accordingly
     //Define A Number For the First Recommended Action
     $actionNumber = 1;

     if ($googleAuthorship == true) {
     //For None Priority
     $pdf->Image('none.png', 145, 22);

     } else {
     //For Severe Priority
     $pdf->Image('severe.png', 145, 22);
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "$actionNumber) Immediately use Google Authorship technique on your website pages.");
     $actionNumber = $actionNumber + 1;
     $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);
     $pdf->Write(0, "(See Appendix ".googleAuthorship_appendixNumber." for information about implementing Google Authorship).");
     }

///////////////Import & Process Page for "Meta Tags"
  //First Get Meta Tags
  get_urlInfo($internalLinks['url']);

     //Now Get Links with Long Description Tags
     get_longDescriptionTags($urlInfo);
     $longDescriptionTags_count = count($longDescriptionTags['description']);

     //Now Get Links with Long Keywords Tags
     get_faultyKeywordTags($urlInfo);
     $longKeywordTags_count = count($longKeywordTags['keywords']);

     //Now Get Links with Redundant Keywords Tags
     $redundantKeywordTags_count = count($redundantKeywordTags['redundantKeywords']); //Redundant Keywords are Already Processed in the Above Function

  //Set PDF Page
  $pdf->AddPage();
  $page_metaTags = $pdf->importPage(metaTags_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_metaTags, 0, 0, 0, 0, true);

  //Write Results Accordingly
   //Define a number for the first result
   $resultNumber = 1;

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Long Description Meta Tags
   if($longDescriptionTags_count > 0) {
   $pdf->Write(0, "$resultNumber) Your website has $longDescriptionTags_count page(s) with Long Description Meta tags.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No page is found on your website with Long Description Meta tag.");
   $resultNumber = $resultNumber + 1;
   }
   
   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Long Keywords Meta Tags
   if($longKeywordTags_count > 0) {
   $pdf->Write(0, "$resultNumber) Your website has $longKeywordTags_count page(s) with Meta Keywords more than 10.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No page is found on your website with Keywords more than 10.");
   $resultNumber = $resultNumber + 1;
   }
   
   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);
   
   //Write for Redundant Keywords Meta Tags
   if($redundantKeywordTags_count > 0) {
   $pdf->Write(0, "$resultNumber) Your website has $redundantKeywordTags_count page(s) with Keywords Stuffing issues.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No page is found on your website with Keywords Stuffing issue.");
   $resultNumber = $resultNumber + 1;
   }


  //Add "Actions to Take" Headline if Needs Be
  if ($longDescriptionTags_count + $redundantKeywordTags_count + $longKeywordTags_count > 0) {
   $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
   //Also Define Actions To Take X & Y Axix for Further Use
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
  } else {
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
  }


  //Write Actions to Take Accordingly
   //Define A Number For the First Recommended Action
    $actionNumber = 1;

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Long Description Meta Tags
   if($longDescriptionTags_count > 0) {
   $pdf->Write(0, "$actionNumber) Edit Lengthy Description Tags from your website pages (See Appendix ".metaTags_descriptionLengthError_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Long Keywords Meta Tags
   if($longKeywordTags_count > 0) {
   $pdf->Write(0, "$actionNumber) Edit Lengthy Keyword Tags from your website pages (See Appendix ".metaTags_keywordsLengthError_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }
   
   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Redundant Keywords Meta Tags
   if($redundantKeywordTags_count > 0) {
   $pdf->Write(0, "$actionNumber) Modify Stuffed Keyword Tags from your website pages (See Appendix ".metaTags_keywordsRedundancyError_appendixNumber.").");
   $actionNumber = $actionNumber + 1;
   }

  //Add Priority Images Accordingly
    if ($longDescriptionTags_count == 0 && $longKeywordTags_count == 0 && $redundantKeywordTags_count == 0) {
    //For None Priority
    $pdf->Image('none.png', 145, 22);
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);
    }
    else if ($longDescriptionTags_count > 0 && $longKeywordTags_count > 0 && $redundantKeywordTags_count > 0) {
    //For Severe Priority
    $pdf->Image('severe.png', 145, 22);
    }
    else {
    //For Medium Priority
    $pdf->Image('medium.png', 145, 22);
    }

///////////////Import & Process Page for "Sitemap.XML + Robots.TXT"
  
  //If  Sitemap.XML file is Present in a Website
  if(!is_404($homepage_url.'/sitemap.xml')) {
  $is_sitemapXML =  1;
  } else {
  $is_sitemapXML =  0;
  }

  //If  Robots.TXT file is Present in a Website
  if(!is_404($homepage_url.'/robots.txt')) {
  $is_robotsTXT =  1;
  } else {
  $is_robotsTXT =  0;
  }

  //Set PDF Page
  $pdf->AddPage();
  $page_sitemapXML_robotsTXT = $pdf->importPage(sitemapXML_robotsTXT_pageNumber);

  //Set Pointer to Page Size
  $pdf->useTemplate($page_sitemapXML_robotsTXT, 0, 0, 0, 0, true);

  //Write Results Accordingly
   //Define a number for the first result
   $resultNumber = 1;

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Sitemap.XML
   if($is_sitemapXML == 1) {
   $pdf->Write(0, "$resultNumber) A Sitemap.XML file is present in your website.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No Sitemap.XML file is present in your website.");
   $resultNumber = $resultNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY(results_X, results_Y + $resultNumber * margin);

   //Write for Robots.TXT
   if($is_robotsTXT == 1) {
   $pdf->Write(0, "$resultNumber) A Robots.TXT file is present in your website.");
   $resultNumber = $resultNumber + 1;
   } else {
   $pdf->Write(0, "$resultNumber) No Robots.XML file is present in your website.");
   $resultNumber = $resultNumber + 1;
   }

  //Add "Actions to Take" Headline if Needs Be
  if ($is_sitemapXML == 0 or $is_robotsTXT  == 0) {
   $pdf->Image('actions.png', results_X, (results_Y + $resultNumber * margin) + 3);
   //Also Define Actions To Take X & Y Axix for Further Use
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 14;
  } else {
   $actionsToTake_X = results_X;
   $actionsToTake_Y = (results_Y + $resultNumber * margin) + 11;
  }


  //Write Actions to Take Accordingly
   //Define A Number For the First Recommended Action
    $actionNumber = 1;

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Long Description Meta Tags
   if($is_sitemapXML == 0) {
   $pdf->Write(0, "$actionNumber) Use our Sitemap Generator Tool for your website.");
   $actionNumber = $actionNumber + 1;
   }

   //Set position in pdf document
   $pdf->SetXY($actionsToTake_X, $actionsToTake_Y + $actionNumber * margin);

   //Write for Long Keywords Meta Tags
   if($is_robotsTXT == 0) {
   $pdf->Write(0, "$actionNumber) Make a Robots.TXT website for your website");
   $actionNumber = $actionNumber + 1;
   }

  //Add Priority Images Accordingly
    if ($is_robotsTXT + $is_sitemapXML == 2) {
    //For None Priority
    $pdf->Image('none.png', 145, 22);
     //For None Priority Remove The "Actions to Take" Headline By Overriding it with a Blank Image
     $pdf->Image('blankscreen.png', 24, 185);
    }
    else if ($is_robotsTXT + $is_sitemapXML == 1) {
    //For Medium Priority
    $pdf->Image('medium.png', 145, 22);
    }
    else {
    //For Severe Priority
    $pdf->Image('severe.png', 145, 22);
    }

/////////Add "No Alt Images" References to Appendix Page                                                                                 
   $pdf->SetFont('Helvetica', '', '8');
   $pdf->SetTextColor(0,0,0);

    $pdf->AddPage();
    $page_noAltImages_appendix = $pdf->importPage(noAltImages_appendix_pageNumber);
    $pdf->useTemplate($page_noAltImages_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<count($noAltImages['src']);$i++) {
     if(!empty($noAltImages['url'][$i])) {
      if($refNumber < 20) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) IMAGE: ".$noAltImages['url'][$i]);

       $appendixLineNumber = $appendixLineNumber + 1;

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "    SOURCE: ".$noAltImages['src'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
    }
   }



/////////Add "URL Structure" References to Appendix Page
  $pdf->SetFont('Helvetica', '', '8');
  $pdf->SetTextColor(0,0,0);

    ////For Length Error Appendix
    $pdf->AddPage();
    $page_length_appendix = $pdf->importPage(length_appendix_pageNumber);
    $pdf->useTemplate($page_length_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    if($length_error == 1) {
     foreach ($urlStructure['length_error'] as $abc) {
      //No More Than 40 References
      if($refNumber < 40) {

      $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
      $pdf->Write(0, "$refNumber) $abc");

      //Increase Miscellaneous Constants After Each Successful Reference
      $refNumber = $refNumber + 1;
      $appendixLineNumber = $appendixLineNumber + 1;
      }
     }
    }

    ////For Hyphen Error Appendix
    $pdf->AddPage();
    $page_hyphen_appendix = $pdf->importPage(hyphen_appendix_pageNumber);
    $pdf->useTemplate($page_hyphen_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    if($hyphen_error == 1) {
     foreach ($urlStructure['noHyphen_error'] as $abc) {
      //No More Than 40 References
      if($refNumber < 40) {

      $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
      $pdf->Write(0, "$refNumber) $abc");

      //Increase Miscellaneous Constants After Each Successful Reference
      $refNumber = $refNumber + 1;
      $appendixLineNumber = $appendixLineNumber + 1;
      }
     }
    }

    ////For Stop Words Error Appendix
    $pdf->AddPage();
    $page_stopWords_appendix = $pdf->importPage(stopWords_appendix_pageNumber);
    $pdf->useTemplate($page_stopWords_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    if($stopWords_error == 1) {
     foreach ($urlStructure['stopWords_error'] as $abc) {
      //No More Than 40 References
      if($refNumber < 40) {

      $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
      $pdf->Write(0, "$refNumber) $abc");

      //Increase Miscellaneous Constants After Each Successful Reference
      $refNumber = $refNumber + 1;
      $appendixLineNumber = $appendixLineNumber + 1;
      }
     }
    }

    ////For Clean URL Error Appendix
    $pdf->AddPage();
    $page_cleanURL_appendix = $pdf->importPage(cleanURL_appendix_pageNumber);
    $pdf->useTemplate($page_cleanURL_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    if($cleanURL_error == 1) {
     foreach ($urlStructure['cleanURL_error'] as $abc) {
      //No More Than 40 References
      if($refNumber < 40) {

      $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
      $pdf->Write(0, "$refNumber) $abc");

      //Increase Miscellaneous Constants After Each Successful Reference
      $refNumber = $refNumber + 1;
      $appendixLineNumber = $appendixLineNumber + 1;
      }
     }
    }



/////////Add URL Canonicalization Appendix Page
    $pdf->AddPage();
    $page_urlCanonicalization_appendix = $pdf->importPage(urlCanonicalization_appendix_pageNumber);
    $pdf->useTemplate($page_urlCanonicalization_appendix, 0, 0, 0, 0, true);



$pdf->SetFont('Helvetica', '', '8');
$pdf->SetTextColor(0,0,0);


/////////Add "Internal Broken Links" References to Appendix Page
    $pdf->AddPage();
    $page_internalBrokenLinks_appendix = $pdf->importPage(internalBrokenLinks_appendix_pageNumber);
    $pdf->useTemplate($page_internalBrokenLinks_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$internalBrokenLinks_count;$i++) {
      if($refNumber < 20) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) URL: ".$internalBrokenLinks['url'][$i]);

       $appendixLineNumber = $appendixLineNumber + 1;

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "    SOURCE: ".$internalBrokenLinks['source'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

/////////Add "External Broken Links" References to Appendix Page
    $pdf->AddPage();
    $page_externalBrokenLinks_appendix = $pdf->importPage(externalBrokenLinks_appendix_pageNumber);
    $pdf->useTemplate($page_externalBrokenLinks_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$externalBrokenLinks_count;$i++) {
      if($refNumber < 20) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) URL: ".$externalBrokenLinks['url'][$i]);

       $appendixLineNumber = $appendixLineNumber + 1;

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "    SOURCE: ".$externalBrokenLinks['source'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

$pdf->SetFont('Helvetica', '', '8');
$pdf->SetTextColor(0,0,0);

/////////Add "Lengthy Title Tags" References to Appendix Page
    $pdf->AddPage();
    $page_webpageTitle_lengthError_appendix = $pdf->importPage(webpageTitle_lengthError_appendix_pageNumber);
    $pdf->useTemplate($page_webpageTitle_lengthError_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$longTitleTags_count;$i++) {
      if($refNumber < 20) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) TITLE: ".$longTitleTags['title'][$i]);

       $appendixLineNumber = $appendixLineNumber + 1;

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "    URL: ".$longTitleTags['src'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

/////////Add "Duplicate Title Tags" References to Appendix Page
    $pdf->AddPage();
    $page_webpageTitle_duplicateError_appendix = $pdf->importPage(webpageTitle_duplicateError_appendix_pageNumber);
    $pdf->useTemplate($page_webpageTitle_duplicateError_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$duplicateTitleTags_count;$i++) {
      if($refNumber < 10) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) TITLE: ".$duplicateValues['value'][$i]);
       $appendixLineNumber = $appendixLineNumber + 1;

       //Now Write URLs Which Have That Duplicate Title
         //Get URLs with Duplicate Title Tag from This String: URL1|URL2|URL3 => to an array: URL1, URL2, URL3
         $URLarray = explode("|",$duplicateValues['src'][$i]);

       for($k=0;$k<count($URLarray);$k++) {
        //Only Write a Maximum of 3 URLs
        if($k < 4 && $k > 0) {

         $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
         $pdf->Write(0, "    URL$k: ".$URLarray[$k]);
         $appendixLineNumber = $appendixLineNumber + 1;
        }
       }
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

/////////Add GZIP Compression Appendix Page
    $pdf->AddPage();
    $page_gzipCompression_appendix = $pdf->importPage(gzipCompression_appendix_pageNumber);
    $pdf->useTemplate($page_gzipCompression_appendix, 0, 0, 0, 0, true);

/////////Add Google Authorship Appendix Page
    $pdf->AddPage();
    $page_googleAuthorship_appendix = $pdf->importPage(googleAuthorship_appendix_pageNumber);
    $pdf->useTemplate($page_googleAuthorship_appendix, 0, 0, 0, 0, true);


/////////Add "Lengthy Description Meta Tags" References to Appendix Page
    $pdf->AddPage();
    $page_metaTags_descriptionLengthError_appendix = $pdf->importPage(metaTags_descriptionLengthError_appendix_pageNumber);
    $pdf->useTemplate($page_metaTags_descriptionLengthError_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$longDescriptionTags_count;$i++) {
      if($refNumber < 20) {

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "URL: ".$longDescriptionTags['src'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

/////////Add "Lengthy Keywords Meta Tags" References to Appendix Page
    $pdf->AddPage();
    $page_metaTags_keywordsLengthError_appendix = $pdf->importPage(metaTags_keywordsLengthError_appendix_pageNumber);
    $pdf->useTemplate($page_metaTags_keywordsLengthError_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$longKeywordTags_count;$i++) {
      if($refNumber < 20) {

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "URL: ".$longKeywordTags['src'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

/////////Add "Redundant Keyword Meta Tags" References to Appendix Page
    $pdf->AddPage();
    $page_metaTags_keywordsRedundancyError_appendix = $pdf->importPage(metaTags_keywordsRedundancyError_appendix_pageNumber);
    $pdf->useTemplate($page_metaTags_keywordsRedundancyError_appendix, 0, 0, 0, 0, true);

    //Set a miscellinious number for each line in Appendix Page
    $appendixLineNumber = 1;

    //Set a miscellinious number for first reference in Appendix Page
    $refNumber = 1;

    for ($i=0;$i<$redundantKeywordTags_count;$i++) {
      if($refNumber < 20) {
       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "$refNumber) WORDS: ".$redundantKeywordTags['redundantKeywords'][$i]);

       $appendixLineNumber = $appendixLineNumber + 1;

       $pdf->SetXY(appendix_X, appendix_Y + $appendixLineNumber * margin_appendix);
       $pdf->Write(0, "       URL: ".$redundantKeywordTags['src'][$i]);

      $appendixLineNumber = $appendixLineNumber + 1;
      }
      //Also Increase Reference Number by 1 after each Successful Reference in PDF
      $refNumber = $refNumber + 1;
   }

////////////////////////////////////////////Save the Output PDF at Server
$pdf->Output("$websiteName.pdf", 'F');

//Send the Email with Attachment
send_email("$websiteName.pdf",$email,$name,$websiteName);
?>