<?php
/*
Plugin Name: 		پلاگین پرداخت زرین پال برای کلاسی پرس
Plugin URI: 		http://zarinpal.com
Description: 		پلاگین پرداخت زرین پال برای کلاسی پرس | از سری محصولات وب سایت <a href="http://zarinpal.com">زرین پال</a>
Version: 			1.0.0
Author: 			Armin Zahedi
Author URI: 		http://ar4min.ir
License: 			GPLv2
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
*/
//redirect nosy to Mahak....
if (!defined( 'ABSPATH' )) 
{
	header('Location: http://www.mahak-charity.org/bankingform_parsian.php');
	exit;
}
include_once("include/Zarinpal_Appthemes.php");

?>