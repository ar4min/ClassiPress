<?php
ob_start();
if (!defined( 'ABSPATH' )) 
	exit;
add_action( 'init', 'zarinpal_app_gateway_load', 1);
function zarinpal_app_gateway_load() {
	
	if (!class_exists('APP_Gateway') || !current_theme_supports('app-payments')) 
		return;
		
	
	if ( !function_exists('Webforest_Appthemes_Iran_Currencies') && !function_exists('NasimNet_Appthemes_Iran_Currencies') )
	{
	
		function Webforest_Appthemes_Iran_Currencies() {
			$Iran_Currency = array('IRR' => 'ریال', 'IRT' => 'تومان');
			foreach ($Iran_Currency as $k => $v)
			{
				$details = array('symbol' => $v, 'name' => $v.' ایران' );
				APP_Currencies::add_currency( $k, $details );
			}
			return true;
		}
		Webforest_Appthemes_Iran_Currencies();
	
	}
	
	define( 'ZARINPALAP', 'Appthemes_Zarinpal');
	
	//Webforest_Zarinpal Class ....
	class Webforest_Zarinpal extends APP_Gateway {
		
		protected $options;
		
		public function __construct() {
			
			$title = trim(get_option(ZARINPALAP."_title"));
			if (!$title) 
				$title = __( 'درگاه پرداخت زرین پال', ZARINPALAP );
			
			parent::__construct( 'zarinpal', array(
					'admin' 	=> __( 'زرین پال', ZARINPALAP ),
					'dropdown' 	=> $title
				) 
			);
		
		}
		
		public function create_form( $order, $options ){ 
			//not required ...
		}
		
		public function form() {
			$title   	  = __( 'درگاه پرداخت زرین پال', ZARINPALAP );
			$description  = "<a target='_blank' href='http://webforest.ir/'><img border='0' style='float:right; margin-left:15px' src='".plugin_dir_url( __FILE__ )."assets/Webforest.png'></a><br/>";
			$description .= sprintf(__( '<br/><a target="_blank" style="text-decoration:none;" href="%s">وب سایت پشتیبانی درگاه پرداخت &#187;</a>', ZARINPALAP ), "http://webforest.ir") . "<br/><br/><br/><br/>";
			$description .= '<strong>'.__( 'تنظیمات درگاه زرین پال', ZARINPALAP ) . '</strong>';
			$fields = array(
					array(
							'name'			=> 'title',
							'title'       	=> __( 'نام نمایشی درگاه', ZARINPALAP ),
							'type'        	=> 'text',
							'default'     	=> __( 'درگاه پرداخت زرین پال', ZARINPALAP ),
							'desc' 			=> '<p>'.__( 'عنوان نمایشی درگاه پرداخت', ZARINPALAP ).'</p>'
					),
					// array(
					// 		'name'			=> 'server',
					// 		'title' 		=> __('انتخاب سرور', ZARINPALAP ),
					// 		'type' 			=> 'select',
					// 		'values' 		=> array('german' => 'آلمان', 'iran' => 'ایران'),
					// 		'default' 		=> 'german',
					// 		'desc' 			=> '<p>'.__("کشور سرور زرین پال", ZARINPALAP).'</p>'
					// ),
					array(
							'name'			=> 'merchant',
							'title' 		=> '<br/>'.__('مرچنت', ZARINPALAP ),
							'type' 			=> 'text',
							'default' 		=> '',
							'desc' 			=> '<p>'.__("مرچنت زرین پال", ZARINPALAP).'</p>'
					),
					array(
							'name'			=> 'query',
							'title' 		=> '<br/>'.__('نام لاتین درگاه', ZARINPALAP ),
							'type' 			=> 'text',
							'default' 		=> 'ZarinPal',
							'desc' 			=> '<p>'.__("این نام در هنگام بازگشت از بانک در آدرس بازگشت از بانک نمایان خواهد شد . از به کاربردن حروف زائد و فاصله جدا خودداری نمایید .", ZARINPALAP).'</p>'
					),
					array(
							'name'			=> 'success_massage',
							'title'       => __( 'پیام پرداخت موفق', ZARINPALAP ),
							'type'        => 'textarea',
							'desc' => __( '<br/>متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {transaction_id} برای نمایش کد رهگیری (شماره سند) زرین پال استفاده نمایید .', ZARINPALAP ),
							'default'     => __( 'با تشکر از شما . سفارش شما با موفقیت پرداخت شد .', ZARINPALAP ),
							'extra' => array(
								'style' => 'width:500px;height:100px'
							),
					),
					array(
							'name'			=> 'failed_massage',
							'title'       => __( 'پیام پرداخت ناموفق', ZARINPALAP ),
							'type'        => 'textarea',
							'desc' => __( '<br/>متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {fault} برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت زرین پال ارسال میگردد .', ZARINPALAP ),
							'default'     => __( 'پرداخت شما ناموفق بوده است . لطفا مجددا تلاش نمایید یا در صورت بروز اشکال با مدیر سایت تماس بگیرید .', ZARINPALAP ),
							'extra' => array(
								'style' => 'width:500px;height:100px'
							),
					),
					array(
							'name'			=> 'cancelled_massage',
							'title'       => __( 'پیام انصراف از پرداخت', ZARINPALAP ),
							'type'        => 'textarea',
							'desc' => __( '<br/>متن پیامی که میخواهید بعد از انصراف کاربر از پرداخت نمایش دهید را وارد نمایید . این پیام بعد از بازگشت از بانک نمایش داده خواهد شد .', ZARINPALAP ),
							'default'     => __( 'پرداخت به دلیل انصراف شما ناتمام باقی ماند .', ZARINPALAP ),
							'extra' => array(
								'style' => 'width:500px;height:100px'
							),
					)
			);
			
			if (isset($_POST["gateways"]["zarinpal"]["title"]) && strip_tags($_POST["gateways"]["zarinpal"]["title"])) 
				update_option(ZARINPALAP."_title", strip_tags($_POST["gateways"]["zarinpal"]["title"]));
			
			
			if (isset($_POST["gateways"]["zarinpal"]["success_massage"])) 
				update_option(ZARINPALAP."_success_massage", $_POST["gateways"]["zarinpal"]["success_massage"]);

			
			if (isset($_POST["gateways"]["zarinpal"]["failed_massage"])) 
				update_option(ZARINPALAP."_failed_massage", $_POST["gateways"]["zarinpal"]["failed_massage"]);

			
			if (isset($_POST["gateways"]["zarinpal"]["cancelled_massage"])) 
				update_option(ZARINPALAP."_cancelled_massage", $_POST["gateways"]["zarinpal"]["cancelled_massage"]);
			
			
			$config = array( array(
							'title' => $title."<br/><br/><div style='font-size:13px;font-weight:normal;'>".$description."</div><br/>",
							'fields' => $fields
				)
			);
			return apply_filters( 'appthemes_zarinpal_settings_form', $config );
		}
	
		public function process( $order, $options ){
			Proccess_ZarinPal_By_Webforest ($order, $options, false);
			return true;
		}
	}
	appthemes_register_gateway( 'Webforest_Zarinpal' );
	//Webforest_Zarinpal Class ....
	
	
		
	class APP_Escrow_Zarinpal extends Webforest_Zarinpal implements APP_Escrow_Payment_Processor {
		
		public function supports( $service = 'instant' ){
			switch ( $service ) {
				case 'escrow':
					return true;
				break;
				default:
					return parent::supports( $service );
				break;
			}
		}
		
		public function form() {
			$fields = parent::form();
			return apply_filters( 'appthemes_zarinpal_escrow_settings_form', $fields );
		}
		
		public function user_form() {
			$fields = array(
				'title' => __( 'اطلاعات پرداخت کاربر', ZARINPALAP ),
				'fields' => array(
					array(
						'title' => __( 'نام بانک', ZARINPALAP ),
						'type' => 'text',
						'name' => 'BANK',
						'extra' => array(
							'class' => 'text regular-text',
						),
						'desc' => __( 'نام بانک صادر کننده عابر بانک', ZARINPALAP ),
					),
					array(
						'title' => __( 'شماره 16 رقمی کارت', ZARINPALAP ),
						'type' => 'text',
						'name' => 'card-Number',
						'extra' => array(
							'class' => 'text regular-text',
						),
						'desc' => __( 'شماره 16 رقمی کارت بانکی خود را وارد کنید .', ZARINPALAP ),
					),
				),
			);
			return apply_filters( 'appthemes_zarinpal_escrow_user_settings_form', $fields );
		}
		
		public function get_details( APP_Escrow_Order $order, array $options ) {
			//not required ...
		}	
		
		public function process_escrow( APP_Escrow_Order $order, array $options ) {
			Proccess_ZarinPal_By_Webforest ($order, $options, true);
			return true;
		}


		public function complete_escrow( APP_Escrow_Order $order, array $options ) {

		}

		public function fail_escrow( APP_Escrow_Order $order, array $options ) {

		}

	}
	
	add_action( 'init', 'Webforest_Zarinpal_Appthemes_Init_Escrow', 15 );
	function Webforest_Zarinpal_Appthemes_Init_Escrow() {

		if (function_exists('appthemes_is_escrow_enabled') && appthemes_is_escrow_enabled()) {
	
			appthemes_register_gateway( 'APP_Escrow_Zarinpal' );	
		
			add_action('parse_request', 'Webforest_Zarinpal_escrow_parse_request');
			function Webforest_Zarinpal_escrow_parse_request() {
				if (stripos($_SERVER["REQUEST_URI"], "/transfer-funds/") === 0 && isset($_GET['oid']) && intval($_GET['oid'])){
					$order = appthemes_get_order( intval($_GET['oid']) );
					if ($order && is_object($order) && $order->get_gateway() == "zarinpal" && $order->is_escrow())
					{
						$url = $order->get_return_url();
						if ($url && !stripos($url, $_SERVER["REQUEST_URI"])) { wp_redirect($url); die; }
					}
				}
				return true;
			}
			
			add_action('hrb_before_workspace_project_details', 'Webforest_Zarinpal_escrow_pay');
			function Webforest_Zarinpal_escrow_pay(){
				$order = (get_the_ID()) ? appthemes_get_order_connected_to( get_the_ID() ) : "";
				
			if ($order && is_object($order) && $order->is_escrow() && $order->get_author() == get_current_user_id() && $order->get_status() != APPTHEMES_ORDER_PAID )
				echo '<a href="'.($order->get_gateway()=="zarinpal" ? $order->get_return_url() : site_url("transfer-funds/?oid=".$order->get_id())).'"><span class="label right project-status">'.__( 'Transfer Funds Now &#187;', ZARINPALAP ).'</span></a>';
				return true;
			}
		
		}
	
	}
		
	function Proccess_ZarinPal_By_Webforest ( $order, $options, $escrow = false )	{
		
		$options["title"] 	= $options["title"] ? $options["title"] : __('درگاه پرداخت زرین پال', ZARINPALAP);

		$query = $options["query"] ? $options["query"] : 'zarinpal';
		$user			= $order->get_author();
		$recipient = get_user_by( 'id', $order->get_author() );
		$order_id 		= $order->get_id();
		$order_currency = $order->get_currency();
		$amount	= $order->get_total();
		$status = appthemes_get_order($order_id)->get_status();
		
		if (!$order || !$order_id || !$amount || ($escrow && !$order->is_escrow()))
			throw new Exception('متاسفانه برخی متغیر های مورد نیاز برای شماره سفارش ' . $order_id . ' وجود ندارد و امکان پرداخت وجود ندارد .');
		
		if ( $status == APPTHEMES_ORDER_COMPLETED || $status == APPTHEMES_ORDER_PAID  || $status == APPTHEMES_ORDER_ACTIVATED) {
			echo '<h2>' . __( 'اخطار !', ZARINPALAP ) . '</h2>' . PHP_EOL;
			echo "<div align='center'>";
			echo "تراکنش قبلا انجام شده است .<br/><br/>";
			echo "</div>";
		}
		else {
			if (!$user && !$escrow) 
				$user = __( 'مهمان', ZARINPALAP );
			if (!$user){
				echo '<h2>' . __( 'اخطار !', ZARINPALAP ) . '</h2>' . PHP_EOL;
				echo "<div align='center'><a href='".wp_login_url(get_permalink())."'>".__( 'شما باید وارد سایت شوید . برای ورود به سایت کلیک نمایید .', ZARINPALAP )."</a></div>";
			}
			elseif ($amount < 0){
				echo '<h2>' . __( 'خطا !', ZARINPALAP ) . '</h2>' . PHP_EOL;
				echo "<div class='notice error alert-box'>".__( 'مبلغ پرداخت کوچک تر از صفر است و امکان پرداخت وجود ندارد . این موضوع را با مدیر سایت در میان بگذارید .', ZARINPALAP )."</div>";
			}
			elseif ( $amount == 0 ) {
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Success_before', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Success_before', $order_id, $options, $escrow );
			
				if ($escrow)
				{
					$item = $order->get_item();
				//	echo $item["post"]->post_title;
					$order->paid(); 
				}
				else
				{
					$order->complete(); 
				}
				
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Success_after', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Success_after', $order_id, $options, $escrow );
			
				wp_redirect( $order->get_return_url() );
				exit;
			}
			else if ( !isset($_GET['checkout_gateway']) ) {
				Send_to_ZarinPal_By_Webforest ( $order, $options, $escrow );
			}
			else if ( isset($_GET['checkout_gateway']) and $_GET['checkout_gateway'] == $query ) {
				Return_from_ZarinPal_By_Webforest ( $order, $options, $escrow );
			}
		}
		return true;
	}
	
	
	function Send_to_ZarinPal_By_Webforest ( $order, $options, $escrow = false ) {
		
		if ( isset($_GET['checkout_gateway']))
			return;
		ob_start();
		if(!extension_loaded('soap')){
			echo '<h1 class="single dotted">'.__('وضعیت پرداخت', ZARINPALAP).'</h1><br/>';
			$Note = __( 'خطا در هنگام ارسال به بانک : تابع SOAP بر روی سرور فعال نیست .', ZARINPALAP);
			echo "<div class='notice error alert-box'>".$Note."</div>";
			$order->log( __( 'خطا در هنگام ارسال به بانک : تابع SOAP بر روی سرور فعال نیست .', ZARINPALAP ) ,'failed');
			return false;
		}
		
		$query = $options["query"] ? $options["query"] : 'zarinpal';
		$user			= $order->get_author();
		$recipient = get_user_by( 'id', $order->get_author() );
		$order_id 		= $order->get_id();
		$order_currency = $order->get_currency();
		$amount	= $order->get_total();
		
		update_post_meta( $order_id, '_checked', 'no' );
		
		$Amount = intval($amount);
		if ( strtolower($order_currency) == strtolower('IRR') || strtolower($order_currency) == strtolower('RIAL') )
				$Amount = $Amount/10;
			
		$MerchantID = $options["merchant"];
		$Description = 'پرداخت به شماره سفارش : '.$order_id.' | پرداخت کننده : '.($recipient->display_name ? $recipient->display_name : $recipient->user_email);
		$Email = $recipient->user_email ? $recipient->user_email : '-';
		$Mobile = '-';
		$CallbackURL = add_query_arg( 'checkout_gateway' , $query , $order->get_return_url() );
					
				
		//Hooks for iranian developer
		$Description = apply_filters( 'Appthemes_ZarinPal_Description', $Description, $order_id );
		$Email = apply_filters( 'Appthemes_ZarinPal_Email', $Email, $order_id );
		$Mobile = apply_filters( 'Appthemes_ZarinPal_Mobile', $Mobile, $order_id );
		do_action( 'Appthemes_ZarinPal_Gateway_Payment', $order_id, $options, $escrow, $Description, $Email, $Mobile );
		do_action( 'Appthemes_Gateway_Payment', $order_id, $options, $escrow );
					
				
		$data = array("merchant_id" => $MerchantID,
  		  "amount" =>$Amount,
  		  "callback_url" => $CallbackURL,
   		 "description" => $Description,
   		 "metadata" => [ "email" => $Email,"mobile"=>$Mobile],
   		 );
		$jsonData = json_encode($data);
		$ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
		curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 		   'Content-Type: application/json',
		    'Content-Length: ' . strlen($jsonData)
		));

		$result = curl_exec($ch);
		$err = curl_error($ch);
		$result = json_decode($result, true, JSON_PRETTY_PRINT);
		curl_close($ch);
					
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			if (empty($result['errors'])) {
				if ($result['data']['code'] == 100) {
					
					do_action( 'Ar4min_ZarinPal_Before_Send_to_Gateway', $order_id, $options, $escrow );
					do_action( 'Ar4min_Before_Send_to_Gateway', $order_id, $options, $escrow );
					ob_end_flush();
					ob_end_clean();
					if (!headers_sent()) {
						header('Location: https://www.zarinpal.com/pg/StartPay/'.$result['data']["authority"]);
						exit;
					}
					else {
						$redirect_page = 'https://www.zarinpal.com/pg/StartPay/'.$result['data']["authority"];
						echo "<script type='text/javascript'>window.onload = function () { top.location.href = '" . $redirect_page . "'; };</script>";
						exit;
					}		
				}
				else {
					$fault = $result['errors']['code'];
					
					do_action( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
					do_action( 'Ar4min_Send_to_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
					
					echo '<h1 class="single dotted">'.__('وضعیت پرداخت', ZARINPALAP).'</h1><br/>';
					$Note = sprintf( __( 'خطا در هنگام ارسال به بانک : %s', ZARINPALAP), Ar4min_Fault_ZarinPal($fault) );
					$Note = apply_filters( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_Note', $Note, $order_id, $fault );
					echo "<div class='notice error alert-box'>".$Note."</div>";
						
					$order->log( sprintf( __( 'خطا در هنگام ارسال به بانک : %s', ZARINPALAP), Ar4min_Fault_ZarinPal($fault) ) ,'failed');
					
					do_action( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
					do_action( 'Ar4min_Send_to_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
				}
			} 
			
			else {
				$fault = $result['errors']['code'];
					
				do_action( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
				do_action( 'Ar4min_Send_to_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
				
				echo '<h1 class="single dotted">'.__('وضعیت پرداخت', ZARINPALAP).'</h1><br/>';
				$Note = sprintf( __( 'خطا در هنگام ارسال به بانک : %s', ZARINPALAP), Ar4min_Fault_ZarinPal($fault) );
				$Note = apply_filters( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_Note', $Note, $order_id, $fault );
				echo "<div class='notice error alert-box'>".$Note."</div>";
					
				$order->log( sprintf( __( 'خطا در هنگام ارسال به بانک : %s', ZARINPALAP), Ar4min_Fault_ZarinPal($fault) ) ,'failed');
				
				do_action( 'Ar4min_ZarinPal_Send_to_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
				do_action( 'Ar4min_Send_to_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
		
			}
		}		
		
	}
	
	
	
	function Return_from_ZarinPal_By_Webforest ( $order, $options, $escrow = false ) {
		
		$query = $options["query"] ? $options["query"] : 'zarinpal';
		if ( !isset($_GET['checkout_gateway']) || ( isset($_GET['checkout_gateway']) and $_GET['checkout_gateway'] != $query ) )
			return;
		ob_start();
		$user_id			= $order->get_author();
		$recipient = get_user_by( 'id', $order->get_author() );
		$order_id 		= $order->get_id();
		$order = appthemes_get_order( $order_id );
		$order_currency = $order->get_currency();
		$amount	= $order->get_total();
		$checked = get_post_meta( $order_id, '_checked', true );
		echo '<h1 class="single dotted">'.__('وضعیت پرداخت', ZARINPALAP).'</h1><br/>';
	
		
		$Amount = intval($amount);
		if ( strtolower($order_currency) == strtolower('IRR') || strtolower($order_currency) == strtolower('RIAL') )
				$Amount = $Amount/10;
			
		$pay_Status = get_post_meta( $order_id, '_pay_Status', true ) ? get_post_meta( $order_id, '_pay_Status', true ) : '';
		$pay_Authority = get_post_meta( $order_id, '_pay_Authority', true ) ? get_post_meta( $order_id, '_pay_Authority', true ) : '';
		
		$pay_Status = isset($_GET['Status']) ? $_GET['Status'] : $pay_Status;
		$pay_Authority = isset($_GET['Authority']) ? $_GET['Authority'] : $pay_Authority;
		
		if ( isset($_GET['Status']) )
			update_post_meta( $order_id, '_pay_Status', $pay_Status );
	
		if ( isset($_GET['Authority']) )
			update_post_meta( $order_id, '_pay_Authority', $pay_Authority );
		
		
		if ( !isset($pay_Status) || !isset($pay_Authority) )
			return;
		
		if ( $pay_Status == '' || $pay_Authority == '' )
			return;
		
		
		if($pay_Status == 'OK'){
		
			$MerchantID = $options["merchant"];
			$Authority = $pay_Authority;
		
			$MerchantID = $options["merchant"];
			$Authority = $pay_Authority;
		
			$data = array("merchant_id" => $MerchantID, "authority" => $Authority, "amount" => $Amount);
		$jsonData = json_encode($data);
		$ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
		curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   		 'Content-Type: application/json',
   		 'Content-Length: ' . strlen($jsonData)
		));

		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);
		
			if($result['data']['code'] == 100){
				$status = 'completed';
				$transaction_id = $result['data']['ref_id'];
				$fault = 0;
			}
			else {
				$status = 'failed';
				$transaction_id = 0;
				$fault = $result['errors']['code'];
			}
		}
		else {
			$status = 'cancelled';
			$transaction_id = 0;
			$fault = 0;
		}
		
		
		update_post_meta( $order_id, '_checked', 'yes' );
		
		if ( isset($transaction_id) and $transaction_id!=0 and $checked != 'yes' ) {
			update_post_meta( $order_id, 'transaction_id', $transaction_id );
			$order->log( sprintf( __( 'کد رهگیری زرین پال : %s', ZARINPALAP), $transaction_id ), 'major' );
		}
		
		
		if ($status == "completed") {
			
			if ( $checked != 'yes' ) {
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Success_before', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Success_before', $order_id, $options, $escrow );
			
				if ($escrow)
				{
					$item = $order->get_item();
				//	echo $item["post"]->post_title;
					$order->paid(); 
				}
				else
				{
					$order->complete(); 
				}
				
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Success_after', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Success_after', $order_id, $options, $escrow );
				ob_end_flush();
				ob_end_clean();
				if (!headers_sent()) {
					wp_redirect( $order->get_return_url() );
					exit;
				}
				else {
					$redirect_page = $order->get_return_url();
					echo "<script type='text/javascript'>window.onload = function () { top.location.href = '" . $redirect_page . "'; };</script>";
					exit;
				}
			}			
		}
		elseif ( $status == 'cancelled') {
			
			if ( $checked != 'yes' ) {
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Cancelled_before', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Cancelled_before', $order_id, $options, $escrow );
			}
			
			
			$cancelled_massage = wpautop( wptexturize(get_option(ZARINPALAP."_cancelled_massage")) );
			$cancelled_massage = str_replace("{transaction_id}",$transaction_id, $cancelled_massage);
			if (!$cancelled_massage)
				$cancelled_massage = __( '', ZARINPALAP );
			$cancelled_massage = apply_filters( 'Appthemes_ZarinPal_Return_from_Gateway_Cancelled_Message', $cancelled_massage, $order_id, $options, $escrow, $transaction_id );
			echo "<div class='notice error alert-box'>".$cancelled_massage."</div>";
			echo '<br/><a class="re-pay button mbtn" href="'.$order->get_return_url().'">'.__('پرداخت', ZARINPALAP).'</a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;<a class="cancel-gateway button mbtn secondary previous-step" href="'.$order->get_cancel_url().'">'.__('تغییر روش پرداخت', ZARINPALAP).'</a>';
			echo '<br/>';
			
			if ( $checked != 'yes' ) {
				$order->log( __('تراکنش به دلیل انصراف کاربر ناتمام باقی ماند . روش پرداخت : درگاه زرین پال', ZARINPALAP) ,'failed' );
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Cancelled_after', $order_id, $options, $escrow );
				do_action( 'Appthemes_Return_from_Gateway_Cancelled_after', $order_id, $options, $escrow );
			}
		}
		else {
			
			$fault_error = Appthemes_Fault_ZarinPal($fault);
			
			if ( $checked != 'yes' ) {
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
				do_action( 'Appthemes_Return_from_Gateway_Failed_before', $order_id, $options, $escrow, $fault );
				$order->failed();
			}
			
			 
			$failed_massage = wpautop( wptexturize(get_option(ZARINPALAP."_failed_massage")) );
			$failed_massage = str_replace("{transaction_id}",$transaction_id, $failed_massage);
			$failed_massage = str_replace("{fault}", $fault_error, $failed_massage);
			if (!$failed_massage)
				$failed_massage = __( '', ZARINPALAP );
			$failed_massage = apply_filters( 'Appthemes_ZarinPal_Return_from_Gateway_Failed_Message', $failed_massage, $order_id, $options, $escrow, $transaction_id, $fault );
			echo "<div class='notice error alert-box'>".$failed_massage."</div><br/><br/>";
		
		
			if ( $checked != 'yes' ) {
				$order->log( sprintf( __( 'خطا در هنگام بازگشت از بانک : %s روش پرداخت : درگاه زرین پال', ZARINPALAP), $fault_error  ) , 'failed'  );
				do_action( 'Appthemes_ZarinPal_Return_from_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
				do_action( 'Appthemes_Return_from_Gateway_Failed_after', $order_id, $options, $escrow, $fault );
			}
			
		}
		
		return true;
	}
	
	
	add_action( 'appthemes_before_order_summary', 'Appthemes_ZarinPal_Order_Summary' );
	function Appthemes_ZarinPal_Order_Summary( $order ){
		
		if ( $order->get_gateway() != 'zarinpal')
			return;
		
		$order_id 		= $order->get_id();
		$order = appthemes_get_order( $order_id );
		$status = appthemes_get_order($order_id)->get_status();
		$transaction_id = get_post_meta( $order_id, 'transaction_id', true ) ? get_post_meta( $order_id, 'transaction_id', true ) : '-';
				
		$success_massage = wpautop( wptexturize(get_option(ZARINPALAP."_success_massage")) );
		$success_massage = str_replace("{transaction_id}",$transaction_id, $success_massage);
		if (!$success_massage)
			$success_massage = __( '', ZARINPALAP );
		$success_massage = apply_filters( 'Appthemes_ZarinPal_Return_from_Gateway_Success_Message', $success_massage, $order_id, $transaction_id );
				
		do_action( 'Appthemes_ZarinPal_Order_Summary_before', $order_id );
		do_action( 'Appthemes_Order_Summary_before', $order_id );
		
		if (  $status == APPTHEMES_ORDER_COMPLETED || $status == APPTHEMES_ORDER_PAID || $status == APPTHEMES_ORDER_ACTIVATED  ) {
			echo $success_massage;
		}
		
		do_action( 'Appthemes_ZarinPal_Order_Summary_after', $order_id );
		do_action( 'Appthemes_Order_Summary_after', $order_id );
		
	}
	
	
	function Appthemes_Fault_ZarinPal($err_code){
		$message = __('در حین پرداخت خطای سیستمی رخ داده است .', ZARINPALAP );
		switch($err_code){

			case "-1" :
				$message =  __("اطلاعات ارسال شده ناقص است .", ZARINPALAP );
			break;

			case "-2" :
				$message =  __("آی پی یا مرچنت زرین پال اشتباه است .", ZARINPALAP );
			break;

			case "-3" :
				$message =  __("با توجه به محدودیت های شاپرک امکان پرداخت با رقم درخواست شده میسر نمیباشد .", ZARINPALAP );
			break;
                
			case "-4" :
				$message =  __("سطح تایید پذیرنده پایین تر از سطح نقره ای میباشد .", ZARINPALAP );
			break;
                
			case "-11" :
				$message =  __("درخواست مورد نظر یافت نشد .", ZARINPALAP );
			break;
                
			case "-21" :
				$message =  __("هیچ نوع عملیات مالی برای این تراکنش یافت نشد .", ZARINPALAP );
			break;
                
			case "-22" :
				$message =  __("تراکنش نا موفق میباشد .", ZARINPALAP );
			break;
                
			case "-33" :
				$message =  __("رقم تراکنش با رقم وارد شده مطابقت ندارد .", ZARINPALAP );
			break;
                
			case "-40" :
				$message =  __("اجازه دسترسی به متد مورد نظر وجود ندارد .", ZARINPALAP );
			break;
                
			case "-54" :
				$message =  __("درخواست مورد نظر آرشیو شده است .", ZARINPALAP );
			break;
					
			case "100" :
				$message =  __("اتصال با زرین پال به خوبی برقرار شد و همه چیز صحیح است .", ZARINPALAP );
			break;
				
			case "101" :
				$message =  __("تراکنش با موفقیت به پایان رسیده بود و تاییدیه آن نیز انجام شده بود .", ZARINPALAP );
			break;
				
		}
		return $message;
	}
}
?>