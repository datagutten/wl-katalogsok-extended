// Shortcode generator

jQuery(document).ready(function() {
  initCatalogFormListener();
});


function initCatalogFormListener(){
  jQuery("#wlkatalogshortcodeform").change(function() {
    var attributes = new Array();

    attributes['library_id']        = jQuery('select[name="wl_katalogsok_option_mittbibliotek"]').val();
    attributes['target_page']       = jQuery('select[name="wl_katalogsok_option_target_page"]').val();
    attributes['results_per_page']  = jQuery('select[name="wl_katalogsok_option_treffperside"]').val();
    attributes['show_images']       = jQuery('#wl_katalogsok_option_hamedbilder').prop('checked') ? 1 : 0;

    var resources = jQuery('.wl-search-resource:checked');

    if ( resources.length &&  resources.length < jQuery('.wl-search-resource').length  ){
      attributes['resources'] = new Array();
      jQuery.each(resources, function(index, element) {
        attributes['resources'][index] = element.value;
      });
    }

    // jQuery("#wlkatalogferdigshortcode").html('[wl-ils mittbibliotek="' + library_id + '" treffperside="' + results_per_page + '" hamedbilder="' + show_images + '"]');
    if (  attributes['library_id'].length && attributes['library_id'] != 'NULL' ){
      jQuery("#shortcode-content").html('[wl-ils '+buildShortcodeAttributes(attributes)+']');
      jQuery("#shortcode-explanation").hide();
    }
    else{
      jQuery("#shortcode-content").html('');
      jQuery("#shortcode-explanation").show();
    }
  });
}


function buildShortcodeAttributes( attributes ){
  var string = '';
  for (var name in attributes) {
    string += ' '+name+'="'+attributes[name]+'" ';
  }

  return string;
}



jQuery.fn.selectText = function(){
  var doc = document
      , element = this[0]
      , range, selection
  ;
  if (doc.body.createTextRange) {
    range = document.body.createTextRange();
    range.moveToElementText(element);
    range.select();
  }
  else if (window.getSelection) {
    selection = window.getSelection();
    range = document.createRange();
    range.selectNodeContents(element);
    selection.removeAllRanges();
    selection.addRange(range);
  }
};

jQuery(function() {
  jQuery('#wlkatalogferdigshortcode').click(function() {
      jQuery('#wlkatalogferdigshortcode').selectText();
  });
});

// End shortcode generator
