<?php

// Hent alle innlegg (og sider samtidig?)
// Lagre id og tittel der hvor [wl-ils] forekommer

$args = array(
'post_type' => array('post' , 'page'),
'posts_per_page' => '-1'
    );

if ($post_query = get_posts($args)) {

	$hits = array();
	foreach ($post_query as $post) {
	    if ((stripos($post->post_content, '[wl-ils') !== false ) && (stripos($post->post_content, '[wl-ils-enkeltpost') === false )) { // den f&oslash;rste kortkoden finnes men det er ikke den andre!!
			$hits[] = $post->ID . "|x|" . $post->post_title;
		}
	}
	if (is_array($hits)) { // vi har minst ett treff
		echo "<p>\n";
		echo "<label for=\"" . $this->get_field_id( 'title' ) . "\">" . __('Tittel:', 'inter-library-search-by-webloft') . "\n";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . $title . "\" />\n";
		echo "</label>\n";
		echo "</p>\n\n";

		echo "<p>\n";
		echo "<label for=\"" . $this->get_field_id('resultatside') . "\">" . __('Hvilken bibliotekkatalog vil du s&oslash;ke i?', 'inter-library-search-by-webloft') . "</label>\n";
		echo "<select name=\"" . $this->get_field_name('katalog') . "\" id=\"" . $this->get_field_id('katalog') . "\" class=\"widefat\">\n";
		include (  getConfigPath("library_list.php") );
		foreach ($libraries as $library_id => $library) {
			printf ('<option value="%s" %s>%s</option>', $library_id, selected($katalog, $library_id, false), $library['name'] );
		}

		echo "</select>\n";
		echo "</p>\n";

		echo "<p>\n";
		echo "<label for=\"" . $this->get_field_id('resultatside') . "\">" . __('Hvilken side vil du vise trefflisten p&aring;?', 'inter-library-search-by-webloft') . "</label>\n";
		echo "<select name=\"" . $this->get_field_name('resultatside') . "\" id=\"" . $this->get_field_id('resultatside') . "\" class=\" widefat\">\n";

		foreach ($hits as $hit) {
			$digg = '';
			list ($id , $tittel) = explode ("|x|" , $hit);
			if ($id == $resultatside) { $digg = " selected"; }
			echo "<option value=\"" . $id . "\"" . $digg . ">" . $tittel . "</option>\n";
		}

		echo "</select>\n";
		echo "</p>\n";
	} else {
		echo __("Du m&aring; sette inn kortkoden [wl-ils] i et innlegg eller p&aring; en side f&oslash;rst!", 'inter-library-search-by-webloft');
	}

}

?>
