<?php

/**
 * Created by Agababaev Dmitriy © 2020
 * d.agababaev @ duncat.net
 * License MIT
 */

class SMS_data_handle {

  public $PDO;

  public function __construct ($PDO)
  {
      $this->PDO = $PDO;

  }

  public function save() {

    $msg = $_REQUEST['msg'];
    $search  = array(' ', '+');
    $replace = array('', '');
    $phone_array = explode(",", str_replace($search, $replace, $_REQUEST['tel']));

    $query = $this->PDO->query("SELECT uuid FROM users WHERE login = ?", [$_REQUEST['login']]);
    $user_uuid = $query->fetch();

    $data = [
      'message' => $msg,
      'flash' => @$_REQUEST['flash'],
      'replacemessages_id' => @$_REQUEST['replacemessages_id'],
      'attempts' => @$_REQUEST['attempts']
    ];
    $data['attempts'] = (@$_REQUEST['attempts']) ?: 1;
    
    if (empty($_REQUEST['tel'])) http_response::return(200, ["success" => false, "description" => "Phone number is empty"]);
    if (empty($_REQUEST['msg'])) http_response::return(200, ["success" => false, "description" => "Message is empty"]);

    foreach ($phone_array as $phone) {
      $data['phone'] = $phone;
      $this->PDO->query("INSERT INTO sms_queue SET uuid= ?, data= ?, phone= ?", [$user_uuid->uuid, json_encode($data, JSON_UNESCAPED_UNICODE), $phone]);
    }

    http_response::return(200, ["success" => true, "description" => "Saved to queue"]);

  }

  public function get_sms_queue($sms_count) {
    return $this->PDO->query("SELECT * FROM sms_queue WHERE status LIKE '' OR status IS NULL AND dateTime >= NOW() - INTERVAL 10 MINUTE LIMIT {$sms_count}");
  }


  public function sms_2archive($id, $user_gateway_id) {
    $insert = $this->PDO->query("INSERT INTO sms_archive (uuid, dateTime, data, phone) SELECT uuid, dateTime, data, phone FROM sms_queue WHERE id = ?", [$id]);
    $this->PDO->query("UPDATE sms_archive SET gateway_id = ?, status = ? WHERE id= ?", [$user_gateway_id, 'OK', $this->PDO->last_insert_id()]);
    $this->PDO->query("DELETE FROM sms_queue WHERE id = {$id}");
  }


  public function update_gwcount($user_gateway_phone) {
    $count = $this->PDO->query("SELECT id FROM gateway_smscount WHERE gw_phone= ? AND date= ?", [$user_gateway_phone, date("Y.m")]);
    if (!$count->rowCount()) $this->PDO->query("INSERT INTO gateway_smscount SET gw_phone= ?, date= ?", [$user_gateway_phone, date("Y.m")]);
    $this->PDO->query("UPDATE gateway_smscount SET count = count +1 WHERE gw_phone= ?", [$user_gateway_phone]);
  }


  public function gateway_lock($user_gateway_id) {
    $this->PDO->query("UPDATE smsc_gateway SET state='busy' WHERE id= ?", [$user_gateway_id]);
  }


  public function gateway_release($user_gateway_id) {
    $this->PDO->query("UPDATE smsc_gateway SET state = NULL WHERE id= ?", [$user_gateway_id]);
  }


  public function gateway_maxcount_check($user_gateway_phone) {
    $max_count = $this->PDO->query("SELECT maxcount FROM smsc_gateway WHERE gw_phone= ?", [$user_gateway_phone]);
    $row = $max_count->fetch();
    $current_count = $this->PDO->query("SELECT id FROM gateway_smscount WHERE gw_phone= ? AND date= ? AND count >= ?", [$user_gateway_phone, date("Y.m"), $row->maxcount]);
    if ($current_count->rowCount() > 0) return false;
    return true;
  }


  public function get_gateway($uuid) {

    $user_gateway = $this->PDO->query("SELECT * FROM smsc_gateway WHERE uuid= ? AND state LIKE '' OR state IS NULL AND status= 1", [$uuid]);
    $rows = $user_gateway->rowCount();

    if ($rows > 0) {
      for ($i = 0; $i < $rows; $i++) {
        $row = $user_gateway->fetch();
        if ($this->gateway_maxcount_check($row->gw_phone)) return $row;
      }
    }

    return false;
  }

  public function send($с = 1) {

    $sended_sms = 0;
    for ($i = 0; $i < $с; $i++) {

      $sms_queue = $this->get_sms_queue(1);
      if (!$sms_queue->rowCount()) http_response::return(200, ["description" => "Nothing to do. Sent. count: {$sended_sms}"]);

      $sms_count = $sms_queue->rowCount();
      $msg_row = $sms_queue->fetch();

      $this->PDO->query("UPDATE sms_queue SET status = ? WHERE id = ?", ["process", $msg_row->id]);

      $user_gateway = ($this->get_gateway($msg_row->uuid));

      if (!$user_gateway) {
        $this->PDO->query("UPDATE sms_queue SET status = NULL WHERE id= ?", [$msg_row->id]);
        http_response::return(403, ["description" => "Not active gateways or get limit of message count"]);
      }

      $this->gateway_lock($user_gateway->id);

      $param = [
          'host' => $user_gateway->host,
          'port' => $user_gateway->port,
          'login' => 'root',
          'password' => $user_gateway->password,
      ];

      $sms_data = json_decode($msg_row->data);
      $sms_data->message = $sms_data->message;

      $attr = [
          'phone' => $sms_data->phone,
          'message' => $sms_data->message,
          'attempts' => $sms_data->attempts,
          'flash' => $sms_data->flash,
          'replacemessages_id' => $sms_data->replacemessages_id,
          'gateway' => $user_gateway->id,
      ];
      // sleep(5);

      $send_proc = new Gammu_SMS($param);
      if ($send_proc->send($attr)) {

        $this->sms_2archive($msg_row->id, $user_gateway->id);
        $this->update_gwcount($user_gateway->gw_phone);
        $sended_sms++;

      } else {
        $this->PDO->query("UPDATE sms_queue SET status = NULL WHERE id= ?", [$msg_row->id]);
      }
      $this->gateway_release($user_gateway->id);

    }

    http_response::return(200, ["success" => true, "description" => "Message sent. Count: {$sended_sms}"]);

  }


}

?>
