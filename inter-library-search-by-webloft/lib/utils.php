<?php


function getTemplatePath( $filename ){
  return plugin_dir_path( KS_FILE ) . "templates"."/". $filename;
}

function getTemplateUrl( $filename ){
  return ILS_URL  . "/templates"."/". $filename;
}

function getConfigPath( $filename ){
  return plugin_dir_path( KS_FILE ) . "conf"."/". $filename;
}


if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}



//***********************************************************
function url_exists($url) {
//***********************************************************
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode == 200) {
            $ret = true;
        }
    }

    curl_close($curl);

    return $ret;
}

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


function getStockInformationByStatusCode($status , $restriction){
	switch ($status) {
		case "1": 	return "Ukjent status";
		case "2": 	return "I bestilling";
		case "3": 	return "Ukjent status, utilgjengelig";
		case "4": 	return "Utl&aring;nt";
		case "5": 	return "Utl&aring;nt";
		case "6": 	return "Under behandling";
		case "7": 	return "Innkalt";
		case "8": 	return "P&aring; vent";
		case "9": 	return "Venter p&aring; klargj&oslash;ring";
		case "10": 	return "P&aring; vei mellom to bibliotek";
		case "11": 	return "Hevdet innlevert eller aldri l&aring;nt";
		case "12": 	return "Tapt";
		case "13": 	return "Savnet - vi leter";
		case "14": 	return "Ukjent status";
		case "15": 	return "Til innbinding";
		case "16": 	return "Til reparasjon";
		case "17": 	return "Venter p&aring; overf&oslash;ring";
		case "18": 	return "Purring sendt";
		case "19": 	return "Trukket tilbake";
		case "20": 	return "Ukjent status";
		case "21": 	return "Ukjent status";
		case "22": 	return "Skadet";
		case "23": 	return "Ikke i oml&oslash;p";
		case "24": 	return "Annen status";
		case "0":
			switch ($restriction) {
				case "1": 	return "[Ikke til utl&aring;n]";
				case "2": 	return "Ledig [Til bruk i biblioteket]";
				case "3": 	return "Ledig [Dagsl&aring;n]";
				case "4": 	return "Ledig [Til bruk p&aring; lesesal el.l.]";
				case "5": 	return "Ledig [Kan ikke fornyes]";
				case "6": 	return "Ledig [Begrenset l&aring;netid]";
				case "8": 	return "Ledig [Utvidet l&aring;netid]";
				case "9": 	return "Ledig";
				case "10": 	return "Ledig";
				default: 	return "Ledig";
			}
		default:	return "Ukjent status";
	}
}


function getLibraryNameById($library_id){
  $library_name = null;

  if( $library = getLibraryById( $library_id ) ){
    $library_name = $library['name'];
  }

  return $library_name;
}


function getLibraryById( $library_id ){
  include( getConfigPath("library_list.php") );

  if ( _is($libraries, $library_id) ){
    return _is($libraries, $library_id);
  }
  else{
    return null;
  }
}





function wl_is_plugin_active($slug){
  $active_plugins = get_option('active_plugins');

  if ( is_numeric( array_search($slug, $active_plugins) ) ){
    return true;
  }
  else{
    return false;
  }
}



//***********************************************************
function hente_omslag ($treff) { // henter omslag for ett enkelt treff
//***********************************************************

// Det enkleste er &aring; bruke v&aring;r egen server hvis vi har ISBN
			if ((isset($treff['isbn'])) && (!isset($treff['omslag']))) { // vi har ISBN men ikke omslag
        $tempisbn = cleanIsbn($treff['isbn']);
        // $omslag   = COVER_SERVER . "/isbn/" . $tempisbn . ".jpg";
				$omslag   = null;
				// if (url_exists($omslag)) { // hurra, ISBN-omslag finnes - da grabber vi lenke til fulltekst ogs&aring;
				// 	$treff['omslag'] = $omslag;
				// 	$tittelsok = "http://www.nb.no/services/search/v2/search?q=*&fq=isbn:%22" . $treff['isbn'] . "%22&fq=contentClasses:(public%20OR%20bokhylla)";
				// 	$tybring   = get_content($tittelsok);
				// 	$firsttry  = simplexml_load_string($tybring);
				// 	foreach ($firsttry->entry as $item) {
				// 		$namespaces = $item->getNameSpaces(true);
				// 		$nb         = $item->children($namespaces['nb']); // alle som er nb:ditten og nb:datten
				// 		$treff['fulltekst'] = "http://urn.nb.no/" . $nb->urn;
				// 	}
				// }
			}

			// Hvis vi fortsatt ikke har omslag

			if ( !isset($treff['omslag']) ) {

        // _log($treff);
				if ( $isbn = _is($treff, 'isbn') ) {
					$isbnsearch = "https://www.bokkilden.no/SamboWeb/partner.do?format=XML&uttrekk=5&ept=3&xslId=117&enkeltsok=" . $isbn;
          $panda = get_content($isbnsearch);

					$firsttry = simplexml_load_string($panda);
					$treff['omslag'] = $firsttry->Produkt->BildeURL;
					$treff['omslag'] = str_replace("&width=80", "", $treff['omslag']); // knegg, knegg
					if (!isset($treff['beskrivelse'])) {
						$treff['beskrivelse'] = (string)$firsttry->Produkt->Ingress;
					}
				}
			}
			// Siste fors&oslash;k: S&oslash;ke i NB via URN , hvis vi fortsatt ikke har omslag OG vi ikke har ISBN (har allerede s&oslash;kt p&aring; ISBN)
			// Men det m&aring; være b&oslash;ker (annet finnes jo ikke i NB)

			if ((!isset($treff['omslag'])) && (!isset($treff['isbn'])) && isset($treff['type']) && ($treff['type'] == "bok")) {

				// Vi s&oslash;ker p&aring; tittel og ser hvilke URN-er vi f&aring;r

				$tittelsok = "http://www.nb.no/services/search/v2/search?q=*&fq=title:%22" . urlencode($treff['tittel']) . "%22&fq=contentClasses:(public%20OR%20bokhylla)";
				$tybring   = get_content($tittelsok);

				$firsttry  = simplexml_load_string($tybring);
				foreach ($firsttry->entry as $item) {
					$namespaces = $item->getNameSpaces(true);
					$nb         = $item->children($namespaces['nb']); // alle som er nb:ditten og nb:datten
					// $omslag     = COVER_SERVER . "/urn/" . substr(($nb->urn), 8) . ".jpg";
					// if ((url_exists($omslag)) && ($nb->urn != '')) {
					// 	$treff['omslag']    = $omslag;
					// 	$treff['fulltekst'] = "http://urn.nb.no/" . $nb->urn; // grabber lenke ogs&aring; med det samme
					// }
				}
			}

return ($treff);
} // end function


//***********************************************************
function krydre_some ($treff) { // tar et enkelt treff, legger til Twitter og Facebookinfo
//***********************************************************

// Facebook f&oslash;rst

// params: 0: Tittel 1: Beskrivelse 2: enkeltpostlenke 3: bilde 4: Forfatter 5: ISBN
// adskilt med |x|

$params = _is($treff, 'tittelinfo');
$params .= "|x|";

if ( $description = _is($treff, 'beskrivelse') ){
	$params .= $description;
}
else {
	$params .= _is($treff, 'omfang'). "  ";

  $utgitt = "[s.n.]";
	if ( $value = _is($treff, 'utgitthvem') ) {
		$utgitt = $value;
	}

  $utgitt .= ", [s.l.]";
  if ( $value = _is($treff, 'utgitthvor') ){
		$utgitt .= ", " . $value;
	}


  if ( $value =_is($treff, 'utgittaar') ) {
		$utgitt .= ", " . $value;
	}

	$params .= '<strong>Utgitt : </strong>' . $utgitt . "<br>";
}

$params .= "|x|";
$params .= "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$params .= "|x|";
@$params .= $treff['omslag'];
$params .= "|x|";
@$params .= $treff['opphav'];
$params .= "|x|";
@$params .= $treff['isbn'];

$twitterdescription = null;
if ( $info = _is($treff,'tittelinfo') ){
  $twitterdescription = substr( $info, 0, 80);
  if (strlen($info > 80)) {
    $twitterdescription .= "[...]";
  }
}


if ( $origin = _is($treff, 'opphav') ){
  @$twitterdescription .= " (" . substr( $origin, 0, 40);
  if (@strlen($origin) > 40) {
    $twitterdescription .= "[...]";
  }
}


$pub_year = _is($treff, 'utgittaar');
if ( $pub_year) {
	$twitterdescription .= " - " . $pub_year;
}

$twitterdescription .= ")";

$twitterdescription = html_entity_decode($twitterdescription);
$params = html_entity_decode($params);
$params = base64_encode(urlencode($params));

$treff['twitter'] = $twitterdescription;
$treff['facebook'] = $params;

return ($treff);


/*

$gotourn = $fbdescription . "|x|" . "Delt via Bibliotekarens beste venn: http://www.bibvenn.no/nbsok" . "|x|" . $lenke . "|x|" . $fbsharethumb[$x];
$gotourn = base64_encode(urlencode($gotourn));

$niceandlang = "http://www.bibvenn.no/nbsok/gotourn.php?params=" . $gotourn;
$niceandshort = make_bitly_url($niceandlang,'sundaune','R_096021159a86478688c3b34a32de31c3','json');



<a target="_self" href="javascript:fbShare('<?php echo $niceandshort;?>', 700, 350)">
<img style="width: 50px; height: 21px;" src="/maler/g/litenface.png" alt="Facebook-deling" />
</a>

*/




} // end function

function cleanValue($string){
  return trim(stripslashes(strip_tags($string)));
}


function twitter_ikon () {
  return getIconUrl("twitter.png");
}


function facebook_ikon () {
  return getIconUrl("fb.png" , __FILE__);
}


function getIconUrl( $file ){
  return plugin_dir_url(KS_FILE)."assets/images/icons/".$file;
}


function cleanIsbn($isbn){
  $tempisbn = str_replace(" ", "", $isbn); // fjerne mellomrom
  $tempisbn = str_replace("-", "", $isbn); // fjerne streker

  return $tempisbn;
}


//***********************************************************
function wptuts_add_color_picker( $hook ) { // color picker i settings
//**********************************************************
    if( is_admin() ) {
        // Add the color picker css file
        wp_enqueue_style( 'wp-color-picker' );
    }
}

//***********************************************************
function wlils_ribbon ( $tekst , $url , $bgfarge ) { // Legger p&aring; skr&aring;stilt banner
//***********************************************************

	$outhtml = "<div class=\"wlilsribbon\" style=\"background-color: " . $bgfarge . "\">";
	$outhtml .= "<a target=\"_blank\" href=\"" . $url . "\">" . $tekst . "</a>";
	$outhtml .= "</div>";

return $outhtml;

}


if ( !function_exists('_is') ){
  function _is($object, $attribute, $fallback = null ){
    $value = $fallback;
    if ( is_object($object) ){
      if ( isset($object->$attribute) ){
        $value = $object->$attribute;
      }
    }
    else if ( is_array($object) ){

      if ( isset($object[$attribute]) && is_array($object[$attribute]) && count($object[$attribute]) == 1 && isset($object[$attribute][0]) ){
        $value = $object[$attribute][0];
      }
      elseif ( isset($object[$attribute]) ){
        $value = $object[$attribute];
      }
    }


    if ( is_string($value) ){
      return trim($value);
    }
    else{
      // _log($value);
      return $value;
    }

  }
}


function getBase64ItemInfo( $url ){
  $splits = explode('enkeltpostinfo=', $url);
  if ( isset($splits[1]) ){
    return maybe_unserialize(  base64_decode($splits[1]) );
  }
  else{
    return null;
  }
}


function getSingleItemUrl(){
  if ( $post_id = get_option('wl_katalogsok_option_enkeltpost') ){
    return get_permalink($post_id );
  }
  else{
    return null;
  }

}