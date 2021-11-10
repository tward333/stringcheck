<?php


$fileName = "sites.json";
$jsonData = file_get_contents("$fileName");
$jsonData = json_decode($jsonData,TRUE);

print_r($jsonData);
