<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       crestinfosystems.com
 * @since      1.0.0
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/public
 * @author     Crest Infosystems Pvt Ltd <admin@crestinfosystems.com>
 */
class Adobeintegration_Public {
	use UtilsAdobeintegration;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version,$api_endpoint,$transient_expiration  ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;		
		$this->api_endpoint=$api_endpoint;
		$this->transient_expiration = $transient_expiration;		
		$this->page_limit = 20;
		$this->product_from_price = 100;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Adobeintegration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Adobeintegration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/adobeintegration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Adobeintegration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Adobeintegration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/adobeintegration-public.js', array( 'jquery' ),time() , false ); //$this->version

		wp_localize_script($this->plugin_name,$this->plugin_name, array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'plugin_name' =>$this->plugin_name,
					'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),           
					'plugin_url' =>plugins_url(),
					'offset'=>0,
					'lib_offset'=>0,
				) );

	}


	public function register_shortcode()
	{   
		add_shortcode( 'adobeintegration_product', array( $this, 'adobeintegration_shortcode' ) );
	}


	public function adobeintegration_shortcode( $atts )
	{	
	    ob_start();		
	
		if($atts['type']=='product_cat'){
		    
		    $this->adobeintegration_api_get_catgories();
		}	
	
		if($atts['type']=='product_lib_cat'){
		    //echo '134line';
		    $this->adobeintegration_api_get_lib_media();
		}
		
		return ob_get_clean();
		
	}


	public function adobeintegration_api_get_catgories()
	{
			$params = array();

			if ( false === ( $value = get_transient( $this->plugin_name.'_api_get_catgories') ) )
			{
				$contentRes = $this->api_request($params, 'Rest/Media/1/Search/CategoryTree', 'GET');
				set_transient( $this->plugin_name.'_api_get_catgories', $contentRes, $this->transient_expiration);	
			}
			else
			{
				$contentRes = get_transient($this->plugin_name.'_api_get_catgories');
			}
		   
		    if($contentRes)
		  	{
		  		include( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-public-categories.php' );
		  		
		  	}
		  	else
		  	{
		  		echo 'Data could not be found!';	
		  	}
	} 


	public function adobeintegration_api_get_subcategories()
	{
			$params = array();

			if ( false === ( $value = get_transient( $this->plugin_name.'_api_get_subcatgories_'.$_POST['category_id']) ) )
			{
				$contentRes = $this->api_request($params, '/Rest/Media/1/Search/CategoryTree?category_id='.$_POST['category_id'], 'GET');
				set_transient( $this->plugin_name.'_api_get_subcatgories_'.$_POST['category_id'], $contentRes, $this->transient_expiration);	
			}
			else
			{
				$contentRes = get_transient($this->plugin_name.'_api_get_subcatgories_'.$_POST['category_id']);
			}
		   
		    if($contentRes)
		  	{	ob_start();
		  		include( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-public-subcategories.php' );
		  		$content = ob_get_clean();
		  	}
		  	else
		  	{
		  		$return_data = array('status'=>'error','msg'=>__('Data could not be found!',$this->plugin_name));	
		  	}

			if($content)
		  	{
		  		$return_data = array('status'=>'success','msg'=>__('Data is avilable.',$this->plugin_name),'data'=>$content);	
		  		
		  	}

		    wp_die(json_encode($return_data));
	}

	public function adobeintegration_api_get_media()
	{		
			$page_limit= $this->page_limit;
			if(isset($_POST['limit'])){
				$page_limit = $_POST['limit'];
			}

			$params = array(
						'locale'=>'en_US',
						'search_parameters[category]'=>$_POST['category_id'],
						'search_parameters[limit]'=>$page_limit,
						'search_parameters[offset]'=>$_POST['offset']
						);

			  if ( false === ( $value = get_transient( $this->plugin_name.'_api_get_media_'.$_POST['category_id'].'_'.$_POST['offset']) ) )
			  {
				$contentRes = $this->api_request($params, '/Rest/Media/1/Search/Files', 'GET');
				set_transient( $this->plugin_name.'_api_get_media_'.$_POST['category_id'].'_'.$_POST['offset'], $contentRes, $this->transient_expiration);	
			  }
			  else
			  {
			  	$contentRes = get_transient($this->plugin_name.'_api_get_media_'.$_POST['category_id'].'_'.$_POST['offset']);
			  }
		   
		    if($contentRes)
		  	{	
		  		ob_start();
		  		include( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-public-media.php' );
		  		$content = ob_get_clean();
		  	}
		  	else
		  	{
		  		$return_data = array('status'=>'error','msg'=>__('Data could not be found!',$this->plugin_name));	
		  	}

			if($content)
		  	{
		  		$return_data = array('status'=>'success','msg'=>__('Data is avilable.',$this->plugin_name),'data'=>$content);	
		  		
		  	}

		    wp_die(json_encode($return_data));
	}
    
    //Function to get the library history images from user account
	public function adobeintegration_api_get_lib_media()
	{
			$page_limit= $this->page_limit;
			$post_offset = (isset($_POST['offset']) && !empty($_POST['offset']))?$_POST['offset']:0;

			$params = array(
						'locale'=>'en_US',
						'result_columns[]'=>'thumbnail_1000_url',
						//'result_columns[]'=>'thumbnail_1000_height',
						//'result_columns[]'=>'thumbnail_1000_width',
						'search_parameters[limit]'=>$page_limit,
						'search_parameters[offset]'=>$post_offset
						);

			  if ( false === ( $value = get_transient( $this->plugin_name.'_api_get_lib_media_'.$post_offset) ) )
			  {
				$contentRes = $this->api_request($params, '/Rest/Libraries/1/Member/LicenseHistory', 'GET');
				set_transient( $this->plugin_name.'_api_get_media_'.$post_offset, $contentRes, $this->transient_expiration);	
			  }
			  else
			  {
			  	$contentRes = get_transient($this->plugin_name.'_api_get_lib_media_'.$post_offset);
			  }
		   	
			$contentData1 = json_decode($contentRes,true);
			/*echo "<pre>272";
			print_r($contentData);die;*/
			
		    if(!empty($contentData1['files']))
		  	{
		  		//include( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-public-categories.php' );
		  		include( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-public-lib-media.php' );
		  		
		  	}
		  	else if(!empty($contentData1['message'])){
		  	    echo $contentData1['message'];
		  	}
		  	    
		  	else
		  	{
		  		echo 'Data could not be found!';	
		  	}
	}

	public function adobeintegration_create_product()
	{		$return_data = array();

			$cat_id = $this->check_and_create_prod_cat();

		    if(isset($_POST['stock-id']) && $_POST['stock-id']!='')
		  	{
		  		$objProduct = new WC_Product();

		  		$name = explode('.',$_POST['name']);
		  		$name = $name[0];

		  		//$product = get_page_by_title( $name, OBJECT, 'product' );
		  		$product = get_active_page_by_title($name, OBJECT,'product');

		  		if(isset($product->ID))
		  		{	
		  			$return_data = array('status'=>'success','msg'=>__('Product has been created.',$this->plugin_name),'data'=>array('product_url'=>$product->guid));
		  			
		  			wp_die(json_encode($return_data));
		  		}

		  		$objProduct->set_name($name); //Set product name.
				$objProduct->set_status('publish'); //Set product status.
				$objProduct->set_featured(TRUE); //Set if the product is featured.                          | bool
				$objProduct->set_catalog_visibility('visible'); //Set catalog visibility.                   | string $visibility Options: 'hidden', 'visible', 'search' and 'catalog'.
				//$objProduct->set_description('Set product description'); //Set product description.
				$objProduct->set_short_description($_POST['name']); //Set product short description.
				$sku = 'A-'.date('his').substr(str_shuffle("0123456789"), 0, 2);
				$objProduct->set_sku($sku); //Set SKU

				$objProduct->set_price(100.00); //Set the product's active price.
				$objProduct->set_regular_price(100.00); //Set the product's regular price.
				//$objProduct->set_sale_price(); //Set the product's sale price.
				//$objProduct->set_date_on_sale_from(); //Set date on sale from.                              | string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
				//$objProduct->set_date_on_sale_to();//Set date on sale to.                                   | string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.

				$objProduct->set_manage_stock(FALSE); //Set if product manage stock.                         | bool
				//$objProduct->set_stock_quantity(10); //Set number of items available for sale.
				$objProduct->set_stock_status('instock'); //Set stock status.                               | string $status 'instock', 'outofstock' and 'onbackorder'
				//$objProduct->set_backorders('no'); //Set backorders.                                        | string $backorders Options: 'yes', 'no' or 'notify'.
				$objProduct->set_sold_individually(TRUE); //Set if should be sold individually.            | bool

				//$objProduct->set_weight(); //Set the product's weight.
				//$objProduct->set_length(); //Set the product length.
				//$objProduct->set_width(); //Set the product width.
				//$objProduct->set_height(); //Set the product height.

				//$objProduct->set_upsell_ids($upsell_ids); //Set upsell IDs.                               | array $upsell_ids IDs from the up-sell products.
				//$objProduct->set_cross_sell_ids($cross_sell_ids); //Set crosssell IDs.                    | array $cross_sell_ids IDs from the cross-sell products.

				$objProduct->set_reviews_allowed(TRUE); //Set if reviews is allowed.                        | bool

				//$objProduct->set_purchase_note($purchase_note); //Set purchase note.                      | string $purchase_note Purchase note.


				// $attribute = new WC_Product_Attribute();
				// $attribute->set_id(wc_attribute_taxonomy_id_by_name('pa_color')); //if passing the attribute name to get the ID
				// $attribute->set_name('pa_color'); //attribute name
				// $attribute->set_options('red'); // attribute value
				// $attribute->set_position(1); //attribute display order
				// $attribute->set_visible(1); //attribute visiblity
				// $attribute->set_variation(0);//to use this attribute as varint or not

				// $raw_attributes[] = $attribute; //<--- storing the attribute in an array

				// $attribute = new WC_Product_Attribute();
				// $attribute->set_id(25);
				// $attribute->set_name('pa_size');
				// $attribute->set_options('XL');
				// $attribute->set_position(2);
				// $attribute->set_visible(1);
				// $attribute->set_variation(0);

				// $raw_attributes[] = $attribute; //<--- storing the attribute in an array

				// $objProduct->set_attributes($raw_attributes); //Set product attributes.                   | array $raw_attributes Array of WC_Product_Attribute objects.				

				$objProduct->set_category_ids(array($cat_id)); //Set the product categories.                   | array $term_ids List of terms IDs.
				//$objProduct->set_tag_ids($term_ids); //Set the product tags.                              | array $term_ids List of terms IDs.

				//$objProduct->set_image_id(); //Set main image ID.                                         | int|string $image_id Product image id.
				//$objProduct->set_gallery_image_ids(); //Set gallery attachment ids.                       | array $image_ids List of image ids.

				$new_product_id = $objProduct->save(); //Saving the data to create new product, it will return product ID.
				if($new_product_id){

					$product_url = get_permalink( $new_product_id );

					if($_POST['thumbnail_url']){
						
						//$image_id  = $this->upload_image($_POST['name'],$_POST['thumbnail_url'], $new_product_id);
						$image_id  = $this->upload_image($_POST['name'], $_POST['thumbnail_url'], $new_product_id);
						
						if(isset($image_id['status']) && $image_id['status']=='error'){

							$return_data = array('status'=>'error','msg'=>__('Product has been created but issue in image upload.',$this->plugin_name),'data'=>array('product_id'=>$new_product_id,'product_url'=>$product_url));	
							wp_die(json_encode($return_data));
						}
						else
						{
							// $objProduct = new WC_Product($new_product_id);
							// $objProduct->set_image_id($image_id);
							// $objProduct->save();
						}
					}
					
					$return_data = array('status'=>'success','msg'=>__('Product has been created.',$this->plugin_name),'data'=>array('product_id'=>$new_product_id,'product_url'=>$product_url));
				}

		  	}
		  	else
		  	{
		  		$return_data = array('status'=>'error','msg'=>__('Product could not be created.',$this->plugin_name));	
		  	}

		    wp_die(json_encode($return_data));
	}

	public function check_and_create_prod_cat()
	{
		$category = get_term_by( 'slug', 'adobe-stock', 'product_cat' );
		$cat_id = $category->term_id;
		if($cat_id)
		{
			return $cat_id;
		}

		if(empty($cat_id)){
			$data = array('name' => 'Adobe Stock','description' => 'Adobe Stock Collections','slug' => 'adobe-stock','parent' =>0);

			$cid = wp_insert_term(
		        $data['name'], // the term 
		        'product_cat', // the taxonomy
		        array(
		            'description'=> $data['description'],
		            'slug' => $data['slug'],
		            'parent' => $data['parent']
		        )
	    	);

	    	return $cat_id = isset( $cid['term_id'] ) ? $cid['term_id'] : 0;
    	}
	}


	function upload_image($filename=null, $url = null, $post_id = null, $post_data = array() ) {

        if ( !$url || !$post_id  || !$filename) return new WP_Error('missing', "Need a valid URL and Post ID and Name...");

		// required libraries for media_handle_sideload
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp

        $tmp = download_url( $url );     
        
        if ( is_wp_error( $tmp ) ) { 				// If error storing temporarily, unlink

            @unlink($file_array['tmp_name']);   	// clean up
            $file_array['tmp_name'] = '';
            return array('status'=>'error','msg'=>$tmp[0]); // output wp_error
        }
     
        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);    // fix file filename for query strings

        $url_filename = basename($matches[0]);                                                  // extract filename from url for title
        $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)

	 	// override filename if given, reconstruct server path
	    if ( !empty( $filename ) ) {

	        $filename = sanitize_file_name($filename);
	        $tmppath = pathinfo( $tmp );                                                        // extract path parts
	        $new = $tmppath['dirname'] . "/". $filename . "." . $tmppath['extension'];          // build new path
	        rename($tmp, $new);                                                                 // renames temp file on server
	        $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
	    }
     
        // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
        $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file
 
        //$file_array['name'] =  $url_filename;
        if ( !empty( $filename ) ) {
        	$file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
	    } else {
	        $file_array['name'] = $url_filename;                                                // just use original URL filename
	    }
     
        // set additional wp_posts columns
        if ( empty( $post_data['post_title'] ) ) {
            //$post_data['post_title'] = basename($url_filename, "." . $url_type['ext']);         // just use the original filename (no extension)
            $post_data['post_title'] = $filename;
        }
     
        // make sure gets tied to parent
        if ( empty( $post_data['post_parent'] ) ) {
            $post_data['post_parent'] = $post_id;
        }
     
        // do the validation and storage stuff
        $att_id = media_handle_sideload( $file_array, $post_id, null, $post_data );             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status
     
        // If error storing permanently, unlink
        if ( is_wp_error($att_id) ) {

            @unlink($file_array['tmp_name']);   // clean up
	        $error = $media->get_error_messages();
	        return array('status'=>'error','msg'=>$error[0]);            
        }
     
        // set as post thumbnail if desired
        set_post_thumbnail($post_id, $att_id);     
        return $att_id;
	}
	
	//To get active page using title
    function get_active_page_by_title($page_title, $output = OBJECT,$pate_type)
    {
    	global $wpdb;
        $resuls =  $wpdb->get_results($wpdb->prepare( "SELECT ID,guid FROM $wpdb->posts WHERE post_title = %s AND post_type= %s AND post_status= 'publish'", $page_title,$pate_type ) ,  OBJECT );

        if ( $resuls )
            return $resuls[0];

        return null;
    }

    //Function to generate the Auth code and access token from adobe stock.
    public function auth_process(){
        
    	if($_REQUEST['proc_access'] && $_REQUEST['code'] && !empty($_REQUEST['code'])){
    	    
    	    $ch = curl_init();
    	    $auth_code = $_REQUEST['code'];
    	    $x_api_key =  get_option( $this->plugin_name .'_'.'api_key');
            $x_product =  get_option( $this->plugin_name .'_'.'client_secret');
            
    	    $absUrl = 'https://ims-na1.adobelogin.com/ims/token/v1';

            $params = array(
                        'locale'=>'en_US',
                        'grant_type'=>'authorization_code',
                        'client_id'=>$x_api_key,
                        'client_secret'=>$x_product,
                        'code'=>$auth_code
                        );
         
            curl_setopt($ch, CURLOPT_URL, $absUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $result = curl_exec($ch);        
            // Check if any error occurred
            if(curl_errno($ch))
            {
                echo 'Curl error: ' . curl_error($ch);
                wp_die();
            }
        
            curl_close($ch);
            $contentData = json_decode($result,true);
            $created_time = strtotime(date('Y-m-d H:i:s'));
            
            if ($contentData['access_token']){
                update_option( 'adobeintegration_access_token',$contentData['access_token']);
            }
            if ($contentData['refresh_token'])
                update_option( 'adobeintegration__refresh_token',$contentData['refresh_token']);
            update_option( $this->plugin_name .'_generated_time',$created_time);
            
            $redirect_url = '/wp-admin/admin.php?page=adobeintegration&success=true';
            wp_redirect( $redirect_url );
            exit;
            
    	}
    }

}