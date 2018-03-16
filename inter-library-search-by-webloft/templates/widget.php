<?php
$page_id_search = $instance['resultatside']; // ID til side som skal være TARGET for FORM
$title = apply_filters( 'widget_title', $instance['title'] );
$enkeltpost = get_option('wl_katalogsok_option_enkeltpost' );

$katalog = $instance['katalog']; // Hvilken bibliotekkatalog skal vi s&oslash;ke i

// Finne guid for ID resultatside, sette som target
// Display the widget
if ($title) {
	echo $before_title . $title . $after_title;
}

// Plukk ut query string
$search_permalink = get_permalink($page_id_search);
$link = explode ("?" , $search_permalink); // alle query strings i $link[1] HVIS DET ER NOEN

echo "<form class=\"wlils_widget\" target=\"_self\" action=\"" . $link[0] . "\" method=\"GET\">\n";
echo "<input type=\"text\" id=\"search\" name=\"webloftsok_query\" placeholder=\"" . __('S&oslash;keord...', 'inter-library-search-by-webloft') . "\" accept-charset=\"utf-8\" />";
echo "<input type=\"hidden\" name=\"katalog\" value=\"" . $katalog . "\" />";

if (isset($link[1])) {
	$parameters = explode ("&" , $link[1]); // array med parametre
	if (is_array($parameters)) {
		foreach ($parameters as $parameter) {
			$ettparameter = explode ("=" , $parameter);
			echo "<input type=\"hidden\" name=\"" . $ettparameter[0] . "\" value=\"" . $ettparameter[1] . "\" />";
		}
	}
}

if (trim($enkeltpost) != "") {
	echo "<input type=\"hidden\" name=\"enkeltposturl\" value=\"" . base64_encode(get_permalink($enkeltpost)) . "\" />";
	}

echo '<input type="submit" value="' . __('S&oslash;k', 'inter-library-search-by-webloft') . '" />' . "\n";
echo "</form>";

?>
