<?php
/**
 * Created by Agababaev Dmitry
 * d.agababaev @ duncat.net
 * License MIT
 */

class MYSQL_PDO {

    protected $db_host;
    protected $db_name;
    protected $db_user;
    protected $db_pass;
    protected $db_charset;

    protected $pdo_opt;
    public $PDO;
    public $stmt;

    public function __construct($sql_param) {

        $this->db_host = $sql_param['db_host'];
        $this->db_name = $sql_param['db_name'];
        $this->db_user = $sql_param['db_user'];
        $this->db_pass = $sql_param['db_pass'];
        $this->db_charset = $sql_param['db_charset'];
        $this->pdo_opt = $sql_param['pdo_opt'];

        $pdo_dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset={$this->db_charset}";

        $this->PDO = new PDO($pdo_dsn, $this->db_user, $this->db_pass, $this->pdo_opt);
    }

    public function query($sql_rqst, $pm = NULL) {

      if ($pm) {
        $this->stmt = $this->PDO->prepare($sql_rqst);
        $this->stmt->execute($pm);
      } else {
        $this->stmt = $this->PDO->query($sql_rqst);
      }
      return $this->stmt;

    }

    public function last_insert_id() {

      return $this->PDO->lastInsertId();

    }
}
