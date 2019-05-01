<?php

if (!defined('ABSPATH')) {
    exit;
}

trait UtilsAdobeintegration
{	
	var $logging = true;
	public function date_formatter($date='',$format='d/m/Y')
	{
		global $timezone;

	    if($date)
	    {
	      $date = str_replace(' ' , '', $date);
	      $date = new DateTime($date, new DateTimeZone($timezone));
	      return  $date->format($format);         
	    }

	    return;
	}    	

	public function handleRequestError($responseCode) {

        switch ($responseCode) {

            case "1000":
                $msg = "Servicio no disponible.";
                break;

            case "1003":
                $msg = "La identificación del token no existe.";
                break;
            
            case "1004":
                $msg = "Servicio no disponible.";
                break;

            default:                
                $msg = "Servicio no disponible.";
                break;            
        }

        return $msg;
    }
  

    public function write_log($email, $log)
    {
        if($this->logging==false)
        {
        	return;
        }

        $path = plugin_dir_path( __DIR__) ."includes/logs/";        
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, true);
        }
        
        if($email)
		{			
        	$logfile = $path.$email."_".date("Ymd").".log";
        	file_put_contents($logfile, $log.PHP_EOL, FILE_APPEND | LOCK_EX);	
		}	
        
        return;
    }


    public function api_request($params, $api, $method = 'GET')
    {   
        $ch = curl_init();

        // if(array_key_exists('api_endpoint', $params))
        // {            
        //     $this->api_endpoint = $params['api_endpoint'];
        //     unset($params['api_endpoint']);
        // }

        // if(array_key_exists('username', $params))
        // {
        //     $username = $params['username'];
        // }else
        // {
        //     $username =  get_option( $this->plugin_name .'_'.'username');    
        // }

        // if(array_key_exists('password', $params))
        // {
        //     $password = $params['password'];
        // }else
        // {
        //     $password =  get_option( $this->plugin_name .'_'.'password');            
        // }


        $x_api_key =  get_option( $this->plugin_name .'_'.'api_key');
        $x_product =  get_option( $this->plugin_name .'_'.'client_secret');
        $x_access_token =  get_option( $this->plugin_name .'_'.'access_token');
        $x_refresh_token =  get_option( $this->plugin_name .'__refresh_token');
        $x_generate_time =  get_option( $this->plugin_name .'_generated_time');
        $current_time = strtotime(date('Y-m-d H:i:s'));
        
        $diff_seconds = $current_time - $x_generate_time;
        
        if($diff_seconds >= 79200){ //call when diff is more then 22hours
        
            $absUrl = 'https://ims-na1.adobelogin.com/ims/token/v1';
            $params = array(
                        //'locale'=>'en_US',
                        'grant_type'=>'refresh_token',
                        'client_id'=>$x_api_key,
                        'client_secret'=>$x_product,
                        'refresh_token'=>$x_refresh_token
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
                $x_access_token = $contentData['access_token'];
            }
            if ($contentData['refresh_token'])
                update_option( 'adobeintegration__refresh_token',$contentData['refresh_token']);
            
            update_option( $this->plugin_name .'_generated_time',$created_time);
            
        }

        

        $absUrl = $this->api_endpoint.'/';
        $absUrl .= $api;

        switch ($method){

          case "POST":
             curl_setopt($curl, CURLOPT_POST, 1);
             if ($params)
             {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
             }                
             break;

          case "PUT":
             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
             if ($params)
             {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);                              
             }             
             break;

          default:
             if ($params){
                    if( strpos($absUrl , '?') !== false)
                    {
                      $absUrl = sprintf("%s&%s", $absUrl, http_build_query($params));  
                    }
                    else
                    {
                        $absUrl = sprintf("%s?%s", $absUrl, http_build_query($params));    
                    }
                    
            }
        }       
                                  
        curl_setopt($ch, CURLOPT_URL, $absUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-api-key:'.$x_api_key,
            'x-product:'.$x_product,
            'Authorization: Bearer '.$x_access_token,
            'Content-Type: application/json; charset=UTF-8',            
            'Cache-Control: no-cache'
            
            )            
        );

        $result = curl_exec($ch);        
        // Check if any error occurred
        if(curl_errno($ch))
        {
            echo 'Curl error: ' . curl_error($ch);
            wp_die();
        }

        curl_close($ch);
        
        //return json_decode($result);
        return ($result);
    }
   
}

// class MyHelloWorld{
//     use Utils;
// }

// $o = new MyHelloWorld();

// $option['to_mail'] = 'himanshu.u@crestinfosystems.com';
// $option['order_id'] = 'abc_123456';
// $option['amount'] = '100.00';
// $option['next_payment_date'] = '29/06/2018';
// $option['cancel_date'] = '29/06/2018';

//  $o->send_mail('payment_subscription_cancel_request',$option);

