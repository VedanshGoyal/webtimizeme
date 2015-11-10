<?php
//Function for Sending a Custom Generated Email
require_once('PHPMailer/PHPMailerAutoload.php');


function send_email($attachement,$toEmail,$toName,$siteName) {

$mail = new PHPMailer();

$mail->AddAddress($toEmail,$toName);

$mail->AddAttachment($attachement, $attachement, 'base64', 'application/pdf');

$mail->Subject = "Download SEO Report for $siteName!";

$body = "Hi There...<br /><br />Website SEO Report for $siteName has been generated and is attached in this email. Hope it helps boost your website search engine rankings. Your Feedback is always welcomed. <br /> <br />Thanks, <br />Talha Paracha,<br />CEO Webtimize.Me.";
$mail->MsgHTML($body);

$mail->SetFrom('webtimizeme@gmail.com', 'Webtimize.Me');

$mail->Send(); 
}
/*
//Demo Time
send_email('pakservers.com.pdf','talhaparacha@outlook.com','Ali','pakservers.com');
*/
?>