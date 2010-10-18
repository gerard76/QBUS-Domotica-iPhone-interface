<?php

/* functies voor connectiviteit
 * webadressen parsen ip's, sockets ed.
 */

function url_components($url)
{
  // [http://][username][:password][@]hostname[:port][/directory][/page]
  $pattern = '#^(?:(.+)://)?(?:(.+):(.+)@)?([^:/]+)(?::(\d+))*(.*)#i';
  preg_match($pattern, $url, $matches);

  $components = array(
    'protocol' => $matches[1],
    'user'     => $matches[2],
    'pass'     => $matches[3],
    'host'     => $matches[4],
    'port'     => $matches[5],
    'path'     => $matches[6],
    'url'      => $url);

  return $components;
}

function hostname_from_webpage($url)
{
  $components = url_components($url);
  return $components['host'];
}

function port_from_webpage($page)
{
  $components = url_components($url);
  return $components['port'];
}

function is_valid_ip($ip)
{
  // true / false on $ip == valid ip
  $pattern = '/((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}'.
             '(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/';
  preg_match($pattern, $ip, $matches);
  return $matches[0] == $ip;
}

function is_valid_url($url)
{
  $components = url_components($url);
  if(empty($components['protocol']) || 
     empty($components['host']) ||
     empty($components['path']))
    return false;
  return true;
}

function validify_url($url)
{
  // doet een poging om een url valide te maken

  if(is_valid_url($url))
    return $url;

  $components = url_components($url);
  if(!is_valid_hostname($components['host']))
    return false;
  else
    $host = $components['host'];

  if(substr($components['protocol'],0,4) != 'http')
    $protocol = 'http://';
  else
    $protocol = $components['protocol'].'://';
  
  if(empty($components['path']))
    $path = '/';
  else
    $path = $components['path'];

  if(!empty($components['user']) || !empty($components['pass']))
    $credentials = $components['user'].':'.$components['pass'].'@';

  if(!empty($components['port']))
    $port = ':'.$components['port'];

  $url = $protocol.$credentials.$host.$port.$path;
  if(is_valid_url($url))
    return $url;

  // lukt niet
  return false;
}

function is_valid_hostname($hostname)
{
  // hostname is geldig als:
  // er mbv DNS een ip van kan worden gemaakt
  // OF het een geldig ip is
  if(is_valid_ip($hostname))
    return true;
  if(gethostbyname($hostname) != $hostname)
    return true;
  return false;
}

function webpage_reachable($page)
{
  // true / false if page can be read
  $url = url_components($page);
  $hostname = $url['host'];
  $service_port = $url['port']? $url['port']:80;

  /* Get the IP address for the target host. */
  if(!is_valid_ip($hostname))
  {
   $address = gethostbyname($hostname);
   if($address == $hostname)
     return false;
  }
  else
    $address = $hostname;

  /* Create a TCP/IP socket. */
  $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  if ($socket === false)
    return false;

 $result = socket_connect($socket, $address, $service_port);
 if ($result === false)
   return false;

 $in = "HEAD / HTTP/1.1\r\n";
 $in .= "Host: $hostname\r\n";
 $in .= "Connection: Close\r\n\r\n";
 $out = '';
 
 socket_write($socket, $in, strlen($in));
 while ($out = socket_read($socket, 2048)) {
   $resp .= $out;
 }
 
 socket_close($socket);

 // indien http/1 in resp zit, dan vind ik het goed .. 
 if(strpos(strtolower($resp),'http/1')===false)
   return false;
 return true;
}

function uri2host($uri) {
  $url = parse_url($uri);
  return $url['host'];
}

function uri2url($uri)
{
  if($url = validify_url($uri))
    return $url;
  return $uri;
}

function send_http_post($url, $data, $optional_headers = null)
{
  $url = url_components($url);
  $http_post = create_http_post($url['url'], $data, $optional_headers = null);
  $result = send_http_request($url['host'], $http_post, $url['port']);
  // stuur enkel content terug
// echo $result;
  $content = substr($result, strpos($result, "\r\n\r\n")+4);
  return $content;
}

function send_http_request($host, $data, $port = 80)
{
  $response = '';
  $errno    = 0;
  $errstr   = '';
  if(!is_numeric($port))
    $port = 80;
  $handle   = fsockopen($host, $port, $errno, $errstr, 30);
  stream_set_timeout($handle,10);

  if(!$handle) 
  {
    echo 'Error opening the socket';
    return false;
  }
  fwrite($handle, $data);
  while($handle && !feof($handle))
    $response .= fgets($handle, 128);
  fclose($handle);
  return $response;
}

function create_http_post($url, $data, $optional_headers = null)
{
  $url = url_components($url);
  if(is_numeric($url['port']))
    $port = ':'.$url['port'];
  $http_post  = "POST ".$url['path']." HTTP/1.1\r\n";
  $http_post .= "Host: ".$url['host'].$port."\r\n";
  // $http_post .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $http_post .= "Connection: Close\r\n";
  $http_post .= "Content-Length: ".strlen($data)."\r\n\r\n";
  $http_post .= $data."\r\n";

  return $http_post;
}

?>
