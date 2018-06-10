<?php
define("APP_NAME","Izere");
define("APP_AUTHOR","David NIWEWE");
define("APP_ORGANIZATION","Addax tech");
define("DB_NAME","izere");
/*
This is the function that is responsible for database connection
*/
class connection{
 public $db;
    public $host;
    public $db_user;
    public $pass_phrase;
    public function __construct()
    {
        if (!isset($this->host) || !isset($this->db_user) || !isset($this->db) || !isset($this->pass_phrase)) {
            if (null !== getenv("OPTS_ENV") && "live" == getenv("OPTS_ENV")) {
                $url = parse_url(getenv("DATABASE_URL"));
                if (isset($url) && isset($url["host"]) && isset($url["user"])) {
                    $this->host = $url["host"];
                    $this->db_user = $url["user"];
                    $this->pass_phrase = $url["pass"];
                    $this->db = substr($url["path"], 1);
                }
            }
            if (null !== getenv("OPTS_ENV") && "local" == getenv("OPTS_ENV")) {
                $this->host = getenv("PGQL_HOST");
                $this->db_user = getenv("PGQL_DB_USER");
                $this->pass_phrase = getenv("PGQL_DB_PASS");
                $this->db = getenv("IZERE_DB");
            }
        }
    }
}
