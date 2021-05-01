<?php

/**
 * Created by Agababaev Dmitriy © 2020
 * d.agababaev @ duncat.net
 * License MIT
 */

class Users_Auth
{
    static function do($PDO)
    {
      if (!@$_REQUEST['login'] || !@$_REQUEST['pass']) http_response::return(403, ['description' => 'Check your login and password']);
      // Проверяем авторизацию
      $user = $PDO->query("SELECT id, status FROM users WHERE login= ? AND password= ?", [$_REQUEST['login'], md5(trim($_REQUEST['pass']))]);
      if ($user->rowCount() == 0)
          return 0;
          // http_response::return(401, ["description" => "User not found. Login and password is incorrect"]);

      $row = $user->fetch();
      if ($row->status != 'active')
          return 0;
          // http_response::return(401, ["description" => "User status: {$row->status}"]);
      return 1;
    }
}
