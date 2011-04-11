<?php
/* 
    To start with this script you just need to edit CONFIG section
    of this file.
*/

/************** CONFIG **************/
$dest_host = "example.com"; // What is destination site
/************** CONFIG **************/
/************** EXPERT CONFIG **************/
$proxied_headers = array('Set-Cookie', 'Content-Type', 'Cookie', 'Location'); // What headers proxy from origin to client
/************** EXPERT CONFIG **************/

/* Init CURL */
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://".$dest_host.$_SERVER['REQUEST_URI']);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

/* Collect and pass client request headers */
if(isset($_SERVER['HTTP_COOKIE']))     { $hdrs[]="Cookie: ".$_SERVER['HTTP_COOKIE'];        }
if(isset($_SERVER['HTTP_USER_AGENT'])) { $hdrs[]="User-Agent: ".$_SERVER['HTTP_USER_AGENT']; }
curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);

/* pass POST params */
if(sizeof($_POST) > 0){ curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST); }

$res = curl_exec($ch);
curl_close($ch);

/* parse response */
list($headers, $body) = explode("\r\n\r\n", $res, 2);

$headers = explode("\r\n", $headers);
$hs = array();
foreach($headers as $header){
    if(false !== strpos($header, ':')){
        list($h, $v) = explode(':', $header);
        $hs[$h][] = $v;
    } else {
	$header1  = $header;
    }
}

/* set headers */
list($proto, $code, $text) = explode(' ', $header1);
header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$text);
foreach($proxied_headers as $hname){
    if(isset($hs[$hname])){
	foreach($hs[$hname] as $v){
	    if($hname === 'Set-Cookie'){
		header($hname.": ".$v, false);
	    }else{
		header($hname.": ".$v);
	    }
	}
    }
}

die($body);
