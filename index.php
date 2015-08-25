<?php
  require_once 'vendor/autoload.php';
  use Howapi\Resources\WikiHow;
//   $dom = new Dom;
//   $dom->load('http://www.wikihow.com/Use-a-Map');
//   $contents = $dom->getElementsByClass('firstHeading');
// echo $contents->firstChild()->text ; // 10
$wk = new WikiHow('http://www.wikihow.com/Use-a-Map');
//$wk = new WikiHow('http://www.wikihow.com/Make-an-Origami-Mouse');

echo $wk->getJson ();
