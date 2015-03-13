<?
class JabberConfig {
  public $host = "talk.google.com";
  public $port = 5222;
  public $username = "sample@gmail.com";
  public $password = "defaultpassword";
  public $recipient = "sample-recipient@gmail.com";

  private function __construct($config_filename = ".config.php") {
    if ($config_filename != null) {
      $data = include($config_filename);
      foreach ($data as $var => $val)
        $this->$var = $val;
    }
  }

  // Factory method for dependency injection.
  public static function GetConfig() {
    return new JabberConfig();
  }

}

?>