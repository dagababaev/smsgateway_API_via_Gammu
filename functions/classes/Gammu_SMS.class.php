<?php
/**
 * Created by Agababaev Dmitry
 * d.agababaev @ duncat.net
 * License MIT
 */

class Gammu_SMS
{
    private $host;
    private $port;
    private $login;
    private $password;
    private $usb_port;
    private $replacemessage;

    public function __construct ($param)
    {
        $this->host = $param['host'];
        $this->port = $param['port'];
        $this->login = $param['login'];
        $this->password = $param['password'];
    }

    public function send($attr)
    {

        $flash = ($attr['flash']) ? '-flash' : '';
        $replacemessages_id = ($attr['replacemessages_id']) ? "-replacemessages {$attr['replacemessages_id']}" : '';
        $attempts = ($attr['attempts']) ? $attr['attempts'] : 1;

        $cmd = "gammu -s {$attr['gateway']} sendsms TEXT +{$attr['phone']} -unicode -autolen 5 {$flash} {$replacemessages_id} -text \"{$attr['message']}\"";

        for ($i = 0; $i < $attempts; $i++) {

          $connection = ssh2_connect($this->host, $this->port);
          if (ssh2_auth_password($connection, $this->login, $this->password)) {
              $stream = ssh2_exec($connection, $cmd);
        	    stream_set_blocking($stream, true);
              $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        	    $result = stream_get_contents($stream_out);
              fclose($stream);

            	if (strstr($result, "OK")) {
            		return true;
            	}

            } else {
                return false;
            }
        }

        return false;
    }
}
