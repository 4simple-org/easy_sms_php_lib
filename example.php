<?php

/**
 * Examples with info about how use sms_easy lib
 * to send SMS text messages.
*/

//Step #1 is import required code with API client class
include 'sms_easy.php';

// Step #2 is create an API client object using credential 
// obtained from https://easysms.4simple.org/user/panel/
$api_obj = new SMS_Easy(230431, "39ewcac5sssd248f9ijs81cf5231a");

//How send an SMS
$result = $api_obj->send_sms("18096943320", "Hello I am doing a test!");
echo "SEND SMS RESPONSE: " . print_r($result, true)  . "\n";

//How get the account balance
$balance = $api_obj->get_balance();
echo "Account balance: " . print_r($balance, true) . "\n";

// How check SMS processing status
$result = $api_obj->get_sms_status(5);
echo "SMS PROCESSING STATUS: " . print_r($result, true) . "\n";

?>
