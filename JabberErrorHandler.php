<?

interface IJabberErrorStager {
  public function stageMessage($message);
}

class JabberErrorStager implements IJabberErrorStager {
   public function stageMessage($message) {
    // Set up directory.
    $directory = dirname(__FILE__)  . "/tracked_errors";
    if (!file_exists($directory))
      mkdir($directory, 0755, true);

    // Write to temp file.
    $tmp_file = tempnam("/tmp",".err");
    file_put_contents($tmp_file, $message);

    // Move temp file.
    $filename = $directory . "/" . md5($message) . ".err";
    rename($tmp_file, $filename);
  }
}

class JabberDirectSender implements IJabberErrorStager {

  private $config;
  public function __construct(JabberConfig $config) {
    $this->config = $config;
  }

  public function stageMessage($message) {
    $config = $this->config;

    // TODO: move other params to config.
    $conn = new XMPPHP_XMPP($config->host, $config->port,
                        $config->username, $config->password,
                        'xmpphp', 'gmail.com', $printlog=false,
                        $loglevel=XMPPHP_Log::LEVEL_INFO);
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    $conn->message($config->recipient, $message);
    $conn->presence();
    $conn->disconnect();
  }
}

class JabberErrorHandler {
  const QUEUED = 1;
  const DIRECT = 2;

  private $stager;

  private function __construct(IJabberErrorStager $stager) {
    $this->stager = $stager;
  }

  public function Listen() {
     set_error_handler(array($this, '__error_handler'));
  }

  public function __error_handler($errno, $errstr, $errfile, $errline) {
    $msg = "Error Occured [$errno] $errstr\n" .
           " Error on line $errline in $errfile\n";
    $this->stager->stageMessage($msg);
  }

  public static function CreateHandler($type) {
    switch ($type) {
      case self::DIRECT:
        $config = JabberConfig::GetConfig();
        $stager = new JabberDirectSender($config);
        return new JabberErrorHandler($stager);
      case self::QUEUED:
        $stager = new JabberErrorStager();
        return new JabberErrorHandler($stager);
      default:
        return new JabberErrorHandler();
    }
  }

}

?>
