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

	$sql_columns = "
        billing_country.meta_value as billing_country,
        DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') 													AS order_date,
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
		
		,pw_woocommerce_order_itemmeta3.meta_value 													AS 'product_quantity'					
		,pw_posts.post_status 																			AS post_status
		,pw_posts.post_status 																			AS order_status
		
		";

	$sql_joins ="{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id				
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta 	ON woocommerce_order_itemmeta.order_item_id		=	pw_woocommerce_order_items.order_item_id";

	$sql_joins.="
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta2 	ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta3 	ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta4 	ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal'
        LEFT JOIN  {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID
        ";

	$post_type_condition="pw_posts.post_type = 'shop_order' AND billing_country.meta_key	= '_billing_country'";

	$other_condition_1 = "
		AND woocommerce_order_itemmeta.meta_key = '_product_id' ";

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

	//echo $sql;
	//pw_rfm_segment_customer_ids
	$pw_rfm_segment_customer_ids			= $this->pw_get_woo_requests('pw_rfm_segment_customer_ids','-1',true);
	//echo 'llllllllll'.$pw_rfm_segment_customer_ids;
	$customer_condition_1='';
	$customer_condition_2='';
	if($pw_rfm_segment_customer_ids!=-1 && $pw_rfm_segment_customer_ids!=''){
		$ids=str_replace(",","','",$pw_rfm_segment_customer_ids);
		$customer_condition_1=" AND pw_postmeta2.meta_value IN ('$ids') ";
		$customer_condition_2=" AND pw_postmeta_customer_user.meta_value IN ('$pw_rfm_segment_customer_ids') ";
	}


	$pw_order_status=$this->pw_shop_status;
	$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."','wc-refunded'";

	//////////////////////////////////////////////////////
	/// MAIN SQL OF CUSTOMERS
	//////////////////////////////////////////////////////
	$sql="SELECT pw_posts.ID as order_id,DATE(pw_posts.post_date) as post_date,pw_posts.post_status, (pw_postmeta1.meta_value) AS 'total_amount' ,pw_postmeta2.meta_value AS 'billing_email' ,(pw_postmeta2.meta_value) AS 'order_count' ,pw_postmeta4.meta_value AS customer_id  FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID  LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_posts.ID  WHERE pw_posts.post_type='shop_order' AND pw_postmeta1.meta_key='_order_total' AND pw_postmeta2.meta_key='_billing_email' AND pw_postmeta4.meta_key='_customer_user' $customer_condition_1 $date_condition AND  pw_posts.post_status IN ($pw_order_status)  $pw_hide_os_condition /*GROUP BY pw_postmeta2.meta_value*/ Order By total_amount DESC";


	//echo $sql;

	//////////////////////////////////////////////////////
	/// FETCH CUSTOMERS OF CHOSEN SEGMENT
	//////////////////////////////////////////////////////
	$this->sql_int_customer_segement_clicked="SELECT pw_posts.ID as order_id,DATE(pw_posts.post_date) as post_date,pw_posts.post_status,SUM(pw_postmeta1.meta_value) AS 'total_amount' ,pw_postmeta2.meta_value AS 'billing_email'  ,Count(pw_postmeta2.meta_value) AS 'order_count' ,pw_postmeta4.meta_value AS customer_id  FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID  LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_posts.ID  WHERE pw_posts.post_type='shop_order' AND pw_postmeta1.meta_key='_order_total' AND pw_postmeta2.meta_key='_billing_email' AND pw_postmeta4.meta_key='_customer_user' $customer_condition_1 $date_condition AND pw_posts.post_status IN ($pw_order_status) $pw_hide_os_condition GROUP BY pw_postmeta2.meta_value Order By total_amount DESC";


	//echo $this->sql_int_customer_segement_clicked;

	//////////////////////////////////////////////////////
	/// FETCH CUSTOMER'S PRODUCTS
	//////////////////////////////////////////////////////
	$this->sql_int_customer_products="
SELECT pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,SUM(woocommerce_order_itemmeta.meta_value)	AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'total_amount' ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,pw_postmeta_customer_user.meta_value	AS customer_id ,DATE(pw_posts.post_date) AS post_date ,pw_postmeta_billing_billing_email.meta_value	AS billing_email ,CONCAT(pw_postmeta_billing_billing_email.meta_value,' ',pw_woocommerce_order_itemmeta7.meta_value,' ',pw_postmeta_customer_user.meta_value)	AS group_column ,CONCAT(pw_postmeta_billing_first_name.meta_value,' ',postmeta_billing_last_name.meta_value)	AS billing_name,pw_postmeta_billing_country.meta_value	AS billing_country	FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.id=pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_billing_first_name ON pw_postmeta_billing_first_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as postmeta_billing_last_name ON postmeta_billing_last_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_billing_billing_email ON pw_postmeta_billing_billing_email.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_billing_country ON pw_postmeta_billing_country.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_customer_user ON pw_postmeta_customer_user.post_id	= pw_woocommerce_order_items.order_id WHERE woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_postmeta_billing_first_name.meta_key	= '_billing_first_name' AND postmeta_billing_last_name.meta_key	= '_billing_last_name' AND pw_postmeta_billing_billing_email.meta_key	= '_billing_email' AND pw_postmeta_billing_country.meta_key	= '_billing_country' AND pw_postmeta_customer_user.meta_key	= '_customer_user'  $date_condition $pw_order_status_condition $pw_hide_os_condition GROUP BY group_column ORDER BY post_date DESC";

	//echo $this->sql_int_customer_products;

}
elseif($file_used=="data_table"){

	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);

	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);
	$diff_days = $diff->format('%a')+1;

	$pw_rfm_segment_value		= $this->pw_get_woo_requests('pw_rfm_segment_value','-1',true);

	//////////////////////////////////////////////////////
	/// CUSTOMER'S PRODUCTS
	//////////////////////////////////////////////////////
	$result_customer_products=$wpdb->get_results($this->sql_int_customer_products);
	$customer_products=array();

	if(count($result_customer_products)>0){
		foreach($result_customer_products as $items){
			$customer_products[$items->billing_email][]=$items;
		}
	}

	//print_r($customer_products);

	//////////////////////////////////////////////////////
	/// FETCH ALL CUSTOMERS
	//////////////////////////////////////////////////////
	$country      	= $this->pw_get_woo_countries();
	$customer_html='';
	$customer_rfm_chart=array();
	$today_date=date("Y-m-d");
	$order_count=0;
	$first_order_id='';
	$customer_chart_array=array();
	$currency_decimal=get_option('woocommerce_price_decimal_sep','.');
	$currency_thousand=get_option('woocommerce_price_thousand_sep',',');
	$currency_thousand=',';

	$total_all_refund=$all_refund_count=0;

	if(count($this->results)>0)
		$i=0;
	foreach($this->results as $items){
		//SET guest@guest.com for empty Email
		if($items->billing_email=='') $items->billing_email='guest@guest.com';

		if($items->post_status=='wc-refunded'){
			$all_refund_count++;
			$total_all_refund+=$items -> total_amount;

			$customer_chart_array[$items -> post_date]['date']=$items -> post_date;
			$value=  (is_numeric($items->total_amount) ?  number_format($items->total_amount,2):0);
			$value=str_replace($currency_thousand,"",$value);
			if(isset($customer_chart_array[$items -> post_date]['refund']))
				$customer_chart_array[$items -> post_date]['refund'] += $this->price_value($value);
			else
				$customer_chart_array[$items -> post_date]['refund'] = $this->price_value($value);
			$customer_chart_array[$items -> post_date]['color_n']= '#FF0F00';

			continue;
		}

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
			$order_count++;
		}

		$order_refund_amnt= $this->pw_get_por_amount($items -> order_id);
		$part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);
		$total_all_refund+=$part_refund;
		$items_total_amunt= isset($items -> total_amount) ? ($items -> total_amount)-$part_refund : 0;

		$customer_chart_array[$items -> post_date]['date']=$items -> post_date;
		$value=  (is_numeric($items_total_amunt) ?  number_format($items_total_amunt,2):0);
		$value=str_replace($currency_thousand,"",$value);
		if(isset($customer_chart_array[$items -> post_date]['revenue']))

			$customer_chart_array[$items -> post_date]['revenue']+= $this->price_value($value);
		else
			$customer_chart_array[$items -> post_date]['revenue']= $this->price_value($value);
		$customer_chart_array[$items -> post_date]['color_p']= '#0D8ECF';

		$total_amount= $items_total_amunt == 0 ? $this->price(0) : $this->price($items_total_amunt);

		$avatar=get_avatar_url($items->billing_email);
		$customer_items=$customer_products[$items->billing_email];
		$customer_name=$location='';
		$customer_items_html='';
		$item_no=0;

		//Order Count
//                if(isset($customer_rfm_chart[$items->billing_email]['frequency']))
//		            $customer_rfm_chart[$items->billing_email]['frequency']+=$items->order_count;
//                else
//	                $customer_rfm_chart[$items->billing_email]['frequency']=$items->order_count;
		//Total Amount
		if(isset($customer_rfm_chart[$items->billing_email]['monetary'])) {
			$customer_rfm_chart[ $items->billing_email ]['monetary'] += $items_total_amunt;
			$customer_rfm_chart[$items->billing_email]['frequency']++;
		}
		else {
			$customer_rfm_chart[ $items->billing_email ]['monetary'] = $items_total_amunt;
			$customer_rfm_chart[$items->billing_email]['frequency']=1;
		}

		foreach($customer_items as $c_items){
			//SET guest@guest.com for empty Email
			if($c_items->billing_email=='') $c_items->billing_email='guest@guest.com';

			$customer_name=$c_items->billing_name;

			$customer_rfm_chart[$c_items->billing_email]['name']=$customer_name;
			//Date of last purchase
			if(!isset($customer_rfm_chart[$c_items->billing_email]['date'])){

				$customer_rfm_chart[$c_items->billing_email]['date']=$c_items->post_date;
				$from=date_create($today_date);
				$to=date_create($c_items->post_date);
				$diff=date_diff($to,$from);
				$customer_rfm_chart[$c_items->billing_email]['recency']= $diff->format('%a')+1;
			}

		}
		$i++;

	}


	//print_r($customer_rfm_chart);

	//print_r($customer_rfm_chart);
	$customer_all_no=count($customer_rfm_chart);
	$customer_sold_no= round((count($customer_rfm_chart)*20)/100);


	//////////////////////////////////////////////////////
	/// SET RFM SCORE ACCORDING SETTINGS - RFM ANALYSE
	//////////////////////////////////////////////////////
	$r_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_recency_point');
	$f_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_frequency_point');
	$m_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_monetary_point');

	$i=$top_20_customers=$total_customer_amnt=$total_customer_frequency=0;
	foreach($customer_rfm_chart as $key => $customer){

		$total_customer_amnt+=$customer['monetary'];
		$total_customer_frequency+=$customer['frequency'];
		if($i<$customer_sold_no){
			$top_20_customers+=$customer['monetary'];
		}
		$i++;

		if(isset($customer['recency'])){
			foreach($r_points as $ind=>$r){
				if($customer['recency']<=$r){
					$customer_rfm_chart[$key]['r_score']=$ind;
					break;
				}
			}
			if(!isset($customer_rfm_chart[$key]['r_score'])){
				$customer_rfm_chart[$key]['r_score']=1;
			}
		}else{
			$customer_rfm_chart[$key]['r_score']=1;
		}

		if(isset($customer['frequency'])){
			foreach ( $f_points as $ind => $f ) {
				if ( $customer['frequency'] >= $f ) {
					$customer_rfm_chart[ $key ]['f_score'] = $ind;
					break;
				}
			}
			if(!isset($customer_rfm_chart[$key]['f_score'])){
				$customer_rfm_chart[$key]['f_score']=1;
			}
		}else{
			$customer_rfm_chart[ $key ]['f_score'] = 1;
		}

		if(isset($customer['monetary'])){
			foreach ( $m_points as $ind => $m ) {
				if ( $customer['monetary'] >= $m ) {
					$customer_rfm_chart[ $key ]['m_score'] = $ind;
					break;
				}
			}
			if(!isset($customer_rfm_chart[$key]['m_score'])){
				$customer_rfm_chart[$key]['m_score']=1;
			}
		}else{
			$customer_rfm_chart[ $key ]['m_score'] = 1;
		}
	}


	//////////////////////////////////////////////////////
	/// TOP-CENTER BOX VALUES
	//////////////////////////////////////////////////////
	//Purchase Frequency = No orders/ no.unique customers
	$pf=$customer_all_no!=0 ? round($order_count/$customer_all_no) : 0;

	//Time Between Purchases = Day Range / PF
	$tbp=$pf!=0 ? round($diff_days/$pf) : 0;

	//AOV = Revenue / Order No
	$aov=$order_count!=0 ? ($total_customer_amnt/$order_count) : 0;
	//CV = AOV*PF
	$cv=$aov*$pf;
	$aov= $aov == 0 ? $this->price(0) : $this->price($aov);
	$cv= $cv == 0 ? $this->price(0) : $this->price($cv);

	$top_20_customers_percent='0%';
	if($total_customer_amnt!=0)
	    $top_20_customers_percent=(float)number_format(($top_20_customers/$total_customer_amnt)*100,2 ) ."%";
	$top_20_customers= $top_20_customers == 0 ? $this->price(0) : $this->price($top_20_customers);

	$avg_value=$customer_all_no!=0 ? ($total_customer_amnt/$customer_all_no) : 0;
	$avg_value= $avg_value == 0 ? $this->price(0) : $this->price($avg_value);
	$avg_orders=$customer_all_no!=0 ? ((float)number_format($total_customer_frequency/$customer_all_no,2)) : 0;

	//echo $total_all_refund.'---'.$total_customer_amnt;
	$refund_percent=$total_customer_amnt!=0 ? ((float)number_format(($total_all_refund*100)/$total_customer_amnt,2 ) ."%") : '0%';


	//////////////////////////////////////////////////////
	/// CALCULATE THE CUSTOMER SEGMENTS
	//////////////////////////////////////////////////////
	$customer_segment=array(
		"champions" => array(
			"r" => array(4,5),
			"f" => array(4,5),
			"m" => array(4,5),
		),
		"loyal" => array(
			"r" => array(2,5),
			"f" => array(3,5),
			"m" => array(3,5),
		),
		"potential" => array(
			"r" => array(3,5),
			"f" => array(1,3),
			"m" => array(1,3),
		),
		"new_customer" => array(
			"r" => array(4,5),
			"f" => array(1), //<=1
			"m" => array(1), //<=1
		),
		"promising" => array(
			"r" => array(3,4),
			"f" => array(1), //<=1
			"m" => array(1), //<=1
		),
		"attention" => array(
			"r" => array(2,3),
			"f" => array(2,3),
			"m" => array(2,3),
		),
		"sleep" => array(
			"r" => array(2,3),
			"f" => array(2), //<=1
			"m" => array(2), //<=1
		),
		"at_risk" => array(
			"r" => array(2),
			"f" => array(2,5), //<=1
			"m" => array(2,5), //<=1
		),
		"no_lose" => array(
			"r" => array(1),
			"f" => array(4,5), //<=1
			"m" => array(4,5), //<=1
		),
		"hibernating" => array(
			"r" => array(1,2),
			"f" => array(1,2), //<=1
			"m" => array(1,2), //<=1
		),
		"lose" => array(
			"r" => array(2),
			"f" => array(2), //<=1
			"m" => array(2), //<=1
		),
	);

	//print_r($customer_rfm_chart);

	//ALTERNATE SEGMENT : FOR EXCEPT ITEMS : 115 : Insert in closest segment
	$alternate_segment = array();
	foreach($customer_rfm_chart as $c_id => $customer){
		$r_score=$customer['r_score'];
		$f_score=$customer['f_score'];
		$m_score=$customer['m_score'];

		//SET guest@guest.com for empty Email
		if($c_id=='') $c_id='guest@guest.com';

		foreach($customer_segment as $key => $seg){
			$r_flag=false;
			//FOR (x,y)
			if(isset($seg["r"][0]) && isset($seg["r"][1])){
				if($r_score>=$seg['r'][0] && $r_score<=$seg['r'][1]){
					$r_flag=true;
					$alternate_segment[$key]=1;

				}else{
					$alternate_segment[$key]='0';
				}

				//FOR (x)
			}else if(isset($seg["r"][0])){
				if($r_score<=$seg['r'][0]){
					$r_flag=true;
					$alternate_segment[$key]='1';
				}else{
					$alternate_segment[$key]='0';
				}
			}
			$f_flag=false;
			if(isset($seg["f"][0]) && isset($seg["f"][1])){
				if($f_score>=$seg['f'][0] && $f_score<=$seg['f'][1]){
					$f_flag=true;
					$alternate_segment[$key].='1';
				}else{
					$alternate_segment[$key].='0';
				}

			}else if(isset($seg["f"][0])){
				if($f_score<=$seg['f'][0]){
					$f_flag=true;
					$alternate_segment[$key].='1';
				}else{
					$alternate_segment[$key].='0';
				}
			}
			$m_flag=false;
			if(isset($seg["m"][0]) && isset($seg["m"][1])){
				if($m_score>=$seg['m'][0] && $m_score<=$seg['m'][1]){
					$m_flag=true;
					$alternate_segment[$key].='1';
				}else{
					$alternate_segment[$key].='0';
				}

			}else if(isset($seg["m"][0])){
				if($m_score<=$seg['m'][0]){
					$m_flag=true;
					$alternate_segment[$key].='1';
				}else{
					$alternate_segment[$key].='0';
				}
			}
//                if($r_flag){
//
//
//	                if($f_flag){
//
//	                }else{
//		                continue;
//                    }
//
//                }else{
//                    continue;
//                }
			if($r_flag && $f_flag && $m_flag){
				break;
			}elseif($r_flag && $f_flag && $m_flag){
				break;
			}
		}

//            echo $c_id;
		//  print_r($alternate_segment);
		if($r_flag && $f_flag && $m_flag && $c_id){
			$customer_segment[$key]['items'][]=$c_id;
			$customer_rfm_chart[$c_id]['segment']=$key;
		}else if($c_id){
			foreach($alternate_segment as $alt_key => $alt_seg){
				if($alt_seg=='110' || $alt_seg=='101' || $alt_seg=='110'){
					$customer_segment[$alt_key]['items'][]=$c_id;
					$customer_rfm_chart[$c_id]['segment']=$alt_key;
					break;
				}
			}

		}
//elseif($c_id){
//		        $customer_segment['others']['items'][]=$c_id;
//		        $customer_rfm_chart[$c_id]['segment']='others';
//            }
		$alternate_segment=array();
		//die();
	}
	//////////////////////////////////////////////////////
	/// END CALCULATE THE CUSTOMER SEGMENTS
	//////////////////////////////////////////////////////



	//print_r($customer_segment);

	//////////////////////////////////////////////////////
	/// CUSTOMER HTML - ALL CUSTOMERS OF CLICKED SEGMENT CUSTOMERS
	//////////////////////////////////////////////////////
	$pw_rfm_segment_customer_ids			= $this->pw_get_woo_requests('pw_rfm_segment_customer_ids','-1',true);

	$result_customer_segement_clicked=$wpdb->get_results($this->sql_int_customer_segement_clicked);
	$customer_html='';
	$customer_chart_array_clicked = array();
	if(count($result_customer_segement_clicked)>0) {

		$i = 0;
		foreach ( $result_customer_segement_clicked as $items ) {

			$main_email=$items->billing_email;
			//SET guest@guest.com for empty Email
			if($items->billing_email=='') $items->billing_email='guest@guest.com';

			if ( $items->post_status == 'wc-refunded' ) {
				$customer_chart_array_clicked[ $items->post_date ]['date'] = $items->post_date;
				$value                                             = ( is_numeric( $items->total_amount ) ? number_format( $items->total_amount, 2 ) : 0 );
				$value                                             = str_replace( $currency_thousand, "", $value );
				if ( isset( $customer_chart_array_clicked[ $items->post_date ]['refund'] ) ) {
					$customer_chart_array_clicked[ $items->post_date ]['refund'] += $this->price_value( $value );
				} else {
					$customer_chart_array_clicked[ $items->post_date ]['refund'] = $this->price_value( $value );
				}
				$customer_chart_array_clicked[ $items->post_date ]['color_n'] = '#FF0F00';

				continue;
			}

			$total_amount = $customer_rfm_chart[ $items->billing_email ]['monetary'];

			$customer_chart_array_clicked[ $items->post_date ]['date'] = $items->post_date;
			$value                                             = ( is_numeric( $total_amount ) ? number_format( $total_amount, 2 ) : 0 );
			$value                                             = str_replace( $currency_thousand, "", $value );
			if ( isset( $customer_chart_array_clicked[ $items->post_date ]['revenue'] ) ) {
				$customer_chart_array_clicked[ $items->post_date ]['revenue'] += $this->price_value( $value );
			} else {
				$customer_chart_array_clicked[ $items->post_date ]['revenue'] = $this->price_value( $value );
			}
			$customer_chart_array_clicked[ $items->post_date ]['color_p'] = '#0D8ECF';


			//$total_amount= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

			$avatar              = get_avatar_url( $main_email );
			$customer_items      = $customer_products[ $main_email ];
			$customer_name       = $location = '';
			$customer_items_html = '';
			$item_no             = 0;

			foreach ( $customer_items as $c_items ) {
				$customer_name = $c_items->billing_name;

				$location = isset( $country->countries[ $c_items->billing_country ] ) ? $country->countries[ $c_items->billing_country ] : $c_items->billing_country;

				$_product = wc_get_product( $c_items->product_id );

				$img     = wp_get_attachment_image( $_product->get_image_id(), 'thumbnail' );
				$img_url = wp_get_attachment_image_url( $_product->get_image_id(), 'thumbnail' );
				$p_title = get_the_title( $c_items->product_id );

				if ( $item_no < 2 ) {
					$customer_items_html .= '<img src="' . $img_url . '" >';
				}
				$item_no ++;

			}
			if ( $item_no > 3 ) {
				$customer_items_html .= '+' . ( $item_no - 2 ) . ' More';
			}


			$class = $customer_rfm_chart[ $items->billing_email ]['segment'];


			//pw-customer-green pw-customer-yellow
			$customer_class = " pw-customer-$class ";
			$total_amount= $total_amount == 0 ? $this->price(0) : $this->price($total_amount);
			$customer_html .= '
                    <div class="pw-cols col-xs-12 col-md-2 pw_int_customers_single" data-customer-id="' . $items->customer_id . '" data-customer-email="' . $main_email . '" data-customer-segment="' . $class . '">
                        <div class="pw-customer-cards-cnt ' . $customer_class . '">
                            <div class="awr-int-loading">
                                <div class="awr-loading-css"><div class="rect1"></div><div class="rect2"></div> <div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>
                            </div>
                            <span class="pw-customer-link pw-val pw-md-font"><i class="fa fa-external-link-square"></i></span>
                            <div class="pw-color-arc ' . $customer_class . '"></div>
                            <div class="pw-customer-card-thumb pw-center-align">
                                
                                <img src="' . $avatar . '">
                            </div>
                            
                            <div class="pw-customer-detail pw-center-align">
                                <div class="pw-md-font">' . $customer_name . '</div>
                                <div class="pw-val pw-sm-font pw-customer-email">' . $main_email . '</div>
                                <div class="pw-xs-font pw-map-cnt"><i class="fa fa-map-marker"></i>' . $location . '</div>
                                
                                <div class="pw-customer-product-imgs">
                                    ' . $customer_items_html . '
                                </div>
                                
                                <div class="pw-xs-font pwl-lbl">Revenue</div>
                                <div class="pw-md-font pw-green">' . $total_amount . '</div>
                            </div>
                            
                        </div>
                    </div>';
		}
	}

	//print_r($customer_chart_array);


	//////////////////////////////////////////////////////
	/// CUSTOMER CHART - TOP-LEFT BOX
	//////////////////////////////////////////////////////
	if($pw_rfm_segment_customer_ids!=-1 && $pw_rfm_segment_customer_ids!='')
	{
		$customer_chart_array=$customer_chart_array_clicked;
	}

	$i=0;
	$customer_chart_array_f=array();
	foreach ($customer_chart_array as $key=>$data){

		$customer_chart_array_f[$i]['date']= $key;
		$customer_chart_array_f[$i]['refund']= number_format(($data['refund']*(-1)),2);
		$customer_chart_array_f[$i]['color_n']= $data['color_n'];
		$customer_chart_array_f[$i]['revenue']= number_format($data['revenue'],2);
		$customer_chart_array_f[$i]['color_p']= $data['color_p'];

		$i++;
	}

	//print_r($customer_segment);

	$total_customers=0;
	foreach($customer_segment as $slug=>$segemnt){
		$total_customers+=(isset($segemnt['items'])?count($segemnt['items']):0);
	}
	$total_customers+=(isset($customer_segment['others']['items'])?count($customer_segment['others']['items']):0);

//        echo 'ssssssssssss'.$total_customers;

	$seg_champions_no=$seg_loyal_no=$seg_potential_no=$seg_new_customer_no=$seg_promising_no=$seg_attention_no=$seg_sleep_no=$seg_at_risk_no=$seg_no_lose_no=$seg_hibernating_no=$seg_lose_no=0;
	$seg_champions_percent=$seg_loyal_percent=$seg_potential_percent=$seg_new_customer_percent=$seg_promising_percent=$seg_attention_percent=$seg_sleep_percent=$seg_at_risk_percent=$seg_no_lose_percent=$seg_hibernating_percent=$seg_lose_percent='0%';

    if($total_customers!=0){
	    $seg_champions_no=(isset($customer_segment['champions']['items'])?count($customer_segment['champions']['items']):0);
	    $seg_champions_percent=$seg_champions_no.' ('.round((100*$seg_champions_no)/$total_customers).'%)';

	    $seg_loyal_no=(isset($customer_segment['loyal']['items'])?count($customer_segment['loyal']['items']):0);
	    $seg_loyal_percent=$seg_loyal_no.' ('.round((100*$seg_loyal_no)/$total_customers).'%)';

	    $seg_potential_no=(isset($customer_segment['potential']['items'])?count($customer_segment['potential']['items']):0);
	    $seg_potential_percent=$seg_potential_no.' ('.round((100*$seg_potential_no)/$total_customers).'%)';

	    $seg_new_customer_no=(isset($customer_segment['new_customer']['items'])?count($customer_segment['new_customer']['items']):0);
	    $seg_new_customer_percent=$seg_new_customer_no.' ('.round((100*$seg_new_customer_no)/$total_customers).'%)';

	    $seg_promising_no=(isset($customer_segment['promising']['items'])?count($customer_segment['promising']['items']):0);
	    $seg_promising_percent=$seg_promising_no.' ('.round((100*$seg_promising_no)/$total_customers).'%)';

	    $seg_attention_no=(isset($customer_segment['attention']['items'])?count($customer_segment['attention']['items']):0);
	    $seg_attention_percent=$seg_attention_no.' ('.round((100*$seg_attention_no)/$total_customers).'%)';

	    $seg_sleep_no=(isset($customer_segment['sleep']['items'])?count($customer_segment['sleep']['items']):0);
	    $seg_sleep_percent=$seg_sleep_no.' ('.round((100*$seg_sleep_no)/$total_customers).'%)';

	    $seg_at_risk_no=(isset($customer_segment['at_risk']['items'])?count($customer_segment['at_risk']['items']):0);
	    $seg_at_risk_percent=$seg_at_risk_no.' ('.round((100*$seg_at_risk_no)/$total_customers).'%)';

	    $seg_no_lose_no=(isset($customer_segment['no_lose']['items'])?count($customer_segment['no_lose']['items']):0);
	    $seg_no_lose_percent=$seg_no_lose_no.' ('.round((100*$seg_no_lose_no)/$total_customers).'%)';

	    $seg_hibernating_no=(isset($customer_segment['hibernating']['items'])?count($customer_segment['hibernating']['items']):0);
	    $seg_hibernating_percent=$seg_hibernating_no.' ('.round((100*$seg_hibernating_no)/$total_customers).'%)';

	    $seg_lose_no=(isset($customer_segment['lose']['items'])?count($customer_segment['lose']['items']):0);
	    $seg_lose_percent=$seg_lose_no.' ('.round((100*$seg_lose_no)/$total_customers).'%)';
    }

	//////////////////////////////////////////////////////
	/// ENDCUSTOMER CHART - TOP-LEFT BOX
	//////////////////////////////////////////////////////

	$output.= '
        <div id="pw_rpt_fetch_single_customer_main">
            <div class="pw-cols col-xs-12 col-md-4">
                <div class="int-awr-box pw-main-box">
                    <div class="int-awr-box-content">
                        <div class="pw-box-padder">                    
                            <div id="pw_int_customer_chartdiv"></div>
                        </div> 
                    </div>
                </div>
            </div>
            
            <div class="pw-cols col-xs-12 col-md-3">
                <div class="int-awr-box pw-main-box">
                    <div class="int-awr-box-content">
                        <div class="pw-box-padder">                    
                            
                                <div class="pw-info">
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$cv.'</div>
                                            <div class="pw-xs-font pw-val"  title="Customer Value = AOV / PF">'.__('Customer Value(CV)',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Customer Value = AOV / PF',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>    
                                    </div>
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$top_20_customers.' • '.$customer_sold_no.'</div>
                                            <div class="pw-xs-font pw-val">'.__('Top 20% Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Top 20% Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>    
                                    </div>
                                </div>    
                                
                                <div class="pw-info">
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$avg_value.'</div>
                                            <div class="pw-xs-font pw-val">'.__('Avg Value',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Avg Value',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>    
                                    </div>
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$avg_orders.'</div>
                                            <div class="pw-xs-font pw-val">'.__('Avg Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Avg Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>    
                                    </div>
                                </div>    
                                
                                <div class="pw-info">
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$pf.'</div>
                                            <div class="pw-xs-font pw-val" title="PF = Orders No / Unique Customers No">'.__('Purchase Frequency(PF)',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Purchase Frequency = Orders No / Unique Customers No',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>    
                                    </div>
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$tbp.'</div>
                                            <div class="pw-xs-font pw-val" title="Time Between Purchases = Day Range / PF">'.__('TBP',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Time Between Purchases = Day Range / PF',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>        
                                    </div>
                                </div>
                                    
                                <div class="pw-info">
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="tooltip ">
                                            <div class="pw-md-font pw-blue">'.$aov.'</div>
                                            <div class="pw-xs-font pw-val">'.__('AOV',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                            <span class="tooltiptext">'.__('Average Order Value = Revenue / Orders No',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
                                        </div>
                                    </div>
                                    <div class="pw-cols col-xs-12 col-md-6 pw-center-align">
                                        <div class="pw-md-font pw-blue">'.$all_refund_count.' • '.$refund_percent.'</div>
                                        <div class="pw-xs-font pw-val">'.__('Refunded',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                    </div>
                                </div>    
                            
                        </div> 
                    </div>
                </div>
            </div>
            <div class="pw-cols col-xs-12 col-md-5">
                <div class="int-awr-box pw-main-box">
                    <div class="awr-title">
                        <h3>
                            <i class="fa fa-money"></i>'.__('RFM Analysis',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h3>
                        <div class="awr-title-icons">
                            <div class="awr-title-icon awr-add-fav-icon awr-tooltip-wrapper" data-smenu="all_orders">
                                <i class="fa  fa-info "></i>
                                <div class="awr-tooltip-cnt">
                                    <div class="awr-tooltip-header">'.__('RFM Analysis',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                    <div class="awr-tooltip-content">'.__('Customer Grid gives you a bird eye view of your Customer\'s health. Your customers are grouped according to their current relationship with you.',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                </div>
                            </div>
                            <div class="awr-title-icon awr-setting-icon" style="display: none;"><i class="fa fa-cog"></i></div>
                            <div class="awr-title-icon awr-close-icon" style="display: none;"><i class="fa fa-times"></i></div>
                        </div>
                    </div>
                    
                    <div class="int-awr-box-content">
                        <div class="pw-box-padder">                    
                            <div style="width:100%;overflow: auto">
                                <div class="">
                                    <table class="rfm-table" border="0" cellspacing="0" style="width: 100%;;color: #fff!important;font-size: 11px;table-layout: fixed!important;font-weight: 400;word-wrap: break-word ;">
                                        <tbody><tr>
                                            <td colspan="2" rowspan="2" bgcolor="#f77575" data-segment="no_lose" class="pw_rfm_segment_td">
                                                <div class="fs-12">'.__('CAN`T LOSE THEM',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13">'.$seg_no_lose_percent.'</div>
                                            </td>
                        
                                            <td colspan="2" rowspan="2" bgcolor="#f6be74" class="pw-right-align pw_rfm_segment_td" data-segment="at_risk">
                                                <div class="">'.__('AT RISK',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_at_risk_percent.'</div>
                                            </td>
                        
                                            <td colspan="4" rowspan="3" bgcolor="#65c73c" data-segment="loyal" class="pw_rfm_segment_td">
                                                <div class="fs-16" >'.__('LOYAL CUSTOMERS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-16" >'.$seg_loyal_percent.'</div>
                                            </td>
                        
                                            <td colspan="2" rowspan="2" bgcolor="#363"  class="pw-right-align pw_rfm_segment_td" data-segment="champions">
                                                <div class="" >'.__('CHAMPIONS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_champions_percent.'</div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                        
                                            <td colspan="4" rowspan="4" bgcolor="#f6be74" data-segment="at_risk" class="pw_rfm_segment_td">
                                            </td>
                        
                                            <td colspan="2" bgcolor="#65c73c"  style="height: 40px;" data-segment="loyal" class="pw_rfm_segment_td">
                                                
                                            </td>
                        
                                        </tr>
                                        <tr>
                        
                                            <td colspan="2" rowspan="3" bgcolor="#3ec4a9" class="pw-right-align pw_rfm_segment_td" data-segment="attention">
                                                <div class="fs-14" >'.__('NEEDING ATTENTION',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_attention_percent.'</div>
                                                
                                            </td>
                        
                                            <td colspan="4" rowspan="5" bgcolor="#8cdcf5" class="pw-right-align pw_rfm_segment_td" data-segment="potential">
                                                <div class="fs-16" >'.__('POTENTIAL LOYALIST',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-14" >'.$seg_potential_percent.'</div>
                                                
                                            </td>
                        
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                        
                                            <td colspan="2" rowspan="4" bgcolor="#9c9c9c" data-segment="lose" class="pw_rfm_segment_td"> 
                                            </td>
                                            
                        
                                            <td colspan="2" rowspan="2" bgcolor="#c1c1c1" class="pw-right-align pw_rfm_segment_td" data-segment="hibernating">
                                                <div class="" >'.__('HIBERNATING',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_hibernating_percent.'</div>
                                                
                                            </td>
                        
                                            <td colspan="2" rowspan="4" bgcolor="#5270d2" class="pw-right-align pw_rfm_segment_td" data-segment="sleep">
                                                <div class="fs-14" >'.__('ABOUT TO SLEEP',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_sleep_percent.'</div>
                                                
                                            </td>
                        
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                                            <td colspan="2" rowspan="2" bgcolor="#9c9c9c" data-segment="lose" class="pw_rfm_segment_td">
                                                <div class="" >'.__('LOST',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_lose_percent.'</div>
                                            </td>
                                            <td colspan="2" rowspan="2" bgcolor="#7592f6" class="pw-right-align pw_rfm_segment_td" data-segment="promising">
                                                <div class="" >'.__('PROMISING',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_promising_percent.'</div>
                                                
                                            </td>
                                            <td colspan="2" rowspan="2" bgcolor="#f68ecd" class="pw-right-align pw_rfm_segment_td" data-segment="new_customer">
                                                <div class="" >'.__('NEW CUSTOMERS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                                <div class="fs-13" >'.$seg_new_customer_percent.'</div>
                                                
                                            </td>
                        
                                        </tr>
                                        <tr>
                                        </tr>
                                    </tbody></table>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12">
                <h3 class="pw-out-title">'.__('Customer Cards',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h3>
                '.$customer_html.'
            </div>
        </div>
        
        <div id="pw_rpt_fetch_single_customer_html" class="pw-cols col-xs-12 col-md-12"></div>
        ';

	?>
    <script>
        var pw_rfm_segment_value=[];
        pw_rfm_segment_value['champions'] = "<?php echo (isset($customer_segment['champions']['items']) ? implode($customer_segment['champions']['items'],','):''); ?>";

        pw_rfm_segment_value['loyal'] = "<?php echo (isset($customer_segment['loyal']['items']) ? implode($customer_segment['loyal']['items'],','):''); ?>";
        pw_rfm_segment_value['potential'] = "<?php echo (isset($customer_segment['potential']['items']) ? implode($customer_segment['potential']['items'],','):''); ?>";

        pw_rfm_segment_value['new_customer'] = "<?php echo (isset($customer_segment['new_customer']['items']) ? implode($customer_segment['new_customer']['items'],','):''); ?>";

        pw_rfm_segment_value['promising'] = "<?php echo (isset($customer_segment['promising']['items']) ? implode($customer_segment['promising']['items'],','):''); ?>";

        pw_rfm_segment_value['attention'] = "<?php echo (isset($customer_segment['attention']['items']) ? implode($customer_segment['attention']['items'],','):''); ?>";

        pw_rfm_segment_value['sleep'] = "<?php echo (isset($customer_segment['sleep']['items']) ? implode($customer_segment['sleep']['items'],','):''); ?>";

        pw_rfm_segment_value['at_risk'] = "<?php echo (isset($customer_segment['at_risk']['items']) ? implode($customer_segment['at_risk']['items'],','):''); ?>";

        pw_rfm_segment_value['no_lose'] = "<?php echo (isset($customer_segment['no_lose']['items']) ? implode($customer_segment['no_lose']['items'],','):''); ?>";

        pw_rfm_segment_value['hibernating'] = "<?php echo (isset($customer_segment['hibernating']['items']) ? implode($customer_segment['hibernating']['items'],','):''); ?>";

        pw_rfm_segment_value['lose'] = "<?php echo (isset($customer_segment['lose']['items']) ? implode($customer_segment['lose']['items'],','):''); ?>";

        var chart_customer_int_data =<?php echo json_encode(($customer_chart_array_f)); ?>;
    </script>
	<?php

}elseif($file_used=="search_form"){
	global $pw_rpt_main_class;
	$this->pw_get_date_form_to();
	$pw_from_date=$pw_rpt_main_class->pw_from_date_dashboard;
	$pw_to_date=$pw_rpt_main_class->pw_to_date_dashboard;
	?>
    <form class='alldetails search_form_report' action='' method='post' id="intelligence_customer_chart">
        <input type='hidden' name='action' value='submit-form' />


        <input type='hidden' name='action' value='submit-form' />
        <input type='hidden' name="pw_from_date" id="pwr_from_date_dashboard" value="<?php echo $pw_from_date;?>"/>
        <input type='hidden' name="pw_to_date" id="pwr_to_date_dashboard"  value="<?php echo $pw_to_date;?>"/>
        <input type="hidden" name="pw_orders_status[]" id="order_status" value="<?php echo $this->pw_shop_status; ?>">


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
            <input type="hidden" name="pw_rfm_segment_value" value="" class="pw_rfm_segment_value">
            <input type="hidden" name="pw_rfm_segment_customer_ids" value="" class="pw_rfm_segment_customer_ids">
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
?>