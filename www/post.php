<?php
include('libs/global.lib.php');
// POST data naar Qbus
$res = qbus_send_action($_REQUEST['id'], $_REQUEST['action']);

// parse result to update state!!


?>
