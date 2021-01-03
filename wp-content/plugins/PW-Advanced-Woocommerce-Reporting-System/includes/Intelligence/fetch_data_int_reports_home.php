<?php
if($file_used=="sql_table")
{
	$request 			= array();
	$start				= 0;

	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
	$date_format = $this->pw_date_format($pw_from_date);

	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid',NULL,true);
	$txtProduct 		= $this->pw_get_woo_requests('txtProduct',NULL,true);
	$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
	$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id 		= $this->pw_get_woo_requests('pw_brand_id','-1',true);

	$limit 				= $this->pw_get_woo_requests('limit',15,true);
	$p 					= $this->pw_get_woo_requests('p',1,true);

	$page 				= $this->pw_get_woo_requests('page',NULL,true);
	$order_id 			= $this->pw_get_woo_requests('pw_id_order',NULL,true);
	$pw_from_date 		= $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date 			= $this->pw_get_woo_requests('pw_to_date',NULL,true);

	$pw_txt_email 			= $this->pw_get_woo_requests('pw_email_text',NULL,true);

	$pw_txt_first_name		= $this->pw_get_woo_requests('pw_first_name_text',NULL,true);

	$pw_detail_view		= $this->pw_get_woo_requests('pw_view_details',"no",true);
	$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code',NULL,true);
	$state_code			= $this->pw_get_woo_requests('pw_states_code','-1',true);
	$pw_payment_method		= $this->pw_get_woo_requests('payment_method',NULL,true);
	$pw_order_item_name	= $this->pw_get_woo_requests('order_item_name',NULL,true);//for coupon
	$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code',NULL,true);//for coupon
	$pw_publish_order		= $this->pw_get_woo_requests('publish_order','no',true);//if publish display publish order only, no or null display all order
	$pw_coupon_used		= $this->pw_get_woo_requests('pw_use_coupon','no',true);
	$pw_order_meta_key		= $this->pw_get_woo_requests('order_meta_key','-1',true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

	$pw_paid_customer		= str_replace(",","','",$pw_paid_customer);
	//$pw_country_code		= str_replace(",","','",$pw_country_code);
	//$state_code		= str_replace(",","','",$state_code);
	//$pw_country_code		= str_replace(",","','",$pw_country_code);

	$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code','-1',true);
	$pw_coupon_codes		= $this->pw_get_woo_requests('pw_codes_of_coupon','-1',true);

	$pw_max_amount			= $this->pw_get_woo_requests('max_amount','-1',true);
	$pw_min_amount			= $this->pw_get_woo_requests('min_amount','-1',true);

	$pw_billing_post_code		= $this->pw_get_woo_requests('pw_bill_post_code','-1',true);

	////ADDED IN V4.0
	$pw_variation_id		= $this->pw_get_woo_requests('pw_variation_id','-1',true);
	$pw_variation_only		= $this->pw_get_woo_requests('pw_variation_only','-1',true);
	$pw_variations=$pw_item_meta_key='';
	if($pw_variation_id != '-1' and strlen($pw_variation_id) > 0){

		$pw_variations = explode(",",$pw_variation_id);
		//$this->print_array($pw_variations);
		$var = array();
		$item_att = array();
		foreach($pw_variations as $key => $value):
			$var[] .=  "attribute_pa_".$value;
			$var[] .=  "attribute_".$value;
			$item_att[] .=  "pa_".$value;
			$item_att[] .=  $value;
		endforeach;
		$pw_variations =  implode("', '",$var);
		$pw_item_meta_key =  implode("', '",$item_att);
	}
	$pw_variation_attributes= $pw_variations;
	$pw_variation_item_meta_key= $pw_item_meta_key;

	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','"trash"',true);

	$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

	///////////HIDDEN FIELDS////////////
	$pw_hide_os=$this->otder_status_hide;
	$pw_publish_order='no';
	$pw_order_item_name='';
	$pw_coupon_code='';
	$pw_coupon_codes='';
	$pw_payment_method='';

	$pw_order_meta_key='';

	$data_format=$this->pw_get_woo_requests('date_format',get_option('date_format'),true);

	$amont_zero='';
	//////////////////////

	/////////////////////////
	//APPLY PERMISSION TERMS
	$key='all_orders';

	$category_id=$this->pw_get_form_element_permission('pw_category_id',$category_id,$key);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id=$this->pw_get_form_element_permission('pw_brand_id',$brand_id,$key);

	$pw_product_id=$this->pw_get_form_element_permission('pw_product_id',$pw_product_id,$key);

	$pw_country_code=$this->pw_get_form_element_permission('pw_countries_code',$pw_country_code,$key);

	if($pw_country_code != NULL  && $pw_country_code != '-1')
		$pw_country_code  		= "'".str_replace(",","','",$pw_country_code)."'";

	$state_code=$this->pw_get_form_element_permission('pw_states_code',$state_code,$key);

	if($state_code != NULL  && $state_code != '-1')
		$state_code  		= "'".str_replace(",","','",$state_code)."'";

	$pw_order_status=$this->pw_get_form_element_permission('pw_orders_status',$pw_order_status,$key);

	if($pw_order_status != NULL  && $pw_order_status != '-1')
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	///////////////////////////

	$pw_variations_formated='';

	if(strlen($pw_max_amount)<=0) $_REQUEST['max_amount']	= 	$pw_max_amount = '-1';
	if(strlen($pw_min_amount)<=0) $_REQUEST['min_amount']	=	$pw_min_amount = '-1';

	if($pw_max_amount != '-1' || $pw_min_amount != '-1'){
		if($pw_order_meta_key == '-1'){
			$_REQUEST['order_meta_key']	= "_order_total";
		}
	}

	$last_days_orders 		= "0";
	if(is_array($pw_id_order_status)){		$pw_id_order_status 	= implode(",", $pw_id_order_status);}
	if(is_array($category_id)){ 		$category_id		= implode(",", $category_id);}

	/////ADDED IN VER4.0
	//BRANDS ADDONS
	if(is_array($brand_id)){ 		$brand_id		= implode(",", $brand_id);}

	if(!$pw_from_date){	$pw_from_date = date_i18n('Y-m-d');}
	if(!$pw_to_date){
		$last_days_orders 		= apply_filters($page.'_back_day', $last_days_orders);//-1,-2,-3,-4,-5
		$pw_to_date = date('Y-m-d', strtotime($last_days_orders.' day', strtotime(date_i18n("Y-m-d"))));}

	$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','order_id',true);
	$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);


	//pw_first_name_text
	$pw_txt_first_name_cols='';
	$pw_txt_first_name_join = '';
	$pw_txt_first_name_condition_1 = '';
	$pw_txt_first_name_condition_2 = '';

	//pw_email_text
	$pw_txt_email_cols ='';
	$pw_txt_email_join = '';
	$pw_txt_email_condition_1 = '';
	$pw_txt_email_condition_2 = '';

	//SORT BY
	$pw_sort_by_cols ='';

	//CATEGORY
	$category_id_join ='';
	$category_id_condition = '';

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id_join ='';
	$brand_id_condition = '';

	//ORDER ID
	$pw_id_order_status_join ='';
	$pw_id_order_status_condition = '';

	//COUNTRY
	$pw_country_code_join = '';
	$pw_country_code_condition_1 = '';
	$pw_country_code_condition_2 = '';

	//STATE
	$state_code_join= '';
	$state_code_condition_1 = '';
	$state_code_condition_2 = '';

	//PAYMENT METHOD
	$pw_payment_method_join= '';
	$pw_payment_method_condition_1 = '';
	$pw_payment_method_condition_2 = '';

	//POSTCODE
	$pw_billing_post_code_join = '';
	$pw_billing_post_code_condition= '';

	//COUPON USED
	$pw_coupon_used_join = '';
	$pw_coupon_used_condition = '';

	//VARIATION ID
	$pw_variation_id_join = '';
	$pw_variation_id_condition = '';

	////ADDED IN V4.0
	//VARIATION
	$pw_variation_item_meta_key_join='';
	$sql_variation_join='';
	$pw_show_variation_join='';
	$pw_variation_item_meta_key_condition='';
	$sql_variation_condition='';

	//VARIATION ONLY
	$pw_variation_only_join = '';
	$pw_variation_only_condition = '';

	//VARIATION FORMAT
	$pw_variations_formated_join = '';
	$pw_variations_formated_condition = '';

	//ORDER META KEY
	$pw_order_meta_key_join = '';
	$pw_order_meta_key_condition = '';

	//COUPON CODES
	$pw_coupon_codes_join = '';
	$pw_coupon_codes_condition = '';

	//COUPON CODE
	$pw_coupon_code_condition = '';

	//DATA CONDITION
	$date_condition = '';

	//ORDER ID
	$order_id_condition = '';

	//PAID CUSTOMER
	$pw_paid_customer_condition = '';

	//PUBLISH ORDER
	$pw_publish_order_condition_1 = '';
	$pw_publish_order_condition_2 = '';

	//ORDER ITEM NAME
	$pw_order_item_name_condition = '';

	//txt PRODUCT
	$txtProduct_condition = '';

	//PRODUCT ID
	$pw_product_id_condition = '';

	//CATEGORY ID
	$category_id_condition = '';

	//ORDER STATUS ID
	$pw_id_order_status_condition = '';

	//ORDER STATUS
	$pw_order_status_condition = '';

	//HIDE ORDER STATUS
	$pw_hide_os_condition = '';

	////ADDED IN VER4.0
	/// COST OF GOOD
	$pw_show_cog_cols='';
	$pw_show_cog_join='';
	$pw_show_cog_condition='';

	$sql_columns .= "
        billing_country.meta_value as billing_country,
        DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') 													AS order_date,
        pw_posts.post_date  													AS full_date,
		pw_woocommerce_order_items.order_id 															AS order_id,					
		pw_woocommerce_order_items.order_item_name 													AS product_name,					
		pw_woocommerce_order_items.order_item_id														AS order_item_id,
		woocommerce_order_itemmeta.meta_value 														AS woocommerce_order_itemmeta_meta_value,					
		(pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) 			AS sold_rate,
		(pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) 			AS product_rate,
		(pw_woocommerce_order_itemmeta4.meta_value) 													AS item_amount,
		(pw_woocommerce_order_itemmeta2.meta_value) 													AS item_net_amount,
		(pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) 			AS item_discount,					
		pw_woocommerce_order_itemmeta2.meta_value 														AS total_price,
		count(pw_woocommerce_order_items.order_item_id) 												AS product_quentity,
		woocommerce_order_itemmeta.meta_value 														AS product_id
		,woocommerce_order_itemmeta_v.meta_value 														AS variation_id
		
		,pw_woocommerce_order_itemmeta3.meta_value 													AS 'product_quantity'					
		,pw_posts.post_status 																			AS post_status
		,pw_posts.post_status 																			AS order_status
		
		";

	$sql_joins ="{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id				
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta 	ON woocommerce_order_itemmeta.order_item_id		=	pw_woocommerce_order_items.order_item_id
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta_v 	ON woocommerce_order_itemmeta_v.order_item_id		=	pw_woocommerce_order_items.order_item_id
		
		";

	$sql_joins.="
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta2 	ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta3 	ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta4 	ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal'
        LEFT JOIN  {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID
        ";

	$post_type_condition="pw_posts.post_type = 'shop_order' AND billing_country.meta_key	= '_billing_country'";

	$other_condition_1 = "
		AND woocommerce_order_itemmeta.meta_key = '_product_id' AND woocommerce_order_itemmeta_v.meta_key = '_product_id' ";

	$other_condition_1 .= "
		AND pw_woocommerce_order_itemmeta2.meta_key='_line_total'
		AND pw_woocommerce_order_itemmeta3.meta_key='_qty' ";


	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
	}

	if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
		$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";

	if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
		$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";


	$sql ="SELECT $sql_columns FROM $sql_joins";

	$sql .="$category_id_join $brand_id_join $pw_id_order_status_join $pw_txt_email_join $pw_txt_first_name_join
				$pw_country_code_join $state_code_join $pw_payment_method_join $pw_billing_post_code_join
				$pw_coupon_used_join $pw_variation_id_join $pw_variation_only_join $pw_variations_formated_join
				$pw_order_meta_key_join $pw_coupon_codes_join $pw_variation_item_meta_key_join $pw_show_cog_join ";

	$sql .= " Where $post_type_condition $pw_txt_email_condition_1 $pw_txt_first_name_condition_1
						$other_condition_1 $pw_country_code_condition_1 $state_code_condition_1
						$pw_billing_post_code_condition $pw_payment_method_condition_1 $date_condition
						$order_id_condition $pw_txt_email_condition_2 $pw_paid_customer_condition
						$pw_txt_first_name_condition_2 $pw_publish_order_condition_1 $pw_publish_order_condition_2
						$pw_country_code_condition_2 $state_code_condition_2 $pw_payment_method_condition_2
						$pw_order_meta_key_condition $pw_order_item_name_condition $txtProduct_condition
						$pw_product_id_condition $category_id_condition $brand_id_condition $pw_id_order_status_condition 
						$pw_coupon_used_condition $pw_coupon_code_condition $pw_coupon_codes_condition $pw_variation_item_meta_key_condition
						$pw_variation_id_condition $pw_variation_only_condition $pw_variations_formated_condition $pw_show_cog_condition
						$pw_order_status_condition $pw_hide_os_condition ";

	$sql_group_by = " GROUP BY pw_woocommerce_order_items.order_item_id ";
	$sql_order_by = " ORDER BY {$pw_sort_by} {$pw_order_by}";

	$sql .=$sql_group_by.$sql_order_by;


	//////////////////////////////////////////////////////
	//GET LAST x DAYS FOR DETECT RANK of ITEMS
	// X = from-lenofrange
	//////////////////////////////////////////////////////
	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);

	$days = $diff->format('%a')+1;
	$days_ago = date('Y-m-d', strtotime("-$days days", strtotime($pw_from_date)));
	$to=$pw_from_date;
	$pw_from_date=$days_ago;
	$pw_to_date = date('Y-m-d', strtotime("-1 days", strtotime($to)));
	if ($pw_from_date != NULL &&  $pw_to_date !=NULL) {
		$date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format') ";
	}

	$sql_last_x_days ="SELECT $sql_columns FROM $sql_joins";

	$sql_last_x_days .="$category_id_join $brand_id_join $pw_id_order_status_join $pw_txt_email_join $pw_txt_first_name_join
				$pw_country_code_join $state_code_join $pw_payment_method_join $pw_billing_post_code_join
				$pw_coupon_used_join $pw_variation_id_join $pw_variation_only_join $pw_variations_formated_join
				$pw_order_meta_key_join $pw_coupon_codes_join $pw_variation_item_meta_key_join $pw_show_cog_join ";
	$sql_last_x_days .= " Where $post_type_condition $pw_txt_email_condition_1 $pw_txt_first_name_condition_1
						$other_condition_1 $pw_country_code_condition_1 $state_code_condition_1
						$pw_billing_post_code_condition $pw_payment_method_condition_1 $date_condition
						$order_id_condition $pw_txt_email_condition_2 $pw_paid_customer_condition
						$pw_txt_first_name_condition_2 $pw_publish_order_condition_1 $pw_publish_order_condition_2
						$pw_country_code_condition_2 $state_code_condition_2 $pw_payment_method_condition_2
						$pw_order_meta_key_condition $pw_order_item_name_condition $txtProduct_condition
						$pw_product_id_condition $category_id_condition $brand_id_condition $pw_id_order_status_condition 
						$pw_coupon_used_condition $pw_coupon_code_condition $pw_coupon_codes_condition $pw_variation_item_meta_key_condition
						$pw_variation_id_condition $pw_variation_only_condition $pw_variations_formated_condition $pw_show_cog_condition
						$pw_order_status_condition $pw_hide_os_condition ";

	$sql_group_by = " GROUP BY pw_woocommerce_order_items.order_item_id ";
	$sql_order_by = " ORDER BY {$pw_sort_by} {$pw_order_by}";

	$sql_last_x_days .=$sql_group_by.$sql_order_by;
	$this->sql_int_last_x_days=$sql_last_x_days;
	//echo $sql;
	//echo $this->sql_int_last_x_days;

}
elseif($file_used=="data_table"){

	//print_r($this->results);

	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);

	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);

	$days = $diff->format('%a')+1;


	$order_items=$this->results;
	$categories = array();
	$order_meta = array();
	if(count($order_items)>0)
		foreach ( $order_items as $key => $order_item ) {

			$order_id								= $order_item->order_id;
			$order_items[$key]->billing_first_name  = '';//Default, some time it missing
			$order_items[$key]->billing_last_name  	= '';//Default, some time it missing
			$order_items[$key]->billing_email  		= '';//Default, some time it missing

			if(!isset($order_meta[$order_id])){
				$order_meta[$order_id]					= $this->pw_get_full_post_meta($order_id);
			}

			//die(print_r($order_meta[$order_id]));

			foreach($order_meta[$order_id] as $k => $v){
				$order_items[$key]->$k			= $v;
			}


			$order_items[$key]->order_total			= isset($order_item->order_total)		? $order_item->order_total 		: 0;
			$order_items[$key]->order_shipping		= isset($order_item->order_shipping)	? $order_item->order_shipping 	: 0;


			$order_items[$key]->cart_discount		= isset($order_item->cart_discount)		? $order_item->cart_discount 	: 0;
			$order_items[$key]->order_discount		= isset($order_item->order_discount)	? $order_item->order_discount 	: 0;
			$order_items[$key]->total_discount 		= isset($order_item->total_discount)	? $order_item->total_discount 	: ($order_items[$key]->cart_discount + $order_items[$key]->order_discount);


			$order_items[$key]->order_tax 			= isset($order_item->order_tax)			? $order_item->order_tax : 0;
			$order_items[$key]->order_shipping_tax 	= isset($order_item->order_shipping_tax)? $order_item->order_shipping_tax : 0;
			$order_items[$key]->total_tax 			= isset($order_item->total_tax)			? $order_item->total_tax 	: ($order_items[$key]->order_tax + $order_items[$key]->order_shipping_tax);

			$transaction_id = "ransaction ID";
			$order_items[$key]->transaction_id		= isset($order_item->$transaction_id) 	? $order_item->$transaction_id		: (isset($order_item->transaction_id) ? $order_item->transaction_id : '');
			$order_items[$key]->gross_amount 		= ($order_items[$key]->order_total + $order_items[$key]->total_discount) - ($order_items[$key]->order_shipping +  $order_items[$key]->order_shipping_tax + $order_items[$key]->order_tax );


			$order_items[$key]->billing_first_name	= isset($order_item->billing_first_name)? $order_item->billing_first_name 	: '';
			$order_items[$key]->billing_last_name	= isset($order_item->billing_last_name)	? $order_item->billing_last_name 	: '';
			$order_items[$key]->billing_name		= $order_items[$key]->billing_first_name.' '.$order_items[$key]->billing_last_name;
			$order_items[$key]->customer_user		= $order_items[$key]->customer_user;


		}

	//print_r($order_items);

	$heat_chart_array=$sale_chart_array=$date_array=array();
	$heat_chart_max=0;

	$currency_decimal=get_option('woocommerce_price_decimal_sep','.');
	$currency_thousand=get_option('woocommerce_price_thousand_sep',',');
	$currency_thousand=',';

	$this->results=$order_items;
	$net_amnt=$part_refund_amnt=$order_count=0;
	$first_order_id='';
	$customer_array=array();
	foreach($this->results as $items){

		$date_format		= get_option( 'date_format' );

		$order_refund_amnt= $this->pw_get_por_amount($items -> order_id);
		$part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);


		//Order Total
		$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
		$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;

		$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
		// $pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;

		$new_order=false;
		if($first_order_id=='')
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}else if($first_order_id!=$items->order_id)
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}
		if($new_order){

			$part_refund_amnt+=$order_refund_amnt[$items->order_id];
			$net_amnt+=$pw_table_value;

			$customer_array[$items->order_id]['order_id']=$items->order_id;
			$customer_array[$items->order_id]['id']=$items->customer_user;
			$customer_array[$items->order_id]['date']=date("M d, Y",strtotime($items->order_date));
			$customer_array[$items->order_id]['name']=$items->billing_name;
			$customer_array[$items->order_id]['total']=$pw_table_value;
			$customer_array[$items->order_id]['status']=$items->order_status;

			$tempDate = $items->full_date;
			$time = ltrim(date("H",strtotime($tempDate)),0);
			$weekday= date('l', strtotime( $tempDate));
			$heat_chart_array[$weekday][$time][]= $pw_table_value;
			if($pw_table_value>$heat_chart_max) $heat_chart_max=$pw_table_value;


			//FOR SALE CHART
			if(isset($date_array[$items->order_date]))
			{
				$date_array[$items->order_date]+=$pw_table_value;
			}else{
				$date_array[$items->order_date]=$pw_table_value;
			}

			$order_count++;
		}
	}

	//////////////////////////////////////////////////////
	/// CALCULATE THE PERCENTAGE OF SALES - TOP-BOX LEFT
	//////////////////////////////////////////////////////
	$order_items=$wpdb->get_results($this->sql_int_last_x_days);

	if(count($order_items)>0)
		foreach ( $order_items as $key => $order_item ) {

			$order_id								= $order_item->order_id;
			$order_items[$key]->billing_first_name  = '';//Default, some time it missing
			$order_items[$key]->billing_last_name  	= '';//Default, some time it missing
			$order_items[$key]->billing_email  		= '';//Default, some time it missing

			if(!isset($order_meta[$order_id])){
				$order_meta[$order_id]					= $this->pw_get_full_post_meta($order_id);
			}

			//die(print_r($order_meta[$order_id]));

			foreach($order_meta[$order_id] as $k => $v){
				$order_items[$key]->$k			= $v;
			}


			$order_items[$key]->order_total			= isset($order_item->order_total)		? $order_item->order_total 		: 0;
			$order_items[$key]->order_shipping		= isset($order_item->order_shipping)	? $order_item->order_shipping 	: 0;


			$order_items[$key]->cart_discount		= isset($order_item->cart_discount)		? $order_item->cart_discount 	: 0;
			$order_items[$key]->order_discount		= isset($order_item->order_discount)	? $order_item->order_discount 	: 0;
			$order_items[$key]->total_discount 		= isset($order_item->total_discount)	? $order_item->total_discount 	: ($order_items[$key]->cart_discount + $order_items[$key]->order_discount);


			$order_items[$key]->order_tax 			= isset($order_item->order_tax)			? $order_item->order_tax : 0;
			$order_items[$key]->order_shipping_tax 	= isset($order_item->order_shipping_tax)? $order_item->order_shipping_tax : 0;
			$order_items[$key]->total_tax 			= isset($order_item->total_tax)			? $order_item->total_tax 	: ($order_items[$key]->order_tax + $order_items[$key]->order_shipping_tax);

			$transaction_id = "ransaction ID";
			$order_items[$key]->transaction_id		= isset($order_item->$transaction_id) 	? $order_item->$transaction_id		: (isset($order_item->transaction_id) ? $order_item->transaction_id : '');
			$order_items[$key]->gross_amount 		= ($order_items[$key]->order_total + $order_items[$key]->total_discount) - ($order_items[$key]->order_shipping +  $order_items[$key]->order_shipping_tax + $order_items[$key]->order_tax );


			$order_items[$key]->billing_first_name	= isset($order_item->billing_first_name)? $order_item->billing_first_name 	: '';
			$order_items[$key]->billing_last_name	= isset($order_item->billing_last_name)	? $order_item->billing_last_name 	: '';
			$order_items[$key]->billing_name		= $order_items[$key]->billing_first_name.' '.$order_items[$key]->billing_last_name;
			$order_items[$key]->customer_user		= $order_items[$key]->customer_user;


		}
	$net_amnt_last_x_days=0;
	$first_order_id='';

	foreach($order_items as $items){

		$date_format		= get_option( 'date_format' );

		$order_refund_amnt= $this->pw_get_por_amount($items -> order_id);
		$part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);


		//Order Total
		$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
		$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;

		$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
		// $pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;

		$new_order=false;
		if($first_order_id=='')
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}else if($first_order_id!=$items->order_id)
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}
		if($new_order){
			$net_amnt_last_x_days+=$pw_table_value;
		}
	}

	//Percent Increase/Decrease = (This Year - Last Year) ÷ Last Year
	// if This Year> Last Year -> Increase ,, else decrease
	$percentage_down_up='100';
	$percentage_down_up_html='
            <span class="pw-val"><i class="fa fa-arrow-up pw-green"></i></span>
            <span class="pw-green">'.$percentage_down_up.'%</span>';
	$percentage_down_up_class="green";
	if($net_amnt_last_x_days!=0){
		$percentage_down_up= abs(($net_amnt-$net_amnt_last_x_days)/$net_amnt_last_x_days)*100;
		$percentage_down_up=number_format($percentage_down_up,2);
		if($net_amnt>$net_amnt_last_x_days){

			$percentage_down_up_html='
	                <span class="pw-val "><i class="fa fa-arrow-down pw-red"></i></span>
                    <span class="pw-red ">'.$percentage_down_up.'%</span>';
		}else{
			$percentage_down_up_html='
	                <span class="pw-val "><i class="fa fa-arrow-up pw-green"></i></span>
                    <span class="pw-green ">'.$percentage_down_up.'%</span>';
		}

	}



	//////////////////////////////////////////////////////
	//SALE CHART
	//////////////////////////////////////////////////////
	ksort ($date_array);
	$i=0;
	foreach($date_array as $key=>$value){
		$date=trim($key);
		$date=explode("/",$date);
		$mm=$date[0];
		$dd=$date[1];
		$yy=$date[2];
		$value=  (is_numeric($value) ?  number_format($value,2):0);
		$value=str_replace($currency_thousand,"",$value);

		$sale_chart_array[$i]['value']= $this->price_value($value);
		$sale_chart_array[$i]['date']= $yy.'-'.$mm.'-'.$dd;
		$i++;
	}

	//////////////////////////////////////////////////////
	//HEATMAP CHART
	//////////////////////////////////////////////////////
	$week_array = array(
		'Sunday',
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday',
	);

	foreach($week_array as $week) {
		for ( $f = 0; $f <= 23; $f ++ ) {
			//for ( $g = 1; $g <= 23; $g ++ ) {
			$g=($f+1==24) ? 0 : ($f+1);
			if(isset($heat_chart_array[$week][$f])){
				if(isset($heat_chart_array_final[$week][$f." - ".$g])){
					$heat_chart_array_final[$week][$f." - ".$g]+=$heat_chart_array[$week][$f];
				}else{
					$heat_chart_array_final[$week][$f." - ".$g]=$heat_chart_array[$week][$f];
				}
			}else{
				$heat_chart_array_final[$week][$f." - ".$g]= 0;
			}

			//}
		}
	}


	$heatmap_html= '<table class="pw_heatmap_tbl" width="100%"><tr><td style="display: inline-block;"></td>';
	for ( $g = 1; $g <= 23; $g ++ ) {
		$hour='';
		if($g%3==0) {
			$hour=$g;
			if($hour<12)
				$hour.='am';
//	            if ( $g == 12 ) {
//		            $hour = "N";
//	            }
		}
		$heatmap_html .= '<td class="pw_heatmap_chart_head" >' . $hour . '</td>';
	}
	$heatmap_html.='</tr>';

	foreach($week_array as $week){
		$heatmap_html.= '<tr ><td style="display: inline-block;">'.$week[0].'</td>';
		foreach($heat_chart_array_final[$week] as $key=>$circle){
			$value=$circle." #0";
			$main_value=$circle;
			$count=0;

			if(is_array($circle)){
				$value=0;
				foreach($circle as $val){
					$value+=$val;
				}
				$main_value=$value;
				$value=$value.' #'.count($circle);
				$count=count($circle);
			}


			$color=$randomcolor = '#' . dechex(round($main_value)+255);
			$onsale_time="0min";
			if($main_value==0){
				$color="#fff";
				$onesale_time="0min";
			}else{
				$onesale_time=$count!=0 ? (60/$count)."min" : "0 min";
			}



			$heatmap_html.= '<td class="pw_int_heatmap_circle" data-value="'.$value.'" data-time="'.$week.', '.$key.'" data-onesale="'.$onesale_time.'" style="background-color: '.$color.';"></td>';
		}
		$heatmap_html.= '</tr>';
	}
	$heatmap_html.= '</table>';

	$avg_rev_sale=$order_count!=0 ? (number_format($net_amnt/$order_count,2 )) : 0;
	$avg_rev_day=$days!=0 ? (float)number_format($net_amnt/$days,2 ) : 0;
	$net_amnt=($net_amnt) == 0 ? $this->price(0) : $this->price($net_amnt);
	$part_refund_amnt=($part_refund_amnt) == 0 ? $this->price(0) : $this->price($part_refund_amnt);
	$avg_rev_sale=($avg_rev_sale) == 0 ? $this->price(0) : $this->price($avg_rev_sale);
	$avg_rev_day=($avg_rev_day) == 0 ? $this->price(0) : $this->price($avg_rev_day);


	$sold_every_html='-';
	$sold_every=1;

	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);

	$days = $diff->format('%a')+1;
	$sold_every_html='';
	if($order_count>0){
		$sold_every = round( $days / $order_count );

		$sold_every = $sold_every * 86400;

		$dtF = new DateTime( "@0" );
		$dtT = new DateTime( "@$sold_every" );

		$year_sold  = $dtF->diff( $dtT )->y;
		$month_sold = $dtF->diff( $dtT )->m;
		$day_sold   = $dtF->diff( $dtT )->d;
		$week_sold=0;
		if ( $day_sold > 7 ) {
			$week_sold = floor( $day_sold / 7 ) ;
			$day_sold=$day_sold-($week_sold*7);
		}
		$hour_sold       = $dtF->diff( $dtT )->h;
		$sold_every_html = ( $year_sold != 0 ? $year_sold . "y " : "" ) . ( $month_sold != 0 ? $month_sold . "m " : "" ) . ( $week_sold != 0 ? $week_sold . "w " : "" ). ( $day_sold != 0 ? $day_sold . "d " : "" ) . ( $hour_sold != 0 ? $hour_sold . "h " : "" );
	}
	//////////////////////////////////////////////////////
	// END HEATMAP CHART
	//////////////////////////////////////////////////////


	$output.= '
        <div class="col-xs-12 col-md-3">
            <div class="int-awr-box pw-main-box">
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">                    
                        <div class="pw-pull-left pw-val">'.$net_amnt.' 
                            <div class="pw-pull-right">
                            '.$percentage_down_up_html.'
                            </div>
                        </div>
                        <div class="pw-pull-right">
                            $net_amnt/User (ARPU)
                        </div>
                    </div>    
                    
                    <div id="pw_int_sale_chartdiv"></div>
                    
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="int-awr-box pw-main-box">
                
                <div class="int-awr-box-content">
                      
                    <div class="pw-box-padder pw_heatmap_chart">   
                    </div>
                    <div class="pw-box-padder">                    
                        <div class="pw-pull-left pw-center-align">
                            <div class="pw-val pw-sm-font pw_int_heatmap_time">
                                '.$pw_from_date.' - '.$pw_to_date.'
                            </div>
                            <span class="pw-blue pw-sm-font pw_int_heatmap_value">
                                '.$net_amnt.'#'.$order_count.'
                            </span>
                        </div>
                        <div class="pw-pull-left pw-center-align pw-col2">
                            <div class="pw-val pw-sm-font">
                                '.__('One Sale Every',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                            </div>
                            <span class="pw-blue pw-sm-font pw_int_heatmap_onesale">
                                '.$sold_every_html.'
                            </span>     
                        </div>
                    </div>  
                </div>
               
            </div>
        </div>
        <div class="pw-cols col-xs-12 col-md-3">
            <div class="int-awr-box pw-main-box">
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">                    
                        <div id="pw_int_customer_chartdiv"></div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="pw-cols col-xs-12 col-md-3">
            <div class="int-awr-box pw-main-box pw-main-box-half">
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">                    
                    </div> 
                </div>
            </div>
        </div>
        <div class="pw-cols col-xs-12 col-md-3">
            <div class="int-awr-box pw-main-box pw-main-box-half ">
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">                    
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-9">
            <div class="int-awr-box pw-center-align pw-pr-sum-box">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-lg-font pw-green">'.$net_amnt.'#'.$order_count.'</div>
                        <div class="pw-val pwl-lbl">'.__('NET SALES',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>    
                </div>
                
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-lg-font pw-green">'.$part_refund_amnt.'</div>
                        <div class="pw-val pwl-lbl">'.__('REFUNDS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>    
                </div>
                
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-lg-font pw-green">'.$avg_rev_sale.'</div>
                        <div class="pw-val pwl-lbl">'.__('AVERAGE REVENUE / SALE',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>    
                </div>
                
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-lg-font pw-green">'.$avg_rev_day.'</div>
                        <div class="pw-val pwl-lbl">'.__('AVERAGE REVENUE / DAY',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>    
                </div>
            </div>
        </div>
        <div class="pw-cols col-xs-12 col-md-3">
            <div class="int-awr-box int-fixed-height-box">
                <div class="awr-title">
                    <h3>
                        <i class="fa fa-money"></i>'.__('Recent Transactions',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' 
    
                    </h3>
                    
                </div>
    
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pw-cols col-xs-6 col-md-6 col-lg-6">
            <div class="int-awr-box int-fixed-height-box">
                <div class="awr-title">
                    <h3>
                        <i class="fa fa-money"></i>'.__('Top Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' 
    
                    </h3>
                    
                </div>
    
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pw-cols col-xs-6 col-md-6 col-lg-6">
            <div class="int-awr-box int-fixed-height-box">
                
                <div class="int-awr-box-content">
                    <div class="pw-box-padder">
                    </div>
                </div>
            </div>
        </div>
        ';

	$table_html='';
	//print_r($customer_array);
	foreach($customer_array as $customer){

		$pw_table_value_status = $customer['status'];

		if($pw_table_value_status=='wc-completed')
			$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.$this->price($customer['total']).'</span>';
		else if($pw_table_value_status=='wc-refunded')
			$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.$this->price($customer['total']).'</span>';
		else
			$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.$this->price($customer['total']).'</span>';

		$table_html.='<tr role="row" class="odd"><td style="" data-order-id="'.$customer['order_id'].'"><a target="_blank" href="'.admin_url().'post.php?post='.$customer['order_id'].'&action=edit">'.$customer['order_id'].'</a></td><td style="">'.$customer['date'].'</td><td style="">'.$customer['name'].'</td><td style="">'.$pw_table_value_status.'</td></tr>';
	}



	//////////////////////////////////////////////////////
	/// SORT ARRAY BY DATE
	//////////////////////////////////////////////////////
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}

	array_sort_by_column($sale_chart_array, 'date');


	?>
    <script>
        var chart_int_data=<?php echo json_encode(($sale_chart_array)); ?>;
    </script>
	<?php
}elseif($file_used=="search_form"){
	global $pw_rpt_main_class;
	$this->pw_get_date_form_to();
	$pw_from_date=$pw_rpt_main_class->pw_from_date_dashboard;
	$pw_to_date=$pw_rpt_main_class->pw_to_date_dashboard;
	?>
    <form class='alldetails search_form_report' action='' method='post' id="intelligence_customer_datatable">
        <input type='hidden' name='action' value='submit-form' />


        <input type='hidden' name='action' value='submit-form' />
        <input type='hidden' name="pw_from_date" id="pwr_from_date_dashboard" value="<?php echo $pw_from_date;?>"/>
        <input type='hidden' name="pw_to_date" id="pwr_to_date_dashboard"  value="<?php echo $pw_to_date;?>"/>


        <button type="submit" value="Search" class="button-primary"><i class="fa fa-search"></i> <span><?php echo __('Search',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
        <div id="dashboard-report-range" class="pull-right tooltips  btn-fit-height grey-salt" data-placement="top" data-original-title="Change dashboard date range">
            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <b class="caret"></b>
            </div>
        </div>



        <div class="col-md-12">

			<?php
			$pw_hide_os=$this->otder_status_hide;
			$pw_publish_order='no';
			$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
			?>
            <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
            <input type="hidden" name="pw_orders_status[]" id="order_status" value="<?php echo $this->pw_shop_status; ?>">
            <input type="hidden" name="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
            <input type="hidden" name="publish_order" value="<?php echo $pw_publish_order;?>" />
            <input type="hidden" name="list_parent_category" value="">
            <input type="hidden" name="pw_category_id" value="-1">
            <input type="hidden" name="group_by_parent_cat" value="0">

            <input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os;?>" />

            <input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format;?>" />

            <input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
            <div class="fetch_form_loading search-form-loading"></div>

        </div>

    </form>
	<?php
}
//print_r($sale_chart_array);
//echo json_encode($sale_chart_array);
?>