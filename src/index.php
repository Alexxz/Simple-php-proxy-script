<?php
/* 
    Set the config options in your own config.php which you copy from example.config.php 
*/

include(dirname(__file__) . '/config.php');
//canonical trailing slash
$proxy_base_url_canonical = rtrim($proxy_base_url, '/ ') . '/';

/* Init CURL */
$ch = curl_init();

if( strpos($_SERVER['REQUEST_URI'], $proxy_base_url) !== 0 )
{
    die("The config paramter \$prox_base_url \"$proxy_base_url\" that you specified 
        does not match the beginning of the request URI: ".
        $_SERVER['REQUEST_URI']);
}

//form proxied request url
$proxy_request_url = "http://" . rtrim($dest_host, '/ ') . 
    substr($_SERVER['REQUEST_URI'], strlen($proxy_base_url_canonical . 'index.php'));

curl_setopt($ch, CURLOPT_URL, $proxy_request_url);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

/* Collect and pass client request headers */
if(isset($_SERVER['HTTP_COOKIE']))     
{ 
    $hdrs[]="Cookie: " . $_SERVER['HTTP_COOKIE'];        
}

if(isset($_SERVER['HTTP_USER_AGENT'])) 
{ 
    $hdrs[]="User-Agent: " . $_SERVER['HTTP_USER_AGENT']; 
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);

/* pass POST params */
if( sizeof($_POST) > 0 )
{ 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST); 
}

$res = curl_exec($ch);
curl_close($ch);

/* parse response */
list($headers, $body) = explode("\r\n\r\n", $res, 2);

$headers = explode("\r\n", $headers);
$hs = array();

foreach($headers as $header)
{
    if( false !== strpos($header, ':') )
    {
        list($h, $v) = explode(':', $header);
        $hs[$h][] = $v;
    }
    else 
    {
        $header1  = $header;
    }
}

/* set headers */
list($proto, $code, $text) = explode(' ', $header1);
header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $text);

foreach($proxied_headers as $hname)
{
    if( isset($hs[$hname]) )
    {
        foreach( $hs[$hname] as $v )
        {
            if( $hname === 'Set-Cookie' ) 
            {
                header($hname.": " . $v, false);
            }
            else
            {
                header($hname.": " . $v);
            }
        }
    }
}

die($body);
