<?php
	global $wpdb;

	$order_data 		= array();
	$sql_error 			= "";
	$status_sql_query 	= "";
	$status_join_query 	= "";

	$date_format		= get_option( 'date_format' );

	$datetime= date_i18n("Y-m-d H:i:s");
	$shop_order_status			= $this->pw_shop_status;

	if(count($shop_order_status)>0){
		$in_shop_order_status=$shop_order_status;
		if(is_array($shop_order_status))
			$in_shop_order_status		= implode("', '",$shop_order_status);
		$status_sql_query = " AND  posts.post_status IN ('{$in_shop_order_status}')";
	}

	if(strlen($this->otder_status_hide)>0){
		$status_sql_query .= " AND  posts.post_status NOT IN ('{$this->otder_status_hide}')";
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

	$body='<div style="width: 520px; margin: 0 auto">';
	$body.='<h1 style="font-size: 18px; color: #fac34f; margin-bottom: 5px">'.$title. " " .__('Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .$start .date("F d, Y",strtotime($end_date)).'</h1>
    				<div style="width: 100px; height: 3px; background-color:#fac34f; margin-bottom: 20px"></div>';


	//PURCHASE PRODUCT BY CUSTOMER
	$sql = "SELECT pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,SUM(woocommerce_order_itemmeta.meta_value)	AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'total_amount' ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,pw_postmeta_customer_user.meta_value	AS customer_id ,DATE(shop_order.post_date) AS post_date ,pw_postmeta_billing_billing_email.meta_value	AS billing_email ,CONCAT(pw_postmeta_billing_billing_email.meta_value,' ',pw_woocommerce_order_itemmeta7.meta_value,' ',pw_postmeta_customer_user.meta_value)	AS group_column ,CONCAT(pw_postmeta_billing_first_name.meta_value,' ',postmeta_billing_last_name.meta_value)	AS billing_name	FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_billing_first_name ON pw_postmeta_billing_first_name.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as postmeta_billing_last_name ON postmeta_billing_last_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_billing_billing_email ON pw_postmeta_billing_billing_email.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta_customer_user ON pw_postmeta_customer_user.post_id	=	pw_woocommerce_order_items.order_id WHERE woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_postmeta_billing_first_name.meta_key	= '_billing_first_name' AND postmeta_billing_last_name.meta_key	= '_billing_last_name' AND pw_postmeta_billing_billing_email.meta_key	= '_billing_email' AND pw_postmeta_customer_user.meta_key	= '_customer_user' AND (DATE(shop_order.post_date) BETWEEN '2018-01-01' AND '2018-07-11') AND shop_order.post_status IN ('wc-processing','wc-on-hold','wc-completed') AND shop_order.post_status NOT IN ('{$this->otder_status_hide}') GROUP BY group_column ORDER BY billing_name ASC, product_name ASC, total_amount DESC";

	$wpdb->flush();
	$wpdb->query("SET SQL_BIG_SELECTS=1");
	$purchse_buy_customer =  $wpdb->get_results($sql);

	$datatable_value='';


$body.='<table width="100%" cellspacing="0">
						<tr>
				            <td style="padding: 10px; background-color: #fac34f; color: #fff; font-size: 13px; text-transform: uppercase; font-weight: bold">
				                '.__("Product SKU",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 13px; text-transform: uppercase; font-weight: bold; border-right: 1px solid #fff">
				                '.__("Customer Name",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Customer Email",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Product Name",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Sale Qty.",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Current Stock",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				            <td style="padding: 10px; background-color: #4d4d4f; color: #fff; font-size: 14px; text-transform: uppercase; font-weight: bold">
				                '.__("Amount",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
				            </td>
				        </tr>';
	foreach($purchse_buy_customer as $items){
		$datatable_value.=("<tr>");

		//Product SKU
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
		$datatable_value.=("</td>");

		//Customer Name
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $items->billing_name;
		$datatable_value.=("</td>");

		//Customer Email
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $items->billing_email;
		$datatable_value.=("</td>");

		//Product Name
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $items->product_name;
		$datatable_value.=("</td>");

		//Sales Qty.
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $items->quantity;

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$datatable_value.=("</td>");

		//Current Stock
		$pw_table_value = $this->pw_get_prod_stock_($items->order_item_id, $items->product_id);
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $pw_table_value;
		$datatable_value.=("</td>");

		//Amount
		$display_class='';
		$datatable_value.=('<td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">');
		$datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

		$datatable_value.=("</td>");

		$datatable_value.=("</tr>");
	}
	$body.=$datatable_value;
	$body .= '</table>';

	$body .= '<div style="height: 50px;"></div>';
	$body .= '</div>';
	//$body .= '</div>';

?>