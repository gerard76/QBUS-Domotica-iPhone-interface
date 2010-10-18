<?php

class QBUS {

  var $handle;
  var $host = '192.168.1.3';
  var $port = '8445';

  // todo check if i need singleton
  function open_tcp() {
    $this->handle = @fsockopen($this->host, $this->port, $errno, $errstr);

    if(!$this->handle) {
      // remember. it's sloooooow
      sleep(1);
      $this->handle = @fsockopen($this->host, $this->port, $errno, $errstr);
      if(!$this->handle) {
        die("could not open socket");
      }
    }
    // clear welcome line from buffer
    $this->fgets();
  }

  function close_tcp() {
    if($this->handle) {
      fclose($this->handle);
    }
  }
  
  function verify_password($password) {
    if(strlen($password) != 4) {
      return false;
    }
    $res = $this->send(240, 4, $password);
    if(ord($res[0]) == 0) return true;
    return false;
  }

  function set_password($old_password, $new_password) {
    $data = $old_password . $new_password;
    if(strlen($data) != 8) {
      return false;
    }
    $res = $this->send(241, 8, $data);
    if(ord($res[0]) == 0) return true;
    return false;
  }

  function get_parameters() {
    $offset = 0;
    $number = 6;

    $data = chr($offset) . chr($number);
    $res = $this->send(2, 2, $data);
  }
// **************** PRIVATE ********************

  private function send($function_number, $length, $data) {
    $command = $this->command($function_number, $length, $data);
echo "<br>send: ";
$this->visualize($command);
    fputs($this->handle, $command);
    $res = $this->fgets();

echo "<br>resp: ";
$this->visualize($res);

    $return = substr($res, 10, 2);
    return $return;
  }
 
  private function command($function_number, $length, $data) {
    $header = 'QBUS';
    $header .= chr(0) . chr(1);
    $header .= chr(0) . chr(1);
    $header .= chr(0);
    $header .= chr($function_number);
    $header .= chr(0) . chr($length);
    $header .= $data;
    return $header;
  }

  private function fgets($length = null) {
    // Blocking/Non-Blocking (B/NB) mode
    // only blocks on the first element of the sequence
    stream_set_blocking($this->handle, 1);

    $blocked = true;
    $res = '';
    while((strlen($res) < $length) || !$length) {
      $c = fgetc($this->handle);
      if(!$c) break;

      $res .= $c;
      if($blocked) {
        if(!stream_set_blocking($this->handle, 0))
          die("unable to unblock!");
        $blocked = false;
      }
    }
    return $res;
  }

  private function visualize($res){
    echo "<br>";
    var_dump($res);
    echo " - ";
    for($i=0;$i<strlen($res);$i++) {
      echo ord($res[$i]) . ' - ';
    }
  }
}


function qbus_login() {
  global $CFG;
  $data_post = "data=L_ff&pwd=&valideren=+++OK+++";
  send_http_post($CFG->qbus_url, $data_post);
}

function qbus_send_action($id, $action)
{
 global $CFG;
 
 $id = sprintf("%02d", $id);
 $data_post = "data=U000$id$action";
 $status_data = send_http_post($CFG->qbus_url, $data_post);
 return $status_data;
}

function qbus_update_status()
{
  global $CFG;
  foreach($CFG->qbus_menus AS $menu) {
    $modules_status = qbus_get_status($menu);
    foreach($modules_status AS $module_id => $module_status) {
      update_module_status($module_id, $module_status);
    }
  }
}

function qbus_get_status($menu="000")
{
 global $CFG;
 // retourneert geparsed resultaat
 $res = tidy_repair_string(send_http_post($CFG->qbus_url, "data=M".$menu));
 $status = qbus_parse_status(strtolower($res));
 return $status;
}

function qbus_parse_status($content)
{
  // get all tags
  $regex = "/<\/?\w+((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/i";
  preg_match_all($regex, $content, $matches);
  // parse info from HTML
  $modules_status = array();
  $module_id = 0;
  for($i=0;$i<count($matches[0]);$i++) {
    $match = $matches[0][$i];
    if(substr($match,0,2) == "<a") {
      $pos = strpos($match, 'sendu');
      $ident = substr($match, $pos + 7, 6);
      if(is_numeric($ident)) {
        $modules_status[$module_id]['id'] = sprintf("%d", substr($ident, 0, 2));
        // state active?
        $active = (strpos($matches[0][$i+1], 'pass') === FALSE)? true:false;
        // determine type
// echo $matches[0][$i+1];
        if(strpos($matches[0][$i+1], 'butt_o'))
          $type = 'toggle';
        elseif(strpos($matches[0][$i+1], 'level_') !== FALSE)
          $type = 'dim';
        else
          $type = 'scene';
        $modules_status[$module_id]['type'] = $type;
        $modules_status[$module_id]['state'] = substr($ident, 2);
        $modules_status[$module_id++]['active'] = $active;
      }
    }
  }
  // parse the bits of information we gathered
  foreach($modules_status AS $module) {
    switch($module['type']) {
      case 'toggle':
        if($module['active']) {
          $ret[$module['id']] = $module['state'];
        }
        break;
      case 'dim':
        if($module['active']) {
          $ret[$module['id']] = $module['state'];
        }
        elseif(!isset( $ret[$module['id']])){
           $ret[$module['id']] = '1000';
        }
        break;
    }
  }
  return $ret;
}
?>
