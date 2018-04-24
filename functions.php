<?php
// Add custom Theme Functions here

/**
 * Add the field to order emails
 **/


add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_product_add_to_cart_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_product_add_to_cart_text' );  // 2.1 +
  
function woo_custom_product_add_to_cart_text() {
	if(function_exists('qtranxf_getLanguage')){
		$crr_user_language=qtranxf_getLanguage();
	}else{
		$crr_user_language=get_locale();
	}
  
  $txttoreturn=' +1 to cart';
  if($crr_user_language=='fr' || $crr_user_language=='fr_BE' || $crr_user_language=='FR'){
    $txttoreturn=' +1 au panier';
  }else if($crr_user_language=='nl' || $crr_user_language=='nl_BE' || $crr_user_language=='NL'){
		$txttoreturn=' +1 aan WINKELMAND';
	}
  return __( $txttoreturn, 'woocommerce' );
}


 
add_filter('woocommerce_email_order_meta_keys', 'my_custom_checkout_field_order_meta_keys');

function my_custom_checkout_field_order_meta_keys( $keys ) {
	$keys[] = 'delivery_time';
	return $keys;
}


add_action( 'woocommerce_order_status_completed', 'action_woocommerce_order_status_completed', 10, 2 ); 

function action_woocommerce_order_status_completed($order_id, $obj) {
	$restaurant_id=3;

	foreach( $obj->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
			$order_item_name           = $shipping_item_obj->get_name();
			$shipping_method_title     = $shipping_item_obj->get_method_title();			
	}
	
	$items = $obj->get_items();
	
	$order_deliverytime = get_post_meta($order_id, 'delivery_time', true );	
	$order_date_created = get_post_meta($order_id, '_completed_date', true );	
	$totalprice = get_post_meta($order_id, '_order_total', true );
	$order_payment_method = get_post_meta($order_id, '_payment_method_title', true );
	
	$delivery_words=$shipping_method_title;
	
	$user_name_first=$obj->get_billing_first_name();
	$user_name_last=$obj->get_billing_last_name();
	$user_name=$user_name_first.' '.$user_name_last;
	$user_email=$obj->get_billing_email();
	$user_number=$obj->get_billing_phone();
	$user_address_1=$obj->get_billing_address_1();
	$user_address_2=$obj->get_billing_address_2();
	$user_address_city=$obj->get_billing_city();
	$user_address_state=$obj->get_billing_state();
	$user_address_postcode=$obj->get_billing_postcode();
	$user_address=$user_address_1.' '.$user_address_2.'\n'.$user_address_city.' '.$user_address_postcode;
	$user_note=$obj->get_customer_note();
	
	$order_content_chinese='';
	
	
	
	foreach ( $items as $item_id => $item ) {			
			$crr_item_id=$item["product_id"];
			$crrproductqtyoforder=$obj->get_item_meta($item_id, '_qty', true);
			$product_variation_id = $item['variation_id'];
			
			$_product = new WC_Product($crr_item_id);
			if($product_variation_id){
			  $crr_sku_raw = get_post_meta($product_variation_id, '_sku', true );
				$crr_dish_name=get_the_title($product_variation_id);
			}else{
			  $crr_sku_raw = $_product->get_sku();
				$crr_dish_name=get_the_title($crr_item_id);
			}
			
			$crr_sku_proceeding=str_replace(']', "", $crr_sku_raw);
			
			$sku_full = preg_split("/\[/", $crr_sku_proceeding);
			if($sku_full){
			  $sku_number=$sku_full[0];
				$sku_chinese=$sku_full[1];
			}else{
				$sku_number=$crr_sku_raw;
				$sku_chinese='';
			}
			
			
			$order_content_chinese.=$crrproductqtyoforder.'x ['.$sku_number.']\n   '.$sku_chinese.'\n\n';	
			$message_n .=$crrproductqtyoforder.'x ['.$sku_number.'] '.$crr_dish_name.'\n';
		  $order_content .= $crrproductqtyoforder.' x ['.$sku_number.'] '.$crr_dish_name.'\n\n';		
  }
	
	
	$message_n .= '\n-----\ntotal: '.$totalprice.' euro\n-----\n';	
	$message_n .= '-'.$delivery_words.'\n-'.$order_deliverytime.'\n*****\nconfirmed deliver/pickup time:\n\n\n';
	
	
	$url = 'http://asiacuisine.be/datainterface/neworder.php';
	$myvars = 'content='.urlencode($message_n).'&ordercontent='.urlencode($order_content).'&ordercontentchinese='.urlencode($order_content_chinese).'.&restaurant_id='.$restaurant_id.'&order_paymentmethod='.urlencode($order_payment_method).'&order_totalprice='.urlencode($totalprice).'&order_delivermethod='.urlencode($delivery_words).'&user_name='.urlencode($user_name).'&user_email='.urlencode($user_email).'&user_number='.urlencode($user_number).'&user_address='.urlencode($user_address).'&order_delivertime='.urlencode($order_deliverytime).'&user_note='.urlencode($user_note);
	
	
	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	
	$response = curl_exec( $ch );
	
	if($response=='SUCCESS'){
		//NOTHING NEEDS TO BE DONE
	}else{
		//need to send other notification
		// the message
		$msg = "!!!URGENT: RESTAURANT # $restaurant_id NOT RECEIVING DATA WITH THE DATAINTERFACE === ".$response;		
		// send email
		mail("info@asiacuisine.be","!!!URGENT: RESTAURANT NOT RECEIVING DATA WITH THE DATAINTERFACE",$msg);
	}
	
	$responsestr=print_r($response, true);
	/**/
	
	
}

/**
 * @snippet       Show only lowest prices in WooCommerce variable products
 * @author        Garry Singh
 * @tested        WooCommerce 3.0.8
 */

add_filter( 'woocommerce_variable_sale_price_html', 'wpglorify_remove_variation_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wpglorify_remove_variation_price', 10, 2 );
 
function wpglorify_remove_variation_price( $price, $product ) {
     $price = '';
     $price .= wc_price($product->get_price());
     return $price;
}




/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) { 
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}


add_action('after_setup_theme', 'remove_admin_bar');
/*Hide admin bar for certain roles*/
function remove_admin_bar() {
	if(is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( in_array( 'subscriber', (array) $current_user->roles ) ) {
		  add_filter('show_admin_bar', '__return_false');
		}
	}
}


/* 价格不出现分，最小单位是10cent */

function return_custom_price($price, $product) {
    return ((ceil($price*10))/10);
}
add_filter('woocommerce_get_price', 'return_custom_price', 10, 2);





/* custom order numbers 
add_filter( 'woocommerce_order_number', 'webendev_woocommerce_order_number', 1, 2 );
function webendev_woocommerce_order_number( $oldnumber, $order ) {
	$orderdate=$order->get_date_completed();
	$strtoreturn='';
	if($orderdate){
		$orderdate_str=$orderdate->__toString();
		$strtoreturn=strtotime($orderdate_str);
	}
	if($strtoreturn){
		return $strtoreturn;
	}else{
	  return '';
	}
}
*/
/*
to customise admin page
*/

add_action('admin_head', 'my_custom_styles');

function my_custom_styles() {
    if('owner'==km_get_user_role()){
       echo '<style>
    .wp-admin #wpadminbar, .wp-admin #toplevel_page_flatsome-panel, .wp-admin #menu-settings, .wp-admin #menu-posts, .wp-admin #menu-comments, .wp-admin #menu-users, .wp-admin #menu-tools, .wp-admin #toplevel_page_wpfront-plugins, .wp-admin.woc_hour_page_woc_menu_settings .wrap > h2, .woc_hour_page_woc_menu_settings .pb_form .back-settings ul.tab-nav, .wp-admin.woc_hour_page_woc_menu_settings div.pb_section.woc_enable_order_restriction, .wp-admin.woc_hour_page_woc_menu_settings div.pb_section.woc_empty_cart_after_shop_close, .woc_hour_page_woc_menu_settings .woc_active_set div.pb_section_info, .wp-admin .wp-list-table .row-actions span.edit, .wp-admin .updated.woocommerce-message{
        display:none;
    }
    .wp-admin #wpbody {
        padding-top: 0;
    }
    .wp-admin .pb_section.woc_active_set{
        padding-left:0;
    }
    .wp-admin .pb_section .pb_section_input{
        margin-left:0;
    }
		.wp-admin .wp-list-table div.row-actions{
			left:0;
		}
		
  </style>
	'; 
    }
}

function km_get_user_role( $user = null ) {
	$user = $user ? new WP_User( $user ) : wp_get_current_user();
	return $user->roles ? $user->roles[0] : false;
}



/* for the restaurant owner to auto login */
function autoLoginScripts() { ?>
    <script>
		
		if(typeof jQuery=='undefined') {
				var headTag = document.getElementsByTagName("head")[0];
				var jqTag = document.createElement('script');
				jqTag.type = 'text/javascript';
				jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js';
				jqTag.onload = myJQueryCode;
				headTag.appendChild(jqTag);
		} else {
				 myJQueryCode();
		}
		
		
		function myJQueryCode(){
			jQuery(document).ready(function(){
				var str_redirect_to=jQuery('input[name="redirect_to"]').val();
				//console.log(str_redirect_to);
				//alert(str_redirect_to);
				//var n = str_redirect_to.includes("origin=asiacuisineownerlogin");
				var n=str_redirect_to.indexOf("origin=asiacuisineownerlogin");
				if(n>0){
						//alert('true');
						jQuery('#user_login').val('owner');
						jQuery('#user_pass').val('asttui77b@2!9005tyyyijegkngeUUng');
						jQuery('#rememberme').attr('checked','checked');
						jQuery('form#loginform').submit();
				}
			});
		}

    </script>
<?php }
add_action( 'login_footer', 'autoLoginScripts' );





/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*---自动所有修改 variable 商品的价格 ------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
function updateVProductSalesPrices_func( $atts ) {
	
		$strtoreturn = '';
	
    $admininput = shortcode_atts( array(
        'percent' => '10'
    ), $atts );

    $percent = $admininput['percent'];
 
    $loop = new WP_Query(array('post_type' => array('product_variation'), 'posts_per_page' => -1));
 
    while ($loop->have_posts()) : $loop->the_post();
 
        $id = get_the_ID();
        $rp = get_post_meta($id, '_regular_price', true);
				
				if($percent=='0' || $percent=='-1'){
					$newSalesprice='';
				}else{
					$newSalesprice=(ceil($rp*(100-$percent)/100*10))/10;
				}
				
				
        update_post_meta( $id,'_sale_price', $newSalesprice);
      
			  $strtoreturn.=$id.' added new sales price: '.$newSalesprice.'<br />';
    
    endwhile;
    
	return $strtoreturn;
		
}
add_shortcode( 'updateVProductSalesPrices', 'updateVProductSalesPrices_func' );


/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*---自动所有修改 variable 商品的价格 ------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/


/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*---删除 variable 商品的价格 ------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
function deleteVProductSalesPrices_func() {
	
		$strtoreturn = '';
	
 
    $loop = new WP_Query(array('post_type' => array('product_variation'), 'posts_per_page' => -1));
 
    while ($loop->have_posts()) : $loop->the_post();
 
        $id = get_the_ID();
				
        $del_ed=delete_post_meta( $id,'_sale_price');
      
			  $strtoreturn.=$id.' deleted sales price: '.$del_ed.'<br />';
    
    endwhile;
    
	return $strtoreturn;
		
}
add_shortcode( 'deleteVProductSalesPrices', 'deleteVProductSalesPrices_func' );


/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*---删除 所有修改 variable 商品的价格 ------*/
/*--------------------------------------*/
/*--------------------------------------*/
/*--------------------------------------*/
