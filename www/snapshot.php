<?

include('/home/domotica/www/libs/global.lib.php');

// get image from cam
$host = "192.168.1.4"; 
$file = "/snapshot.jpg?Authorization: Basic YWRtaW46aXN3bWxl\r\n";

$headers = "GET $file HTTP/1.1\r\n".
  "Host: $host\r\n".
  "Authorization: Basic YWRtaW46aXN3bWxl\r\n\r\n";
$res = send_http_request($host, $headers);
// knip headers eraf
header('Content-Type: image/jpeg');
echo substr($res, strpos($res, "\r\n\r\n")+4);
?>
