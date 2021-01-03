<?php
    global $wpdb,$pw_rpt_main_class;

	$pw_order_status=$pw_rpt_main_class->pw_shop_status;
	$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."','wc-refunded'";

    $sql="SELECT pw_posts.post_status,postmeta.meta_value AS billing_email, billing_country.meta_value as billing_country, DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date, pw_woocommerce_order_items.order_id AS order_id,	pw_woocommerce_order_items.order_item_name AS product_name,	pw_woocommerce_order_items.order_item_id	AS order_item_id, woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value, (pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS sold_rate, (pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS product_rate, (pw_woocommerce_order_itemmeta4.meta_value) AS item_amount, (pw_woocommerce_order_itemmeta2.meta_value) AS item_net_amount, (pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) AS item_discount,	pw_woocommerce_order_itemmeta2.meta_value AS total_price, count(pw_woocommerce_order_items.order_item_id) AS product_quentity, woocommerce_order_itemmeta.meta_value AS product_id ,woocommerce_order_itemmeta_v.meta_value AS variation_id ,pw_woocommerce_order_itemmeta3.meta_value AS 'product_quantity'	,pw_posts.post_status AS post_status ,pw_posts.post_status AS order_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_v ON woocommerce_order_itemmeta_v.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta2 ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta3 ON pw_woocommerce_order_itemmeta3.order_item_id	= pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta4 ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal' LEFT JOIN {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=pw_woocommerce_order_items.order_id Where pw_posts.post_type = 'shop_order' AND pw_posts.ID<>'$order_id' AND billing_country.meta_key	= '_billing_country' AND postmeta.meta_key='_billing_email' AND woocommerce_order_itemmeta.meta_key = '_product_id' AND woocommerce_order_itemmeta_v.meta_key = '_variation_id' AND pw_woocommerce_order_itemmeta2.meta_key='_line_total' AND pw_woocommerce_order_itemmeta3.meta_key='_qty' AND postmeta.meta_value IN ('$customer_email') AND pw_posts.post_status IN ($pw_order_status) AND pw_posts.post_status NOT IN ('trash') GROUP BY order_item_id ORDER BY order_date DESC";
    $order_items=$wpdb->get_results($sql);
    //print_r($order_items);
    //echo $sql;

	$back_html=__("Back to product",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	if($page_id=='pw_rpt_fetch_single_customer_main'){
		$back_html=__("Back to all customers",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	}


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
    $activity_html='';
    $activity_array=array();

    foreach($order_items as $key => $order_item  ){

        //post_status

	    $p_id=$order_item->product_id;
	    if($order_item->variation_id)
		    $p_id=$order_item->variation_id;

	    if($order_item->post_status=='wc-refunded'){
		    $total_all_refund+=$order_item -> order_total;
		    continue;
	    }

        $date_format		= get_option( 'date_format' );
//echo $order_item -> order_id;
        $order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount($order_item -> order_id);
        //print_r($order_refund_amnt);
        $part_refund=(isset($order_refund_amnt[$order_item->order_id])? $order_refund_amnt[$order_item->order_id]:0);
	    $total_all_refund+=$part_refund;

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

	        $activity_array[$order_item->order_id][$p_id]['total']=$pw_table_value;
	        $activity_array[$order_item->order_id][$p_id]['date']=$order_item->order_date;
	        $activity_array[$order_item->order_id][$p_id]['name']=$order_item->product_name;
	        $activity_array[$order_item->order_id][$p_id]['quantity']=$order_item->product_quantity;
	        $activity_array[$order_item->order_id][$p_id]['item_amount']=$order_item->item_amount;

        }
	    $activity_array[$order_item->order_id][$p_id]['total']=$pw_table_value;
	    $activity_array[$order_item->order_id][$p_id]['date']=$order_item->order_date;
	    $activity_array[$order_item->order_id][$p_id]['name']=$order_item->product_name;
	    $activity_array[$order_item->order_id][$p_id]['quantity']=$order_item->product_quantity;
	    $activity_array[$order_item->order_id][$p_id]['item_amount']=$order_item->item_amount;

        $products_array[$p_id]['name']=$order_item->product_name;

        if(isset($products_array[$p_id]['quantity']))
            $products_array[$p_id]['quantity']+=$order_item->product_quantity;
        else
	        $products_array[$p_id]['quantity']=$order_item->product_quantity;

	    if(isset($products_array[$p_id]['item_amount']))
            $products_array[$p_id]['item_amount']+=$order_item->item_amount;
	    else
            $products_array[$p_id]['item_amount']=$order_item->item_amount;


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

    }

    $net_amnt=($net_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($net_amnt);
    $gross_amnt=($gross_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($gross_amnt);

    $items = new WC_Order($order_id);
    $date_format		= get_option( 'date_format' );
    $order_date=date($date_format,strtotime($items->order_date));
    //$customer_name=$items->billing_name;

    $country      	= $pw_rpt_main_class->pw_get_woo_countries();
    $customer_email=$items->billing_email;

    $billing_country = isset($country->countries[$items->billing_country]) ? $country->countries[$items->billing_country]: $items->billing_country;
    $billing_state = $pw_rpt_main_class->pw_get_woo_bsn($items->billing_country,$items->billing_state);
    $billing_city = $items->billing_city;
    $billing_address_1 = $items->billing_address_1;
    $billing_address_2 = $items->billing_address_2;
    $billing_phone = get_post_meta($order_id, '_billing_phone', true);//
    $billing_fname = get_post_meta($order_id, '_billing_first_name', true);//
    $billing_lname = get_post_meta($order_id, '_billing_last_name', true);//
    $billing_info= $billing_address_1 ."<br/>".$billing_address_2."<br />".$billing_city.', '.$billing_state.', '.$billing_country;

    //print_r($products_array);



    foreach ($activity_array as $order_ids => $activity){
	    $activity_products_name='';
	    $activity_products_item_amount='';
	    $activity_products_date='';
	    $activity_products_total='';
	    $today_date=date("Y-m-d");
        foreach($activity_array[$order_ids] as $prod_id => $act_products){

	        $from=date_create($today_date);
	        $to=date_create($act_products['date']);
	        $diff=date_diff($to,$from);
	        $diff = $diff->format('%a')+1;

	        $datetime1 = new DateTime($today_date);
	        $datetime2 = new DateTime($act_products['date']);
	        $diff = $datetime1->diff($datetime2);

	        $year_text[0]=($diff->y>1 ? $diff->y. " ".__('years',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->y==1 ? $diff->y. " ".__('year',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));
	        $year_text[1]=($diff->m>1 ? $diff->m. " ".__('months',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->m==1 ? $diff->m. " ".__('month',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));
	        $year_text[2]=($diff->d>1 ? $diff->d. " ".__('days',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->d==1 ? $diff->d. " ".__('day',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));


	        $diff_days= implode(array_filter($year_text),', ')." ".__('ago',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	        if(implode(array_filter($year_text),', ')=='')
	        	$diff_days=__("Today",__PW_REPORT_WCREPORT_TEXTDOMAIN__);

	        $activity_products_name.='<div class="pw-xs-font">'.$act_products['quantity'].' x '.$act_products['name'].'</div>';
	        $activity_products_item_amount.='<div class="pw-xs-font">'.$pw_rpt_main_class->price($act_products['item_amount']).'</div>';
	        $activity_products_date='<span class="pw-xs-font pw-val">'.$act_products['date'].' - '.$diff_days.'</span>';
	        $activity_products_total='<div class="pw-sm-font">'.$pw_rpt_main_class->price($act_products['item_amount']).'</div>';
        }
	    $activity_html.='
            <tr>
                <td class="pw-left-align ">
                    <div>
                        <span class="pw-sm-font ">
                            '.__('Purchase',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' <i class="fa fa-check pw-green"></i>
                        </span>
                        '.$activity_products_date.'
                    </div>
                    '.$activity_products_name.'
                </td>
                <td class="pw-right-align pw-black">
                    '.$activity_products_total.'
                    '.$activity_products_item_amount.'
                </td>
            
            </tr>';
    }

    $product_html=$items_count_prd=$gross_amnt_prd='';
    foreach($products_array as $key => $product){
	    $_product = wc_get_product( $key );

	    $img=wp_get_attachment_image( $_product->get_image_id(), 'thumbnail' );
	    $img_url=wp_get_attachment_image_url( $_product->get_image_id(), 'thumbnail' );
	    $items_count_prd+=$product['quantity'];
	    $gross_amnt_prd+=$product['item_amount'];

        $product_html.='
        <tr>
            <td class="pw-left-align pw-mini-img">
                <img src="'.$img_url.'" >
            </td>
            <td class="pw-left-align pw-black">
                '.$product['name'].'
            </td>
            <td class="pw-center-align pw-black">
                '.$product['quantity'].'
            </td>
            <td class="pw-right-align pw-md-font pw-green">
                '.$pw_rpt_main_class->price($product['item_amount']).'
            </td>
        </tr>';
    }
    $product_html.='
    <tr>
        <td class="pw-left-align pw-black pw-md-font">
            '.__('Total',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' : 
        </td>
        <td class="pw-left-align pw-black"></td>
        <td class="pw-center-align pw-black">'.$items_count_prd.'</td>
        <td class="pw-right-align pw-black pw-md-font">
            '.$pw_rpt_main_class->price($gross_amnt_prd).'
        </td>
    </tr>';

    $part_refund_amnt=$pw_rpt_main_class->price($part_refund_amnt);
    $part_refund_amnt=$pw_rpt_main_class->price($total_all_refund);


    $avatar=get_avatar_url($customer_email);


	//////////////////////////////////////////////////////
	// GOOGLE MAP OF USER ADDRESS
	//////////////////////////////////////////////////////
	$order_id = $wpdb->get_var( $wpdb->prepare( "
		        SELECT post_id 
		        FROM $wpdb->postmeta 
		        WHERE meta_key = '_billing_email'
		        AND meta_value = '%s'
		    ", $customer_email ) );

	if ( ! $order_id ) {
		$customer_phone = get_user_meta( $customer_id, 'billing_phone', true );
		$customer_coutry = get_user_meta( $customer_id, 'billing_country', true );
		$customer_state = get_user_meta( $customer_id, 'billing_state', true );
		$customer_state=$pw_rpt_main_class->pw_get_woo_bsn($customer_coutry,$customer_state);
		$customer_city = get_user_meta( $customer_id, 'billing_city', true );
		$country      	= $pw_rpt_main_class->pw_get_woo_countries();
		$customer_coutry = isset($country->countries[$customer_coutry]) ? $country->countries[$customer_coutry]: $customer_coutry;
		$google_map_address=$customer_coutry.($customer_state!=''?',+'.$customer_state:"").($customer_city!=''?',+'.$customer_city:"");
	}else{
		$customer_phone = get_post_meta( $order_id, '_billing_phone', true );
		$customer_coutry = get_post_meta( $order_id, '_billing_country', true );
		$customer_state = get_post_meta( $order_id, '_billing_state', true );
		$customer_state=$pw_rpt_main_class->pw_get_woo_bsn($customer_coutry,$customer_state);
		$customer_city = get_post_meta( $order_id, '_billing_city', true );
		$country      	= $pw_rpt_main_class->pw_get_woo_countries();
		$customer_coutry = isset($country->countries[$customer_coutry]) ? $country->countries[$customer_coutry]: $customer_coutry;
		$google_map_address=$customer_coutry.($customer_state!=''?',+'.$customer_state:"").($customer_city!=''?',+'.$customer_city:"");
	}

	//////////////////////////////////////////////////////
	// USER REGISTER DATE
	//////////////////////////////////////////////////////
	$udata = get_userdata( $customer_id );
	$registered = $udata->user_registered;
	$datetime1 = new DateTime($today_date);
	$datetime2 = new DateTime($registered);
	$diff = $datetime1->diff($datetime2);

	$year_text[0]=($diff->y>1 ? $diff->y. " ".__('years',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->y==1 ? $diff->y. " ".__('year',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));
	$year_text[1]=($diff->m>1 ? $diff->m. " ".__('months',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->m==1 ? $diff->m. " ".__('month',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));
	$year_text[2]=($diff->d>1 ? $diff->d. " ".__('days',__PW_REPORT_WCREPORT_TEXTDOMAIN__):($diff->d==1 ? $diff->d. " ".__('day',__PW_REPORT_WCREPORT_TEXTDOMAIN__):""));

	$registered= __('Joined',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' '.implode(array_filter($year_text),', ')." ".__('ago',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	if(implode(array_filter($year_text),', ')=='')
		$registered= __('Joined',__PW_REPORT_WCREPORT_TEXTDOMAIN__)." ".__('Today',__PW_REPORT_WCREPORT_TEXTDOMAIN__);



	//////////////////////////////////////////////////////
	// CUSTOMER CONTACT INFO - LEFT BOX
	//////////////////////////////////////////////////////
	$customer_segment_array=array(
		"champions" => __("CHAMPIONS",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"loyal" => __("LOYAL CUSTOMERS",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"potential" => __("POTENTIAL LOYALIST",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"new_customer" => __("NEW CUSTOMERS",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"promising" => __("PROMISING",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"attention" => __("NEED ATTENTION",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"sleep" => __("ABOUT TO SLEEP",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"at_risk" => __("AT RISK",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"no_lose" => __("CAN'T LOSE THEM",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"hibernating" => __("HIBERNATING",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"lose" => __("LOST",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		"others" => __("OTHERS",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
	);
	$customer_class=" pw-customer-label-$customer_segment ";
	$customer_class_bg=" pw-customer-$customer_segment ";

    echo '
	
    <div class="pw-cols col-xs-12 col-md-12">
    	<span class="pw_rpt_fetch_single_customer_back">'.$back_html.' |  </span>
    	<span class="pw_rpt_fetch_single_customer_prev"  title="Previous customer">
			<i class="fa fa-angle-left fa-2x"></i>
		</span>
		<span class="pw_rpt_fetch_single_customer_next" title="Next customer">
			<i class="fa fa-angle-right fa-2x"></i>
		</span>
    </div>

    <div class="pw-cols col-xs-12 col-md-3">
        <div class="int-awr-box">
            <div class="int-awr-box-content pw-customer-info-cnt">
                <div class="pw-box-padder">
                    <span class="pw-customer-label pw-val pw-sm-font pw-customer-singel-cards-cnt '.$customer_class.'" >'.$customer_segment_array[$customer_segment].'</span>
                    <div class="pw-color-arc-single '.$customer_class_bg.'"></div>
                    <div class="pw-customer-card-thumb pw-center-align">
                        <img class="pw-customer-thumb-single" src="'. $avatar.'">
                    </div>
                    <div class="pw-customer-detail pw-center-align">
                        <div class="pw-md-font">'.$customer_name.'</div>
                        <div class="pw-val pw-sm-font pw-customer-regdate">'.$registered.'</div>
                        <div class="pw-xs-font pw-social-cnt">
                            '.($customer_phone!=''?'<a class="pw-val" href="mailto:'.$email.'"><i class="fa fa-envelope"></i></a>':'').'
                            '.($customer_phone!=''?'<a class="pw-val" href="javascript:void(0)" title="'.$customer_phone.'"><i class="fa fa-phone"></i></a>':'').'
                            <!--<a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-google"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-youtube"></i></a>-->
                        </div>

                        <div class="pw-xs-font pwl-lbl pw-left-align">'.$customer_city.' '.$customer_state.' '.$customer_coutry.'</div>
                        <div class="pw-customer-map-img">
                            <img width="600" src="https://maps.googleapis.com/maps/api/staticmap?center='.$google_map_address.'&zoom=9&scale=1&size=200x100&maptype=roadmap&format=png&visual_refresh=true" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pw-cols col-xs-12 col-md-6">
        <div class="int-awr-box int-fixed-height-box-pr">
            <div class="awr-title">
                <h3>
                    <i class="fa fa-money"></i>'.__('Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                </h3>

            </div>

            <div class="int-awr-box-content">
                <div class="pw-box-padder">
                    <table class="pw-simple-dashed-tbl pw-sm-font">
                        <tbody>
                        '.$product_html.'
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="pw-cols col-xs-12 col-md-3">
        <div class="row">
            <div class="col-xs-12">
                <div class="int-awr-box pw-center-align pw-pr-sum-box">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="pw-box-padder">
                            <div class="pw-lg-font pw-green">'.$net_amnt.'</div>
                            <div class="pw-val pwl-lbl">'.__('REVENUW',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="pw-box-padder">
                            <div class="pw-lg-font pw-red">'.$part_refund_amnt.'</div>
                            <div class="pw-val pwl-lbl">'.__('REFUNDS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="pw-box-padder">
                            <div class="pw-lg-font pw-green">'.$order_count.'</div>
                            <div class="pw-val pwl-lbl">'.__('ORDERS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="pw-box-padder">
                            <div class="pw-lg-font pw-green">'.$items_count_prd.'</div>
                            <div class="pw-val pwl-lbl">'.__('ITEMS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                        </div>
                    </div>
                </div>

                <div class="int-awr-box int-fixed-height-box">
                    <div class="awr-title">
                        <h3>
                            <i class="fa fa-money"></i>'.__('Activity',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                        </h3>

                    </div>

                    <div class="int-awr-box-content">
                        <div class="pw-box-padder">
                            <table class="pw-simple-dashed-tbl pw-sm-font">
                                <tbody>
                                '.$activity_html.'
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>';
?>