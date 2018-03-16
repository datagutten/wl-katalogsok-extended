jQuery(document).ready(function() {
  jQuery('.tabs > ul a').on('click', function(e)  {
    var currentAttrValue = jQuery(this).attr('href');

    // Show/Hide Tabs
    jQuery('.tabs ' + currentAttrValue).show().siblings().hide();

    // Change/remove current tab to active
    jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

    // Resize iframe
    setIFramHeight();

    e.preventDefault();
  });
});

function setIFramHeight(){
  jQuery('#ils_results_frame').iframeHeight({
    debugMode: false,
    defaultHeight: 400,
    minimumHeight: 400
  });
}

jQuery(document).ready(function() {

// Laste in taber og sende med query string og s&aring;nt

//  jQuery.get('http://mars14.sundaune.no', function(data) {
//    jQuery('#tab3').html(data);
//  });
});
