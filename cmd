<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/settings.php");
import("/includes/class-autoload.inc.php");
import("functions.php");
import("settings.php");
define("direct_access", 1);
############################################################################
// function send_email($receiver, $subject = null, $body = null)
// {
//     try {
//         $mail = php_mailer(new PHPMailer());
//         $mail->setFrom(email, SITE_NAME . " SECRET LINK");
//         $mail->isHTML(true);
//         $mail->Subject = $subject;
//         $mail->Body = $body;
//         $mail->addAddress("$receiver", "$receiver");
//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         echo $e;
//         return false;
//     }
// }

// send_email($receiver="mail2pkarn@gmail.com", $subject = "Test mail", $body = "check mail");
exit;

// function send2(string $pmtid, string $trn, string $link, string $to = "263780995266")  {
//     $curl = curl_init();

//     $postdata =  array(
//       "from" => "Pay2Play",
//       "to" => "$to",
//       "message" => "Payment confirmed!! TR No. $trn.  you have only one chance to use this coupon link, so don't share with anyone: $link Good luck!",
//       "refId" => "INV{$pmtid}"
//     );
//     try {
//       curl_setopt_array($curl, array(

//         CURLOPT_URL => 'https://clicksmsgateway.com',

//         CURLOPT_RETURNTRANSFER => true,

//         CURLOPT_ENCODING => '',

//         CURLOPT_MAXREDIRS => 10,

//         CURLOPT_TIMEOUT => 0,

//         CURLOPT_FOLLOWLOCATION => true,

//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

//         CURLOPT_CUSTOMREQUEST => 'POST',

//         CURLOPT_POSTFIELDS => json_encode($postdata),

//         CURLOPT_HTTPHEADER => array(

//           'Accept: application/json',

//           'Content-Type: application/json',

//           'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI0OTYiLCJvaWQiOjQ5NiwidWlkIjoiYjUzNDEwODYtNmMzNy00NjQ5LWJjNzYtZDFkOTdiOWZlNWQ4IiwiYXBpZCI6Mjc3LCJpYXQiOjE2OTYyMzk0OTQsImV4cCI6MjAzNjIzOTQ5NH0.tLyDYskyHR2_ZhXkCR9WEQKGidSN_lqMYy8YNqLJnPJYG7qtmecEPWmeA7BLhAZ2ijcfF-hqPjnfykoVymab1A'

//         ),

//       ));
//       $response = curl_exec($curl);
//       curl_close($curl);
//       echo $response;
//       return true;
//     } catch (Exception $e) {
//         echo $e;
//       return false;
//     }


// }

// send2($pmtid="2676", $trn="jhkhh", $link="testing", $to = "263787218195");

function updateProgressBar($current, $total)
{
    $percent = ($current / $total) * 100;
    $barWidth = 50;
    $numBars = (int) ($percent / (100 / $barWidth));
    $progressBar = "[" . str_repeat("=", $numBars) . str_repeat(" ", $barWidth - $numBars) . "] $percent%";
    echo "\r$progressBar";
    // flush();
}


echo "\nTask complete!\n";
