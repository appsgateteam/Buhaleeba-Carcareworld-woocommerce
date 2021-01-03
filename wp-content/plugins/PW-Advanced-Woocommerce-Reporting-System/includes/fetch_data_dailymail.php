<?php
	$date_format		= get_option( 'date_format' );
	$pw_total_shop_day 		= $this->pw_get_dashboard_tsd();
	$datetime= date_i18n("Y-m-d H:i:s");

	$total_part_refund_amt	= $this->dashboard_pw_get_por_amount('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$_total_orders 			= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$total_orders 			= $this->pw_get_dashboard_value($_total_orders,'total_count',0);
	$total_sales 			= $this->pw_get_dashboard_value($_total_orders,'total_amount',0);

	$total_sales			= $total_sales - $total_part_refund_amt;


	//type, color,icon, title, amount, count, progress_amount_1, progress_amount_1
	//

	$total_sales_avg		= $this->pw_get_dashboard_avarages($total_sales,$total_orders);
	$total_sales_avg_per_day= $this->pw_get_dashboard_avarages($total_sales,$pw_total_shop_day);

	$_todays_orders 		= $this->pw_get_dashboard_totals_orders('today',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$total_today_order 		= $this->pw_get_dashboard_value($_todays_orders,'total_count',0);
	$total_today_sales 		= $this->pw_get_dashboard_value($_todays_orders,'total_amount',0);

	$total_today_avg		= $this->pw_get_dashboard_avarages($total_today_sales,$total_today_order);

	$total_categories  		= $this->pw_get_dashboard_tcc();
	$total_products  		= $this->pw_get_dashboard_tpc();
	$total_orders_shipping	= $this->pw_get_dashboard_toss('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);


	$total_refund 			= $this->pw_get_dashboard_tbs("total","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);
	$today_refund 			= $this->pw_get_dashboard_tbs("today","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);

	$today_part_refund_amt	= $this->dashboard_pw_get_por_amount('today',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$total_refund_amount 	= $this->pw_get_dashboard_value($total_refund,'total_amount',0);
	$total_refund_count 	= $this->pw_get_dashboard_value($total_refund,'total_count',0);

	$total_refund_amount	= $total_refund_amount + $total_part_refund_amt;

	$todays_refund_amount 	= $this->pw_get_dashboard_value($today_refund,'total_amount',0);
	$todays_refund_count 	= $this->pw_get_dashboard_value($today_refund,'total_count',0);

	$todays_refund_amount	= $todays_refund_amount + $today_part_refund_amt;

	$today_coupon 			= $this->pw_get_dashboard_totals_coupons("today",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$total_coupon 			= $this->pw_get_dashboard_totals_coupons("total",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$today_coupon_amount 	= $this->pw_get_dashboard_value($today_coupon,'total_amount',0);
	$today_coupon_count 	= $this->pw_get_dashboard_value($today_coupon,'total_count',0);

	$total_coupon_amount 	= $this->pw_get_dashboard_value($total_coupon,'total_amount',0);
	$total_coupon_count 	= $this->pw_get_dashboard_value($total_coupon,'total_count',0);

	$today_order_tax 		= $this->pw_get_dashborad_totals_orders("today","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$total_order_tax 		= $this->pw_get_dashborad_totals_orders("total","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$today_ord_tax_amount	= $this->pw_get_dashboard_value($today_order_tax,'total_amount',0);
	$today_ord_tax_count 	= $this->pw_get_dashboard_value($today_order_tax,'total_count',0);

	$total_ord_tax_amount	= $this->pw_get_dashboard_value($total_order_tax,'total_amount',0);
	$total_ord_tax_count 	= $this->pw_get_dashboard_value($total_order_tax,'total_count',0);

	$today_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("today","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$total_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("total","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$today_ordshp_tax_amount= $this->pw_get_dashboard_value($today_ord_shipping_tax,'total_amount',0);
	$today_ordshp_tax_count = $this->pw_get_dashboard_value($today_ord_shipping_tax,'total_count',0);

	$total_ordshp_tax_amount= $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_amount',0);
	$total_ordshp_tax_count = $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_count',0);

	$ytday_order_tax		= $this->pw_get_dashborad_totals_orders("yesterday","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$ytday_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("yesterday","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$ytday_tax_amount		= $this->pw_get_dashboard_value($ytday_order_tax,'total_amount',0);
	$ytday_ordshp_tax_amount= $this->pw_get_dashboard_value($ytday_ord_shipping_tax,'total_amount',0);
	$ytday_total_tax_amount = $ytday_tax_amount + $ytday_ordshp_tax_amount;

	$today_tax_amount		= $today_ordshp_tax_amount + $today_ord_tax_amount;
	$today_tax_count 		= '';

	$total_tax_amount		= $total_ordshp_tax_amount + $total_ord_tax_amount;
	$total_tax_count 		= '';

	$last_order_details 	= $this->pw_get_dashboard_lod($pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);

	$last_order_date 		= $this->pw_get_dashboard_value($last_order_details,'last_order_date','');
	$last_order_time		= strtotime($last_order_date);

	$short_date_format		= str_replace("F","M",$date_format);//Modified 20150209

	$current_time 			= strtotime($datetime);
	$last_order_time_diff	= $this->pw_get_dashboard_time($last_order_time, $current_time ,' ago');

	$users_of_blog 			= count_users();
	$total_customer 		= isset($users_of_blog['avail_roles']['customer']) ? $users_of_blog['avail_roles']['customer'] : 0;

	$total_reg_customer 	= $this->pw_get_dashboard_ttoc('total',false);
	$total_guest_customer 	= $this->pw_get_dashboard_ttoc('total',true);

	$today_reg_customer 	= $this->pw_get_dashboard_ttoc('today',false);
	$today_guest_customer 	= $this->pw_get_dashboard_ttoc('today',true);

	$yesterday_reg_customer	= $this->pw_get_dashboard_ttoc('yesterday',false);
	$yesterday_guest_customer= $this->pw_get_dashboard_ttoc('yesterday',true);

	$yesterday_orders 			= $this->pw_get_dashboard_totals_orders('yesterday',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$total_yesterday_order 		= $this->pw_get_dashboard_value($yesterday_orders,'total_count',0);
	$total_yesterday_sales 		= $this->pw_get_dashboard_value($yesterday_orders,'total_amount',0);

	$total_yesterday_avg		= $this->pw_get_dashboard_avarages($total_yesterday_sales,$total_yesterday_order);

	$yesterday_part_refund_amt	= $this->dashboard_pw_get_por_amount('yesterday',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$yesterday_refund 			= $this->pw_get_dashboard_tbs("yesterday","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);


	$yesterday_refund_amount 	= $this->pw_get_dashboard_value($yesterday_refund,'total_amount',0);
	$yesterday_refund_amount 	= $yesterday_refund_amount + $yesterday_part_refund_amt;

	$yesterday_coupon 			= $this->pw_get_dashboard_totals_coupons("yesterday",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$yesterday_coupon_amount 	= $this->pw_get_dashboard_value($yesterday_coupon,'total_amount',0);

	$yesterday_tax 				= $this->pw_get_dashborad_totals_orders("yesterday","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
	$yesterday_tax_amount 		= $this->pw_get_dashboard_value($yesterday_tax,'total_amount',0);

	$days_in_this_month 		= date('t', mktime(0, 0, 0, date('m', $current_time), 1, date('Y', $current_time)));



	$pw_cur_projected_sales_year=substr($pw_from_date,0,4);
	//$pw_cur_projected_sales_year	= $this->pw_get_dashboard_numbers('cur_projected_sales_year',date('Y',$current_time));
	$projected_pw_from_date		= $pw_cur_projected_sales_year."-01-01";
	$projected_pw_to_date			= $pw_cur_projected_sales_year."-12-31";

	$projected_total_orders		= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$projected_pw_from_date,$projected_pw_to_date);
	$projected_order_amount 	= $this->pw_get_dashboard_value($projected_total_orders,'total_amount',0);
	$projected_order_count 		= $this->pw_get_dashboard_value($projected_total_orders,'total_count',0);

	$total_projected_amount		= $this->pw_get_dashboard_numbers('total_projected_amount',0);

	//////////////////////////////////
	$months = array("January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December");
	$projected_amounts 			=array();
	foreach($months as $month){
		$key= $month;
		$value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$pw_cur_projected_sales_year.'_'.$month);
		$total_projected_amount+=$value;
	}
	/////////////////////////////////

	$projected_percentage		= $this->pw_get_number_percentage($projected_order_amount,$total_projected_amount);

	$projected_pw_from_date_cm	= date($pw_cur_projected_sales_year.'-m-01',$current_time);
	$projected_pw_to_date_cm		= date($pw_cur_projected_sales_year.'-m-t',$current_time);
	$projected_sales_month		= date('F',$current_time);
	$projected_sales_month_shrt	= date('M',$current_time);

	$projected_total_orders_cm	= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$projected_pw_from_date_cm,$projected_pw_to_date_cm);
	$projected_order_amount_cm 	= $this->pw_get_dashboard_value($projected_total_orders_cm,'total_amount',0);
	$projected_order_count_cm 	= $this->pw_get_dashboard_value($projected_total_orders_cm,'total_count',0);


	$months = array("January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December");
	$projected_amounts 			=array();
	foreach($months as $month){
		$key= $month;
		$value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$pw_cur_projected_sales_year.'_'.$month);
		if($value=='')
			$value=$month;
		$projected_amounts[$key]=$value;
	}


	$total_projected_amount_cm	= isset($projected_amounts[$projected_sales_month]) ? $projected_amounts[$projected_sales_month] : 100;


	$projected_percentage_cm	= $this->pw_get_number_percentage($projected_order_amount_cm,$total_projected_amount_cm);

	$this_month_date			= date('d',$current_time);

	$per_day_sales_amount		= $this->pw_get_dashboard_avarages($projected_order_amount_cm,$this_month_date);

	$per_day_sales_amount		= round(($per_day_sales_amount),2);
	$sales_forcasted 			= $per_day_sales_amount * $days_in_this_month;

	$current_total_sales_apd	= $this->pw_get_dashboard_avarages($projected_order_amount_cm,$this_month_date);


	//echo '<div class="clearboth"></div><div class="awr-box-title awr-box-title-nomargin">'.__('Total Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';

	$type='simple';
	///////TOTAL SUMMARY////////
	//////////////////////////
	$body='<div style="width: 520px; margin: 0 auto">';
	$body.='<h1 style="font-size: 18px; color: #fac34f; margin-bottom: 5px">'.__('Total Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .date("F d, Y",strtotime($pw_from_date)).' - '.date("F d, Y",strtotime($pw_to_date)).'</h1>
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

	$body .= $this->pw_email_table_row_html(__('Total Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales, '#'.$total_orders);

	$body.=$this->pw_email_table_row_html(__('Total Refund',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_refund_amount, '#'.$total_refund_count);

	$body.=$this->pw_email_table_row_html(__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_tax_amount, '#'.$total_tax_count);

	$body.=$this->pw_email_table_row_html(__('Total Coupons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_coupon_amount, '#'.$total_coupon_count);

	$body.=$this->pw_email_table_row_html(__('Total Registered',__PW_REPORT_WCREPORT_TEXTDOMAIN__), '', '#'.$total_customer);

	$body.=$this->pw_email_table_row_html(__('Total Guest Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), '', '#'.$total_guest_customer);

	$body .= '</table>';
	$body .= '<div style="height: 50px;"></div>';


	///////OTHER SUMMARY////////
	//////////////////////////
	$body.='<div style="width: 520px; margin: 0 auto">';
	$body.='<h1 style="font-size: 18px; color: #fac34f; margin-bottom: 5px">'.__('Other Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .date("F d, Y",strtotime($pw_from_date)).' - '.date("F d, Y",strtotime($pw_to_date)).'</h1>
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

	$body.=$this->pw_email_table_row_html(__('Cur. Yr Proj. Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$pw_cur_projected_sales_year.')', $total_projected_amount, '%'.$projected_percentage);

	$body.=$this->pw_email_table_row_html(__('Current Year Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$pw_cur_projected_sales_year.')', $projected_order_amount, '#'.$projected_order_count);

	$body.=$this->pw_email_table_row_html(__('Average Sales Per Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales_avg, '#'.$total_orders);

	$body.=$this->pw_email_table_row_html(__('Average Sales Per Day',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales_avg_per_day, '#'.$total_orders);

	$body.=$this->pw_email_table_row_html(__('Current Month Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')', $projected_order_amount_cm, '%'.$projected_percentage_cm);

	$body.=$this->pw_email_table_row_html(__('Cur. Month Proj. Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')', $total_projected_amount_cm, '#'.$projected_order_count_cm);

	$body.=$this->pw_email_table_row_html('('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')'. __('Average Sales/Day',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $current_total_sales_apd, '');

	$body.=$this->pw_email_table_row_html('('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')'. __('Forecasted Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $sales_forcasted, '');

	$body.=$this->pw_email_table_row_html(__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_ord_tax_amount, $total_ord_tax_count.'%');

	$body.=$this->pw_email_table_row_html(__('Order Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_ordshp_tax_amount, $total_ordshp_tax_count.'%');

	$body.=$this->pw_email_table_row_html(__('Order Shipping Total',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_orders_shipping, '#'.$total_orders);

	$amount='';
	$count='';


	if ( $last_order_date) $amount= date($short_date_format,$last_order_time); 	  else $amount=__( '01', __PW_REPORT_WCREPORT_TEXTDOMAIN__);

	if ( $last_order_time_diff) $count= $last_order_time_diff; else $count=__( '0', __PW_REPORT_WCREPORT_TEXTDOMAIN__);

	$body.=$this->pw_email_table_row_html(__('Last Order Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $amount, $count,'no_price');

	$body .= '</table>';
	$body .= '<div style="height: 50px;"></div>';

	///////TODAY SUMMARY////////
	//////////////////////////
	$today_summary='';
	$type='progress';
	$body.='<div style="width: 520px; margin: 0 auto">';
	$body.='<h1 style="font-size: 18px; color: #fac34f; margin-bottom: 5px">'.__('Today Summary -',__PW_REPORT_WCREPORT_TEXTDOMAIN__). " " .date("F d, Y",strtotime($pw_from_date)).' - '.date("F d, Y",strtotime($pw_to_date)).'</h1>
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

	$body.=$this->pw_email_table_row_html(__('Todays Total Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_today_sales, '#'.$total_today_order);

	$body.=$this->pw_email_table_row_html(__('Todays Average Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_today_avg, '');

	$body.=$this->pw_email_table_row_html(__('Todays Total Refund',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $todays_refund_amount, '#'.$todays_refund_count);

	$body.=$this->pw_email_table_row_html(__('Todays Total Coupons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_coupon_amount, '#'.$today_coupon_count);

	$body.=$this->pw_email_table_row_html(__('Todays Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_ord_tax_amount, '#'.$today_ord_tax_count);

	$body.=$this->pw_email_table_row_html(__('Todays Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_ordshp_tax_amount, '#'.$today_ordshp_tax_count);

	$body.=$this->pw_email_table_row_html(__('Todays Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_tax_amount, '#'.$today_tax_count);

	$body.=$this->pw_email_table_row_html(__('Todays Registered Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), '', '#'.$today_reg_customer);

	$body.=$this->pw_email_table_row_html(__('Todays Guest Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), '', '#'.$today_guest_customer);

	$body .= '</table>';
	$body .= '<div style="height: 50px;"></div>';
	$body .= '</div>';
?>