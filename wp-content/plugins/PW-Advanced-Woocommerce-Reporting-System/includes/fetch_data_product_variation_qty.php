<?php

if($file_used=="sql_table")
{

	//GET POSTED PARAMETERS
	$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','product_name',true);
	$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
	$group_by 			= $this->pw_get_woo_requests('pw_groupby','variation_id',true);


	$pw_order_ids		= $this->pw_get_woo_requests('pw_order_ids',"-1",true);
	if($pw_order_ids != NULL  && $pw_order_ids != '-1')
	{
		$pw_order_ids = "'".str_replace(",", "','",$pw_order_ids)."'";
	}


	$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid',"-1",true);

	if($pw_paid_customer != NULL  && $pw_paid_customer != '-1')
	{
		$pw_paid_customer = "'".str_replace(",", "','",$pw_paid_customer)."'";
	}

	$pw_billing_post_code	= $this->pw_get_woo_requests('pw_bill_post_code',"-1",true);

	$pw_product_sku 		= $this->pw_get_woo_requests('pw_sku_products','-1',true);
	if($pw_product_sku != NULL  && $pw_product_sku != '-1'){
		$pw_product_sku  		= "'".str_replace(",","','",$pw_product_sku)."'";
	}

	$pw_variation_sku 		= $this->pw_get_woo_requests('pw_sku_variations','-1',true);
	if($pw_variation_sku != NULL  && $pw_variation_sku != '-1'){
		$pw_variation_sku  		= "'".str_replace(",","','",$pw_variation_sku)."'";
	}

	$page				= $this->pw_get_woo_requests('page',NULL);
	$pw_show_variation 	= get_option($page.'_show_variation','variable');
	$report_name 		= apply_filters($page.'_default_report_name', 'product_page');

	$report_name 		= $this->pw_get_woo_requests('report_name',$report_name,true);
	$admin_page			= $this->pw_get_woo_requests('admin_page',$page,true);

	$pw_EndDate				= $this->pw_get_woo_requests('pw_to_date',false);
	$pw_StareDate			= $this->pw_get_woo_requests('pw_from_date',false);
	$category_id		= $this->pw_get_woo_requests('pw_category_id','-1',true);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id		= $this->pw_get_woo_requests('pw_brand_id','-1',true);

	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
	$pw_product_id			= $this->pw_get_woo_requests('pw_product_id','-1',true);
	$pw_variations			= $this->pw_get_woo_requests('pw_variations','-1',true);
	$pw_variation_column	= $this->pw_get_woo_requests('pw_variation_cols','1',true);
	$pw_show_variation		= $this->pw_get_woo_requests('pw_show_adr_variaton',$pw_show_variation,true);
	$count_generated	= $this->pw_get_woo_requests('count_generated',0,true);

	$pw_show_variation='-1';

	$item_att = array();
	$pw_item_meta_key =  '-1';
	if($pw_show_variation=='variable' && $pw_variations != '-1' and strlen($pw_variations) > 0){

		$pw_variations = explode(",",$pw_variations);
		//$this->print_array($pw_variations);
		$var = array();
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



	//GET POSTED PARAMETERS
	$start				= 0;
	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
	$date_format = $this->pw_date_format($pw_from_date);

	$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
	$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
	$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id 		= $this->pw_get_woo_requests('pw_brand_id','-1',true);
	$pw_brand_prod_id_string = $this->pw_get_woo_pli_category($brand_id,$pw_product_id);

	$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
	$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);

	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

	$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

	///////////HIDDEN FIELDS////////////
	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
	$pw_publish_order='no';


	/////////////////////////
	//APPLY PERMISSION TERMS
	$key=$this->pw_get_woo_requests('table_names','',true);

	$category_id=$this->pw_get_form_element_permission('pw_category_id',$category_id,$key);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id=$this->pw_get_form_element_permission('pw_brand_id',$brand_id,$key);

	$pw_order_status=$this->pw_get_form_element_permission('pw_orders_status',$pw_order_status,$key);

	if($pw_order_status != NULL  && $pw_order_status != '-1')
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	///////////////////////////



	$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
	//////////////////////


	//CATEGORY
	$category_id_join='';
	$category_id_condition='';
	$pw_cat_prod_id_string_condition='';

	//ORDER ID
	$order_id_condition='';

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id_join='';
	$brand_id_condition='';
	$pw_brand_prod_id_string_condition='';

	//DATE
	$pw_from_date_condition='';

	//PRODUCT ID
	$pw_product_id_condition='';

	//ORDER
	$pw_id_order_status_join='';

	//VARIATION
	$pw_variation_item_meta_key_join='';
	$sql_variation_join='';
	$pw_show_variation_join='';
	$pw_variation_item_meta_key_condition='';
	$sql_variation_condition='';

	//SKU
	$product_variation_sku_condition='';
	$pw_variation_sku_condition='';
	$pw_product_sku_condition='';

	//PAID CUSTOMER
	$pw_paid_customer_join='';
	$pw_paid_customer_condition='';

	//BILLING CODE
	$pw_billing_post_code_join='';
	$pw_billing_post_code_condition='';

	//ORDER STATUS
	$pw_id_order_status_condition='';
	$pw_order_status_condition='';

	//HIDE ORDER
	$pw_hide_os_condition='';


	$sql_columns = "
		pw_woocommerce_order_items.order_item_name			AS 'product_name'
		,SUM(woocommerce_order_itemmeta.meta_value)		AS 'quantity'
		,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'amount'";

	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_columns .= " ,SUM(woocommerce_order_itemmeta.meta_value * pw_woocommerce_order_itemmeta22.meta_value) AS 'total_cost'";
	}

	$sql_columns .= "
		,DATE(shop_order.post_date)						AS post_date
		,pw_woocommerce_order_itemmeta7.meta_value			AS product_id
		,pw_woocommerce_order_items.order_item_id 			AS order_item_id";

	$sql_columns .= ", pw_woocommerce_order_itemmeta8.meta_value AS 'variation_id'";

	if($pw_show_variation == 'variable') {

		$sql_columns .= ", pw_woocommerce_order_itemmeta8.meta_value AS 'variation_id'";

		if($pw_sort_by == "sku")
			$sql_columns .= ", IF(pw_postmeta_sku.meta_value IS NULL or pw_postmeta_sku.meta_value = '', IF(pw_postmeta_product_sku.meta_value IS NULL or pw_postmeta_product_sku.meta_value = '', '', pw_postmeta_product_sku.meta_value), pw_postmeta_sku.meta_value) as pw_sku ";

	}else{
		if($pw_sort_by == "sku")
			$sql_columns .= ", IF(pw_postmeta_product_sku.meta_value IS NULL or pw_postmeta_product_sku.meta_value = '', '', pw_postmeta_product_sku.meta_value) as pw_sku";

	}


	if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
		$sql_columns .= " , pw_woocommerce_order_itemmeta_variation.meta_key AS variation_key";
		$sql_columns .= " , pw_woocommerce_order_itemmeta_variation.meta_value AS variation_value";
	}


	$sql_joins =  "
			{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	= pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id= pw_woocommerce_order_items.order_item_id";

	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_joins .=	"	
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta22 ON pw_woocommerce_order_itemmeta22.order_item_id=pw_woocommerce_order_items.order_item_id ";
	}

	$sql_joins .=	"
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id= pw_woocommerce_order_items.order_item_id";



	if($category_id  && $category_id != "-1") {
		$category_id_join= " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
	}

	////ADDED IN VER4.0
	//BRANDS ADDONS
	if($brand_id  && $brand_id != "-1") {
		$brand_id_join= " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_brand 	ON pw_term_relationships_brand.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy_brand 		ON term_taxonomy_brand.term_taxonomy_id	=	pw_term_relationships_brand.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_brand 				ON pw_terms_brand.term_id					=	term_taxonomy_brand.term_id";
	}

	if($pw_id_order_status  && $pw_id_order_status != "-1") {
		$pw_id_order_status_join= " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
				LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
	}


	$sql_joins.=$category_id_join.$brand_id_join.$pw_id_order_status_join;
	$sql_joins .= " 
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta8 ON pw_woocommerce_order_itemmeta8.order_item_id = pw_woocommerce_order_items.order_item_id
					";
	if($pw_show_variation == 'variable'){

		if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1') || $pw_variation_sku != '-1')
			$sql_joins .= "	LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_sku 		ON pw_postmeta_sku.post_id		= pw_woocommerce_order_itemmeta8.meta_value";

		if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
			$pw_variation_item_meta_key_join= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_variation ON pw_woocommerce_order_itemmeta_variation.order_item_id= pw_woocommerce_order_items.order_item_id";
		}

		$sql_variation_join='';
		if(isset($this->search_form_fields['pw_new_value_variations']) and count($this->search_form_fields['pw_new_value_variations'])>0){
			foreach($this->search_form_fields['pw_new_value_variations'] as $key => $value){
				$new_v_key = "wcvf_".$this->pw_woo_filter_chars($key);
				$sql_variation_join= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_{$new_v_key} ON woocommerce_order_itemmeta_{$new_v_key}.order_item_id = pw_woocommerce_order_items.order_item_id";
			}
		}

	}

	$sql_joins.=$pw_variation_item_meta_key_join.$sql_variation_join;

	if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1'))
		$sql_joins .= "	LEFT JOIN  {$wpdb->prefix}postmeta		 as pw_postmeta_product_sku 		ON pw_postmeta_product_sku.post_id 			= pw_woocommerce_order_itemmeta7.meta_value	";

	$sql_joins .= " LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id";//For shop_order

	if($pw_show_variation == 2 || ($pw_show_variation == 'grouped' || $pw_show_variation == 'external' || $pw_show_variation == 'simple' || $pw_show_variation == 'variable_')){
		$pw_show_variation_join= " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_type 	ON pw_term_relationships_product_type.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_type 		ON pw_term_taxonomy_product_type.term_taxonomy_id		=	pw_term_relationships_product_type.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_type 				ON pw_terms_product_type.term_id						=	pw_term_taxonomy_product_type.term_id";
	}

	if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'"){
		$pw_paid_customer_join= " 
				LEFT JOIN  {$wpdb->prefix}postmeta 			as pw_postmeta_billing_email				ON pw_postmeta_billing_email.post_id=pw_woocommerce_order_items.order_id";
	}

	if($pw_billing_post_code and $pw_billing_post_code != '-1'){
		$pw_billing_post_code_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_postcode ON pw_postmeta_billing_postcode.post_id	=	pw_woocommerce_order_items.order_id";
	}

	$sql_joins.=$pw_show_variation_join.$pw_paid_customer_join.$pw_billing_post_code_join;

	$sql_condition= "
			woocommerce_order_itemmeta.meta_key	= '_qty'
			AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' ";

	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_condition .="
				AND pw_woocommerce_order_itemmeta22.meta_key	= '".__PW_COG_TOTAL__."' ";
	}

	$sql_condition .="
			AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'						
			AND shop_order.post_type					= 'shop_order'
			";
	$sql_condition.= "
					AND pw_woocommerce_order_itemmeta8.meta_key = '_variation_id' 
					AND (pw_woocommerce_order_itemmeta8.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta8.meta_value > 0)
					";
	if($pw_show_variation == 'variable'){
		$sql_condition.= "
					AND pw_woocommerce_order_itemmeta8.meta_key = '_variation_id' 
					AND (pw_woocommerce_order_itemmeta8.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta8.meta_value > 0)
					";

		if(($pw_sort_by == "sku") || ($pw_variation_sku and $pw_variation_sku != '-1'))
			$sql_condition .=	" AND pw_postmeta_sku.meta_key	= '_sku'";



		if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
			$pw_variation_item_meta_key_condition= " AND pw_woocommerce_order_itemmeta_variation.meta_key IN ('{$pw_variation_item_meta_key}')";
		}

		$sql_variation_condition='';
		if(isset($this->search_form_fields['pw_new_value_variations']) and count($this->search_form_fields['pw_new_value_variations'])>0){
			foreach($this->search_form_fields['pw_new_value_variations'] as $key => $value){
				$new_v_key = "wcvf_".$this->pw_woo_filter_chars($key);
				$key = str_replace("'","",$key);
				$sql .= " AND woocommerce_order_itemmeta_{$new_v_key}.meta_key = '{$key}'";
				$vv = is_array($value) ? implode(",",$value) : $value;
				//$vv = str_replace("','",",",$vv);
				$vv = str_replace(",","','",$vv);
				$sql_variation_condition= " AND woocommerce_order_itemmeta_{$new_v_key}.meta_value IN ('{$vv}') ";
			}
		}
	}

	$sql_condition.=$pw_variation_item_meta_key_condition.$sql_variation_condition;

	if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1'))
		$sql_condition .= " AND pw_postmeta_product_sku.meta_key			= '_sku'";

	if($pw_show_variation == 'variable'){

		if(($pw_product_sku and $pw_product_sku != '-1') and ($pw_variation_sku and $pw_variation_sku != '-1')){
			$product_variation_sku_condition= " AND (pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.") AND pw_postmeta_sku.meta_value IN (".$pw_variation_sku."))";
		}else if ($pw_variation_sku and $pw_variation_sku != '-1'){
			$pw_variation_sku_condition= " AND pw_postmeta_sku.meta_value IN (".$pw_variation_sku.")";
		}else{
			if($pw_product_sku and $pw_product_sku != '-1')
				$pw_product_sku_condition= " AND pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.")";
		}

	}else{

		if($pw_product_sku and $pw_product_sku != '-1')
			$pw_product_sku_condition= " AND pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.")";

	}

	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition= " 
					AND (DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format'))";
	}

	if($pw_product_id  && $pw_product_id != "-1")
		$pw_product_id_condition= "
					AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_product_id .")";

	if($category_id  && $category_id != "-1")
		$category_id_condition= "
					AND pw_terms.term_id IN (".$category_id .")";

	////ADDED IN VER4.0
	//BRANDS ADDONS
	if($brand_id  && $brand_id != "-1")
		$brand_id_condition= " 
                AND term_taxonomy_brand.taxonomy LIKE('".__PW_BRAND_SLUG__."')
                AND pw_terms_brand.term_id IN (".$brand_id .")";

	if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1")
		$pw_cat_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_cat_prod_id_string .")";

	////ADDED IN VER4.0
	//BRANDS ADDONS
	if($pw_brand_prod_id_string  && $pw_brand_prod_id_string != "-1")
		$pw_brand_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_brand_prod_id_string .")";

	if($pw_id_order_status  && $pw_id_order_status != "-1")
		$pw_id_order_status_condition= " 
					AND terms2.term_id IN (".$pw_id_order_status .")";


	$sql_condition.=$product_variation_sku_condition.$pw_variation_sku_condition.$pw_product_sku_condition.$pw_from_date_condition.$pw_product_id_condition.$category_id_condition.$brand_id_condition.$pw_cat_prod_id_string_condition.$pw_brand_prod_id_string_condition.$pw_id_order_status_condition;


	if($pw_show_variation == 'grouped' || $pw_show_variation == 'external' || $pw_show_variation == 'simple' || $pw_show_variation == 'variable_'){
		$sql_condition .= " AND pw_terms_product_type.name IN ('{$pw_show_variation}')";
	}

	if($pw_show_variation == 2){
		$sql_condition .= " AND pw_terms_product_type.name IN ('simple')";
	}

	if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'"){
		$pw_paid_customer_condition= " AND pw_postmeta_billing_email.meta_key='_billing_email'";
		$pw_paid_customer_condition .= " AND pw_postmeta_billing_email.meta_value IN (".$pw_paid_customer.")";
	}

	if($pw_billing_post_code and $pw_billing_post_code != '-1'){
		$pw_billing_post_code_condition= " AND pw_postmeta_billing_postcode.meta_key='_billing_postcode' AND pw_postmeta_billing_postcode.meta_value IN ({$pw_billing_post_code}) ";
	}

	if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
		$pw_order_status_condition= " AND pw_posts.post_status IN (".$pw_order_status.")";

	if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
		$pw_hide_os_condition= " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";


	if($pw_order_ids  && $pw_order_ids != "-1")
		$order_id_condition= "
					AND pw_posts.ID IN (".$pw_order_ids .")";

	$sql_condition.=$pw_paid_customer_condition.$pw_billing_post_code_condition.$pw_order_status_condition.$pw_hide_os_condition;




	$sql_group_by='';
	if($pw_show_variation == 'variable'){
		switch ($group_by) {
			case "variation_id":
				$sql_group_by= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
				break;
			case "order_item_id":
				$sql_group_by= " GROUP BY pw_woocommerce_order_items.order_item_id ";
				break;
			default:
				$sql_group_by= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
				break;

		}
		//$sql .= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
	}else{
		$sql_group_by= " 
					GROUP BY  pw_woocommerce_order_itemmeta7.meta_value";
	}

	$sql_order_by='';
	switch ($pw_sort_by) {
		case "sku":
			$sql_order_by= " ORDER BY sku " .$pw_order_by;
			break;
		case "product_name":
			$sql_order_by= " ORDER BY product_name " .$pw_order_by;
			break;
		case "ProductID":
			$sql_order_by= " ORDER BY CAST(product_id AS DECIMAL(10,2)) " .$pw_order_by;
			break;
		case "amount":
			$sql_order_by= " ORDER BY amount " .$pw_order_by;
			break;
		case "variation_id":
			if($pw_show_variation == 'variable'){
				$sql_order_by= " ORDER BY CAST(variation_id AS DECIMAL(10,2)) " .$pw_order_by;
			}
			break;
		default:
			$sql_order_by= " ORDER BY amount DESC";
			break;
	}

	$sql="SELECT $sql_columns FROM $sql_joins WHERE $sql_condition $sql_group_by $sql_order_by";

	//echo $sql;


	$this->table_cols =$this->table_columns($table_name);
	///////////CHECK IF BRANDS ADD ON IS ENABLE///////////
	$array_index=3;
	$brands_cols=array();
	if(__PW_BRAND_SLUG__){
		$brands_cols[]=array('lable'=>__PW_BRAND_LABEL__,'status'=>'show');
		array_splice($this->table_cols,$array_index,0,$brands_cols);
		$array_index++;
	}


	$sql="SELECT  (DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y')) AS order_date, (pw_woocommerce_order_items.order_id) AS order_id,	(pw_woocommerce_order_items.order_item_name) AS product_name,	(pw_woocommerce_order_items.order_item_id)	AS order_item_id ,(count(pw_woocommerce_order_items.order_item_id)) AS product_quentity, (woocommerce_order_itemmeta.meta_value) AS product_id ,(pw_woocommerce_order_itemmeta4.meta_value) as variation_id ,(pw_woocommerce_order_itemmeta3.meta_value) AS product_quantity	,(pw_posts.post_status) AS order_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	=	pw_woocommerce_order_items.order_item_id  LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta3 ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta4 ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id Where (pw_posts.post_type = 'shop_order' OR pw_posts.post_type='shop_order_refund')   AND pw_woocommerce_order_itemmeta4.meta_key = '_variation_id' AND ((woocommerce_order_itemmeta.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta3.meta_key='_qty') OR (woocommerce_order_itemmeta.meta_key = '_fee_amount')) $pw_from_date_condition $pw_order_status_condition $pw_hide_os_condition $category_id_condition $order_id_condition AND pw_posts.post_status IN ('wc-processing','wc-on-hold','wc-completed') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_woocommerce_order_items.order_item_id ORDER BY variation_id ASC";


	//echo $sql;

}elseif($file_used=="data_table"){

		$pw_show_variation		= $this->pw_get_woo_requests('pw_show_adr_variaton','variable',true);


	    $i=$j=0;

       // var_dump($this->sql2);

	$array_product=array();
        foreach($this->results as $items){
//            $array_variation[$items->variation_id]['name']=$items->product_name;
//            $array_variation[$items->variation_id]['qty']=$items->quantity;


	        $pw_table_value= $this->pw_get_woo_variation($items->order_item_id);
	        $order_item_id			= ($items->order_item_id);
	        $attributes 							= $this->pw_get_variaiton_attributes('order_item_id','',$order_item_id);
	        $varation_string 						= isset($attributes['item_varation_string']) ? $attributes['item_varation_string'] : array();
	        $pw_table_value			= $varation_string[$order_item_id]['varation_string'];

            $product_id=$items->product_id;
	        $variation_id=$items->variation_id;


	        if($variation_id=='0' || $variation_id==''){

	            $row=$i;
	            $qty=$items->product_quantity;
	            foreach($array_product as $key=>$val){
	               // echo $key;
	                if($val[0]==$product_id){
		                $array_product[$key][2]+=$qty;
	                    $row=$key;
	                    break;
                    }
                }

		        if($row!=$i) continue;

	            $array_product[$row][0]=$product_id;
	            $array_product[$row][1]=$items->product_name;



	            $array_product[$row][2]=$qty;

	            $array_product[$row][3]='*';

	            if(!isset($array_product[$row][4])) $array_product[$row][4]='';
	            if(!isset($array_product[$row][5])) $array_product[$row][5]='';
	            if(!isset($array_product[$row][6])) $array_product[$row][6]='';
	            if(!isset($array_product[$row][7])) $array_product[$row][7]='';

	            $i++;
            }

	        if($variation_id!='0' && $variation_id!=''){

		        $row=$j;
		        $qty=$items->product_quantity;
		        foreach($array_product as $key=>$val){
			        if($val[4]==$variation_id){
				        $array_product[$key][7]+=$qty;
				        $row=$key;
				        break;
			        }
		        }

                if($row!=$j) continue;

		        if(!isset($array_product[$row][0])) $array_product[$row][0]='';
		        if(!isset($array_product[$row][1])) $array_product[$row][1]='';
		        if(!isset($array_product[$row][2])) $array_product[$row][2]='';

		        $array_product[$row][3]='*';

		        $array_product[$row][4]=($variation_id);
		        $array_product[$row][5]=($items->product_name);
		        $array_product[$row][6]=$pw_table_value;
		        $array_product[$row][7]=$qty;
		        $j++;
	        }

	        //$i++;
        }
//	    print_r($array_product);
//	    print_r($array_variation);
//var_dump($array_product);


        foreach($array_product as $key=>$value){
	        $datatable_value.=("<tr>");
            foreach($value as $keys=>$items) {

                if($keys==0 || $keys==4) continue;

	            $display_class = '';

	            if($items=='*') {
		            $display_class .= 'background-color: #b0b9bf;';
		            $items=' ';
	            }

	            $datatable_value .= ( "<td style='" . $display_class . "'>" );
	            $datatable_value .= $items;
	            $datatable_value .= ( "</td>" );
            }
	        $datatable_value.=("</tr>");
        }


	////ADDE IN VER4.0
		/// TOTAL ROWS VARIABLES
		$result_count=$sale_qty=$total_amnt=$cog_amnt=$profit_amnt=0;



	}elseif($file_used=="search_form"){
	?>
		<form class='alldetails search_form_report' action='' method='post'>
            <input type='hidden' name='action' value='submit-form' />
            <div class="row">

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('From Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('To Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"/>
                </div>


                <div class="col-md-6">
                    <div class="awr-form-title">
			            <?php _e('Order ID(s)',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_order_ids" id="pw_order_ids" type="text" placeholder="<?php _e('Separate ids with coma(,)',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>"/>
                </div>

                <?php
                	$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_orders_status');
					if($this->get_form_element_permission('pw_orders_status')||  $permission_value!=''){

						if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
							$col_style='display:none';
				?>

                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map"></i></span>
					<?php
                        $pw_order_status=$this->pw_get_woo_orders_statuses();

                        ////ADDED IN VER4.0
                        /// APPLY DEFAULT STATUS AT FIRST
                        $shop_status_selected='';
                        if($this->pw_shop_status)
                            $shop_status_selected=explode(",",$this->pw_shop_status);

                        $option='';
                        foreach($pw_order_status as $key => $value){
							$selected="";
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($key,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
								$selected="selected";*/

	                        ////ADDED IN VER4.0
	                        /// APPLY DEFAULT STATUS AT FIRST
	                        if(is_array($shop_status_selected) && in_array($key,$shop_status_selected))
		                        $selected="selected";

	                        $option.="<option value='".$key."' $selected >".$value."</option>";
                        }
                    ?>

                    <select name="pw_orders_status[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_orders_status') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
							{
						?>
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
							}
						?>
                        <?php
                            echo $option;
                        ?>
                    </select>
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                </div>

                <?php
					}
				?>



            </div>

            <div class="col-md-12">
                    <?php
                    	$pw_hide_os=$this->otder_status_hide;
						$pw_publish_order='no';

						$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
					?>
                    <input type="hidden" name="list_parent_category" value="">
                    <input type="hidden" name="group_by_parent_cat" value="0">

                	<input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os;?>" />

                    <input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format;?>" />

                	<input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
                    <div class="fetch_form_loading search-form-loading"></div>
                    <button type="submit" value="Search" class="button-primary"><i class="fa fa-search"></i> <span><?php echo __('Search',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
					<button type="button" value="Reset" class="button-secondary form_reset_btn"><i class="fa fa-reply"></i><span><?php echo __('Reset Form',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
            </div>

        </form>
    <?php
	}

?>