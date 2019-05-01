<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       crestinfosystems.com
 * @since      1.0.0
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/admin
 * @author     Crest Infosystems Pvt Ltd <admin@crestinfosystems.com>
 */
class Adobeintegration_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/adobeintegration-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/adobeintegration-admin.js', array( 'jquery' ), $this->version, false );

		
		wp_localize_script($this->plugin_name,$this->plugin_name, array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'plugin_name' =>$this->plugin_name,
					'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),            		
					'plugin_url' =>plugins_url(),
				) );

	}

	public function register_menus() {
        add_menu_page(
            __( 'Adobe Integration', 'adobeintegration'),
            __( 'Adobe Integration', 'adobeintegration'),
            'read',
            'adobeintegration',
            array( $this, 'adobeintegration_settings' ),
            plugin_dir_url( __FILE__ ) . '../assets/images/adobe-gray.png',
            9999
        );        

    }

    /**
     * Include admin option page
     */
    public function adobeintegration_settings() {
        //include_once( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-admin-display.php' );
        include_once( plugin_dir_path( __FILE__ ) . 'partials/adobeintegration-options.php' );
    }


	public function adobeintegration_add_options()
	{

	    $status = false;
	    $return_data = array();

		if(!isset($_POST['_wpnonce']) && wp_create_nonce( $this->plugin_name.'_add_options' )!= $_POST['_wpnonce']){

			$return_data = array('status'=>'success','msg'=>__('Settings could not be saved.',$this->plugin_name));	

		}
		else if(isset($_POST['adobeintegration']) && count($_POST['adobeintegration']) > 0)
	 	{
	 		foreach ($_POST['adobeintegration'] as $key => $value){
	 			update_option( $this->plugin_name .'_'. $key, $value);
	 			$status = true;
	 		}

	 	}

	 	if($status)
	  	{
	  		$return_data = array('status'=>'success','msg'=>__('Custom settings are saved.',$this->plugin_name));	
	  		
	  	}

		echo json_encode($return_data);
		wp_die(); 	
 	
    }

    // Revoke the Adobe API access token
	public function adobeintegration_delete_access_token()
	{
	    $status = false;
	    $return_data = array();
	    
	    check_ajax_referer( 'ajax_nonce', 'security' );
	    
	    //Fetch data from database
        $x_access_token =  get_option( $this->plugin_name .'_'.'access_token');
        $x_refresh_token =  get_option( $this->plugin_name .'__refresh_token');
        $x_generate_time =  get_option( $this->plugin_name .'_generated_time');
        
        //Delete table records
        $delete_access_token = delete_option( 'adobeintegration_access_token');
        $delete_refresh_token = delete_option( 'adobeintegration__refresh_token');
        $delete_generated_time = delete_option( 'adobeintegration_generated_time');
        
        //If deleted then send success message
        if($delete_access_token && $delete_refresh_token && $delete_generated_time ){
            $return_data = array('status'=>'success','msg'=>__('Access token is removed!',$this->plugin_name));
        }
        else{//If not deleted then re-save the old data
            
            update_option( $this->plugin_name .'_'.'access_token',$x_access_token);
            update_option( $this->plugin_name .'__refresh_token',$x_refresh_token);
            update_option( $this->plugin_name .'_generated_time',$x_generate_time);
            
            $return_data = array('status'=>'success','msg'=>__('Access token could not be remove!',$this->plugin_name));
        }
        
		echo json_encode($return_data);
		wp_die();
    }

}
