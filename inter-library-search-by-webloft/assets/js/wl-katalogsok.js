var wl_ajax_request = null;

function _log(message){
  console.log(message);
}


jQuery(document).ready(function(){
  initSearchForm();
  initQuerySearch();
});


function initQuerySearch(){
  if ( jQuery('#wl-search').length ){
    var wl_query = jQuery.trim( jQuery('#wl-search').val() );
    if ( wl_query.length ){
      startAjaxSearch( true );
    }
  }
}


function initSearchForm(){
  jQuery('#wl-form').submit(
    function(e){
      e.preventDefault(); // prevent submit

      var query = jQuery.trim ( jQuery('#wl-search').val() );

      if ( jQuery(this).attr('action') ){
        var url = jQuery(this).attr('action');
        var library_id = jQuery.trim ( jQuery('#library_id').val() );
        var query_string = new Array();

        if ( query ){
          query_string.push( 'webloftsok_query='+query );
        }

        if ( library_id ){
          query_string.push( 'katalog='+library_id );
        }

        if ( query_string.length ){
          url += "?"+query_string.join('&');
        }

        window.location = url;
      }
      else{
        if ( query.length ){
          killAjaxRequest();
          hideAjaxSpinner();
          jQuery('.wl-tab-content').html(''); // empty results
          jQuery('#wl-form #pagination').val(1); // reset pagination
          resource_index = 0; // reset resource index
          showAjaxSpinner(); // show ajax spinner in wl-tab-content
          jQuery('.wl-resource-count-results .result-count').html(''); // reset item counts in tabs
          showFirstTab();
          startAjaxSearch( true ); // start new search
        }
      }
    }
  );
}


function killAjaxRequest(){
  if ( wl_ajax_request ){
    wl_ajax_request.abort();
  }
}

function disableForm(){
  jQuery('.wl-disable').prop('disabled', true);
}

function enabledForm(){
  jQuery('.wl-disable').prop('disabled', false);
}


var resource_index = 0;
function startAjaxSearch( recursive ){

    jQuery('.wl-resources').show();
    var search = jQuery.trim ( jQuery('#wl-search').val() );
    var ajaxurl = jQuery('#ajax-url').val();
    var resources = jQuery('#resources').val().split(',');

    if ( search.length && resources.length ){
      if ( typeof resources[resource_index] !== 'undefined' ){
        var data = {
          'action': 'wl_search',
          'query': jQuery('#wl-form').serialize(),
          'resource' : resources[resource_index]
        }

        showWlAjaxSpinner( resources[resource_index] );

        wl_ajax_request = jQuery.get( ajaxurl, data, function(response) {
          if ( resource_index == 0){
            hideAjaxSpinner();
            showFirstTab();
          }

          var result = jQuery.parseJSON(response);

          if ( typeof result.html !== 'undefined' ){
            jQuery('#tab-'+resources[resource_index]).html(result.html);
          }

          if ( typeof result.count !== 'undefined' ){
            jQuery('a.'+resources[resource_index]+ ' .wl-resource-count-results .result-count').html( "("+result.count+")" );
          }

          initPagination();
          hideWlAjaxSpinner();
          resource_index++;

          if ( recursive ){
            startAjaxSearch(resource_index);
          }
        });
      }
    }

    initTabNavigation();

}

function showFirstTab(){
  jQuery('.wl-resources .tabs li:first-child a').trigger('click');
}


function initTabNavigation(){
  jQuery('.wl-resources .tabs a').unbind();
  jQuery('.wl-resources .tabs a').click(function(){
    // set active status
    jQuery('.wl-resources .tabs ul li').removeClass('active');
    jQuery(this).parent().addClass('active')

    // hide tabs
    jQuery('.wl-tab-content').hide();

    // show current tab
    var resource = jQuery(this).attr('data-resource');
    jQuery('#tab-'+resource).show();
  });
}


function initPagination(){
  jQuery('.wl-pagination').unbind();
  jQuery('.wl-pagination').click(function(e){
    e.preventDefault();
    if ( pos = jQuery(this).attr('data-position') ){
      jQuery('#wl-form #pagination').val(pos);
      resource_index = 0;
      startAjaxSearch(false);
      scrollToContent();
    }
  });
}

function scrollToContent(){
  jQuery('html, body').animate({
      scrollTop: ( jQuery("#wl-content").offset().top - 50 )
  }, 500);
}


function showWlAjaxSpinner( resource ){
  hideWlAjaxSpinner();
  jQuery('.wl-resources .'+resource+' .wl-tab-spinner').show();
}

function hideWlAjaxSpinner(){
 jQuery('.wl-resources .wl-tab-spinner').hide();
}

function restResourceCounter(){
  jQuery('.wl-resource-count-results').html('');
}


function showAjaxSpinner(){
 jQuery('.wl-ajax-spinner').show();
}

function hideAjaxSpinner(){
 jQuery('.wl-ajax-spinner').hide();
}



function ilsfbShare (url, winWidth, winHeight){
    var winTop = (screen.height / 2) - (winHeight / 2);
    var winLeft = (screen.width / 2) - (winWidth / 2);
    window.open('http://www.facebook.com/sharer.php?u=' + url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
}


function showreglitreframeLoading (){
  document.getElementById('divreglitreframeFrameHolder').style.opacity = "0.2";
}



function initToggleSearchHints(){
  jQuery('._js-toggle-search-info').click(function(){
    jQuery('.search-info').toggle();
    return false;
  })
}

jQuery(document).ready(function(){
  initToggleSearchHints();
});