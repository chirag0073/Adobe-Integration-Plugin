<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       himanshu.u
 * @since      1.0.0
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/admin/partials
 */
if($_GET['success']){
?>
<div class="notice notice-success is-dismissible">
    <p><?php _e( 'Access token successfully generated!', 'Adobeintegration' ); ?></p>
</div>
<?php } ?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">   

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div id="ajax-response"></div>
    <h2><?php _e('Custom Settings','Adobeintegration');?></h2>
    <p><?php _e('Enter your Site Configurations below:','Adobeintegration');?></p>
    
    <form method="post" name="<?php echo $this->plugin_name;?>_options" id="<?php echo $this->plugin_name;?>_options" class="validate" novalidate="novalidate">        
		<?php

         $site_endpoint_url = get_option($this->plugin_name.'_site_endpoint_url');
         $description = get_option($this->plugin_name.'_description');
         $integration_name = get_option($this->plugin_name.'_integration_name');
 		 $api_key = get_option($this->plugin_name.'_api_key');
         $client_secret = get_option($this->plugin_name.'_client_secret');
         $access_token =  get_option( $this->plugin_name .'_'.'access_token');
         
        $temp=substr($access_token,0,30);
        for($i=0; $i<strlen($access_token)-30; $i++){
            $temp = $temp."*";
        }
        $access_token = $temp;
        
         
         wp_nonce_field($this->plugin_name.'_add_options');

		?>            
            <table class="form-table">
                <tbody>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="<?php echo $this->plugin_name; ?>-site_endpoint-url"><?php _e('Adobe API End Point','Adobeintegration');?>  <span class="description">(<?php _e('required',$this->plugin_name);?>)</span></label>
                        </th>
                        <td>                            
                            <input type="text" id="<?php echo $this->plugin_name; ?>-site_endpoint_url" name="<?php echo $this->plugin_name; ?>[site_endpoint_url]" placeholder="<?php esc_attr_e('https://', $this->plugin_name); ?>" required value="<?php echo $site_endpoint_url; ?>" aria-required="true" autocapitalize="none" autocorrect="off"   />                            

                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="<?php echo $this->plugin_name; ?>-integration_name"><?php _e('Integration Name',$this->plugin_name);?> <span class="description">(required)</span></label>
                        </th>
                        <td>                            
                            <input type="text" id="<?php  echo $this->plugin_name; ?>-integration_name" name="<?php  echo $this->plugin_name; ?>[integration_name]" placeholder="<?php _e('Enter Integration Name',$this->plugin_name);?>" required value="<?php  echo $integration_name ?>" aria-required="true" autocapitalize="none" autocorrect="off"   />
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="<?php echo $this->plugin_name; ?>-api_key"><?php _e('API Key',$this->plugin_name);?>  <span class="description">(required)</span></label>
                        </th>
                        <td>                            
                            <input type="text" id="<?php echo $this->plugin_name; ?>-api_key" name="<?php echo $this->plugin_name; ?>[api_key]" placeholder="<?php _e('Enter Api Key',$this->plugin_name);?>" required value="<?php echo $api_key; ?>" aria-required="true" autocapitalize="none" autocorrect="off"  />
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="<?php echo $this->plugin_name; ?>-client_secret"><?php _e('Client Secret',$this->plugin_name);?> <span class="description">(<?php _e('required',$this->plugin_name);?>)</span></label>
                        </th>
                        <td>                            
                            <input type="text" id="<?php echo $this->plugin_name; ?>-client_secret" name="<?php echo $this->plugin_name; ?>[client_secret]" placeholder="<?php _e('Client Secret',$this->plugin_name);?>"  value="<?php echo $client_secret; ?>"  autocapitalize="none" autocorrect="off"  />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row">
                            <label for="<?php echo $this->plugin_name; ?>-description"><?php _e('Description',$this->plugin_name);?> <span class="description"></span></label>
                        </th>
                        <td>                            
                            <input type="text" id="<?php echo $this->plugin_name; ?>-description" name="<?php echo $this->plugin_name; ?>[description]" placeholder="<?php _e('Description',$this->plugin_name);?>"  value="<?php echo $description; ?>"  autocapitalize="none" autocorrect="off"  />
                        </td>
                    </tr>
                 </tbody>
            </table>
        <?php submit_button(__('Save',$this->plugin_name), 'primary',$this->plugin_name.'_options_submit', TRUE); ?> 
    </form> 
    <table class="form-table">
        <tbody>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-access-token"><?php _e('Adobe API Access Token','Adobeintegration');?> </label>
                </th>
                <td>
                    <input type="text" id="<?php echo $this->plugin_name; ?>_access_token" name="<?php echo $this->plugin_name; ?>-access-token" placeholder="<?php esc_attr_e('Click on  below button to generate the access token.', $this->plugin_name); ?>" value="<?php echo $access_token; ?>" aria-required="true" autocapitalize="none" autocorrect="off" readonly />
                </td>
            </tr>
         </tbody>
    </table>
    <input type='hidden' value='<?php echo site_url(); ?>' name='<?php echo $this->plugin_name; ?>-site-url' id='<?php echo $this->plugin_name; ?>-site-url' />
    <?php
        $other_attributes = array( 'style' => 'width: 10%' );
        
        submit_button(__('Generate Access Token',$this->plugin_name), 'primary',$this->plugin_name.'_adobe_authorize', TRUE, $other_attributes);
        
        submit_button(__('Remove Access Token',$this->plugin_name), 'primary',$this->plugin_name.'_delete_access_token', TRUE, $other_attributes); ?>
</div>