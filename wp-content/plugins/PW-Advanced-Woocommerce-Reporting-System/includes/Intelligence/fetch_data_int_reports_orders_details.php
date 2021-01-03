<?php
	global $wpdb,$pw_rpt_main_class;
	$pw_order_status=$pw_rpt_main_class->pw_shop_status;
	$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

	//////////////////////////////////////////////////////
	/// GET ORDER DETAILS - CUSTOMER ADDRESS- PAYMENT METHOD , ...
	//////////////////////////////////////////////////////
	$sql="SELECT billing_country.meta_value as billing_country, DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date, pw_woocommerce_order_items.order_id AS order_id, pw_woocommerce_order_items.order_item_name AS product_name,	pw_woocommerce_order_items.order_item_id	AS order_item_id, woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value,	(pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS sold_rate, (pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS product_rate, (pw_woocommerce_order_itemmeta4.meta_value) AS item_amount, (pw_woocommerce_order_itemmeta2.meta_value) AS item_net_amount, (pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) AS item_discount,	pw_woocommerce_order_itemmeta2.meta_value AS total_price, count(pw_woocommerce_order_items.order_item_id) AS product_quentity, woocommerce_order_itemmeta.meta_value AS product_id ,pw_woocommerce_order_itemmeta3.meta_value AS 'product_quantity'	,pw_posts.post_status AS post_status ,pw_posts.post_status AS order_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta2 ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta3 ON pw_woocommerce_order_itemmeta3.order_item_id	= pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta4 ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal' LEFT JOIN {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID Where pw_posts.post_type = 'shop_order' AND pw_posts.ID='$order_id' AND billing_country.meta_key	= '_billing_country' AND woocommerce_order_itemmeta.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta2.meta_key='_line_total' AND pw_woocommerce_order_itemmeta3.meta_key='_qty' AND pw_posts.post_status IN ($pw_order_status) GROUP BY pw_woocommerce_order_items.order_item_id";
	$order_items=$wpdb->get_results($sql);
	//print_r($order_items);
	//echo $sql;


	$current_order_status='';
	$order_meta = array();
	if(count($order_items)>0)
		foreach ( $order_items as $key => $order_item ) {

			$order_id								= $order_item->order_id;
			$order_items[$key]->billing_first_name  = '';//Default, some time it missing
			$order_items[$key]->billing_last_name  	= '';//Default, some time it missing
			$order_items[$key]->billing_email  		= '';//Default, some time it missing

			if(!isset($order_meta[$order_id])){
				$order_meta[$order_id]					= $pw_rpt_main_class->pw_get_full_post_meta($order_id);
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

			$current_order_status=$order_item->order_status;

		}

	//print_r($order_items);

	$net_amnt=$part_refund_amnt=$order_count=$gross_amnt=0;
	$first_order_id=$order_date=$payment_method='';
	$customer_array=array();
	$products_array=array();
	$items_count='';
	$email=$customer_name='';
	//print_r($order_items);
	$order_shipping_html='';
	$order_shipping=0;

	foreach($order_items as $key => $order_item  ){



		$date_format		= get_option( 'date_format' );

		$order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount($order_item -> order_id);
		$part_refund=(isset($order_refund_amnt[$order_item->order_id])? $order_refund_amnt[$order_item->order_id]:0);


		//Order Total
		$pw_table_value = isset($order_item -> order_total) ? ($order_item -> order_total)-$part_refund : 0;
		$pw_table_value = $pw_table_value == 0 ? 0 : $pw_table_value;

		$pw_table_value = isset($order_item -> order_total) ? ($order_item -> order_total)-$part_refund : 0;
		// $pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;

		$new_order=false;
		if($first_order_id=='')
		{
			$first_order_id=$order_item->order_id;
			$new_order=true;
		}else if($first_order_id!=$order_item->order_id)
		{
			$first_order_id=$order_item->order_id;
			$new_order=true;
		}
		if($new_order){
			$order_count++;
			$part_refund_amnt+=$order_refund_amnt[$order_item->order_id];
			$net_amnt+=$pw_table_value;

			$customer_array[$order_item->order_id]['order_id']=$order_item->order_id;
			$customer_array[$order_item->order_id]['id']=$order_item->customer_user;
			$customer_array[$order_item->order_id]['date']=date($date_format,strtotime($order_item->order_date));
			$customer_array[$order_item->order_id]['name']=$order_item->billing_name;
			$customer_array[$order_item->order_id]['total']=$pw_table_value;
		}

		$products_array[$order_item->order_item_id]['name']=$order_item->product_name;
		$products_array[$order_item->order_item_id]['quantity']=$order_item->product_quantity;
		$products_array[$order_item->order_item_id]['item_amount']=$order_item->item_amount;

		$items_count = $pw_rpt_main_class->pw_get_oi_count($order_item->order_id,'line_item');
		$items_count=isset($items_count[$order_item->order_id]) ? $items_count[$order_item->order_id] : "";

		$email=$order_item->billing_email;
		$customer_name=$order_item->billing_name;

		//GROSS AMOUNT
		$order_refund_amnt= $pw_rpt_main_class->pw_get_part_order_refund_amount($order_item -> order_id);

		$order_total_amount			= isset($order_item->order_total)		? $order_item->order_total 		: 0;
		$part_order_refund_amount 	= isset($order_refund_amnt[$order_item->order_id]['refund_amount']) ? $order_refund_amnt[$order_item->order_id]['refund_amount']: 0;//Added 20150406

		$order_total				= $order_total_amount - $part_order_refund_amount;

		$order_tax					= isset($order_item->order_tax)		? $order_item->order_tax 		: 0;
		$part_order_refund_tax 		= isset($order_refund_amnt[$order_item->order_id]['order_tax']) ? $order_refund_amnt[$order_item->order_id]['order_tax'] : 0;//Added 20170504
		$order_tax					= $order_tax + $part_order_refund_tax;

		$order_shipping_tax			= isset($order_item->order_shipping_tax)		? $order_item->order_shipping_tax 		: 0;
		$order_shipping				= isset($order_item->order_shipping)			? $order_item->order_shipping 		: 0;
		$gross_amnt 			= $order_total - $order_tax - $order_shipping_tax - $order_shipping	;

		$date_format		= get_option( 'date_format' );
		$order_date=date($date_format,strtotime($order_item->order_date));

		$payment_method=isset($order_item->payment_method_title) ? $order_item->payment_method_title : "-" ;

		if($order_shipping){
			$order_shipping_v=($order_shipping) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($order_shipping);
			$order_shipping_html='<tr><td class="pw-left-align pw-black"> '.__("Shipping",__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </td><td class="pw-right-align pw-black"> '.$order_shipping_v.'</td></tr>';
		}
	}

	/////TAX/////
	$tax_name=$pw_rpt_main_class->pw_oin_list($order_id,'tax');
	$tax_name=isset($tax_name[$order_id]) ? $tax_name[$order_id] : "";
	$order = new WC_Order($order_id);
	$tax_total = $order->get_total_tax();

	$tax_name_rate_html='';
	// Iterating through WC_Order_Item_Tax objects
	foreach( $order->get_items( 'tax' ) as $item_id => $item_tax ){
		## -- Get all protected data in an accessible array -- ##

		$tax_data = $item_tax->get_data(); // Get the Tax data in an array

		$item_tax_rate_code = $tax_data['rate_code'];
		$item_tax_rate_id = $tax_data['rate_id'];
		$item_tax_label = $tax_data['label'];
		$item_tax_total = $tax_data['tax_total']; // Tax total amount
		$item_tax_shipping_total = $tax_data['shipping_tax_total']; // Tax shipping total
		$tax_name_rate_html.='<tr><td class="pw-left-align pw-black "> '.$item_tax_label.' </td><td class="pw-right-align pw-black"> '.$pw_rpt_main_class->price(($item_tax_total+$item_tax_shipping_total)).'</td></tr>';
	}


	//$net_amnt=($net_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($net_amnt);
	$gross_amnt+=$tax_total+$order_shipping;
	$gross_amnt=($net_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($net_amnt);
	$net_amnt=($net_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($net_amnt);

	$items = new WC_Order($order_id);
	$date_format		= get_option( 'date_format' );
	$order_date=date($date_format,strtotime($items->order_date));
	//$customer_name=$items->billing_name;

	$country      	= $pw_rpt_main_class->pw_get_woo_countries();
	$customer_email=$items->billing_email;

	$billing_country = isset($country->countries[$items->billing_country]) ? $country->countries[$items->billing_country]: $items->billing_country;
	$billing_state = $pw_rpt_main_class->pw_get_woo_bsn($items->billing_country,$items->billing_state);
	$billing_city = $items->billing_city;

	$billing_arr=array($billing_city,$billing_state,$billing_country);
	$billing_arr=array_filter($billing_arr);

	$billing_address_1 = $items->billing_address_1!=''? $items->billing_address_1."<br/>":"";
	$billing_address_2 = $items->billing_address_2!=''? $items->billing_address_2."<br/>":"";
	$billing_phone = get_post_meta($order_id, '_billing_phone', true);//
	$billing_fname = get_post_meta($order_id, '_billing_first_name', true);//
	$billing_lname = get_post_meta($order_id, '_billing_last_name', true);//
	$billing_info= $billing_address_1 .$billing_address_2.implode(" , ",$billing_arr);


	$shipping_country = isset($country->countries[$items->shipping_country]) ? $country->countries[$items->shipping_country]: $items->shipping_country;
	$shipping_state = $pw_rpt_main_class->pw_get_woo_bsn($items->shipping_country,$items->shipping_state);
	$shipping_city = $items->shipping_city;
	$shipping_arr=array($shipping_city,$shipping_state,$shipping_country);
	$shipping_arr=array_filter($shipping_arr);

	$shipping_address_1 = $items->shipping_address_1!='' ? $items->shipping_address_1."<br/>":"";
	$shipping_address_2 = $items->shipping_address_2!='' ? $items->shipping_address_2."<br />":"";
	$shipping_phone = get_post_meta($order_id, '_shipping_phone', true);//
	$shipping_fname = get_post_meta($order_id, '_shipping_first_name', true);//
	$shipping_lname = get_post_meta($order_id, '_shipping_last_name', true);//
	$shipping_info= $shipping_address_1 .$shipping_address_2.implode(" , ",$shipping_arr);

	//print_r($products_array);

	$product_html='';
	foreach($products_array as $product){
		$product_html.='<tr>
		                            <td class="pw-left-align pw-black">
		                                '.$product['quantity'].' x '.$product['name'].'
		                            </td>
		                            <td class="pw-right-align pw-black">
		                                '.$pw_rpt_main_class->price($product['item_amount']).'
		                            </td>
		                        </tr>';
	}

	$avatar=get_avatar_url($email);


	//////////////////////////////////////////////////////
	/// RELATED TRANSACTIONS
	//////////////////////////////////////////////////////
	$sql="SELECT postmeta.meta_value AS billing_email, billing_country.meta_value as billing_country, DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date, pw_woocommerce_order_items.order_id AS order_id,	pw_woocommerce_order_items.order_item_name AS product_name,	pw_woocommerce_order_items.order_item_id	AS order_item_id, woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value, (pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS sold_rate, (pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS product_rate, (pw_woocommerce_order_itemmeta4.meta_value) AS item_amount, (pw_woocommerce_order_itemmeta2.meta_value) AS item_net_amount, (pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) AS item_discount,	pw_woocommerce_order_itemmeta2.meta_value AS total_price, count(pw_woocommerce_order_items.order_item_id) AS product_quentity, woocommerce_order_itemmeta.meta_value AS product_id ,pw_woocommerce_order_itemmeta3.meta_value AS 'product_quantity'	,pw_posts.post_status AS post_status ,pw_posts.post_status AS order_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta2 ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta3 ON pw_woocommerce_order_itemmeta3.order_item_id	= pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta4 ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal' LEFT JOIN {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=pw_woocommerce_order_items.order_id Where pw_posts.post_type = 'shop_order' AND pw_posts.ID<>'$order_id' AND billing_country.meta_key	= '_billing_country' AND postmeta.meta_key='_billing_email' AND woocommerce_order_itemmeta.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta2.meta_key='_line_total' AND pw_woocommerce_order_itemmeta3.meta_key='_qty' AND postmeta.meta_value IN ('$email') AND order_id<>'$order_id' AND pw_posts.post_status IN ($pw_order_status) AND pw_posts.post_status NOT IN ('trash') GROUP BY order_id";
	//echo $sql;

	$related_order=$wpdb->get_results($sql);
	$related_order_html='';
	$i=1;
	$net_amnt_r=0;
	$related_array=array();
	foreach($related_order as $key => $related){
		$related_array[]=$related->order_id;
	}

	//print_r($order_items);
	$first_order_id='';
	$i=1;
	foreach($related_array as $related){
		$order = wc_get_order($related);
		$date_format        = get_option( 'date_format' );
		$order_date_r       = $order->order_date;
		$order_date_r=date($date_format,strtotime($order_date_r));

		$pw_table_value=($order->get_total()) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($order->get_total());
		$related_order_html .= '<div class="pw-related-item pw-sm-font">
                                    <span class="pw-related-date">' . $order_date_r . '</span>
                                    <span>' . $i ++ . 'st payment</span>
                                    <span>' . $pw_table_value . '</span>  </span>
                                </div>';

	}



	//////////////////////////////////////////////////////
	/// ORDERS STATUS AND CHANGE IT
	//////////////////////////////////////////////////////
	$pw_table_value_status = isset($current_order_status) ? $current_order_status : '';

	if($pw_table_value_status=='wc-completed')
		$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.ucwords(__($pw_table_value_status, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
	else if($pw_table_value_status=='wc-refunded')
		$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.ucwords(__($pw_table_value_status, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
	else
		$pw_table_value_status = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value_status).'" >'.ucwords(__($pw_table_value_status, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';




	$order_statuses = array(
		'wc-pending'    => __( 'Pending payment', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-processing' => __( 'Processing', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-on-hold'    => __( 'On hold', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-completed'  => __( 'Completed', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-cancelled'  => __( 'Cancelled', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-refunded'   => __( 'Refunded', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
		'wc-failed'     => __( 'Failed', 'Order status', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
	);
	$order_statuses_html='';
	foreach($order_statuses as $key=>$o_status){
		$order_statuses_html.='<option value="'.$key.'" '.selected($current_order_status,$key,0).'>'.$o_status.'</option>';
	}


	$current_order_status= str_replace("Wc-","",$pw_table_value_status);
	echo '
		<div class="int-awr-box pw-pr-cus-detail">
            <div class="awr-int-loading">
            	<div class="awr-loading-css"><div class="rect1"></div><div class="rect2"></div> <div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>
			</div>
            <div class="pw-cus-detail-header">
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="pw-avatar pw-pull-left">
                        <img src="'. $avatar.'">
                    </div>
                    <div class="pw-pull-left">
                        <div class="pw-cus-detail-name">
                        <span class="pw-black pw-lg-font ">'.$customer_name.'</span>
                    </div>
                        <div class="pw-cus-detail-email pw-sm-font">
                        <i class="fa fa-envelope pw-val"></i>
                        <a class="pw-val" href="mailto:'.$email.'">'.$email.'</a>
                    </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-3 pw-align-right">
                    <div class="pw-black pw-slg-font pw-all-pay">
                    '.$net_amnt.'
                    </div>
                    <div class="pw-black pw-sm-font">
                    '.$items_count.' '.__( 'item(s)', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ).'
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-3">
                    
                    <div data-order-id="" class="pw-xs-font pw-backed-lbl pw-green-back pull-right order-lbl ">'.$current_order_status.' | <span class="pw_intelligence_change_order_status"> '.__( 'Change', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ).'</span></div>
                    <div class="clear-fx"></div>
                    <select class="pull-right  pw-xs-font order-select pw_intelligence_order_status">
						'.$order_statuses_html.'						
					</select>
                    
                </div>
            </div>
            <div class="int-awr-box-content">
                <div class="pw-box-padder"> 
                    <div class="col-xs-12 col-md-8">
                        <table class="pw-simple-dashed-tbl pw-sm-font">
                                '.$product_html.'
                                '.$order_shipping_html.$tax_name_rate_html.'
                                <tr>
                                    <td class="pw-left-align pw-black pw-md-font">
                                        '.__('Total',__PW_REPORT_WCREPORT_TEXTDOMAIN__).': '.$items_count.' '.__('items',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                                    </td>
                                    <td class="pw-right-align pw-black pw-md-font">
                                        '.$gross_amnt.'
                                    </td>
                                </tr>
                            </table>
                        <div class="pw-note-cnt">
                            <div class="pw-sm-font">
                                '.__('NOTE',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' 
                                
                            </div>
                            <textarea cols="3" class="pw-sm-font pw_intelligence_note_text" placeholder="'.__('click here to add note for this order...',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'"></textarea>
                            <span class="pw-green pw_intelligence_note_resp pw-md-font"></span>
                            <button class="pw-button pw-pull-right pw_intelligence_note_text_save"  data-id="'.$order_id.'" data-target="order">'.__('Save',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</button>
                            <div class="clear-fx"></div>
                        </div>
                        <div class="pw-prelated-cnt">
                            <div class="pw-val pwl-lbl">'.__('RELATED TRANSACTIONS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                <div class="pw-prelated-content-cnt">
                                    '.$related_order_html.'
                                
                            </div>
                        </div>    
                    </div>
                    <div class="col-xs-12 col-md-4 pw-info-cnt">
                        
                        <div class="pw-info">
                            <div class="pw-val pwl-lbl">'.__('DATE AND TIME',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                            <div class="pw-black pw-sm-font">'.$order_date.'</div>
                        </div>
                        
                        <div class="pw-info">
                            <div class="pw-val pwl-lbl">'.__('PAYMENT METHOD',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                            <div class="pw-black pw-sm-font">'.$payment_method.'</div>
                        </div>
                        
                        <div class="pw-info">
                            <div class="pw-val pwl-lbl">'.__('BILLING ADDRESS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                            <div class="pw-black pw-sm-font">'.$billing_info.'</div>
                        </div>
                        
                        <div class="pw-info">
                            <div class="pw-val pwl-lbl">'.__('SHIPPING ADDRESS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                            <div class="pw-black pw-sm-font">'.$shipping_info.'</div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
	';
?>