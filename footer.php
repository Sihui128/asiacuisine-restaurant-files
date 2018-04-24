<?php
/**
 * The template for displaying the footer.
 *
 * @package flatsome
 */

global $flatsome_opt;
?>

</main><!-- #main -->

<footer id="footer" class="footer-wrapper">	

	<?php do_action('flatsome_footer'); ?>

</footer><!-- .footer-wrapper -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>
<!-- this is Studio FIDES custom footer -->



<script type="text/javascript">
/* ==============================================
// script for lunch availability 把多余的时间选择删掉 
===============================================*/
<?php
$crr_day=date('l');
$crr_h=date('H');
$crr_m=date('i');
$crr_time=$crr_h+($crr_m/60);
?>
	

var crr_time=<?php echo $crr_time; ?>;
var crr_day="<?php echo $crr_day; ?>";

function makeDeliveryTimeListAccurate(){
	jQuery('#delivery_time').children('option').each(function(index, element) {
		var crr_obj=jQuery(this);
		
		var crr_time_value_str=crr_obj.val();
		
		if(jQuery.trim(crr_time_value_str)!='zo spoedig mogelijk' && jQuery.trim(crr_time_value_str)!='Dès que possible' && jQuery.trim(crr_time_value_str)!='as soon as possible'){
		
			var crr_time_v_timeonly=jQuery.trim(crr_time_value_str);
			
			var crr_time_array=crr_time_v_timeonly.split(":");
			var crr_time_hour=parseInt(crr_time_array[0]);
			//console.log(crr_time_hour);
			var crr_time_min=parseInt(crr_time_array[1]);
			var crr_time_in_num=crr_time_hour+(crr_time_min/60);
			//console.log(crr_time_v_timeonly+'---'+crr_time_in_num);
			
			if(crr_time_in_num<=(crr_time+0.15)){
				crr_obj.remove();
			}
			
		}
		
	});
}
makeDeliveryTimeListAccurate();

/* ==============================================
// 餐馆关门，停止网上预订
===============================================*/

if(jQuery('#restaurant-close').length>0){
	disableOrderBTN();
}

function disableOrderBTN(){
	console.log('r closed');
	var wordonBTN=jQuery('a.ajax_add_to_cart').html();
	console.log(' wordonBTN :'+ wordonBTN);
	jQuery('div.add-to-cart-button').html('<span class="no-order-btn">'+wordonBTN+'</span>');
	
	jQuery('button.single_add_to_cart_button').attr('disabled','disabled').css('cursor','not-allowed');
}


//restaurant-nodelivery

/* ==============================================
// 餐馆停止送餐
===============================================*/

if(jQuery('#restaurant-nodelivery').length>0){
	disableDeliverBTN();
}

function disableDeliverBTN(){
	jQuery('tr.delivery-explained').css('display','none');
	
	jQuery( document ).ajaxComplete(function( event, xhr, settings ) {
		
    jQuery('tr.delivery-explained').css('display','none');
		
		jQuery('#shipping_method li').each(function(){
			var crr_obj=jQuery(this);
			var crr_ship_str=crr_obj.children('label').html().toLowerCase();;
			
			if(crr_ship_str=='afhalen' || crr_ship_str=='pickup' || crr_ship_str=='pick-up'){
				crr_obj.children('input').attr('checked','checked');
			}else if(crr_ship_str=='thuisbezorgen' || crr_ship_str=='livraison à domicile'){
				crr_obj.children('input').removeAttr('checked');
				crr_obj.css('display','none');
			}
		});		
  });
	
}






/* ==============================================
//  填充用户信息
===============================================*/
<?php
if(isset($_SESSION['billing_first_name']) && isset($_SESSION['billing_email']) && isset($_SESSION['billing_phone']) && isset($_SESSION['billing_address_1']) && isset($_SESSION['billing_postcode'])
 && isset($_SESSION['billing_city'])){
?>
var passed_billing_first_name="<?php echo $_SESSION['billing_first_name'];?>";
var passed_billing_email="<?php echo $_SESSION['billing_email'];?>";
var passed_billing_phone="<?php echo $_SESSION['billing_phone'];?>";
var passed_billing_address_1="<?php echo $_SESSION['billing_address_1'];?>";
var passed_billing_postcode="<?php echo $_SESSION['billing_postcode'];?>";
var passed_billing_city="<?php echo $_SESSION['billing_city'];?>";


if(jQuery('form.woocommerce-checkout').length>0){
	if(jQuery('input#billing_first_name').val()==''){
		jQuery('input#billing_first_name').val(passed_billing_first_name);
	}
	if(jQuery('input#billing_email').val()==''){
		jQuery('input#billing_email').val(passed_billing_email);
	}
	if(jQuery('input#billing_phone').val()==''){
		jQuery('input#billing_phone').val(passed_billing_phone);
	}
	if(jQuery('input#billing_address_1').val()==''){
		jQuery('input#billing_address_1').val(passed_billing_address_1);
	}
	if(jQuery('input#billing_postcode').val()==''){
		jQuery('input#billing_postcode').val(passed_billing_postcode);
	}
	if(jQuery('input#billing_city').val()==''){
		jQuery('input#billing_city').val(passed_billing_city);
	}
}



<?php	 
}
?>
</script>

</body>
</html>