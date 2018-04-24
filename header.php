<?php
session_start();

if(isset($_POST['billing_first_name'])){
	$_SESSION['billing_first_name']=$_POST['billing_first_name'];
}
if(isset($_POST['billing_email'])){
	$_SESSION['billing_email']=$_POST['billing_email'];
}
if(isset($_POST['billing_phone'])){
	$_SESSION['billing_phone']=$_POST['billing_phone'];
}
if(isset($_POST['billing_address_1'])){
	$_SESSION['billing_address_1']=$_POST['billing_address_1'];
}
if(isset($_POST['billing_postcode'])){
	$_SESSION['billing_postcode']=$_POST['billing_postcode'];
}
if(isset($_POST['billing_city'])){
	$_SESSION['billing_city']=$_POST['billing_city'];
}
?>



<!DOCTYPE html>
<!--[if IE 9 ]> <html <?php language_attributes(); ?> class="ie9 <?php flatsome_html_classes(); ?>"> <![endif]-->
<!--[if IE 8 ]> <html <?php language_attributes(); ?> class="ie8 <?php flatsome_html_classes(); ?>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>"> <!--<![endif]-->
<head>
   
    
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
	<?php
	if(is_product_category()){
	?>
<script type="text/javascript" src="/wp-content/themes/flatsome-child/scriptforstickyelement.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(e) {
    
  jQuery(".hide-for-medium > div.sidebar-inner").stick_in_parent({
		offset_top:188	
	});
	jQuery(document.body).trigger("sticky_kit:recalc");
	
});
</script>
  <?php
	}
	?>

</head>

<body <?php body_class(); // Body classes is added from inc/helpers-frontend.php ?>>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'flatsome' ); ?></a>

<div id="wrapper">

<?php do_action('flatsome_before_header'); ?>

<header id="header" class="header <?php flatsome_header_classes();  ?>">
   <div class="header-wrapper">
	<?php
		get_template_part('template-parts/header/header', 'wrapper');
	?>
   </div><!-- header-wrapper-->
</header>

<?php do_action('flatsome_after_header'); ?>

<main id="main" class="<?php flatsome_main_classes();  ?>">
