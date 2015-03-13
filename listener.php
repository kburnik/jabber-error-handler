<?php
// activate full error reporting
//error_reporting(E_ALL & E_STRICT);
include dirname(__FILE__) . '/XMPPHP/XMPPHP/XMPP.php';
include dirname(__FILE__) . '/JabberConfig.php';

$config = JabberConfig::GetConfig();

$conn = new XMPPHP_XMPP($config->host, $config->port,
                        $config->username, $config->password,
                        'xmpphp', 'gmail.com', $printlog=false,
                        $loglevel=XMPPHP_Log::LEVEL_INFO);
try {
    echo "Connecting.\n";
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    echo "Ready.\n";
    while(true) {
      foreach (glob("tracked_errors/*.err") as $file) {
          echo "Sending to {$config->recipient}: \n";
          $message = file_get_contents($file);
          echo $message . "\n";
          $conn->presence();
          $conn->message($config->recipient, $message);
          unlink($file);
          echo "Sent.\n";
      }
      sleep(0.05);
   }
   $conn->disconnect();
} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}

