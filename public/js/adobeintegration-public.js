(function( $ ) {
	//'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

jQuery(window).scroll(function() {
    
    if(jQuery('#adobintegration_media').is(':visible')) {
        
        if(jQuery('.media_container').length){
    
            if(jQuery(window).scrollTop() + jQuery(window).height() >= jQuery(document).height()) {
                
            	get_media(jQuery('.media_container'),true);	
        
            }
        
        }
    }

});


})( jQuery );

function ajax_loader()
{
	if(jQuery('body .overlay').length==0) 
	{
		jQuery('body').append('<div class="overlay"><img src="'+adobeintegration.plugin_url+'/'+adobeintegration.plugin_name+'/assets/images/loader.gif"></div>');
	}
	else
	{
		jQuery('body .overlay').remove();
	}	
}	 


function get_media(obj,infinteScroll)
{
    
    var id = jQuery(obj).data('id');
	var type = jQuery(obj).data('type');
	var name = jQuery(obj).data('name');
	var breadcrumb = jQuery(obj).data('breadcrumb');
	var paramlink = '';
 	var action='';
 	
 	if(breadcrumb=='yes'){
 	    adobeintegration.offset = 0;
 	}
 	
 	if(jQuery(obj).data('type')=='subcategory'){
 	    
		action = 'adobeintegration_api_get_subcategories';
		console.log(infinteScroll);
		if(infinteScroll==undefined){
		    paramlink = '<a href="javascript:void(0)" data-id="'+id+'" data-name="'+name+'" data-type="'+type+'" data-breadcrumb="yes" onclick="get_media(this)">'+name+'</a>';
		
		    jQuery('.adobintegration.breadcrumb').find('.categories-name-1').html(paramlink);
		    jQuery('.adobintegration.breadcrumb').find('.categories-name-2').html('');
		}   
 	}
 	else if(jQuery(obj).data('type')=='media')
 	{
 		action = 'adobeintegration_api_get_media';
 		
 		if(infinteScroll==undefined){
     		paramlink = '<a href="javascript:void(0)" data-id="'+id+'" data-name="'+name+'" data-type="'+type+'" data-breadcrumb="yes" onclick="get_media(this)">'+name+'</a>';
    		jQuery('.adobintegration.breadcrumb').find('.categories-name-2').html(' / '+paramlink);
 		}
 	}

	if(id)
	{
	    
		var _offset=(adobeintegration.offset)?adobeintegration.offset:0;		
		
		ajax_loader();			
		jQuery.ajax({
		  type: "POST",
		  url: adobeintegration.ajax_url+'?action='+action,
		  data: {'category_id':id,'offset':_offset},
		  cache: false,
		  success: function(result){

		     	var res_json_obj = JSON.parse(result);

		     	if(res_json_obj.status=='error'){
		     		alert(res_json_obj.msg);
		     		return;
		     	}

		     	if(_offset >= 20)
		     	{
		     	   if(breadcrumb=='yes')
		     	    {
		     	        jQuery('#adobintegration_media').empty().append(res_json_obj.data);
		     	    }
		     	    else
		     	    {
		     		    jQuery('#adobintegration_media').append(res_json_obj.data);
		     	    }
		     	}
		     	else
		     	{
		     	    
		     		jQuery('#adobintegration_media').empty().append(res_json_obj.data);
		     	}
		     	
		     	ajax_loader();
		  }

		});
	}
}


function create_adobe_product(obj)
{
 	action = 'adobeintegration_create_product';

 	if(jQuery(obj).data('name')) {

		ajax_loader();

		jQuery.ajax({
		  type: "POST",
		  url: adobeintegration.ajax_url+'?action='+action,
		  data: {'name':jQuery(obj).data('name'),'stock-id':jQuery(obj).data('stock-id'),'thumbnail_url':jQuery(obj).data('thumbnail_url')},
		  cache: false,
		  success: function(result){
		     	var res_json_obj = JSON.parse(result);

		     	if(res_json_obj.status=='error'){
		     		alert(res_json_obj.msg);
		     		return;
			    }

		     	if(res_json_obj.status=='success'){
		     		window.location.href=res_json_obj.data.product_url;
		     	}
		     	
		     	ajax_loader();
		  }

		});

		
	}
}