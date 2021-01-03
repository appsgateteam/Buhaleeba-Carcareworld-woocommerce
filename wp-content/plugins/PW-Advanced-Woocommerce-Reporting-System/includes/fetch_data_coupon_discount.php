<?php

if($file_used=="sql_table")
{

	//GET POSTED PARAMETERS
	$start				= 0;
	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
	$date_format = $this->pw_date_format($pw_from_date);

	$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code','-1',true);
	$pw_coupon_codes	= $this->pw_get_woo_requests('pw_codes_of_coupon','-1',true);
	if($pw_coupon_codes!="-1")
		$pw_coupon_codes  		= "'".str_replace(",","','",$pw_coupon_codes)."'";
	$coupon_discount_types		= $this->pw_get_woo_requests('pw_coupon_discount_types','-1',true);
	if($coupon_discount_types!="-1")
		$coupon_discount_types  		= "'".str_replace(",","','",$coupon_discount_types)."'";
	$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code','-1',true);

	$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
	$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);

	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	if($pw_order_status!="-1")
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";


	///////////HIDDEN FIELDS////////////
	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
	$pw_publish_order='no';
	$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
	//////////////////////


	//COPUN DISCOUNT
	$coupon_discount_types_join='';
	$coupon_discount_types_condition_1='';
	$coupon_discount_types_condition_2='';

	//DATE
	$pw_from_date_condition='';

	//ORDER STATUS ID
	$pw_id_order_status_join='';
	$pw_id_order_status_condition='';
	$pw_order_status_condition='';

	//PUBLISH
	$pw_publish_order_condition='';

	//COUPON COED
	$pw_coupon_code_condition='';
	$pw_coupon_codes_condition ='';

	//COUNTRY
	$pw_country_code_condition='';

	//HIDE ORDER
	$pw_hide_os_condition ='';

	//COUPON DISCOUNT
	$coupon_discount_types_condition='';

	$sql_columns = "
		pw_woocommerce_order_items.order_item_name	AS	'coupon_code_label',
		pw_woocommerce_order_items.order_item_name	AS	'coupon_code',
		pw_coupon_discount_type.meta_value 	AS discount_type,
		pw_woocommerce_order_items.order_item_name	AS 'product_name' ,
		SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value)	AS 'quantity' ,
		CONCAT(pw_woocommerce_order_items_product.order_item_name, '-', pw_coupon_discount_type.meta_value, '-' ,pw_woocommerce_order_items.order_item_name) 	AS order_by,
		SUM(pw_woocommerce_order_itemmeta_line_subtotal.meta_value)	AS line_subtotal,
		SUM(pw_woocommerce_order_itemmeta_line_total.meta_value)	AS line_total,
		(SUM(pw_woocommerce_order_itemmeta_line_subtotal.meta_value) - SUM(pw_woocommerce_order_itemmeta_line_total.meta_value)) AS total_amount";



	$sql_joins = "
		{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		LEFT JOIN	{$wpdb->prefix}posts	as pw_posts ON pw_posts.ID = pw_woocommerce_order_items.order_id
		LEFT JOIN	{$wpdb->prefix}posts	as pw_coupons ON pw_coupons.post_title = pw_woocommerce_order_items.order_item_name
		LEFT JOIN	{$wpdb->prefix}postmeta	as pw_coupon_discount_type ON pw_coupon_discount_type.post_id = pw_coupons.ID
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items_product ON pw_woocommerce_order_items_product.order_id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_qty ON pw_woocommerce_order_itemmeta_product_qty.order_item_id=pw_woocommerce_order_items_product.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_line_subtotal ON pw_woocommerce_order_itemmeta_line_subtotal.order_item_id=pw_woocommerce_order_items_product.order_item_id AND pw_woocommerce_order_itemmeta_line_subtotal.meta_key = '_line_subtotal'
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_line_total ON pw_woocommerce_order_itemmeta_line_total.order_item_id=pw_woocommerce_order_items_product.order_item_id AND pw_woocommerce_order_itemmeta_line_total.meta_key = '_line_total'";

	$sql_condition = "pw_posts.post_type 								=	'shop_order'
		AND pw_woocommerce_order_items.order_item_type		=	'coupon' 
		AND pw_coupons.post_type 								=	'shop_coupon'
		AND pw_coupon_discount_type.meta_key						=	'discount_type'
		AND pw_woocommerce_order_items_product.order_item_type	=	'line_item'
		AND pw_woocommerce_order_itemmeta_product_qty.meta_key	=	'_qty'";

	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition= " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
	}

	if($coupon_discount_types && $coupon_discount_types != "-1"){
		$coupon_discount_types_condition = " AND pw_coupon_discount_type.meta_value IN ({$coupon_discount_types})";
	}

	if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
		$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";

	if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
		$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";

	$sql_group_by = " Group BY order_by";
	$sql_order_by = " ORDER BY -1 DESC";

	$sql = "SELECT $sql_columns
				FROM $sql_joins 
				WHERE $sql_condition $pw_from_date_condition $coupon_discount_types_condition $pw_order_status_condition $pw_hide_os_condition
				$sql_group_by $sql_order_by";

	//echo $sql;
}
elseif($file_used=="data_table"){

	////ADDE IN VER4.0
	/// TOTAL ROWS VARIABLES
	$result_count=$product_count=$discount_amnt=0;

	foreach($this->results as $items){
	    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$result_count++;

		$datatable_value.=("<tr>");

		//Product Name
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items->product_name;
		$datatable_value.=("</td>");

		//Coupon Code
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items->coupon_code;
		$datatable_value.=("</td>");

		//Coupon Type
		$display_class='';
		$discount_type=array(
			'fixed_cart'      => __( 'Fixed cart discount', 			__PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
			'percent'         => __( 'Percentage discount', 		__PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
			'fixed_product'   => __( 'Fixed product discount', 		__PW_REPORT_WCREPORT_TEXTDOMAIN__ ),
			'percent_product' => __( 'Product % Discount', 		__PW_REPORT_WCREPORT_TEXTDOMAIN__ )
		);
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= ($items->discount_type!='' ? $discount_type[$items->discount_type] : "");
		$datatable_value.=("</td>");

		//Qty
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items->quantity;

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$product_count+= $items->quantity;

		$datatable_value.=("</td>");

		//Discount Amount
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$discount_amnt+= $items->total_amount;

		$datatable_value.=("</td>");

		$datatable_value.=("</tr>");
	}

	////ADDE IN VER4.0
	/// TOTAL ROWS
	$table_name_total= $table_name;
	$this->table_cols_total = $this->table_columns_total( $table_name_total );
	$datatable_value_total='';

	$datatable_value_total.=("<tr>");
	$datatable_value_total.="<td>$result_count</td>";
	$datatable_value_total.="<td>$product_count</td>";
	$datatable_value_total.="<td>".(($discount_amnt) == 0 ? $this->price(0) : $this->price($discount_amnt))."</td>";
	$datatable_value_total.=("</tr>");

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
                <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                <input type="hidden" name="pw_orders_status[]" id="order_status" value="<?php echo $this->pw_shop_status; ?>">
            </div>

            <div class="col-md-6">
                <div class="awr-form-title">
					<?php
					$pw_coupon_codes=$this->pw_get_woo_coupons_codes();
					$option='';
					foreach($pw_coupon_codes as $coupon){
						$selected='';
						/*if($current_product==$product->id)
							$selected="selected";*/
						$option.="<option $selected value='".$coupon -> id."' >".$coupon -> label." </option>";
					}
					?>
					<?php _e('Coupon Codes',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-key"></i></span>
                <select name="pw_codes_of_coupon[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                    <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
					<?php
					echo $option;
					?>
                </select>
            </div>

            <div class="col-md-6">
                <div class="awr-form-title">
					<?php _e('Discount Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-money"></i></span>
                <select name="pw_coupon_discount_types" >
                    <option value="-1"><?php _e('Select One',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                    <option value="percent"><?php _e('Percentage Discount',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                    <option value="fixed_cart"><?php _e('Fixed Cart Discount',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                    <option value="fixed_product"><?php _e('Fixed Product Discount',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                </select>
            </div>

        </div>

        <div class="col-md-12">

			<?php
			$pw_hide_os=$this->otder_status_hide;
			$pw_publish_order='no';
			$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
			?>
            <input type="hidden" name="list_parent_category" value="">
            <input type="hidden" name="pw_category_id" value="-1">
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