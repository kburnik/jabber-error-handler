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
      $payloads = $conn->processUntil(array('message', 'presence',
          'end_stream', 'session_start'));
      foreach ($payloads as $event) {
        $pl = $event[1];
        switch ($event[0]) {
          case 'message':
            if ($pl['from'] == "")
              continue;

           if (substr($pl['from'], 0, strlen($config->recipient)) !=
                $config->recipient) {
              $conn->presence();
              $conn->message($pl['from'], "No soup for you! Come back - one year.");
              echo "Rejected: {$pl['from']}\n";
              continue;
            }
	          $cmd = $pl['body'];
            $conn->presence();
            $conn->message($pl['from'], `$cmd 2>&1`);
          break;
        }
      }
      sleep(0.1);
   }

    $conn->disconnect();
} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}

