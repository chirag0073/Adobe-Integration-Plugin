(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	 var wpAjax = jQuery.extend( {
	unserialize: function( s ) {
		var r = {}, q, pp, i, p;
		if ( !s ) { return r; }
		q = s.split('?'); if ( q[1] ) { s = q[1]; }
		pp = s.split('&');
		for ( i in pp ) {
			if ( jQuery.isFunction(pp.hasOwnProperty) && !pp.hasOwnProperty(i) ) { continue; }
			p = pp[i].split('=');
			r[p[0]] = p[1];
		}
		return r;
	},
	parseAjaxResponse: function( x, r, e ) { // 1 = good, 0 = strange (bad data?), -1 = you lack permission
		var parsed = {}, re = jQuery('#' + r).empty(), err = '';

		if ( x && typeof x == 'object' && x.getElementsByTagName('wp_ajax') ) {
			parsed.responses = [];
			parsed.errors = false;
			jQuery('response', x).each( function() {
				var th = jQuery(this), child = jQuery(this.firstChild), response;
				response = { action: th.attr('action'), what: child.get(0).nodeName, id: child.attr('id'), oldId: child.attr('old_id'), position: child.attr('position') };
				response.data = jQuery( 'response_data', child ).text();
				response.supplemental = {};
				if ( !jQuery( 'supplemental', child ).children().each( function() {
					response.supplemental[this.nodeName] = jQuery(this).text();
				} ).length ) { response.supplemental = false; }
				response.errors = [];
				if ( !jQuery('wp_error', child).each( function() {
					var code = jQuery(this).attr('code'), anError, errorData, formField;
					anError = { code: code, message: this.firstChild.nodeValue, data: false };
					errorData = jQuery('wp_error_data[code="' + code + '"]', x);
					if ( errorData ) { anError.data = errorData.get(); }
					formField = jQuery( 'form-field', errorData ).text();
					if ( formField ) { code = formField; }
					if ( e ) { wpAjax.invalidateForm( jQuery('#' + e + ' :input[name="' + code + '"]' ).parents('.form-field:first') ); }
					err += '<p>' + anError.message + '</p>';
					response.errors.push( anError );
					parsed.errors = true;
				} ).length ) { response.errors = false; }
				parsed.responses.push( response );
			} );
			if ( err.length ) { re.html( '<div class="error">' + err + '</div>' ); }
			return parsed;
		}
		if ( isNaN(x) ) { return !re.html('<div class="error"><p>' + x + '</p></div>'); }
		x = parseInt(x,10);
		if ( -1 == x ) { return !re.html('<div class="error"><p>' + wpAjax.noPerm + '</p></div>'); }
		else if ( 0 === x ) { return !re.html('<div class="error"><p>' + wpAjax.broken  + '</p></div>'); }
		return true;
	},
	invalidateForm: function ( selector ) {
		return jQuery( selector ).addClass( 'form-invalid' ).find('input').one( 'change wp-check-valid-field', function() { jQuery(this).closest('.form-invalid').removeClass( 'form-invalid' ); } );
	},
	validateForm: function( selector ) {
		selector = jQuery( selector );
		return !wpAjax.invalidateForm( selector.find('.form-required').filter( function() { return jQuery('input:visible', this).val() === ''; } ) ).length;
	}
}, wpAjax || { noPerm: 'Sorry, you are not allowed to do that.', broken: 'Something went wrong.' } );

	jQuery(document).ready( function($){

		$('#adobeintegration_options.validate').submit( function(event) { 
				event.preventDefault();				
				var response = wpAjax.validateForm( $(this) ); 
				if(response)
				{
					ajax_loader();			
					$.ajax({
					  type: "POST",
					  url: adobeintegration.ajax_url+'?action=adobeintegration_add_options',
					  data: $(this).serialize(),
					  cache: false,
					  success: function(result){		     	
					     	var res_json_obj = JSON.parse(result);		     				     	
					     	alert(res_json_obj.msg);
					     	ajax_loader();			     	
					  }

					});
				}
		});
		
		$(document).on('click','#adobeintegration_adobe_authorize',function(){
		    
		    var url = 'https://ims-na1.adobelogin.com/ims/authorize';
		    var clientID = $('#adobeintegration-api_key').val();
		    var redirectURI = $('#adobeintegration-site-url').val()+'/?proc_access=1';
		    ajax_loader();
            location.href = url+'?client_id='+clientID+'&redirect_uri='+redirectURI+'&scope=openid&response_type=code';

		});
		
		//Check for access token is available or not to hide/show relevent buttons
		if($('#adobeintegration_access_token').val() !== ''){
		    $('#adobeintegration_adobe_authorize').parent().addClass('adobeintegration_adobe_authorize').hide();
		    $('#adobeintegration_delete_access_token').parent().addClass('adobeintegration_delete_access_token').show();
		}
		else{
		    $('#adobeintegration_adobe_authorize').parent().addClass('adobeintegration_adobe_authorize').show();
		    $('#adobeintegration_delete_access_token').parent().addClass('adobeintegration_delete_access_token').hide();
		}
		
		$(document).on('click','#adobeintegration_delete_access_token',function(){
		        ajax_loader();			
				$.ajax({
				  type: "POST",
				  url: adobeintegration.ajax_url+'?action=adobeintegration_delete_access_token',
			      data: 'security='+adobeintegration.ajax_nonce,
				  cache: false,
				  success: function(result){		     	
				     	var res_json_obj = JSON.parse(result);
				     	alert(res_json_obj.msg);
				     	
            		    $('.adobeintegration_adobe_authorize').show();
            		    $('.adobeintegration_delete_access_token').hide();
            		    $('#adobeintegration_access_token').val('');
				     	ajax_loader();
				  }
				});
		});

	});

	function ajax_loader()
	{

		if($('body .overlay').length==0) 
		{
			$('body').append('<div class="overlay"><img src="'+adobeintegration.plugin_url+'/'+adobeintegration.plugin_name+'/assets/images/ajax_loader-2.gif"></div>');
		}else
		{
			$('body .overlay').remove();
		}	

	}


})( jQuery );
