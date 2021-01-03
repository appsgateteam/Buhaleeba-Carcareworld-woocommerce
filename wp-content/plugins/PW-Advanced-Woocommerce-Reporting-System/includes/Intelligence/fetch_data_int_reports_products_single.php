<?php
    global $wpdb,$pw_rpt_main_class;
    $_product = wc_get_product( $product_id );
    $product_price= $_product->get_regular_price();
    $img=wp_get_attachment_image( $_product->get_image_id(), 'medium' );
    $img_url=wp_get_attachment_image_url( $_product->get_image_id(), 'medium' );
    if($img_url==''){
	    $img_url=__PW_REPORT_WCREPORT_URL__ .'/assets/images/no_image.jpg';
    }
    $p_title=get_the_title($product_id);
    $sku=($_product->get_sku()!='' ?$_product->get_sku():"Not Set");

	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);

	$days = $diff->format('%a')+1;


    $pw_order_status=$pw_rpt_main_class->pw_shop_status;
    $pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

    //////////////////////////////////////////////////////
	//FETCH FREQUENTLY BOUGHT TOGETHER
    //////////////////////////////////////////////////////
	$sql_order_products="SELECT DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date,DATE(pw_posts.post_date) AS post_date, pw_woocommerce_order_items.order_id AS order_id, woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value,  woocommerce_order_itemmeta.meta_value AS product_id,  woocommerce_order_itemmeta_v.meta_value AS variation_id	,pw_posts.post_status AS post_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	= pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_v ON woocommerce_order_itemmeta_v.order_item_id	= pw_woocommerce_order_items.order_item_id   Where pw_posts.post_type = 'shop_order' AND woocommerce_order_itemmeta.meta_key = '_product_id' AND woocommerce_order_itemmeta_v.meta_key = '_variation_id' AND DATE(pw_posts.post_date) BETWEEN '$pw_from_date' AND '$pw_to_date' AND pw_posts.post_status IN ($pw_order_status) AND pw_posts.post_status NOT IN ('trash') ORDER BY post_date DESC";


	//echo $sql_order_products;

	$order_itemss=$wpdb->get_results($sql_order_products);
    $order_products=$order_items=array();
    $order_meta = array();
    if(count($order_itemss)>0)
        foreach($order_itemss as $order_item){
            //ITEMS OF ORDERS
	        $p_id=$order_item->product_id;
	        if($order_item->variation_id)
		        $p_id=$order_item->variation_id;
            $order_products[$order_item->order_id][]=$p_id;
        }


    //////////////////////////////////////////////////////
    //FETCH ALL CUSTOMERS FOR DETECT THE RFM OF THEM
    //////////////////////////////////////////////////////
    $sql_int_customer_products="SELECT pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,SUM(woocommerce_order_itemmeta.meta_value)	AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'total_amount' ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,pw_woocommerce_order_itemmeta_v.meta_value	AS product_id ,pw_postmeta_customer_user.meta_value	AS customer_id ,DATE(pw_posts.post_date) AS post_date ,pw_postmeta_billing_billing_email.meta_value	AS billing_email ,CONCAT(pw_postmeta_billing_billing_email.meta_value,' ',pw_woocommerce_order_itemmeta7.meta_value,' ',pw_postmeta_customer_user.meta_value)	AS group_column ,CONCAT(pw_postmeta_billing_first_name.meta_value,' ',postmeta_billing_last_name.meta_value)	AS billing_name,pw_postmeta_billing_country.meta_value	AS billing_country	FROM wp_woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN wp_woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_v ON pw_woocommerce_order_itemmeta_v.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN wp_posts as pw_posts ON pw_posts.id=pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_billing_first_name ON pw_postmeta_billing_first_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as postmeta_billing_last_name ON postmeta_billing_last_name.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_billing_billing_email ON pw_postmeta_billing_billing_email.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_billing_country ON pw_postmeta_billing_country.post_id	= pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_customer_user ON pw_postmeta_customer_user.post_id	= pw_woocommerce_order_items.order_id WHERE woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta_v.meta_key = '_product_id' AND pw_postmeta_billing_first_name.meta_key	= '_billing_first_name' AND postmeta_billing_last_name.meta_key	= '_billing_last_name' AND pw_postmeta_billing_billing_email.meta_key	= '_billing_email' AND pw_postmeta_billing_country.meta_key	= '_billing_country' AND pw_postmeta_customer_user.meta_key	= '_customer_user' AND DATE(pw_posts.post_date) BETWEEN '$pw_from_date' AND '$pw_to_date' AND pw_posts.post_status IN ($pw_order_status) AND pw_posts.post_status NOT IN ('trash') GROUP BY group_column ORDER BY post_date DESC
";
    $result_customer_products=$wpdb->get_results($sql_int_customer_products);
    $customer_products=array();

    if(count($result_customer_products)>0){
        foreach($result_customer_products as $items){
	        //SET guest@guest.com for empty Email
	        if($items->billing_email=='') $items->billing_email='guest@guest.com';
            $customer_products[$items->billing_email][]=$items;
        }
    }


    //////////////////////////////////////////////////////
    /// CALC THE CUSTOMR RFM
    //////////////////////////////////////////////////////

	$sss="SELECT pw_posts.ID as order_id,pw_posts.post_status, (pw_postmeta1.meta_value) AS 'total_amount' ,pw_postmeta2.meta_value AS 'billing_email' ,(pw_postmeta2.meta_value) AS 'order_count' ,pw_postmeta4.meta_value AS customer_id FROM wp_posts as pw_posts LEFT JOIN wp_postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN wp_postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID LEFT JOIN wp_postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_posts.ID WHERE pw_posts.post_type='shop_order' AND pw_postmeta1.meta_key='_order_total' AND pw_postmeta2.meta_key='_billing_email' AND pw_postmeta4.meta_key='_customer_user' AND DATE(pw_posts.post_date) BETWEEN '$pw_from_date' AND '$pw_to_date' AND pw_posts.post_status IN ($pw_order_status) AND pw_posts.post_status NOT IN ('trash') /*GROUP BY pw_postmeta2.meta_value*/ Order By total_amount DESC
";

    $order_items_ss=$wpdb->get_results($sss);
    $customer_rfm_chart=array();
    $today_date=date("Y-m-d");
    if(count($order_items_ss)>0)
	$i=0;
    foreach($order_items_ss as $items){

	    //SET guest@guest.com for empty Email
	    if($items->billing_email=='') $items->billing_email='guest@guest.com';

        $order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount($items -> order_id);
        $part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);
        $total_all_refund+=$part_refund;
        $items_total_amunt= isset($items -> total_amount) ? ($items -> total_amount)-$part_refund : 0;


        $total_amount= $items_total_amunt == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($items_total_amunt);

        $avatar=get_avatar_url($items->billing_email);
        $customer_items=$customer_products[$items->billing_email];
        $customer_name=$location='';
        $customer_items_html='';
        $item_no=0;

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

    }



    //print_r($customer_rfm_chart);

    //GET RFM OF EACH CUSTOMER
    //RFM _ ANALYSE
    $r_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_recency_point');
    $f_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_frequency_point');
    $m_points=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_monetary_point');

    foreach($customer_rfm_chart as $key => $customer){

        $total_customer_amnt+=$customer['monetary'];
        $total_customer_frequency+=$customer['frequency'];
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
        //print_r($alternate_segment);
        if($r_flag && $f_flag && $m_flag && $c_id){
            $customer_segment[$key]['items'][]=$c_id;
            $customer_rfm_chart[$c_id]['segment']=$key;
        }else if($c_id){
            foreach($alternate_segment as $alt_key => $alt_seg){
                if($alt_seg=='110' || $alt_seg=='101' || $alt_seg=='110'){
                    $customer_segment[$alternate_segment['key']]['items'][]=$c_id;
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
    /// END RFM CALC
    //////////////////////////////////////////////////////



    //////////////////////////////////////////////////////
    /// FREQUENCY TOGETHER
    //////////////////////////////////////////////////////
	$arr_p=array();
	$show_frequency_together=false;
	foreach($order_products as $childs){
		if(is_array($childs) && in_array($product_id ,$childs)){
			foreach($childs as $child){
				if($child!=$product_id){
					if(isset($arr_p[$child]))
						$arr_p[$child]+=1;
					else
						$arr_p[$child]=1;
				}
			}
		}
	}
	arsort ($arr_p);
	$with_product_html='';
	$i=0;
	foreach($arr_p as $p_id => $with_product){
		//DISPLAY 5 ITEMS
		if($i++>4)
			break;

		$_products = wc_get_product( $p_id );

		$imgs=wp_get_attachment_image( $_products->get_image_id(), 'thumbnail' );
		$img_urls=wp_get_attachment_image_url( $_products->get_image_id(), 'thumbnail' );
		if($img_urls==''){
			$img_urls=__PW_REPORT_WCREPORT_URL__ .'/assets/images/no_image.jpg';
		}
		$p_titles=get_the_title($p_id);
		//$with_product_html.=$with_product.' Time(s) with '.$p_title;
		$with_product_html.='
			<div class="col-xs-2 ">
				<div class="pw-frequency-together-product-imgs">
              		<img src="'.$img_urls.'">
              		<div class="prod-kpis">'.$with_product.' Time(s) with <span class="bg ng-binding">'.$p_titles.'</span></div>
              	</div>
				
			</div>';

	}
	if($with_product_html!=''){
		$with_product_html='<div class="pw-val pw-sm-font pw-frequency-together">'.__('FREQUENTLY BOUGHT TOGETHER',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' 

<div class="awr-title-icon awr-add-fav-icon awr-tooltip-wrapper" data-smenu="all_orders">
                                <i class="fa fa-info-circle"></i>
                                <div class="awr-tooltip-cnt">
                                    <div class="awr-tooltip-header">'.__('Frequently Bought Together',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                    <div class="awr-tooltip-content">'.__('Customers who purchased \'Web Entrepreneurs\' Club - Annual\' also bought these products.',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                                </div>
                            </div>

</div><div class="pw-frequency-together-items">'.$with_product_html.'</div>';
	}




	//echo $with_product_html;


    //////////////////////////////////////////////////////
    /// CACLULATE : CURRENCT WEEK-MONTH, 3 WEEKS-MONTHS, ...
    //////////////////////////////////////////////////////
	$today = date("l");
	$week_to_date = date("Y-m-d", strtotime("saturday -1 weeks"));

	$current_month_week_from=date("Y-m", strtotime("-0 Months"))."-01";
	$current_month_week_to=date("Y-m", strtotime("-0 Months"))."-31";

	$month_week_3_from=date("Y-m", strtotime("-3 Months"))."-01";
	$month_week_3_to=date("Y-m", strtotime("-1 Months"))."-31";

	$month_week_6_from=date("Y-m", strtotime("-6 Months"))."-01";
	$month_week_6_to=date("Y-m", strtotime("-1 Months"))."-31";

	$month_week_12_from=date("Y-m", strtotime("-12 Months"))."-01";
	$month_week_12_to=date("Y-m", strtotime("-1 Months"))."-31";

	$month_week_title=__('MONTH',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	$month_week_titles=__('MONTHS',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	if($days<=7) {
		$month_week_title=__('WEEK',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$month_week_titles=__('WEEKS',__PW_REPORT_WCREPORT_TEXTDOMAIN__);

		if ( $today == "Sunday" ) {
			$current_month_week_from = date( "Y-m-d", strtotime( "sunday" ) );
			$current_month_week_to   = date( "Y-m-d", strtotime( "saturday" ) );

			$month_week_3_from = date( "Y-m-d", strtotime( "sunday - 3 weeks" ) );
			$month_week_3_to   = $week_to_date;

			$month_week_6_from = date( "Y-m-d", strtotime( "sunday - 6 weeks" ) );
			$month_week_6_to   = $week_to_date;

			$month_week_12_from = date( "Y-m-d", strtotime( "sunday - 12 weeks" ) );
			$month_week_12_to   = $week_to_date;

		} else {
			$current_month_week_from = date( "Y-m-d", strtotime( "last sunday - 0 weeks" ) );
			$current_month_week_to   = date( "Y-m-d", strtotime( "saturday" ) );

			$month_week_3_from = date( "Y-m-d", strtotime( "last sunday - 3 weeks" ) );
			$month_week_3_to   = $week_to_date;

			$month_week_6_from = date( "Y-m-d", strtotime( "last sunday - 6 weeks" ) );
			$month_week_6_to   = $week_to_date;

			$month_week_12_from = date( "Y-m-d", strtotime( "last sunday - 12 weeks" ) );
			$month_week_12_to   = $week_to_date;
		}
	}


	$months_weeks_array_range=array(
		'0' => array($current_month_week_from,$current_month_week_to),
		'3' => array($month_week_3_from,$month_week_3_to),
		'6' => array($month_week_6_from,$month_week_6_to),
		'12' => array($month_week_12_from,$month_week_12_to),
	);

	$months_weeks_array_value=array();
	foreach($months_weeks_array_range as $index => $month){
		$sql="SELECT shop_order.ID as order_id,shop_order.post_status, pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,DATE(shop_order.post_date)	AS post_date ,(woocommerce_order_itemmeta.meta_value) AS 'quantity' ,(pw_woocommerce_order_itemmeta6.meta_value) AS 'total_amount' FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id WHERE 1*1 AND woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total'	AND (pw_woocommerce_order_itemmeta7.meta_key = '_product_id' or pw_woocommerce_order_itemmeta7.meta_key = '_variation_id') AND pw_woocommerce_order_itemmeta7.meta_value='$product_id' AND shop_order.post_type	= 'shop_order' AND (DATE(shop_order.post_date) BETWEEN '$month[0]' AND '$month[1]') AND shop_order.post_status NOT IN ('trash') ORDER BY total_amount DESC";

		//echo $sql;

		$order_items=$wpdb->get_results($sql);
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

		$months_weeks_array_value[$index]=0;
		foreach($order_items as $key => $order_item  ) {

			if($order_item->post_status=='wc-refunded'){
				continue;
			}
			$order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount_individual($order_item -> order_id,$order_item -> order_item_id,'order');
			$part_refund=(isset($order_refund_amnt[$order_item->order_id])? $order_refund_amnt[$order_item->order_id]:0);
			$part_refund=abs($part_refund);
			// BASED PUTLER
			// product price in grid = product price in single - refunds;
			// refund =0 for calc item amount
			$part_refund=0;
			//echo $part_refund.'@';
			$items_total_amunt= isset($order_item -> total_amount) ? ($order_item -> total_amount)-$part_refund : 0;

			$months_weeks_array_value[$index]+=$items_total_amunt;
		}
	}
	$current_month_week_value=($months_weeks_array_value[0]) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($months_weeks_array_value[0]);
	$month_week_3_value= ($months_weeks_array_value[3]) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($months_weeks_array_value[3]);
	$month_week_6_value=($months_weeks_array_value[6]) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($months_weeks_array_value[6]);
	$month_week_12_value=($months_weeks_array_value[12]) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($months_weeks_array_value[12]);
    //////////////////////////////////////////////////////
    /// END CALCULATE CURRENT WEEK-MONTH ...
    //////////////////////////////////////////////////////

	//print_r($months_weeks_array_value);




    //////////////////////////////////////////////////////
    /// SALE HISTORY LIST - BOTTOM OF PAGE   & SET CUSTOMER ARRAY FOR CUSTOMERS HISTORY
    //////////////////////////////////////////////////////
	$sql="SELECT shop_order.ID as order_id,shop_order.post_status, pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,DATE(shop_order.post_date)	AS post_date ,(woocommerce_order_itemmeta.meta_value) AS 'quantity' ,(pw_woocommerce_order_itemmeta6.meta_value) AS 'total_amount' FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id WHERE 1*1 AND woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total'	AND (pw_woocommerce_order_itemmeta7.meta_key = '_product_id' or pw_woocommerce_order_itemmeta7.meta_key = '_variation_id')  AND pw_woocommerce_order_itemmeta7.meta_value='$product_id' AND shop_order.post_type	= 'shop_order' AND (DATE(shop_order.post_date) BETWEEN '$pw_from_date' AND '$pw_to_date') AND shop_order.post_status NOT IN ('trash') group by order_id ORDER BY total_amount DESC";

	//echo $sql;

	$from=date_create($pw_from_date);
	$to=date_create($pw_to_date);
	$diff=date_diff($to,$from);

	$days = $diff->format('%a')+1;

    $order_items=$wpdb->get_results($sql);
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
		    $order_items[$key]->billing_country		= isset($order_item->billing_country)	? $order_item->billing_country 	: '';
		    $order_items[$key]->customer_user		= $order_items[$key]->customer_user;

		    $current_order_status=$order_item->order_status;

	    }

	//print_r($order_items);

    $this_product_amnt=0;
	$this_refund_amnt=$this_refund_count=0;
	$product_chart_array=array();

	$sale_history_html='';
	$customer_array=array();
	$first_order_id='';
	$date_format		= get_option( 'date_format' );
	$i=0;
    foreach($order_items as $key => $order_item  ) {


	    $order_date=date($date_format,strtotime($order_item -> post_date));
	    if($order_item->post_status=='wc-refunded'){
		    $this_refund_count+=$order_item->quantity;
		    $this_refund_amnt+=$order_item -> total_amount;

		    //$sale_history_html.='<tr><td>'.$order_date.'</td><td>0•'.$order_item->quantity.'</td><td>'.$order_item -> total_amount.'</td></tr>';

		    $sale_history_html.='
                <tr>
                    <td class="pw-left-align pw-black">
                        '.$order_date.'
                    </td>
                    <td class="pw-center-align">
                        0•'.$order_item->quantity.'
                    </td>
                    <td class="pw-right-align pw-md-font pw-green">
                        '.$order_item -> total_amount.'
                    </td>
                </tr>';

		    continue;
	    }

	    $order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount_individual($order_item -> order_id,$order_item -> order_item_id,'order');
	    $part_refund=(isset($order_refund_amnt[$order_item->order_id])? $order_refund_amnt[$order_item->order_id]:0);
	    //echo $part_refund.'##';
	    $part_refund=abs($part_refund);
	    if($part_refund!='')
		    $this_refund_count+=1;
	    $this_refund_amnt+=$part_refund;


	    //echo $part_refund.'@';

        // BASED PUTLER
        // product price in grid = product price in single - refunds;
        // refund =0 for calc item amount
        $part_refund=0;

	    $items_total_amunt= isset($order_item -> total_amount) ? ($order_item -> total_amount)-$part_refund : 0;

	    //SALES HISTORY
	    $sale_history_total=($items_total_amunt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($items_total_amunt);
	    //$sale_history_html.='<tr><td>'.$order_date.'</td><td>'.$order_item->quantity.'•0</td><td>'.$sale_history_total.'</td></tr>';
	    $sale_history_html.='
            <tr>
                <td class="pw-lefy-align pw-black">
                    '.$order_date.'
                </td>
                <td class="pw-center-align">
                    '.$order_item->quantity.'•0
                </td>
                <td class="pw-right-align pw-md-font pw-green">
                    '.$sale_history_total.'
                </td>
            </tr>';

	    $this_product_amnt+=$items_total_amunt;
	    $this_product_quantity+=$order_item->quantity;
//	    if ( $order_item->post_status == 'wc-refunded' ) {
//		    $total_all_refund += $order_item->order_total;
//		    continue;
//	    }

	    //SET guest@guest.com for empty Email
	    if($items->billing_email=='') $items->billing_email='guest@guest.com';
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
		    $customer_array[$order_item->billing_email]['id']=$order_item->customer_user;

		    if(!isset($customer_array[$order_item->billing_email]['date']))
			    $customer_array[$order_item->billing_email]['date']=date($date_format,strtotime($order_item->post_date));
		    elseif(isset($customer_array[$order_item->billing_email]['date']) && ($customer_array[$order_item->billing_email]['date']<date($date_format,strtotime($order_item->post_date))))
		        $customer_array[$order_item->billing_email]['date']=date($date_format,strtotime($order_item->post_date));

		    $customer_array[$order_item->billing_email]['name']=$order_item->billing_name;
		    $customer_array[$order_item->billing_email]['location']=$order_item->billing_country;
		    if(isset($customer_array[$order_item->billing_email]['total'])){
			    $customer_array[$order_item->billing_email]['total']+=$items_total_amunt;
		    }else{
			    $customer_array[$order_item->billing_email]['total']=$items_total_amunt;
		    }
	    }

	    //CHART VALUES
	    $date=trim($order_item -> post_date);

	    $value=  (is_numeric($order_item -> total_amount) ?  number_format($order_item -> total_amount,2):0);
	    $value=str_replace($currency_thousand,"",$order_item -> total_amount);

	    $product_chart_array[$i]['income']= $pw_rpt_main_class->price_value($value);
	    $product_chart_array[$i]['expenses']= $order_item->quantity;
	    $product_chart_array[$i]['year']= $date;

	    $i++;
    }
    //////////////////////////////////////////////////////
    /// SALE HOSTORY
    //////////////////////////////////////////////////////

    //print_r($customer_array);



    //////////////////////////////////////////////////////
    /// CUSTOMER HOSTORY
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

    //print_r($customer_rfm_chart);
	//PEOPLE WHO BOUGHT THIS
    $today_date=date("Y-m-d");
	$customer_history_html='';
	$country      	= $pw_rpt_main_class->pw_get_woo_countries();
	foreach($customer_array as $email => $customer){
		$location = isset($country->countries[$customer['location']]) ? $country->countries[$customer['location']]: $customer['location'];

		$customer_segment=$customer_rfm_chart[$email]['segment'];
		$customer_class=" pw-customer-$customer_segment ";
		$customer_id=$customer['name'];

		$from=date_create($today_date);
		$to=date_create($customer['date']);
		$diff=date_diff($to,$from);

		$days = $diff->format('%a')+1;

		$sold_every = $days * 86400;

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

		$avatar=get_avatar_url($email);
    	$customer_history_html.='
			<tr class="pw_int_customers_single" data-customer-id="'.$customer_id.'" data-customer-email="'.$email.'" data-customer-segment="'.$customer_segment.'">
                <td class="pw-left-align pw-people-bought-product-imgs">
                    <img src="'.$avatar.'" >
                    '.$customer['name'].'
                </td>
                <td class="pw-center-align pw-black">
                    '.$location.'
                </td>
                <td class="pw-center-align pw-white pw-customer-singel-cards-cnt '.$customer_class.'">
                    '.$customer_segment_array[$customer_segment].'
                </td>
                <td class="pw-right-align pw-md-font pw-green">
                    '.$sold_every_html.'
                </td>
            </tr>';
	}
    //////////////////////////////////////////////////////
    /// END CUSTOMER HISTORY
    //////////////////////////////////////////////////////




    //////////////////////////////////////////////////////
    /// CALCULATE SOME VALUES OF BOTTOM BOX
    //////////////////////////////////////////////////////
    $this_product_amnt_html=($this_product_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($this_product_amnt);
    $this_product_amnt_percent=(float)number_format(($this_product_amnt*100)/$total_products_amnt,2 ) ."%";
    $this_product_amnt_percent_html=($this_product_quantity>1) ? "$this_product_quantity ".__('SALES',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ' :"$this_product_quantity ".__('SALE',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ';

	$this_refund_html=($this_refund_amnt) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($this_refund_amnt);
	$this_product_refund_percent='100%';
	if($this_product_amnt!=0)
		$this_product_refund_percent=(float)number_format(($this_refund_amnt*100)/$this_product_amnt,2 ) ."%";
	$this_product_refund_percent_html=($this_refund_count>1) ? "$this_refund_count ".__('REFUNDS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ' :"$this_refund_count ".__('REFUND',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ' ;


    $from=date_create($pw_from_date);
    $to=date_create($pw_to_date);
    $diff=date_diff($to,$from);

    $days = $diff->format('%a')+1;

	//SOLD EVERY
	$sold_every_html='-';
	$sold_every=1;


	if($this_product_quantity>0){
		$sold_every = round( $days / $this_product_quantity );

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


    //REFUNDED EVERY
    $refunded_every_html=__("No Refunds",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
    $refunded_every=1;

    //echo 'QQ'.$days .'WW'. round( $days / $this_refund_count ).'EEE';

    if($this_refund_count>0){
	    $refunded_every = round( $days / $this_refund_count );
	    $refunded_every = $refunded_every * 86400;

        $dtF = new DateTime( "@0" );
        $dtT = new DateTime( "@$refunded_every" );

        $year_sold  = $dtF->diff( $dtT )->y;
        $month_sold = $dtF->diff( $dtT )->m;
        $day_sold   = $dtF->diff( $dtT )->d;

        $week_sold=0;
        if ( $day_sold > 7 ) {
            $week_sold = floor( $day_sold / 7 ) ;
            $day_sold=$day_sold-($week_sold*7);
        }
        $hour_sold       = $dtF->diff( $dtT )->h;
	    $refunded_every_html = ( $year_sold != 0 ? $year_sold . "y " : "" ) . ( $month_sold != 0 ? $month_sold . "m " : "" ) . ( $week_sold != 0 ? $week_sold . "w " : "" ). ( $day_sold != 0 ? $day_sold . "d " : "" ) . ( $hour_sold != 0 ? $hour_sold . "h " : "" );
    }

	//AVG PRICE
	$avg_price=(float)number_format($this_product_amnt/$this_product_quantity,2);
	$avg_price_html=($avg_price) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($avg_price);
    //////////////////////////////////////////////////////
    /// END CALCULATE SOME VALUES OF BOTTOM BOX
    //////////////////////////////////////////////////////


    //////////////////////////////////////////////////////
    /// GET TOTAL SALES AND QTY of PRODUCTS
    //////////////////////////////////////////////////////
    $sql="SELECT sum(wmeta1.meta_value) as total,count(wmeta1.meta_value) as qty FROM {$wpdb->prefix}posts as posts inner join {$wpdb->prefix}woocommerce_order_items as witems ON posts.ID=witems.order_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as wmeta ON witems.order_item_id=wmeta.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as wmeta1 ON witems.order_item_id=wmeta1.order_item_id WHERE posts.post_status IN ('wc-processing','wc-on-hold','wc-completed') AND (wmeta.meta_key='_product_id' OR wmeta.meta_key='_variation_id') AND wmeta.meta_value='$product_id' AND wmeta1.meta_key='_line_total' AND posts.post_type='shop_order'";

    $product_sales=$product_qty='-';
    $order_items=$wpdb->get_results($sql);
    if(count($order_items)>0)
        foreach ( $order_items as $key => $order_item ){
	        $product_sales=$order_item->total;
	        $product_qty=$order_item->qty;
        }
    $product_sales=($product_sales) == 0 ? $pw_rpt_main_class->price(0) : $pw_rpt_main_class->price($product_sales);


    //////////////////////////////////////////////////////
    /// GET PRODUCT NOTE
    //////////////////////////////////////////////////////
    $note_text='';
    $product_note=get_post_meta($product_id,"_purchase_note");
    if(($product_note[0])!='')
        $note_text=$product_note[0];

echo '
    <div class="pw-cols col-xs-12 col-md-12">
        <span class="pw_rpt_fetch_single_product_back">'.__('Back to all products',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' |  </span>
        <span class="pw_rpt_fetch_single_product_prev"  title="Previous Product">
                <i class="fa fa-angle-left fa-2x"></i>
            </span>
        <span class="pw_rpt_fetch_single_product_next" title="Next Product">
                <i class="fa fa-angle-right fa-2x"></i>
            </span>
    </div>

    <div class="pw-cols col-xs-12 col-md-4">
        <div class="pw-cards-cnt pw-single-product-detail" href="#">
            <div class="pw-cards-thumb">
                <img width="150" height="150" src="'.$img_url.'" >
            </div>
            <div class="pw-card-detail pw-center-align">
                <div class="pw-box-padder">
                    <div class="pw-md-font">'.$p_title.'</div>
                    <div class="pw-product-subdetail">
                        <div class="pull-left" title="'.$product_rank_title.'">
                            <span class="pw-xs-font">
                                <span>'.$product_rank_val.'</span>
                                <sup>st</sup>
                            </span>
                            <i class="fa '.$product_rank_type.'"></i>
                        </div>
                        <div class="pw-val pw-sm-font pull-right" title="Product ID: sku-woo">'.__('SKU:',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' '.$sku.'</div>
                    </div>
                </div>
            </div>
            <div class="pw-card-bottom">
                <div class="pw-box-padder">
                    <span class="pull-left">
                        <div class="pw-md-font pw-green">'.$this_product_amnt_html.'</div>
                        <div class="pw-val pw-xs-font">'.$this_product_amnt_percent_html.'  ('.$this_product_amnt_percent.' '.__('OF TOTAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__).')</div>
                    </span>
                    <span class="pull-right pw-right-align" >
                        <div class="pw-md-font pw-red">'.$this_refund_html.'</div>
                        <div class="pw-val pw-xs-font">'.$this_product_refund_percent_html.'  ('.$this_product_refund_percent.' '.__('OF TOTAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__).')</div>
                    </span>
                </div>
                <div class="pw-box-padder">
                    <div id="pw_int_single_product_chartdiv"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="pw-cols col-xs-12 col-md-8">
        <div class="int-awr-box pw-center-align pw-pr-sum-box">
            <div class="pw-border-bottom awr-single-sum">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$current_month_week_value.'</div>
                        <div class="pw-val pwl-lbl">'.__('CURRENT',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' '.$month_week_title.'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$month_week_3_value.'</div>
                        <div class="pw-val pwl-lbl">3 '.$month_week_titles.' '.__('AGO',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$month_week_6_value.'</div>
                        <div class="pw-val pwl-lbl">6 '.$month_week_titles.' '.__('AGO',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$month_week_12_value.'</div>
                        <div class="pw-val pwl-lbl">12 '.$month_week_titles.' '.__('AGO',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>




                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$product_sales.' • '.$product_qty.'</div>
                        <div class="pw-val pwl-lbl">'.__('TOTAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' • '.__('QTY SOLD',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$avg_price_html.'</div>
                        <div class="pw-val pwl-lbl">'.__('AVG. PRICE',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.($sold_every_html).'</div>
                        <div class="pw-val pwl-lbl">'.__('SOLD EVERY',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="pw-box-padder">
                        <div class="pw-md-font pw-green">'.$refunded_every_html.'</div>
                        <div class="pw-val pwl-lbl">'.__('TYPICALLY REFUNDED IN',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
                    </div>
                </div>
            </div>
            <div class="pw-box-padder pw-left-align">
                
				'.$with_product_html.'

                <div class="pw-note-cnt">
                    <div class="pw-sm-font">
                        '.__('NOTE',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                    </div>
                    <textarea cols="3" class="pw-sm-font pw_intelligence_note_text" placeholder="'.__('click here to add note for this product ...',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'">'.$note_text.'</textarea>
                    <span class="pw-green pw_intelligence_note_resp pw-md-font"></span>
                    <button class="pw-button pw-pull-right pw_intelligence_note_text_save" data-id="'.$product_id.'" data-target="product">'.__('Save',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</button>
                    <div class="clear-fx"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="awr-clearboth"></div>
    <div class="pw-cols col-xs-12 col-md-6">
        <div class="int-awr-box int-fixed-height-box">
            <div class="awr-title">
                <h3>
                    <i class="fa fa-money"></i>'.__('PEOPLE WHO BOUGHT THIS',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                </h3>

            </div>

            <div class="int-awr-box-content">
                <div class="pw-box-padder">
					<table class="pw-simple-dashed-tbl pw-sm-font">
                        <tbody>
                            <tr>
                                <th class="pw-left-align pw-black">'.__('Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                                <th class="pw-center-align pw-black">'.__('Location',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                                <th class="pw-center-align pw-black">'.__('RFM Segment',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                                <th class="pw-right-align pw-black">'.__('Purchased',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                            </tr>	
                            '.$customer_history_html.'
                        </tbody>
					</table>
                </div>
            </div>
        </div>
    </div>

    <div class="pw-cols col-xs-12 col-md-6">
        <div class="int-awr-box int-fixed-height-box">
            <div class="awr-title">
                <h3>
                    <i class="fa fa-money"></i>'.__('SALES HISTORY',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'

                </h3>
				
            </div>

            <div class="int-awr-box-content">
                <div class="pw-box-padder">
                    <table class="pw-simple-dashed-tbl pw-sm-font">
                        <tbody>
                            <tr>
                                <th class="pw-left-align pw-black">'.__('Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                                <th class="pw-center-align pw-black">'.__('Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' • '.__('Refunds',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                                <th class="pw-right-align pw-black">'.__('Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</th>
                            </tr>	
                            '.$sale_history_html.'
                        </tbody>    
					</table>
                </div>
            </div>
        </div>
    </div>';
?>
	<script>
        var pw_int_single_product_value=<?php echo json_encode(($product_chart_array)); ?>;
    </script>