<?php
if($file_used=="sql_table")
{
	
	//show_seleted_order_status
	global $wpdb;
	
	$pw_from_date=$this->pw_from_date_dashboard;
	$pw_to_date=$this->pw_to_date_dashboard;
	$date_format = $this->pw_date_format($pw_from_date);

	$pw_hide_os=$this->otder_status_hide;
	$pw_shop_order_status=$this->pw_shop_status;
	
	if(isset($_POST['pw_from_date']))
	{
		//parse_str($_REQUEST, $my_array_of_vars);
		$this->search_form_fields=$_POST;

		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
		$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);

	}
	
	$pw_create_date =  date("Y-m-d");
	$pw_url_shop_order_status	= "";
	$pw_in_shop_os	= "";
	$pw_in_post_os	= "";
	
	
	//$pw_hide_os=$this->otder_status_hide;
	//$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
	$pw_hide_os=explode(',',$pw_hide_os);		
	//$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
	//$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
	if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
		$pw_shop_order_status = explode(",",$pw_shop_order_status); 
	else $pw_shop_order_status = array();
	
	if(count($pw_shop_order_status)>0){
		$pw_in_post_os	= implode("', '",$pw_shop_order_status);	
	}
	
	$in_pw_hide_os = "";
	if(count($pw_hide_os)>0){
		$in_pw_hide_os		= implode("', '",$pw_hide_os);				
	}
	
	$per_page=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',5);
	
	$pw_shop_order_status_condition ='';
	$pw_from_date_condition ='';
	$pw_hide_os_condition='';
	
	$sql_columns = " *, 
	pw_woocommerce_order_items.order_item_name, 
	SUM(woocommerce_order_itemmeta.meta_value) As 'Total', 
	woocommerce_order_itemmeta.meta_value AS 'coupon_amount' , 
	Count(*) AS 'Count'";
	
	$sql_joins="{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
	LEFT JOIN	{$wpdb->prefix}posts						as pw_posts 						ON pw_posts.ID										=	pw_woocommerce_order_items.order_id
	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta	ON woocommerce_order_itemmeta.order_item_id		=	pw_woocommerce_order_items.order_item_id";
	
	
	$sql_condition = "
	pw_posts.post_type 	=	'shop_order'
	AND pw_woocommerce_order_items.order_item_type		=	'coupon' 
	AND woocommerce_order_itemmeta.meta_key			=	'discount_amount'";
	
	if(count($pw_shop_order_status)>0){
		$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
		$pw_shop_order_status_condition = " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
	}
	
	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
	}
	
	if(count($pw_hide_os)>0){
		$in_pw_hide_os		= implode("', '",$pw_hide_os);
		$pw_hide_os_condition = " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
	}
	
	$sql_group_by = " Group BY pw_woocommerce_order_items.order_item_name";
	$sql_order_by =" ORDER BY Total DESC";
	$sql_limit =" LIMIT {$per_page}";
	
	$sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition
			$pw_shop_order_status_condition $pw_from_date_condition $pw_hide_os_condition 
			$sql_group_by $sql_order_by $sql_limit";
	
	//echo $sql;
	
}elseif($file_used=="data_table"){
	
	foreach($this->results as $items){		    $index_cols=0;
	//for($i=1; $i<=20 ; $i++){
						
		$datatable_value.=("<tr>");
								
			//NAME
			$display_class='';
			if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $items->order_item_name;
			$datatable_value.=("</td>");
			
			//Target Sales
			$display_class='';
			if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $items->Count;
			$datatable_value.=("</td>");
			
			//Actual Sales
			$display_class='';
			if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items->Total);
			$datatable_value.=("</td>");
			
		$datatable_value.=("</tr>");
	}
}elseif($file_used=="search_form"){}

?>