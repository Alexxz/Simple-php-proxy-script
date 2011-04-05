<?php
/* config */
$dest_host = "example.com"; //Destination domain
$proxied_headers = array('Set-Cookie', 'Content-Type', 'Cookie', 'Location'); // server -> client


/* Init CURL */
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://".$dest_host.$_SERVER['REQUEST_URI']);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

/* headers */
if(isset($_SERVER['HTTP_COOKIE'])){
    $hdrs[]="Cookie: ".$_SERVER['HTTP_COOKIE'];
}
$hdrs[]="User-Agent:".$_SERVER['HTTP_USER_AGENT'];
curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);

/* POST */
if(sizeof($_POST) > 0){curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);}

$res = curl_exec($ch);

curl_close($ch);
/* parse response */
list($headers, $body) = explode("\r\n\r\n", $res, 2);
$headers = explode("\r\n", $headers);
$hs = array();
foreach($headers as $header){
    if(false !== strpos($header, ':')){
        list($h, $v) = explode(':', $header);
        $hs[$h] = $v;
    } else {
        $header1 = $header;
    }
}

/* set headers */
header($header1);
foreach($proxied_headers as $hname){
    if(isset($hs[$hname])){
        header($hname.": ".$hs[$hname]);
    }
}

die($body);

