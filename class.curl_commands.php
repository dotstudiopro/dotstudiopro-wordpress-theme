<?php

class DotStudioz_Commands{
	
	public $country;

function curl_command($command, $args = array()){
	
	global $wpdb, $post;
	
	$api_key = get_option('ds_api_key'); 
	
	if(!$api_key || strlen($api_key) < 1){
				
		return array();
		
	}
	
		
	if($command == "token"){
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://api.myspotlight.tv/token", // This will change to the live URL at some point
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"key\"\r\n\r\n".$api_key."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		

		curl_close($curl);

		if ($err) {
			
			// Maybe log this somewhere?			
			return false;
			
		} else {
			$r = json_decode($response);
			
			if($r->success){
				
				return $r->token;
				
			} else {
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	} else if($command == 'country'){
		
		/** DEV MODE **/
		
		$dev_check = get_option("ds_development_check");
		
		$dev_country = get_option("ds_development_country");
		
		if($dev_check){
		
			$this->country = $dev_country;
								
			return $this->country;
					
		}
		
		/** END DEV MODE **/
		
		
		
		$token = get_option('ds_curl_token');
				
		if(!$token){
			
			return false;
			
		}
				
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://api.myspotlight.tv/country",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			$error = "cURL Error: $err";
		
		} else {
			$r = json_decode($response);
			
			
						
			if($r->success){
								
				$this->country = $r->data->countryCode;
								
				return $this->country;
				
				
				
			} else {
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	} else if($command == 'all-channels'){
		
		$token = get_option('ds_curl_token');
		
		ds_get_country();
		
		if(!$token || !$this->country){
						
			return array();
			
		}
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://api.myspotlight.tv/channels/".$this->country."?detail=partial",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...
		
		} else {
			$r = json_decode($response);
			
			if($r->success){
												
				return $r->channels;
				
			} else {
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	}  else if($command == 'single-channel'){
				
		$token = get_option('ds_curl_token');
		
		$category = get_post_meta($post->ID, "ds-category", TRUE);
		
		ds_get_country();
			
		if(!$category){
			
			$category = 'featured';
			
		}
		
		if(!$token){
								
			return array();
			
		}
		
		$channel_check_grab = get_page_by_path('channels');
	
		$channel_parent = $channel_check_grab->ID;
	
		$channel_grandparent = wp_get_post_parent_id( $post->post_parent );
		
		$revision = channel_revision_check();
				
		if($channel_grandparent == $channel_parent && !$revision){
						
			$parent = get_post($post->post_parent);
			
			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$parent->post_name."/".$post->post_name."/?detail=partial";
						
		} else {
			
			$postname = $post->post_name;
			
			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$postname."/?detail=partial";
			
		}
		
		$curl = curl_init();
				
		$channel_name = $post->post_name;
		
		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...
		
		} else {
			$r = json_decode($response);
			
			if($r->success){
				
				
												
				return $r->channels;
				
			} else {
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	} else if($command == 'parent-channel'){
				
		$token = get_option('ds_curl_token');
		
		$category = get_post_meta($post->ID, "ds-category", TRUE);
		
		ds_get_country();
			
		if(!$category){
			
			$category = 'featured';
			
		}
		
		if(!$token){
								
			return array();
			
		}
		
		$channel_check_grab = get_page_by_path('channels');
	
		$channels_parent = $channel_check_grab->ID;
	
		$channel_grandparent = wp_get_post_parent_id( $post->post_parent );
					
		if($channel_grandparent == $channels_parent){
						
			$parent = get_post($post->post_parent);
			
			$url = "http://api.myspotlight.tv/channels/".$this->country."/$category/".$parent->post_name."/?detail=partial";
						
		} else {
			
			return false;
			
		}
		
		$curl = curl_init();
				
		$channel_name = $post->post_name;
		
		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"postman-token: a917610f-ab5b-ef69-72a7-dacdc00581ee",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...
		
		} else {
			$r = json_decode($response);
			
			if(isset($r->channels[0])){
					
				return $r->channels[0];
				
			} else {
				
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	} else if($command == 'all-categories'){
		
		$token = get_option('ds_curl_token');
		
		ds_get_country();
				
		if(!$token || !$this->country){
									
			return array();
			
		}
		
		$curl = curl_init();
		
		

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://api.myspotlight.tv/categories/".$this->country."",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...
		 
		} else {
			$r = json_decode($response);
			
			if(count($r->categories)){
												
				return $r->categories;
				
			} else {
				
				// Maybe log this somewhere?
				return false;
				
			}
		}
		
	} else if($command == 'single-category'){
		
		$token = get_option('ds_curl_token');
		
		$cat = $args['category'];
		
		if(!$token || !$this->country || !$cat){
						
			return array();
			
		}
		
		
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "http://api.myspotlight.tv/channels/".$this->country."/".$cat."?detail=partial",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"ip\"\r\n\r\n".$_SERVER['REMOTE_ADDR']."\r\n-----011000010111000001101001--",
		CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data; boundary=---011000010111000001101001",
				"x-access-token:".$token
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			
			//"cURL Error #:" . $err;
			// Not sure what to do with this one.		Hm...
		
		} else {
			$r = json_decode($response);
			
			if(isset($r->message)){
				
				$message = $r->message;
				
			} else {
				
				$message = '';
				
			}
									
			if(isset($r->channels) && count($r->channels) && strpos($message, "Error") !== TRUE){
																
				return $r->channels;
				
			} else {
				
				// Maybe log this somewhere?
				$empty_obj = new stdClass();
				return $empty_obj;
				
			}
		}
		
	}
	
}

}
