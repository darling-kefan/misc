<?php
$array = array(
	'className' => 'video',
	'method' => 'generateMediaTag',
	'arguments' => array(
		'videoId' => 250484380
	)
);
$jsonArr = json_encode($array);
echo $jsonArr . "\n";


echo md5('adskeasla' . $jsonArr) . "\n";