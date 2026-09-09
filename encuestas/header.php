<?php
include_once "config.php";
include_once "clases/includes_clases.php";
include_once "Funciones.php";
include_once "Variables.php";

echo "<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <!-- Meta Pixel -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '1867704257992502');
      fbq('track', 'PageView');
    </script>

    <noscript>
      <img height='1' width='1' style='display:none'
           src='https://www.facebook.com/tr?id=1867704257992502&ev=PageView&noscript=1'/>
    </noscript>

    <!-- CSS -->
    <link rel='stylesheet' href='assets/css/bootstrap.min.css'>
    <link rel='stylesheet' href='assets/css/fontawesome.5.14.css'>
    <link rel='stylesheet' href='assets/css/estilos.css'>
  </head>
  <body>";
?>