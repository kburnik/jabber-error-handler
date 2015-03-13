<?php

include dirname(__FILE__) . '/XMPPHP/XMPPHP/XMPP.php';
include_once("JabberConfig.php");
include_once("JabberErrorHandler.php");

$errorHandler = JabberErrorHandler::CreateHandler(JabberErrorHandler::QUEUED);
$errorHandler->Listen();
echo file_get_contents("Non existing file");
echo "Done\n";
?>
