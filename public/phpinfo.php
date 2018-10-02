<?php

echo 'aaa'.PHP_EOL;
$dom = new DOMDocument('1.0', 'iso-8859-1');
echo $dom->saveXML();
dump($dom);
$x=new DOMXPath($dom);
dump($x);
