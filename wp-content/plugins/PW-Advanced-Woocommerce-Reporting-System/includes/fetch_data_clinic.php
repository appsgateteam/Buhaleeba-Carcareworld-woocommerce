<?php

if($file_used=="sql_table")
{

	//GET POSTED PARAMETERS
	$request 			= array();
	$start				= 0;
	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
	$pw_clinic_type			= $this->pw_get_woo_requests('pw_clinic_type',NULL,true);

	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
	$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

	///////////HIDDEN FIELDS////////////
	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
	$pw_publish_order='no';

	$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
	//////////////////////

	//CATEGORY
	$category_id_cols='';
	$category_id_join='';
	$category_id_condition='';

	//TAG
	$tag_id_cols='';
	$tag_id_join='';
	$tag_id_condition='';

	//CATEGORY
	$brand_id_cols='';
	$brand_id_join='';
	$brand_id_condition='';
	$pw_brand_prod_id_string_condition='';

	//PRODUCT
	$pw_clinic_id_condition='';

	//DATE
	$pw_from_date_condition='';

	//CUSTOM WORK - 11899
	//DELIVERY DATA CONDITION
	$date_delivery_condition= '';
	$delivery_date_join='';

	//PRODUCT STRING
	$pw_cat_prod_id_string_condition='';

	//ORDER STATUS
	$pw_order_status_condition='';
	$pw_id_order_status_join='';
	$pw_id_order_status_condition='';

	//PUBLISH STATUS
	$pw_publish_order_condition='';

	//HIDE ORDER STATUS
	$pw_hide_os_condition='';


	/////////////////////////
	//APPLY PERMISSION TERMS
	$key=$this->pw_get_woo_requests('table_names','',true);

	$category_id=$this->pw_get_form_element_permission('pw_category_id',$category_id,$key);
	$tag_id=$this->pw_get_form_element_permission('pw_tag_id',$category_id,$key);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id=$this->pw_get_form_element_permission('pw_brand_id',$brand_id,$key);

	$pw_product_id=$this->pw_get_form_element_permission('pw_product_id',$pw_product_id,$key);

	$pw_order_status=$this->pw_get_form_element_permission('pw_orders_status',$pw_order_status,$key);

	if($pw_order_status != NULL  && $pw_order_status != '-1')
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	///////////////////////////


	$sql = " SELECT ";

	$sql_columns = "shop_order.ID as order_id
	                ,postmeta.meta_value as total_amount
	                ,pw_woocommerce_order_items.order_item_name	AS 'product_name' 
	                ,pw_woocommerce_order_items.order_item_id	AS order_item_id 
	                ,pw_woocommerce_order_itemmeta7.meta_value	AS clinic	
	                ,DATE(shop_order.post_date)	AS post_date
	                ,count(order_id)  as quantity
					";

	//$sql .= " ,woocommerce_order_itemmeta.meta_value AS 'quantity' ,pw_woocommerce_order_itemmeta6.meta_value AS 'total_amount'";
	//$sql_columns .= " ,count(shop_order.ID) AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value) AS 'total_amount' ";


	$sql_joins = "
					{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
					";


	$sql_joins .=	"	
        LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id 						
                ";

	if($pw_id_order_status  && $pw_id_order_status != "-1") {
		$pw_id_order_status_join= " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
					LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
	}

	$sql_joins .= " $pw_id_order_status_join
					LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id
					LEFT JOIN {$wpdb->prefix}postmeta as postmeta ON shop_order.ID=postmeta.post_id
					";

	$sql_condition = "
					1*1
					AND postmeta.meta_key = '_order_total'
					AND woocommerce_order_itemmeta.meta_key	= '_qty'
					";

	$sql_condition .="	 
        AND pw_woocommerce_order_itemmeta7.meta_key 	= 'Gallery Name'						
						
        AND shop_order.post_type					= 'shop_order'
        ";

	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition= "
					AND DATE(shop_order.post_date) BETWEEN '" . $pw_from_date . "' and '" . $pw_to_date . "'";
	}

	if($pw_clinic_type  && $pw_clinic_type != "-1" && $pw_clinic_type != "all")
		$pw_clinic_id_condition = "
					AND pw_woocommerce_order_itemmeta7.meta_value LIKE ('%".$pw_clinic_type ."%')";


	if($pw_id_order_status  && $pw_id_order_status != "-1")
		$pw_id_order_status_condition = " 
					AND terms2.term_id IN (".$pw_id_order_status .")";


	if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
		$in_post_status		= str_replace(",","','",$pw_publish_order);
		$pw_publish_order_condition= " AND  shop_order.post_status IN ('{$in_post_status}')";
	}
	//echo $pw_order_status;
	if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
		$pw_order_status_condition= " AND shop_order.post_status IN (".$pw_order_status.")";

	if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
		$pw_hide_os_condition = " AND shop_order.post_status NOT IN ('".$pw_hide_os."')";

	$sql_group_by = " GROUP BY  order_id";

	$sql_order_by = " ORDER BY total_amount DESC";

	$sql = "SELECT $sql_columns  FROM $sql_joins  WHERE 
							$sql_condition $pw_from_date_condition $pw_clinic_id_condition
							 $pw_id_order_status_condition $pw_publish_order_condition 
							$pw_order_status_condition $pw_hide_os_condition $sql_group_by $sql_order_by";


	$sqls="SELECT shop_order.ID as order_id,postmeta.meta_value as total_amount,	pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,pw_woocommerce_order_itemmeta7.meta_value	AS clinic	,DATE(shop_order.post_date)	AS post_date,count(order_id)  as quantity FROM wp_woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN wp_woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id INNER JOIN wp_postmeta as postmeta ON shop_order.ID=postmeta.post_id WHERE  1*1  AND postmeta.meta_key = '_order_total' AND woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta7.meta_key = 'Gallery Name'	AND shop_order.post_type	= 'shop_order' AND DATE(shop_order.post_date) BETWEEN '2019-03-20' and '2019-03-20' AND shop_order.post_status IN ('wc-processing','wc-on-hold','wc-completed') AND shop_order.post_status NOT IN ('trash') GROUP BY order_id ORDER BY order_id DESC";

	//echo get_option('date_format');

	//echo $sql;

}elseif($file_used=="data_table"){

	////ADDE IN VER4.0
	/// TOTAL ROWS VARIABLES
	$result_count=$order_count=$total_amnt=0;
	$pw_clinic_type			= $this->pw_get_woo_requests('pw_clinic_type',NULL,true);
	$clinic_type=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'clinic_type');
	$clinic_type=explode(",",$clinic_type);

	$final_array=array();
	foreach($this->results as $items){
		$clinic=$items ->clinic;
		if($clinic){
			$clinic=explode("-",$clinic);
			$clinic=$clinic[2];
		}

		if(!in_array(strtolower($clinic), array_map("strtolower", $clinic_type)))
			continue;



		if($pw_clinic_type=='all')
			$clinic=__('All of Clinics',__PW_REPORT_WCREPORT_TEXTDOMAIN__);


		//$final_array[$clinic]['clinic']=$clinic;

		if($pw_clinic_type=="all") {
			if ( isset( $final_array[ $clinic ]['quantity'] ) ) {
				$final_array[ $clinic ]['quantity'] += 1;
			} else {
				$final_array[ $clinic ]['quantity'] = 1;
			}
		}else{
			if ( isset( $final_array[ $clinic ]['quantity'] ) ) {
				$final_array[ $clinic ]['quantity'] += 1;
			} else {
				$final_array[ $clinic ]['quantity'] = 1;
			}
		}

		if ( isset( $final_array[ $clinic ]['total_amount'] ) ) {
			$final_array[ $clinic ]['total_amount'] += $items->total_amount;
		} else {
			$final_array[ $clinic ]['total_amount'] = $items->total_amount;
		}
	}

	//print_r($final_array);


	foreach($final_array as $key=>$items){
		$index_cols=0;
		//for($i=1; $i<=20 ; $i++){

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$result_count++;

		$datatable_value.=("<tr>");


		//Clinic Type
		$display_class='';
		$custom_fields=$key;

		if($custom_fields){
//			$custom_fields=explode("-",$custom_fields);
//			$custom_fields=$custom_fields[2];
		}

		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= strtoupper($custom_fields);
		$datatable_value.=("</td>");

		//Order Count
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items ['quantity'];

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$order_count+= $items['quantity'];
		$datatable_value.=("</td>");

		//Amount
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $items['total_amount'] == 0 ? $this->price(0) : $this->price($items['total_amount']);

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$total_amnt+= $items['total_amount'];
		$datatable_value.=("</td>");

		$datatable_value.=("</tr>");
	}


	if($pw_clinic_type=='-1') {
		////ADDE IN VER4.0
		/// TOTAL ROWS
		$table_name_total       = $table_name;
		$this->table_cols_total = $this->table_columns_total( $table_name_total );
		$datatable_value_total  = '';

		$datatable_value_total .= ( "<tr>" );
		$datatable_value_total .= "<td>$order_count</td>";
		$datatable_value_total .= "<td>" . ( ( $total_amnt ) == 0 ? $this->price( 0 ) : $this->price( $total_amnt ) ) . "</td>";
		$datatable_value_total .= ( "</tr>" );
	}

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

			<?php
			//CUSTOM WORK - 12679
			$clinic_type=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'clinic_type');
			$clinic_type=explode(",",$clinic_type);
			$clinic_options='';
			foreach ($clinic_type as $clinic){
				$clinic_options.='<option value="'.$clinic.'">'.$clinic.'</option>';
			}
			?>
            <div class="col-md-6">
                <div class="awr-form-title">
					<?php _e('Clinic Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-money"></i></span>
                <select name="pw_clinic_type" >
                    <option value="-1"><?php _e('Select One',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
<!--                    <option value="all">--><?php //_e('All of Clinics',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?><!--</option>-->
					<?php echo $clinic_options;?>
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