Simple-PHP-Proxy
=============

First written by Alexxz  
Get it at <https://github.com/rafsoaken/Simple-php-proxy-script>

This is a simple php proxy script. It can be used where it is difficult to use other proxies like 
nginx or lighttpd. You can currently specify one single domain that this script proxies to.
You can also specify a number of headers that are proxied.

### Installation Usage

Download and put the files into an accessible path on your website. Then copy src/example.config.php 
to src/config.php and change the settings in there accordingly.

A request looks like this:

    http://www.alice.com/folder/simple-php-proxy/src/index.php/my_other_uri/that/resides/on/bob

and the part after index.php gets proxied to your specified domain. The script then outputs what it receives from:

    http://bob.com/my_other_uri/that/resides/on/bob
    

