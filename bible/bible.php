<?php

//get passage
$passage = urlencode($_GET['passage']);

//check for cache
$cache = false;

//echo ereg_replace('\+', ' ', $passage);
$file_name = ereg_replace('\+', '.', $passage).'.txt';
if (file_exists('cache/'.$file_name)) {
	$cache = file_get_contents('cache/'.$file_name);
}

if ($cache === false) {
	$key = "IP";
	$options = "include-first-verse-numbers=0&include-footnotes=1&include-audio-link=0&include-copyright=0&include-short-copyright=0";
	$url ="http://www.esvapi.org/v2/rest/passageQuery?key=$key&$options&passage=$passage";
	
	//get data
	$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
	curl_close($ch);
	
	//print out
	print $response;
	
	file_put_contents('cache/'.$file_name, $response);
}
else {
	echo $cache;
}
?>