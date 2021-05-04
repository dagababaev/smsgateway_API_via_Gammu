<?php

require_once __DIR__.'/functions/config.php';

if ($_GET) $_GET = valid_request($_GET);
if ($_POST) $_POST = valid_request($_POST);

if (!Users_Auth::do($PDO))
    http_response::return(401, ["description" => "User not found or login / password is incorrect"]);

$sms_handle = new SMS_data_handle($PDO);
$sms_handle->save();
// $sms_handle->send();

function valid_request($arr) {

      $accepted_params = [
        "login",
        "pass",
        "tel",
        "msg",
        "flash",
        "replacemessages_id"
      ];

      foreach ($arr as $key => $value) {
        if (is_array($value)) http_response::return(400, ["description" => "Bad request"]);
        if (!in_array($key, $accepted_params, true)) unset($arr[$key]);
      }

      $filter = array("<", ">");
      $filter_replace = array("&lt;", "&qt;");

      $arr = str_replace($filter, $filter_replace, $arr);
      return $arr;

}


?>
