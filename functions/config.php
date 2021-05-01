<?php

// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

spl_autoload_register(function ($class_name) {
    require_once "classes/{$class_name}.class.php";
});

$PDO_param = [
'db_host' => '', // database hostname or ip
'db_name' => '', // database name
'db_user' => '', // database username
'db_pass' => '', // databse user password
'db_charset' => 'utf8',
'pdo_opt' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_LAZY,
        PDO::ATTR_EMULATE_PREPARES   => false,
        ]
];

$PDO = new MYSQL_PDO($PDO_param);

?>
