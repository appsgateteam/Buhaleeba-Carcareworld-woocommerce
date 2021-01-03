<?php
	global $wpdb;
	$shop_order_status			= $this->pw_shop_status;
	$order_data 		= array();
	$sql_error 			= "";
	$status_sql_query 	= "";
	$status_join_query 	= "";
$date_format = $this->pw_date_format($start_date);

	if(count($shop_order_status)>0){
		$in_shop_order_status=$shop_order_status;
		if(is_array($shop_order_status))
			$in_shop_order_status		= implode("', '",$shop_order_status);
		$status_sql_query = " AND  posts.post_status IN ('{$in_shop_order_status}')";
	}

	if(strlen($this->otder_status_hide)>0){
		$status_sql_query .= " AND  posts.post_status NOT IN ('{$this->otder_status_hide}')";
	}

	$sql = " 
					SELECT count(*) as 'total_orders'	
					FROM {$wpdb->prefix}posts as posts $status_join_query
					WHERE  posts.post_type='shop_order'				
					AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') $status_sql_query";

	$_total_orders_sql =  '' ;
	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_data['total_orders_count'] = $wpdb->get_var($sql);

	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	$sql = "SELECT 
					SUM(postmeta.meta_value) AS 'total_sales' FROM {$wpdb->prefix}postmeta as postmeta 
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=postmeta.post_id $status_join_query 						
					WHERE  posts.post_type='shop_order'
					
					AND meta_key='_order_total' 
					AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format')
					";
	$sql .= $status_sql_query;

	$_total_sales_sql =  '' ;
	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_data['total_sales_amount'] = $wpdb->get_var($sql);

	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}
	//==== total ====

	if($order_data['total_orders_count'] != '' && $order_data['total_sales_amount'] != '')
	{
		$order_data['total_sales_avg_amount'] = $order_data['total_sales_amount']/$order_data['total_orders_count'];
	}
	$sql = "SELECT SUM( postmeta.meta_value) As 'total_amount', count( postmeta.post_id) AS 'total_count' 
			FROM {$wpdb->prefix}posts as posts	
					LEFT JOIN  {$wpdb->prefix}term_relationships as term_relationships	ON term_relationships.object_id=posts.ID 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=term_relationships.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms as terms ON terms.term_id=term_taxonomy.term_id			
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=posts.ID
					WHERE terms.name ='refunded' AND postmeta.meta_key = '_order_total' AND posts.post_type='shop_order'
					AND DATE(posts.post_modified) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') $status_sql_query 
					Group BY terms.term_id ORDER BY total_amount DESC";

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_items  = $wpdb->get_row($sql);

	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}
	$sql = "SELECT
					SUM(woocommerce_order_itemmeta.meta_value) As 'total_amount', 
					Count(*) AS 'total_count' 
					FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items 
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=woocommerce_order_items.order_item_id
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=woocommerce_order_items.order_id $status_join_query 
					WHERE 
					woocommerce_order_items.order_item_type='coupon' 
					AND woocommerce_order_itemmeta.meta_key='discount_amount'
					AND posts.post_type='shop_order'
					AND DATE(posts.post_modified) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format')				
					";
	$sql .= $status_sql_query;

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_items_coupon = $wpdb->get_row($sql);
	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}


	$sql = "SELECT 						
					COUNT(postmeta6.meta_value) AS 'ItemCount'						
					,SUM(postmeta6.meta_value) As discount_value				
					FROM 
					{$wpdb->prefix}woocommerce_order_items as woocommerce_order_items						
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta6 ON postmeta6.post_id=woocommerce_order_items.order_id
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=woocommerce_order_items.order_id $status_join_query					 
					WHERE 						
					posts.post_type='shop_order'						
					AND	postmeta6.meta_key='_order_discount'
					AND postmeta6.meta_value != 0
					AND DATE(posts.post_modified) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') $status_sql_query 
					GROUP BY woocommerce_order_items.order_id";

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_items_discount = $wpdb->get_row($sql);
	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	$sql = "  SELECT SUM(postmeta1.meta_value) AS 'total_amount',count(woocommerce_order_items.order_id) AS 'total_count'
					FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items				
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta1 ON postmeta1.post_id=woocommerce_order_items.order_id 
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=	woocommerce_order_items.order_id  $status_join_query 
					WHERE postmeta1.meta_key = '_order_shipping_tax' AND woocommerce_order_items.order_item_type = 'tax' 
					AND posts.post_type='shop_order' AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') $status_sql_query";

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");

	$order_items_shipping_tax = $wpdb->get_row($sql);
	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	$sql = "  SELECT SUM(postmeta1.meta_value) AS 'total_amount',count(woocommerce_order_items.order_id) AS 'total_count'
					FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items				
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta1 ON postmeta1.post_id=woocommerce_order_items.order_id 
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=	woocommerce_order_items.order_id $status_join_query 
					WHERE postmeta1.meta_key = '_order_tax' AND woocommerce_order_items.order_item_type = 'tax' 
					AND posts.post_type='shop_order' AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') 
					$status_sql_query";

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$order_items_tax = $wpdb->get_row($sql);
	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	$id = "_order_shipping";
	$sql = "SELECT 					
					SUM(postmeta2.meta_value)						as 'Shipping Total'					
					FROM {$wpdb->prefix}posts as shop_order					
					LEFT JOIN	{$wpdb->prefix}postmeta as postmeta2 on postmeta2.post_id = shop_order.ID
					LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID =	shop_order.ID $status_join_query 
					WHERE shop_order.post_type	= 'shop_order' AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format') 
					AND postmeta2.meta_value > 0 AND postmeta2.meta_key 	= '{$id}' $status_sql_query";


	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$shipping_amount =  $wpdb->get_var($sql);

	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	$sql = "SELECT COUNT(postmeta.meta_value) AS 'OrderCount' ,SUM(postmeta.meta_value) AS 'Total' 
,posts.post_status As 'Status' ,posts.post_status As 'StatusID' FROM {$wpdb->prefix}posts as posts 
					LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=posts.ID
					WHERE postmeta.meta_key = '_order_total'  AND posts.post_type='shop_order' ";

	if ($start_date != NULL &&  $end_date !=NULL){
		$sql .= " AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $start_date . "', '$date_format') and STR_TO_DATE('" . $end_date . "', '$date_format')";
	}

	if(strlen($this->otder_status_hide)>0){
		$sql .= " AND  posts.post_status NOT IN ('{$this->otder_status_hide}')";
	}

	$sql .= " Group BY posts.post_status ORDER BY Total DESC";

	$order_items_status = $wpdb->get_results($sql);
	if(strlen($wpdb->last_error)>0){
		$sql_error .= $wpdb->last_error." <br /> ";
	}

	if(count($order_items_status)>0){
		if(function_exists('wc_get_order_statuses')){
			$order_statuses = wc_get_order_statuses();
		}else{
			$order_statuses = array();
		}

		foreach($order_items_status as $key  => $value){
			$order_items_status[$key]->Status = isset($order_statuses[$value->Status]) ? $order_statuses[$value->Status] : $value->Status;
		}
	}

	$start= '';
	if($title == "Today" || $title == "Yesterday"){
		$start= '';
	}else{
		$start= date("F d, Y",strtotime($start_date)).' To ';
		if($title == "Till Date"){
			$start= " First order To ";
		}
	}

	$body = "";

	$body .= $sql_error;

	$body.='<div style="width: 520px; margin: 0 auto">';
	$body.='<h1 style="font-size: 18px; color: #fac34f; margin-bottom: 5px">'.$title. " " .__('Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .$start .date("F d, Y",strtotime($end_date)).'</h1>
    				<div style="width: 100px; height: 3px; background-color:#fac34f; margin-bottom: 20px"></div>';
	$body.='<table width="100%" cellspacing="0">
						<tr>
				            <td style="padding: 10px; background-color: #fac34f; color: #fff; font-size: 13px; text-transform: uppercase; font-weight: bold">
				                '.__("Ttitle",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 13px; text-transform: uppercase; font-weight: bold; border-right: 1px solid #fff">
				                '.__("Sales Qty.",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Amount",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				        </tr>';




	//$body .= '<div style="width:520px; margin:0 auto; font-family:Arial, Helvetica, sans-serif; font-size:12px;">';
	//$body .= '<div style="padding:5px 10px;">';
	//$body .= '<table style="width:500px; border:1px solid #0066CC; margin:0 auto;">';
	//echo "Line 1 <br>";
	//$body .= '<tr>';
	//$body .= '<td colspan="3" style="padding:6px 10px; background:#BCD3E7; font-size:13px; margin:0px;">';
	//$body .= '<h3 style="padding:0px; margin:0">'.$title. " " .__('Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .$start .date("F d, Y",strtotime($end_date)).'</h3>';
	//$body .= '</td>';
	//$body .= '</tr>';

	if($order_data['total_orders_count'] > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Total Sales:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $order_data['total_orders_count'].$_total_orders_sql.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">#13</td>';
		$body .= '</tr>';
	endif;
	if($order_data['total_sales_amount'] > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Total Sales Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;"></td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_data['total_sales_amount']).$_total_sales_sql.'</td>';
		$body .= '</tr>';
	endif;
	if(isset($order_items_discount->ItemCount) and $order_items_discount->ItemCount > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Discount Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'.$order_items_discount->ItemCount.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_items_discount->discount_value).'</td>';

		$body .= '</tr>';
	endif;
	if(isset($order_items_discount->total_count) and $order_items_coupon->total_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Coupon Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $order_items_coupon->total_count.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_items_coupon->total_amount).'</td>';
		$body .= '</tr>';
	endif;
	if(isset($order_items->total_count) and $order_items->total_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Refund Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'.$order_items->total_count .'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'.$this->price($order_items->total_amount).'</td>';
		$body .= '</tr>';
	endif;
	//echo "Line 2 <br>";
	if(isset($order_items_shipping_tax->total_count) and $order_items_shipping_tax->total_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Shipping Tax Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $order_items_shipping_tax->total_count.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_items_shipping_tax->total_amount).'</td>';
		$body .= '</tr>';
	endif;

	if(isset($order_items_tax->total_count) and $order_items_tax->total_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Order Tax Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $order_items_tax->total_count.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_items_tax->total_amount).'</td>';
		$body .= '</tr>';
	endif;

	//echo "Line 2.1 <br>";

	if(isset($order_items_tax->total_count) and $order_items_shipping_tax->total_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Total Tax Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">&nbsp; &nbsp; &nbsp; </td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_items_tax->total_amount + $order_items_shipping_tax->total_amount).'</td>';
		$body .= '</tr>';
	endif;

	//echo "Line 2.2 <br>";

	if($shipping_amount > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Shipping Amount:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">&nbsp; &nbsp; &nbsp; </td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($shipping_amount).'</td>';
		$body .= '</tr>';
	endif;

	//echo "Line 2.3 <br>";

	if(isset($order_data['total_sales_avg_amount']) and $order_data['total_sales_avg_amount'] > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("Average Sales:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">&nbsp; &nbsp; &nbsp; </td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $this->price($order_data['total_sales_avg_amount']).'</td>';
		$body .= '</tr>';
	endif;

	//echo "Line 3.1 <br>";

	$today_customer_count = $this->pw_today_total_customer($start_date, $end_date);
	if($today_customer_count > 0):
		$body .= '<tr>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.__("New Customer:",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'. $today_customer_count.'</td>';
		$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">&nbsp; &nbsp; &nbsp; </td>';
		$body .= '</tr>';
	endif;


	//echo "Line 3.2 <br>";

	if($order_items_status and count($order_items_status)>0):
		$body .= '<tr>';
		//$body .= "\n";
		$body .= '<td colspan="3" style="padding:3px 6px; background:#d3d3d3; width:100%; font-size:13px;"><b>'.__("Order Status",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</b></td>';
		//$body .= "\n";
		$body .= '</tr>';
		$total_amnt=$total_count=0;
		foreach($order_items_status as $key => $order_item)
		{
			$body .= '<tr>';
			//$body .= "\n";
			$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">'.$order_item->Status.'</td>';
			//$body .= "\n";
			$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'.$order_item->OrderCount.'</td>';
			$total_count+=$order_item->OrderCount;
			//$body .= "\n";
			$body .= '<td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">'.$this->price($order_item->Total).'</td>';
			$total_amnt+=$order_item->Total;
			//$body .= "\n";
			//$body .= '<td>&nbsp; &nbsp; &nbsp; </td>';
			//$body .= '<td>&nbsp; &nbsp; &nbsp; </td>';
			$body .= '</tr>';
			$body .= "\n";
		}


		$body.='
				<tr>
		            <td style="padding: 10px; color: #fac34f; font-size: 17px; text-transform: uppercase; font-weight: bold; text-align: right">
		
		            </td>
		            <td style="padding: 10px; background-color: #e6e6e6; color: #444; font-size: 14px; text-transform: uppercase; font-weight: bold">
		                #'.$total_count.'
		            </td>
		            <td style="padding: 10px; background-color: #e6e6e6; color: #444; font-size: 13px; text-transform: uppercase; font-weight: bold;">
		                '.$this->price($total_amnt).'
		            </td>
		        </tr>';


	endif;

	//echo "Line 4 <br>";
	$body .= "\n";
	$body .= '</table>';
	$body .= '<div style="height: 50px;"></div>';
	$body .= '</div>';
	//$body .= '</div>';

?>