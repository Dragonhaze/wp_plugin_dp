<?php
/*
Plugin Name: Push Notifications by Device Push
Description: Push Notifications by Device Push. Direct and effective communication in real time. A new way to communicate with your customers: Communicate in a personalized way and in real time with your customers. Increase the conversion rate of your campaigns. Increase your customers' commitment to your brand. Manage your campaigns from an intuitive and easy to use control panel: Plan, segment and analyze your campaigns, and make better decisions.
Author: Device Push
Author URI: https://www.devicepush.com
Version: 1.8
*/

// Load user IP
function loadIp(){
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) { $clientIpAddress = $_SERVER['HTTP_X_FORWARDED_FOR']; } else { $clientIpAddress = $_SERVER['REMOTE_ADDR']; }
	return $clientIpAddress;
}

// Create random user id
function createIdRand(){
	return rand(10,100).rand(10,100).rand(10,100).rand(10,100).rand(10,100);
}

function admincookies($action, $key, $value, $expirationDays){

    if($action == 'GET'){


        if(!isset($_COOKIE[$key])) {
            echo "Cookie named '" . $key . "' is not set!";
        } else {
            echo "Cookie '" . $key . "' is set!<br>";
            echo "Value is: " . $_COOKIE[$key];
        }

  }else if($action == 'SET'){
        if ($value != null || $value != '') {
            setcookie($key, $value, time() + (86400 * $expirationDays));
        }
    }else if(action == 'REMOVE'){
        setcookie($key,"",time()-3600);
    }
}

//add_action('init','admincookies');
// Add hook for front-end <head></head>
function include_manifest(){
	echo '<link rel="manifest" href="'.plugins_url( 'sdk/manifest.json.php', __FILE__ ).'">';
}

// Add hook for front-end into footer
function create_devicepush_js(){
	//Get language user visit web
	$accept = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	$lang = explode(",", $accept);
	$language_first = explode('-',$lang[0]);
	$language = $language_first[0];
	//Get data from wordpress site
	$site_name = get_bloginfo('name');
	$site_version = get_bloginfo('version');
	$site_wpurl = get_bloginfo('wpurl');
	$site_language = get_bloginfo('language');
	//Get data from user
	$user = wp_get_current_user();
	$is_logged_in = 'false';
	//Get user id
	if (isset($user->ID) && $user->ID != 0) {
		update_option('wp_user_id', $user->ID);
		$is_logged_in = 'true';
	}else if (get_option('wp_user_id') == ''){

		update_option('wp_user_id', createIdRand());
		$is_logged_in = 'false';
	}

  $text_popup = '';
  if(get_option('dp_option_text_popup') && esc_attr( get_option('dp_option_text_popup') ) != ''){
  	$text_popup = get_option('dp_option_text_popup');
  }else{
    $text_popup = "Activate push notifications to improve your experience on our website.";
  }
  $text_block_popup = '';
  if(get_option('dp_option_text_block_popup') && esc_attr( get_option('dp_option_text_block_popup') ) != ''){
  	$text_block_popup = esc_attr( get_option('dp_option_text_block_popup') );
  }else{
    $text_block_popup = "Later";
  }
  $text_active_popup = '';
  if(get_option('dp_option_text_active_popup') && esc_attr( get_option('dp_option_text_active_popup') ) != ''){
  	$text_active_popup = esc_attr( get_option('dp_option_text_active_popup') );
  }else{
    $text_active_popup = "Activate";
  }
	if(wp_get_attachment_url( get_option( 'image_attachment_id' ) )){
		$thumbnail = wp_get_attachment_url( get_option( 'image_attachment_id' ) );
	}else{
		$thumbnail = plugins_url('/images/logo-device.png', __FILE__);
	}
	$logo = '';
	if(esc_attr( get_option( 'dp_option_legend_option' ) ) == 'on'){
		$logo = '<div class="legend"><img src="'.plugins_url('/images/logo-device.png', __FILE__).'" class="dp_iconlogo"> <a href="https://www.devicepush.com" target="_black">Device Push</a></div>';
	}

  echo '
  	<script>
  		console.log("Hello from Device Push!");
  		function initDevicePush(){
  			document.querySelector("#dp_modal_active").classList.remove("show");
				document.querySelector("#dp_circle_active").classList.remove("show");
  			devicePush.register({
		    	key: "'.get_option('devicepush_key').'",
		    	websitepushid: "'.get_option('devicepush_websitepushid').'",
		    	websiteurl: "'.get_option('devicepush_websiteurl').'",
		    	additionalData: {cms_types: "Wordpress", cms_name: "'.$site_name.'", cms_version: "'.$site_version.'", cms_url: "'.$site_wpurl.'", cms_language: "'.$site_language.'", cms_user_id: "'.get_option('wp_user_id').'", cms_user_language: "'.$language.'", cms_user_is_logged_in: "'.$is_logged_in.'", cms_user_ip: "'.loadIp(). '" }
		    });
  		}
  		document.addEventListener("deviceRegistered", successDeviceRegistered, false);
       function successDeviceRegistered(evt){
            console.log("Device Browser" + evt.devicePushBrowser);
            admincookies("SET","devicePushBrowser",evt.devicePushBrowser, 365);
            console.log("Device Id" + evt.devicePushId);
            admincookies("SET","devicePushId",evt.devicePushId, 365);
            console.log("Device Token" + evt.devicePushToken);
            admincookies("SET","devicePushToken",evt.devicePushToken, 365);
            
       
        }
			function activeDevicePush(){ document.querySelector("#dp_modal_active").classList.add("show"); document.querySelector("#dp_circle_active").classList.remove("show"); }
			function blockDevicePush(){ localStorage.setItem("block_device_push", "1"); document.querySelector("#dp_modal_active").classList.remove("show"); document.querySelector("#dp_circle_active").classList.add("show"); }
  	</script>
  	<div id="dp_modal_active" class="dp_shadow dp_bg_fff"><div class="dp_icon" style="background-color: ' .esc_attr( get_option('dp_option_background_circle_popup') ).'"></div><div class="dp_text">'.$text_popup.'</div><div class="dp_buttons"><input type="button" onclick="blockDevicePush()" class="btn block" style="background-color: '.esc_attr( get_option('dp_option_background_block_popup') ).'" value="'.$text_block_popup.'"/><input type="button" onclick="initDevicePush()" class="btn allow" style="background-color: '.esc_attr( get_option('dp_option_background_active_popup') ).'" value="'.$text_active_popup.'"/></div>'.$logo.'</div><div id="dp_circle_active" onclick="activeDevicePush()" style="background-color: '.esc_attr( get_option('dp_option_background_circle_popup') ).'"><div class="dp_icon"></div></div>';
		if (esc_attr( get_option('dp_option_welcomenotification_option') )){
			echo '
			<script>
			document.addEventListener("deviceRegistered", successWelcomePush, false);
			function successWelcomePush(evt){
					var notification = new Notification("'.get_option('dp_option_title_welcomenotification').'", {
	  				body: "'.get_option('dp_option_text_welcomenotification').'",
	  				icon: "'.$thumbnail.'",
						requireInteraction: true,
						isClickable: true
	  			});
			}
			</script>';
		}
		if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){
			echo '
			<script>
			document.addEventListener("DOMContentLoaded", function(event) {
				if(localStorage.getItem("_DP_registered") != "true"){
	  			if (("serviceWorker" in navigator) || ("safari" in window && "pushNotification" in window.safari)) {
	    			if((!localStorage.getItem("block_device_push") || localStorage.getItem("block_device_push") != "1") && !localStorage.getItem("active_device_push")){
	    				setTimeout(function(){ initDevicePush(); }, 1000);
	    			}
	    			if(localStorage.getItem("block_device_push") == "1"){
	    				setTimeout(function(){ document.querySelector("#dp_circle_active").classList.add("show"); }, 1000);
	    			}
	  			}
				}
			});
			</script>
			';
		}else{
			echo '
			<script>
			document.addEventListener("DOMContentLoaded", function(event) {
				if(localStorage.getItem("_DP_registered") != "true"){
	  			if (("serviceWorker" in navigator) || ("safari" in window && "pushNotification" in window.safari)) {
	    			if((!localStorage.getItem("block_device_push") || localStorage.getItem("block_device_push") != "1") && !localStorage.getItem("active_device_push")){
	    				setTimeout(function(){ document.querySelector("#dp_modal_active").classList.add("show"); }, 1000);
	    			}
	    			if(localStorage.getItem("block_device_push") == "1"){
	    				setTimeout(function(){ document.querySelector("#dp_circle_active").classList.add("show"); }, 1000);
	    			}
	  			}
				}
			});
			</script>
			';
		}
	$sw_data_array = array( 'file' => plugins_url( 'js/sw.js', __FILE__ ) );
  wp_enqueue_script('devicepush', plugins_url('js/devicepush.js?v'.time(), __FILE__));
  wp_localize_script( 'devicepush', 'sw', $sw_data_array );
  wp_enqueue_style('devicepush', plugins_url('/css/devicepush.css?v'.time(), __FILE__));
}

if (
	get_option('devicepush_key') != FALSE 					||
	get_option('devicepush_fcm') != FALSE 					||
	get_option('devicepush_app_name') != FALSE 			||
	get_option('devicepush_key') != '' 							||
	get_option('devicepush_fcm') != '' 							||
	get_option('devicepush_app_name') != ''
){
	add_action('wp_enqueue_scripts', 'include_manifest');
	add_action('wp_footer', 'create_devicepush_js');
}

// If the user login, logout or register update additional data by ip address
function update_user_id($user){
	if (
		get_option('devicepush_key') != FALSE 					&&
		get_option('devicepush_key') != ''
	){
		//Get language user visit web
		$accept = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		$lang = explode(",", $accept);
		$language_first = explode('-',$lang[0]);
		$language = $language_first[0];
		//Get data from wordpress site
		$site_name = get_bloginfo('name');
		$site_version = get_bloginfo('version');
		$site_wpurl = get_bloginfo('wpurl');
		$site_language = get_bloginfo('language');
		$user_current = wp_get_current_user();

		if($user != ''){
			$user_data = get_userdatabylogin($user);
		}
		if (isset($user_data->ID) && $user_data->ID != 0 && $user_data->ID != get_option('wp_user_id')) {
			//$updateby = 'cms_user_id';
			//$updatevalue = get_option('wp_user_id');
			$cms_user_id = $user_data->ID;
			$is_logged_in = 'true';
			update_option('wp_user_id', $user_data->ID);
		}else{

			//$updateby = 'cms_user_id';
			//$updatevalue = $user_data->ID;
			$cms_user_id = '';
			$is_logged_in = 'false';
			update_option('wp_user_id','');
		}

		$postData = array(
			'key' => get_option('devicepush_key'),
			'token' => $_COOKIE["devicePushToken"],
            'device' => $_COOKIE["devicePushBrowser"],

			//'updatevalue' => $updatevalue,
			'additionalData' => array("cms_types" => "Wordpress", "cms_name" => $site_name, "cms_version" => $site_version, "cms_url" => $site_wpurl, "cms_language" => $site_language, "cms_user_id" => $cms_user_id, "cms_user_language" => $language, "cms_user_is_logged_in" => $is_logged_in, "cms_user_ip" => loadIp())
		);
		$context = stream_context_create(array(
			'http' => array('method' => 'POST', 'content' => http_build_query($postData), 'timeout' => 10),
			'ssl' => array('verify_peer' => false)
		));
		$url = 'https://apiweb.devicepush.com:8081/1.0/device/additionaldata/update/'.get_option('devicepush_key');
		$result = file_get_contents($url, false, $context);
		if($result){
			$json = json_decode($result, true);
		}
	}
}
add_action('wp_login', 'update_user_id', 10, 1);
add_action('wp_logout', 'update_user_id', 10, 1);
add_action('user_register', 'update_user_id', 10, 1);

// When active plugin push notifications
function admin_devicepush_activate() {
	if(!get_option('dp_option_legend_option')){
		update_option('dp_option_legend_option', 'on');
	}
}
register_activation_hook( __FILE__, 'admin_devicepush_activate' );





// Add hook for back-end <head></head>
function admin_devicepush_js() {
    wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), null, true);
    wp_enqueue_style('font-awesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?v'.time());

    wp_enqueue_style('emoji', plugins_url('/lib/css/emoji.css?v'.time(), __FILE__));

    wp_enqueue_script('config',plugins_url('/lib/js/config.js', __FILE__));
    wp_enqueue_script('util',plugins_url('/lib/js/util.js', __FILE__));
    wp_enqueue_script('emojiarea',plugins_url('/lib/js/jquery.emojiarea.js', __FILE__));
    wp_enqueue_script('emoji-picker',plugins_url('/lib/js/emoji-picker.js', __FILE__));

    wp_enqueue_style('devicepush', plugins_url('/css/settings.css?v'.time(), __FILE__));
    wp_enqueue_script('settings', plugins_url('js/settings.js?v'.time(), __FILE__));
}
add_action('admin_enqueue_scripts', 'admin_devicepush_js');

// Send notification API
function SendNotification($new_status, $old_status, $post) {
	if(esc_attr( get_option('dp_option_status_'.$post->post_type) ) == 'on'){
		if ( $new_status == 'publish' && ($old_status == 'draft' || $old_status == 'auto-draft') ) {
			if(wp_get_attachment_url( get_option( 'image_attachment_id' ) )){
				$thumbnail = wp_get_attachment_url( get_option( 'image_attachment_id' ) );
			}else{
				$thumbnail = plugins_url('/images/logo-device.png', __FILE__);
			}
			if(has_post_thumbnail()){
				$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
				if(strpos($url, 'https') === false){
					$thumbnail = str_replace("http", "https", $url);
				}
			}else if(get_option( 'image_attachment_id' )){
				$url = wp_get_attachment_url(get_option( 'image_attachment_id' ));
				if(strpos($url, 'https') === false){
					$thumbnail = str_replace("http", "https", $url);
				}
			}
			$content = ' ';
			if(wp_strip_all_tags(substr($post->post_content, 0, 200), TRUE) != NULL){
				$content = wp_strip_all_tags(substr($post->post_content, 0, 200), TRUE);
			}
			$postData = array(
				'idApplication' => esc_attr( get_option('dp_option_idaplication') ),
		    'title' => $post->post_title,
				'content' => $content,
		    'icon' => $thumbnail,
		    'data' => '[{"action": "open", "url": "'.get_permalink($post->ID).'"}]'
			);
			$context = stream_context_create(array(
				'http' => array('method' => 'POST', 'header' => 'token: '.esc_attr( get_option('dp_option_iduser') ), 'content' => http_build_query($postData), 'timeout' => 10),
		    'ssl' => array('verify_peer' => false)
			));
			$result = file_get_contents('https://apiweb.devicepush.com:8081/1.0/send/', false, $context);
		}
	}
}

// Send push notification when order status changed
function SendNotificationOrderChangeStatus($this, $old_status, $new_status) {
	if(esc_attr( get_option('dp_option_iduser') ) && esc_attr( get_option('dp_option_idaplication') ) && esc_attr( get_option('dp_option_status_order') )){
		//Set icon devicepush temp
		if(wp_get_attachment_url( get_option( 'image_attachment_id' ) )){
			$thumbnail = wp_get_attachment_url( get_option( 'image_attachment_id' ) );
		}else{
			$thumbnail = plugins_url('/images/logo-device.png', __FILE__);
		}
		if(get_option( 'image_attachment_id' )){
			$url = wp_get_attachment_url(get_option( 'image_attachment_id' ));
			if(strpos($url, 'https') === false){
				$thumbnail = str_replace("http", "https", $url);
			}
		}
		//Get data order
		$order = new WC_Order( $this );
		//If the user id is different to 0 you can send push notification
		$segmentation = '';
		if($order->user_id != 0){
			$segmentation = '{"cms_user_id":"'.$order->user_id.'"}';
		}else{
			$segmentation = '{"cms_user_ip":"'.$order->customer_ip_address.'"}';
		}
		//Language WP
		$textstatus = '';
		if(strpos(get_bloginfo('language'), 'es') === false){
			$textstatus = 'Order with nº: '.$this.' change to status '. strtolower(wc_get_order_status_name( $order->post->post_status ));
		}else{
			$textstatus = 'El pedido nº: '.$this.' ha cambiado de estado a '. strtolower(wc_get_order_status_name( $order->post->post_status ));
		}
		$postData = array(
			'idApplication' => esc_attr( get_option('dp_option_idaplication') ),
	    'title' => get_bloginfo('name'),
	    'content' => $textstatus,
	    'icon' => $thumbnail,
	    'segmentation' => $segmentation,
	    'data' => '[{"action": "open", "url": "'.$order->get_view_order_url().'"}]'
		);
		$context = stream_context_create(array(
			'http' => array('method' => 'POST', 'header' => 'token: '.esc_attr( get_option('dp_option_iduser') ), 'content' => http_build_query($postData), 'timeout' => 10),
			'ssl' => array('verify_peer' => false)
		));
		$result = file_get_contents('https://apiweb.devicepush.com:8081/1.0/send/', false, $context);
	}
}
add_action( 'woocommerce_order_status_changed', 'SendNotificationOrderChangeStatus', 10, 2);

foreach(get_post_types(array('public' => true), 'object', 'and') as $post_type){
	if(esc_attr( get_option('dp_option_iduser') ) && esc_attr( get_option('dp_option_idaplication') ) && esc_attr( get_option('dp_option_status_'.$post_type->name) ) == 'on'){
		add_action('transition_'.$post_type->name.'_status', 'SendNotification', 10, 3);
	}else{
		remove_action('transition_'.$post_type->name.'_status', 'SendNotification', 10);
	}
}

//Woocommerce abandoned cart testing
add_action('woocommerce_cart_updated','woo_notification_on_cart_abandon');
add_action('woocommerce_checkout_order_processed','woo_notification_on_cart_ended');

function woo_notification_on_cart_ended($user){

    $accept = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    $lang = explode(",", $accept);
    $language_first = explode('-', $lang[0]);
    $language = $language_first[0];
    //Get data from wordpress site
    $site_name = get_bloginfo('name');
    $site_version = get_bloginfo('version');
    $site_wpurl = get_bloginfo('wpurl');
    $site_language = get_bloginfo('language');
    $user_current = wp_get_current_user();

    if ($user != '') {
        $user_data = get_userdatabylogin($user);
    }
    if (isset($user_data->ID) && $user_data->ID != 0 && $user_data->ID != get_option('wp_user_id')) {
        //$updateby = 'cms_user_id';
        //$updatevalue = get_option('wp_user_id');
        $cms_user_id = $user_data->ID;
        $is_logged_in = 'true';
        update_option('wp_user_id', $user_data->ID);
    } else {

        //$updateby = 'cms_user_id';
        //$updatevalue = $user_data->ID;
        $cms_user_id = '';
        $is_logged_in = 'false';
        update_option('wp_user_id', '');
    }


    $postData = array(
        'key' => get_option('devicepush_key'),
        'token' => $_COOKIE["devicePushToken"],
        'device' => $_COOKIE["devicePushBrowser"],
        //'updatevalue' => $updatevalue,
        'additionalData' => array("cms_types" => "Wordpress", "cms_name" => $site_name, "cms_version" => $site_version, "cms_url" => $site_wpurl, "cms_language" => $site_language, "cms_user_id" => $cms_user_id, "cms_user_language" => $language, "cms_user_is_logged_in" => $is_logged_in, "cms_user_ip" => loadIp())
    );


    $context = stream_context_create(array(
        'http' => array('method' => 'POST', 'content' => http_build_query($postData), 'timeout' => 10),
        'ssl' => array('verify_peer' => false)
    ));
    $url = 'https://apiweb.devicepush.com:8081/1.0/device/additionaldata/update/' . get_option('devicepush_key');
    $result = file_get_contents($url, false, $context);
    if ($result) {
        $json = json_decode($result, true);
    }
    //------------------------------------------------------------------------------------------------------------------
    $postData = array(
        'idApplication' => esc_attr( get_option('dp_option_idaplication') ),
        'title' => 'Thank you for buying ',
        'content' => 'Something',
        'icon' => 'http://demowp.devicepush.com/wp-content/uploads/2017/08/Arrow.png',
        'data' => '[{"action": "open", "url": "https://demowp.devicepush.com/finalizar-compra/"}]'
    );
    $context = stream_context_create(array(
        'http' => array('method' => 'POST', 'header' => 'token: '.esc_attr( get_option('dp_option_iduser') ), 'content' => http_build_query($postData), 'timeout' => 10),
        'ssl' => array('verify_peer' => false)
    ));
    $result = file_get_contents('https://apiweb.devicepush.com:8081/1.0/send/', false, $context);
}


function woo_notification_on_cart_abandon($user)
{

    //Get language user visit web
    if ($_COOKIE["woocommerce_items_in_cart"] == 1) {
        $cart_status = "open";
        $date = date('m/d/Y h:i:s a', time());
        $accept = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        $lang = explode(",", $accept);
        $language_first = explode('-', $lang[0]);
        $language = $language_first[0];
        //Get data from wordpress site
        $site_name = get_bloginfo('name');
        $site_version = get_bloginfo('version');
        $site_wpurl = get_bloginfo('wpurl');
        $site_language = get_bloginfo('language');
        $user_current = wp_get_current_user();

        if ($user != '') {
            $user_data = get_userdatabylogin($user);
        }
        if (isset($user_data->ID) && $user_data->ID != 0 && $user_data->ID != get_option('wp_user_id')) {
            //$updateby = 'cms_user_id';
            //$updatevalue = get_option('wp_user_id');
            $cms_user_id = $user_data->ID;
            $is_logged_in = 'true';
            update_option('wp_user_id', $user_data->ID);
        } else {

            //$updateby = 'cms_user_id';
            //$updatevalue = $user_data->ID;
            $cms_user_id = '';
            $is_logged_in = 'false';
            update_option('wp_user_id', '');
        }


        $postData = array(
            'key' => get_option('devicepush_key'),
            'token' => $_COOKIE["devicePushToken"],
            'device' => $_COOKIE["devicePushBrowser"],
            //'updatevalue' => $updatevalue,
            'additionalData' => array("cms_types" => "Wordpress", "cms_name" => $site_name, "cms_version" => $site_version, "cms_url" => $site_wpurl, "cms_language" => $site_language, "cms_user_id" => $cms_user_id, "cms_user_language" => $language, "cms_user_is_logged_in" => $is_logged_in, "cms_user_ip" => loadIp(), "cart_status" => $cart_status, "date_of_cart" => $date)
        );


        $context = stream_context_create(array(
            'http' => array('method' => 'POST', 'content' => http_build_query($postData), 'timeout' => 10),
            'ssl' => array('verify_peer' => false)
        ));
        $url = 'https://apiweb.devicepush.com:8081/1.0/device/additionaldata/update/' . get_option('devicepush_key');
        $result = file_get_contents($url, false, $context);
        if ($result) {
            $json = json_decode($result, true);
        }
    }
}

// Create custom plugin settings menu
function dp_create_menu() {
	add_menu_page('Device Push Plugin Settings', 'Device Push', 'administrator', __FILE__, 'dp_settings_page',plugins_url('/images/icon-small.png', __FILE__));
}
add_action('admin_menu', 'dp_create_menu');


 // Add meta box

function add_meta_boxes( $post ){

	$post_types = get_post_types( array('public' => true) );
	add_meta_box( 'notification_meta_box',
	 __( 'Device Push'),
	 'build_meta_box',
	 $post_types , 'side', 'high');
}
add_action( 'add_meta_boxes', 'add_meta_boxes' );


//Build custom field meta box
function build_meta_box( $post){

        $content = ' ';
        if (wp_strip_all_tags(substr($post->post_content, 0, 200), TRUE) != NULL) {
            $content = wp_strip_all_tags(substr($post->post_content, 0, 200), TRUE);
        }
        $title = $post->post_title;

        $idApplication = esc_attr(get_option('dp_option_idaplication'));

        $idUser = esc_attr(get_option('dp_option_iduser'));

        $posturl = get_permalink($post->ID);


        if (wp_get_attachment_url(get_option('image_attachment_id'))) {
            $thumbnail = wp_get_attachment_url(get_option('image_attachment_id'));
        } else {
            $thumbnail = plugins_url('/images/logo-device.png', __FILE__);
        }
        if (has_post_thumbnail()) {
            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
            if (strpos($url, 'https') === false) {
                $thumbnail = str_replace("http", "https", $url);
            }
        } else if (get_option('image_attachment_id')) {
            $url = wp_get_attachment_url(get_option('image_attachment_id'));
            if (strpos($url, 'https') === false) {
                $thumbnail = str_replace("http", "https", $url);
            }
        }
        // make sure the form request comes from WordPress
        wp_nonce_field(basename(__FILE__), 'meta_box_nonce');
        if ( get_post_status($post->ID) == 'publish' || get_post_status($post->ID) == 'private' ) {
            ?>
            <div class='inside' style="text-align: center">
                <p style="text-align: left">With this button you can send push notifications after publishing this content</p>

                <input type="button" class="button button-primary button-large" id="publish" style="margin-top: 15px" onclick="testpush(
                        '<?php echo $idUser; ?>',
                        '<?php echo $idApplication; ?>',
                        '<?php echo $title; ?>',
                        '<?php echo $content; ?>',
                        '<?php echo $thumbnail; ?>',
                        '<?php echo $posturl ?>')" value="Send Custom Push Notification"/>

                <div id="resultSendPushNotification" style="margin-top: 10px"></div>
            </div>
            <?php
        } else{
            ?>
            <div class='inside' style="text-align: left">
                <p>After publishing this content (It can be private) you will be able to use a button here to send a notification about this post</p>
            </div>
            <?php
        }

}

// Set value input form
function register_devicepush() {
	register_setting('dp-settings-group', 'dp_option_iduser');
	register_setting('dp-settings-group', 'dp_option_idaplication');
	register_setting('dp-settings-group', 'image_attachment_id');
	foreach ( get_post_types( array( 'public' => true ), 'objects', 'and' ) as $post_type ) {
		register_setting( 'dp-check-group', 'dp_option_status_'.$post_type->name );
	}
	register_setting('dp-check-group', 'dp_option_welcomenotification_option');
	register_setting('dp-check-group', 'dp_option_title_welcomenotification');
	register_setting('dp-check-group', 'dp_option_text_welcomenotification');
	register_setting('dp-check-group', 'dp_option_status_order');
	register_setting('dp-popup-group', 'dp_option_prompt_option');
	register_setting('dp-popup-group', 'dp_option_legend_option');
	register_setting('dp-popup-group', 'dp_option_background_circle_popup');
	register_setting('dp-popup-group', 'dp_option_text_popup');
	register_setting('dp-popup-group', 'dp_option_text_block_popup');
	register_setting('dp-popup-group', 'dp_option_background_block_popup');
	register_setting('dp-popup-group', 'dp_option_text_active_popup');
	register_setting('dp-popup-group', 'dp_option_background_active_popup');

	if(
		esc_attr( get_option('dp_option_iduser') ) 				&&
		esc_attr( get_option('dp_option_idaplication') )
	){
		if (
			get_option('devicepush_key') == FALSE 					||
			get_option('devicepush_fcm') == FALSE 					||
			get_option('devicepush_app_name') == FALSE 			||
			get_option('devicepush_websitepushid') == FALSE ||
			get_option('devicepush_websiteurl') == FALSE 		||
			get_option('devicepush_key') == '' 							||
			get_option('devicepush_fcm') == '' 							||
			get_option('devicepush_app_name') == '' 				||
			get_option('devicepush_websitepushid') == '' 		||
			get_option('devicepush_websiteurl') == ''
		){
			$context = stream_context_create(array(
		    'http' => array( 'method' => 'GET', 'header' => 'token: '.get_option('dp_option_iduser'), 'timeout' => 10 ),
		    'ssl' => array( 'verify_peer' => false )
			));
			$url = 'https://apiweb.devicepush.com:8081/1.0/'.get_option('dp_option_idaplication');
			$result = file_get_contents($url, false, $context);
			if($result){
				$json = json_decode($result, true);
				delete_option('devicepush_app_name');
				delete_option('devicepush_fcm');
				delete_option('devicepush_key');
				delete_option('devicepush_websitepushid');
				delete_option('devicepush_websiteurl');
				if($json['name'] != undefined){
					if(get_option('devicepush_app_name')){ update_option( 'devicepush_app_name', $json['name']); }else{ add_option( 'devicepush_app_name', $json['name']); }
				}
				if($json['fcmsenderid'] != undefined){
					if(get_option('devicepush_fcm')){ update_option( 'devicepush_fcm', $json['fcmsenderid']); }else{ add_option( 'devicepush_fcm', $json['fcmsenderid']); }
				}
				if($json['key'] != undefined){
					if(get_option('devicepush_key')){ update_option( 'devicepush_key', $json['key']); }else{ add_option( 'devicepush_key', $json['key']); }
				}
				if($json['websitepushid'] != undefined){
					if(get_option('devicepush_websitepushid')){ update_option( 'devicepush_websitepushid', $json['websitepushid']); }else{ add_option( 'devicepush_websitepushid', $json['websitepushid']); }
				}
				if($json['websiteurl'] != undefined){
					if(get_option('devicepush_websiteurl')){ update_option( 'devicepush_websiteurl', $json['websiteurl']); }else{ add_option( 'devicepush_websiteurl', $json['websiteurl']); }
				}
		  }
		}
	}
}
add_action('admin_init', 'register_devicepush');

// Media Query Image Selector
function media_selector_print_scripts() {
	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
	?><script type='text/javascript'>
		document.addEventListener("DOMContentLoaded", function(event) {
			var upload_image_button = document.querySelector("#upload_image_button");
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id;
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>;
			upload_image_button.onclick = function(event){
				event.preventDefault();
				if ( file_frame ) {
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					file_frame.open();
					return;
				} else {
					wp.media.model.settings.post.id = set_to_post_id;
				}
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false
				});
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();
					document.querySelector("#image-preview").src = attachment.url;
					document.querySelector("#image-preview").width = 'auto';
					document.querySelector("#image_attachment_id").value = attachment.id;
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					file_frame.open();
			}
			var add_media = document.querySelector("a.add_media");
			//if(add_media != null) {


                add_media.onclick = function () {
                    wp.media.model.settings.post.id = wp_media_post_id;
                }
            //}
		});
	</script><?php
}
add_action( 'admin_footer', 'media_selector_print_scripts' );

// Set active tab horizontal menu
function dp_settings_page() {

if(!get_option('dp_option_idaplication') || !get_option('dp_option_iduser')){
	echo "<script>document.addEventListener('DOMContentLoaded', function(event) { document.getElementById('tab_configure').classList.add('active'); document.getElementById('content_configure').classList.add('active'); });</script>";
}else{
	echo "<script>document.addEventListener('DOMContentLoaded', function(event) { document.getElementById('tab_send').classList.add('active'); document.getElementById('content_send').classList.add('active'); });</script>";
}
?>

<div class="dp_wrap"> <!-- wrap -->
     <script>
         jQuery(document).ready(function ($) {
             // Initializes and creates emoji set from sprite sheet
             window.emojiPicker = new EmojiPicker({
                 emojiable_selector: '[data-emojiable=true]',
                 assetsPath: '<?php echo plugins_url('device-push/lib/img/');?>',
                 popupButtonClasses: 'fa fa-smile-o'
             });
             // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
             // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
             // It can be called as many times as necessary; previously converted input fields will not be converted again
             window.emojiPicker.discover();
         });</script>
	<h1><img src="<?php echo plugins_url('/images/logo-device.png', __FILE__); ?>" class="dp_iconlogo"><span class="dp_blue">Device</span> <span class="dp_grey">Push</span> for WordPress</h1>
	<h4 class="dp_grey">Direct and effective communication in real time. Push Notifications for Apps and Webs</h4>

	<div class="dp_contain"> <!-- dp_contain -->

		<div class="tabs">
			<div id="tab_configure" onclick="showTab('configure')" class="tab">Settings</div>
			<div id="tab_advanced" onclick="showTab('advanced')" class="tab">Advanced Settings</div>
			<div id="tab_send" onclick="showTab('send')" class="tab">Send Notification</div>
		</div>

		<div id="content_configure"> <!-- tab_configure -->

			<table class="form-table"><tr><td><h2>How can you start? <button type="button" onclick="openDevicePushWeb()" class="btn shadow marginLeft10">SEE TUTORIAL &#9658;</button></h2></td></tr></table>

			<div class="col">
				<form method="post" action="options.php" style="padding-right:40px" name="frmProduct" id="frmProduct" enctype="multipart/form-data">
				    <?php settings_fields( 'dp-settings-group' ); ?>
				    <?php do_settings_sections( 'dp-settings-group' ); ?>
					<table class="form-table">
						<tr>
							<td>
								<h3 style="margin-top:0px">1. Activate and configure your Device Push User Account</h3>
								<p>Go to <a href="https://www.devicepush.com/?sec=section5" target="_blank">www.devicepush.com</a> and request a user account.</p>
								<br/>
								<h3>2. Register your first App or Web into your Device Push control panel</h3>
								<p>Go to <a href="http://panel.devicepush.com/" target="_blank">panel.devicepush.com</a> and register your first app or web. Copy your "User ID" and your "App or Web ID" and paste into the form below.</p>
							</td>
						</tr>
				        <tr class="dp_form" valign="top">
					        <td class="dp_titleinputform">
					        	<span class="dp_blue">User ID:</span>
					        	<input type="text" class="dp_input_text" id="dp_option_iduser" name="dp_option_iduser" value="<?php echo esc_attr( get_option('dp_option_iduser') ); ?>" />
					        </td>
				        </tr>
				        <tr valign="top">
				        	<td class="dp_titleinputform">
				        		<span class="dp_blue">App or Web ID:</span>
				        		<input type="text" class="dp_input_text" id="dp_option_idaplication" name="dp_option_idaplication" value="<?php echo esc_attr( get_option('dp_option_idaplication') ); ?>" />
				        	</td>
				        </tr>
				        <tr valign="top">
				        	<td>
				        		<input id="checkDataUser" type="submit" class="btn shadow hide" value="">
				        		<input type="button" onclick="checkAccount()" class="btn shadow marginTop10" value="Synchronize account">
				        		<div id="resultCheckDataUser"></div>
				        	</td>
				        </tr>
				        <tr>
					        <td>
					        	<h3>3. Set a generic image for your push notifications</h3>
					        </td>
				        </tr>
				        <tr valign="top">
					        <td>
					        	<div class="check">
					        		<?php
							        // Save attachment ID
									if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) { update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) ); }
									wp_enqueue_media();
									?>
									<div class='image-preview-wrapper'>
										<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'image_attachment_id' ) ); ?>' height='100'>
									</div>
									<div>
										<div style="padding-top:10px; padding-bottom:10px">This image will appear if the content that is sent in the push notification does not have an custom image.</div>
										<input id="upload_image_button" type="button" class="button button-primary btn shadow" value="<?php _e( 'Upload image' ); ?>" /> <input type="button" onclick="saveImage()" class="btn shadow button-save" value="Save">
										<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'image_attachment_id' ); ?>'>
									</div>
					        	</div>
					        </td>
					    </tr>
				    </table>
				</form>
			</div>
			<div class="col">
				<?php if(is_plugin_active('woocommerce/woocommerce.php')){ ?>
					<img class="dp_maciphone" src="<?php echo plugins_url('/images/mac-ecommerce-en.jpg', __FILE__); ?>">
				<?php }else{ ?>
					<img class="dp_maciphone" src="<?php echo plugins_url('/images/mac-web-en.jpg', __FILE__); ?>">
				<?php } ?>
			</div>

		</div> <!-- tab_configure -->

		<div id="content_advanced"> <!-- tab_advaced -->

			<div class="col">
				<form method="post" action="options.php" style="padding-right:40px" name="frmCheck" id="frmPopup" enctype="multipart/form-data">
				  <?php settings_fields( 'dp-popup-group' ); ?>
				  <?php do_settings_sections( 'dp-popup-group' ); ?>
					<table class="form-table">
				<tr>
				<td>
					<h3>Activate the custom prompt to incentivize the user to subscribe active push notification.</h3>
				</td>
				</tr>
				<tr>
				<td>
					<div class="check">
					<input id="noprompt" onchange="activeInputsPopup(this)" type="checkbox" <?php if (esc_attr( get_option('dp_option_prompt_option') )){echo 'checked'; } ?> name='dp_option_prompt_option'> Activate custom notification subscription prompt.
					</div>
				</td>
				</tr>
		        <tr>
			        <td>
			        	<h3>Customize the custom prompt to incentivize the user to subscribe active push notification.</h3>
			        </td>
		        </tr>
		        <tr valign="top">
			        <td style="position:relative">
				        <div class="margin-top10">
					        <span class="titleInput">Background circle color</span>
					        <input type="text" id="backgroundCirclePopup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name="dp_option_background_circle_popup" onkeyup="writePopup('backgroundCircle')" class="dp_input_text input100" placeholder="#000000" value="<?php echo esc_attr( get_option('dp_option_background_circle_popup') ); ?>" ></input>
				        </div>
				        <div class="margin-top10">
					        <span class="titleInput">Text</span>
					        <textarea id="textPopup" name="dp_option_text_popup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> class="dp_input_text" onkeyup="writePopup('text'); countCaracteresPopup(this)" placeholder="The text of your popup activated notification push"><?php echo esc_attr( get_option('dp_option_text_popup') ); ?></textarea>
				        </div>
				        <span id="numberCaracteresPopup"></span>
				        <div class="margin-top10">
					        <span class="titleInput">Text button "Later"</span>
					        <input type="text" id="textBlockPopup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name="dp_option_text_block_popup" onkeyup="writePopup('textBlock')" class="dp_input_text input100" placeholder="Later" value="<?php echo esc_attr( get_option('dp_option_text_block_popup') ); ?>" ></input>
				        </div>
				        <div class="margin-top10">
					        <span class="titleInput">Background color button "Later"</span>
					        <input type="text" id="backgroundBlockPopup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name="dp_option_background_block_popup" onkeyup="writePopup('backgroundBlock')" class="dp_input_text input100" placeholder="#000000" value="<?php echo esc_attr( get_option('dp_option_background_block_popup') ); ?>" ></input>
				        </div>
				        <div class="margin-top10">
					        <span class="titleInput">Text button "Activate"</span>
					        <input type="text" id="textActivePopup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name="dp_option_text_active_popup" onkeyup="writePopup('textActive')" class="dp_input_text input100" placeholder="Activate" value="<?php echo esc_attr( get_option('dp_option_text_active_popup') ); ?>" ></input>
				        </div>
				        <div class="margin-top10">
					        <span class="titleInput">Background color button "Activate"</span>
					        <input type="text" id="backgroundActivePopup" <?php if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name="dp_option_background_active_popup" onkeyup="writePopup('backgroundActive')" class="dp_input_text input100" placeholder="#000000" value="<?php echo esc_attr( get_option('dp_option_background_active_popup') ); ?>" ></input>
				        </div>
						<div class="margin-top10">
							<input id="legendActivePopup" onchange="actionCheckLegend(this)" type="checkbox" <?php if (esc_attr( get_option('dp_option_legend_option') ) != ''){ echo 'checked'; } if (esc_attr( get_option('dp_option_prompt_option') )!= 'on'){echo ' disabled '; } ?> name='dp_option_legend_option'> I love Device Push Plugin <?php echo esc_attr( get_option('dp_option_legend_option') ); ?>.
						</div>
			        </td>
			    	</tr>
				    <tr valign="top">
			        <td>
			        	<input id="saveFrmPopup" type="submit" class="btn shadow hide" value="">
			        	<input type="button" id="configurepopup" onclick="setConfigurePopup()" class="btn shadow marginTop10" value="Save">
		        		<div id="resultSetConfigurePopup"></div>
			        </td>
			     	</tr>
					</table>
				</form>
			</div>

			<div class="col">
				<table class="form-table">
	        <tr>
		        <td>
		        	<h3></h3>
		        </td>
	        </tr>
	        <tr valign="top">

		        <td style="position:relative">
			        <div>
			        	<span class="titleInput">Preview</span>
			        	<div id="dp_modal_active" class="dp_shadow dp_bg_fff">
									<div id="backgroundCirclePreviewPopup" class="dp_icon" style="background-color: <?php echo esc_attr( get_option('dp_option_background_circle_popup') ); ?>"></div>
									<div id="textPreviewPopup" class="dp_text">
			        			<?php if(get_option('dp_option_text_popup') != ''){ echo get_option('dp_option_text_popup'); }else{ echo "Activate push notifications to improve your experience on our website."; } ?>
			        		</div>
				        	<div class="dp_buttons">
				        		<button id="textBlockPreviewPopup" type="button" class="btn_modal block" style="background-color: <?php echo esc_attr( get_option('dp_option_background_block_popup') ); ?>">
					        		<?php if(get_option('dp_option_text_block_popup') != ''){ echo esc_attr( get_option('dp_option_text_block_popup') ); }else{ echo "Later"; } ?>
						        </button>
						        <button id="textActivePreviewPopup" type="button" class="btn_modal allow" style="background-color: <?php echo esc_attr( get_option('dp_option_background_active_popup') ); ?>">
						        	<?php if(get_option('dp_option_text_active_popup') != ''){ echo esc_attr( get_option('dp_option_text_active_popup') ); }else{ echo "Activate"; } ?>
						        </button>
									</div>
									<div id="legend" class="legend <?php if(esc_attr( get_option( 'dp_option_legend_option' ) ) != 'on'){ echo "hide"; } ?>"><img src="<?php echo plugins_url('/images/logo-device.png', __FILE__); ?>"> <a href="https://www.devicepush.com" target="_black">Device Push</a></div>
								</div>
			        </div>
		        </td>
			    </tr>
				</table>
			</div>

		</div> <!-- tab_advaced -->

		<div id="content_send"> <!-- tab_send -->
			<div class="col">
				<form method="post" action="options.php" style="padding-right:40px" name="frmCheck" id="frmCheck" enctype="multipart/form-data">
				  	<?php settings_fields( 'dp-check-group' ); ?>
				  	<?php do_settings_sections( 'dp-check-group' ); ?>
						<table class="form-table">
							<tr>
				        <td>
				        	<h3>Send automatic welcome push notification when the user accept notifications</h3>
				        </td>
			        </tr>
							<tr>
								<td>
									<div class="check">
										<input id="welcomeNotification" type="checkbox" <?php if (esc_attr( get_option('dp_option_welcomenotification_option') )){ echo 'checked'; } ?> name='dp_option_welcomenotification_option'> Activate automatic welcome push notification.
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="margin-top10">
										<span class="titleInput">Title</span>
										<input type="text" id="titleWelcomeNotification" name="dp_option_title_welcomenotification" class="dp_input_text" placeholder="Title of your welcome push notification" value="<?php echo esc_attr( get_option('dp_option_title_welcomenotification') ); ?>"></input>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="margin-top10">
										<span class="titleInput">Text</span>
										<textarea id="textWelcomeNotification" data-emojiable="true" data-emoji-input="unicode" name="dp_option_text_welcomenotification" class="dp_input_text" placeholder="Text of your welcome push notification"><?php echo esc_attr( get_option('dp_option_text_welcomenotification') ); ?></textarea>
									</div>
								</td>
							</tr>
				    	<tr>
				        <td>
				        	<h3>Activate automatic push notification when you want your push notifications sent</h3>
				        </td>
			        </tr>
							<tr valign="top">
								<td>
								<?php
								$post_types = get_post_types( array( 'public' => true ), 'objects', 'and' );
								foreach ( $post_types  as $post_type ){ ?>
									<div class="check">
										<input class="checkDynamic" id="dp_option_status_<?php echo $post_type->name; ?>" type="checkbox" name="dp_option_status_<?php echo $post_type->name; ?>" <?php if (esc_attr( get_option('dp_option_status_'.$post_type->name) )){echo 'checked'; } ?>> Each time I add a <?php echo $post_type->labels->singular_name; ?> on my web.
									</div>
								<?php } ?>
								</td>
							</tr>
					    <tr valign="top">
				        <td>
				        <div style="margin-top:20px; margin-bottom:10px"><img src="<?php echo plugins_url('/images/icon-woocommerce.png', __FILE__); ?>"></div>
				        <div class="check">
				        	<?php
					        	$disabled_check_woocommerce = ' disabled ';
					        	if(is_plugin_active('woocommerce/woocommerce.php')){ $disabled_check_woocommerce = ''; }
				        	?>
				        	<input id="dp_option_status_order" type="checkbox" name="dp_option_status_order" <?php if (esc_attr( get_option('dp_option_status_order') )){echo 'checked'; } echo $disabled_check_woocommerce; ?>> Notify the customer when the status of your order changes. <div style="font-size:10px">(You need this plugin <a href="https://es.wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>)</div>
				        </div>
				        </td>
					    </tr>
					    <tr valign="top">
				        <td>
				        	<input id="saveFrmCheck" type="submit" class="btn shadow hide" value="">
				        	<input type="button" onclick="checkActive()" class="btn shadow marginTop10" value="Activate Automatic Push Notifications">
			        		<div id="resultCheckActive"></div>
				        </td>
			        </tr>
						</table>
				</form>
			</div>
			<div class="col">
				<table class="form-table">
	        <tr>
		        <td>
		        	<h3>Send custom push notification now!</h3>
		        </td>
	        </tr>
			    <tr valign="top">
				  	<td style="position:relative">
				    	<div>
				      	<span class="titleInput">Preview</span>
				       	<div id="notificationPreview" class="shadow">
			        		<div id="iconPreview" style="background-image: url(<?php if(get_option( 'image_attachment_id' )){ echo wp_get_attachment_url( get_option( 'image_attachment_id' ) ); }else{ echo plugins_url('/images/logo-device.png', __FILE__); } ?>)"></div>
			        		<div id="titlePreview">Title of your push notification</div>
			        		<div id="textPreview">Text of your push notification</div>
			        		<div id="urlPreview">
				        		<?php if(strpos(get_option( 'siteurl' ), 'https') === false){ echo str_replace('http://', '', get_option( 'siteurl' )); }else{ echo str_replace('https://', '', get_option( 'siteurl' )); } ?>
			        		</div>
			        		<div id="closePreview">x</div>
			        	</div>
							</div>
			        <div>
			        	<span class="titleInput">Users</span>
			        	<select id="userNotification">
			        		<option value="">All</option>
				        	<?php
				        	$blogusers = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
									foreach ( $blogusers as $user ) { echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->display_name ) . '</option>'; }
									?>
			        	</select>
			        </div>
			        <div class="margin-top10">
				        <span class="titleInput">Title</span>
				        <input type="text" id="titleNotification" class="dp_input_text" onkeyup="writeNotification('title')" placeholder="Title of your push notification"></input>
			        </div>
			        <div class="margin-top10">
				        <span class="titleInput">Text</span>
				        <textarea data-emojiable="true" id="textNotification" class="dp_input_text" onkeyup="writeNotification('text'); countCaracteres(this)" placeholder="Text of your push notification"></textarea>
			        </div>
							<span id="numberCaracteres"></span>
							<div class="margin-top10">
				        <span class="titleInput">Url</span>
				        <input type="text" id="urlNotification" class="dp_input_text" placeholder="Url opens when the user clicks"></input>
			        </div>
				      <input type="hidden" id="iconNotification" value="<?php if(get_option( 'image_attachment_id' )){ echo wp_get_attachment_url( get_option( 'image_attachment_id' ) ); }else{ echo plugins_url('/images/logo-device.png', __FILE__); } ?>" />
				    </td>
				  </tr>
			    <tr valign="top">
		        <td>
		        	<input type="button" onclick="sendPushNotification()" class="btn shadow marginTop10" value="Send Custom Push Notifications">
	        		<div id="resultSendPushNotification"></div>
		        </td>
	        </tr>
				</table>
			</div>

		</div> <!-- tab_send -->

	</div> <!-- dp_contain -->

	<div style="width:100%; margin-top:20px; float:left">
		<hr />
	  <p>Find more information about Device Push on our website: <a href="https://www.devicepush.com/" target="_blank">www.devicepush.com</a></p>
	  <p>Follow us on:</p>
		<div style="height:30px; line-height:30px"><table><tr><td><img src="<?php echo plugins_url('/images/twitter.png', __FILE__); ?>" style="width: 20px; height: auto; margin-right: 5px; margin-bottom: -5px;"></td><td>Twitter: <a href="https://twitter.com/devicepush" target="_blank">@devicepush</a></td></tr></table></div>
		<div style="height:30px; line-height:30px"><table><tr><td><img src="<?php echo plugins_url('/images/facebook.png', __FILE__); ?>" style="width: 20px; height: auto; margin-right: 5px; margin-bottom: -5px;"></td><td>Facebook: <a href="https://fb.com/devicepush" target="_blank">fb.com/devicepush</a></td></tr></table></div>
		<div style="height:30px; line-height:30px"><table><tr><td><img src="<?php echo plugins_url('/images/linkedin.png', __FILE__); ?>" style="width: 20px; height: auto; margin-right: 5px; margin-bottom: -5px;"></td><td>Linkedin: <a href="https://www.linkedin.com/company/device-push" target="_blank">linkedin.com/company/device-push</a></td></tr></table></div>
	</div>

</div> <!-- wrap -->
<?php } ?>
