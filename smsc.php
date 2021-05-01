<?php

require_once __DIR__.'/functions/config.php';

XSS_secure();

if (!Users_Auth::do($PDO))
    http_response::return(401, ["description" => "User not found or login / password is incorrect"]);

$sms_handle = new SMS_data_handle($PDO);
$sms_handle->save();
// $sms_handle->send();


?>
