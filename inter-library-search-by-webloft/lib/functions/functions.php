<?php


if (!function_exists('domp')) {
	function domp ($whattodomp) {
		echo "<pre>";
		print_r ($whattodomp);
		echo "</pre>";
	}
}

if (!function_exists('rop')) {
	function rop ($whattorop) {
		echo "<h1>*" . $whattorop . "*</h1>";
	}
}

// Tar ut f&oslash;rste X ord av en streng

if (!function_exists('trunc')) {
	//***********************************************************
	function trunc($phrase, $max_words) {
	//***********************************************************
	   $phrase_array = explode(' ',$phrase);
	   if(count($phrase_array) > $max_words && $max_words > 0)
	      $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
	   return $phrase;
	}
}

// Failsafe function to read file
if (!function_exists('get_content')) {
//***********************************************************
	function get_content($url) {  // OBS BRUKES IKKE AV MIKROMARC!!!
//***********************************************************

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT , 5);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
		//curl_setopt ($ch, CURLOPT_FRESH_CONNECT, TRUE); // bruk frisk forbindelse

$string = curl_exec($ch);

// Info om hvordan det gikk:
//domp (curl_getinfo ($ch));

return $string;

	}
}