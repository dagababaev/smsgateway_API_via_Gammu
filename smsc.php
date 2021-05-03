<?php

require_once __DIR__.'/functions/config.php';

XSS_secure();

if (!Users_Auth::do($PDO))
    http_response::return(401, ["description" => "User not found or login / password is incorrect"]);

$sms_handle = new SMS_data_handle($PDO);
$sms_handle->save();
// $sms_handle->send();

function XSS_secure() {

	function replace($arr) {
		$filter = array("<", ">");
		$filter_replace = array("&lt;", "&gt;");

		for ($i=0; $i < count($filter) ; $i++) {
			$str = str_replace($filter[$i], $filter_replace[$i], $arr);
		}
		return $str;
	}

	if ($_GET) $_GET = replace($_GET);
	if ($_POST) $_POST = replace($_POST);

}


?>
