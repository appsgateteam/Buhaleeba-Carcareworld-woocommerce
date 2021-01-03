<?php
	
	if($file_used=="sql_table")
	{
		
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$date_format = $this->pw_date_format($pw_from_date);

		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		///////////HIDDEN FIELDS////////////
		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		
		
		//ORDER SATTUS
		$pw_id_order_status_join='';
		$pw_order_status_condition='';
		
		//ORDER STATUS
		$pw_id_order_status_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PUBLISH ORDER
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition ='';	
			
		$sql_columns = "
		SUM(pw_postmeta1.meta_value) AS 'total_amount' 
		,pw_postmeta2.meta_value AS 'billing_country'
		,Count(*) AS 'order_count'";
		
		
		$sql_joins="{$wpdb->prefix}posts as pw_posts
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID";
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
				$pw_id_order_status_join = " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		$sql_condition = "
		pw_posts.post_type			=	'shop_order'  
		AND pw_postmeta1.meta_key	=	'_order_total' 
		AND pw_postmeta2.meta_key	=	'_billing_country'";
		
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
			$pw_id_order_status_condition = " AND  term_taxonomy.term_id IN ({$pw_id_order_status})";
		}
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition = " AND  pw_posts.post_status IN ('{$in_post_status}')";
		}
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND pw_posts.post_status IN (".$pw_order_status.")";
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";
		
		$sql_group_by = "  
		GROUP BY  pw_postmeta2.meta_value ";
		$sql_order_by="
		Order By total_amount DESC";
		
		$sql = "SELECT $sql_columns FROM $sql_joins $pw_id_order_status_join WHERE $sql_condition
		$pw_id_order_status_condition $pw_from_date_condition $pw_publish_order_condition
		$pw_order_status_condition $pw_hide_os_condition 
		$sql_group_by $sql_order_by
		";
		
		//echo $sql;

	}elseif($file_used=="data_table"){

		////ADDE IN VER4.0
		/// TOTAL ROWS VARIABLES
		$country_count=$num_sale=$net_sale=$coupon_amnt=$shipping_amnt=$refund_amnt=$final_amnt=0;

		foreach($this->results as $items){
		    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){

			////ADDE IN VER4.0
			/// TOTAL ROWS
			$country_count++;

			$datatable_value.=("<tr>");

                $pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	        	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
			    $date_format = $this->pw_date_format($pw_from_date);

                //////////GET COUPONS///////////
                global $wpdb;
                $get_orders="
                SELECT
                billing_country.meta_value as billing_country,
                DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date,
                pw_woocommerce_order_items.order_id AS order_id,
                pw_woocommerce_order_items.order_item_id	AS order_item_id,
                woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value,
                (pw_woocommerce_order_itemmeta2.meta_value) AS item_net_amount,
                pw_woocommerce_order_itemmeta2.meta_value AS total_price,
                woocommerce_order_itemmeta.meta_value AS product_id
                FROM
                {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items
                LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	= pw_woocommerce_order_items.order_item_id
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta2 ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id
                LEFT JOIN {$wpdb->prefix}postmeta as billing_country ON billing_country.post_id = pw_posts.ID
                Where
                pw_posts.post_type = 'shop_order' AND
                billing_country.meta_key	= '_billing_country' AND
                billing_country.meta_value='$items->billing_country' AND
                woocommerce_order_itemmeta.meta_key = '_product_id' AND
                pw_woocommerce_order_itemmeta2.meta_key='_line_total' AND
                DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format') AND
                pw_posts.post_status NOT IN ('trash') GROUP BY pw_woocommerce_order_items.order_item_id";




                $get_orders= $wpdb->get_results($get_orders);


                $coupon_amount=array();
                $order_shipping=$sum_order_shipping=$sum_refund_amnt=0;
                $sum_coupon=0;
                foreach($get_orders as $order_items){
                    if(is_array($coupon_amount) && array_key_exists($order_items->order_id,$coupon_amount))
                        continue;

                    $ordersss=$this->pw_get_full_post_meta($order_items->order_id);
                    $order_shipping = isset($ordersss ['order_shipping'] ) ? $ordersss ['order_shipping'] : 0;
                    $sum_order_shipping+=$order_shipping;

                    $order_refund_amnt= $this->pw_get_por_amount($order_items -> order_id);
                    $sum_refund_amnt+=(isset($order_refund_amnt[$order_items->order_id])? $order_refund_amnt[$order_items->order_id]:0);

                    $get_coupons="
                    SELECT
                    pw_woocommerce_order_items.order_item_name	AS	'order_item_name',
                    pw_woocommerce_order_items.order_item_name	AS 'coupon_code',
                    SUM(woocommerce_order_itemmeta.meta_value) AS	'total_amount',
                    woocommerce_order_itemmeta.meta_value AS 'coupon_amount' ,
                    Count(*) AS 'coupon_count'
                    FROM
                    {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items
                    LEFT JOIN	{$wpdb->prefix}posts as pw_posts ON pw_posts.ID = pw_woocommerce_order_items.order_id
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
                    WHERE
                    pw_posts.post_type =	'shop_order' AND
                    pw_woocommerce_order_items.order_item_type	=	'coupon' AND
                    woocommerce_order_itemmeta.meta_key	=	'discount_amount' AND
                    pw_woocommerce_order_items.order_id='$order_items->order_id' AND
                    DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format') AND
                    pw_posts.post_status IN ('wc-completed','wc-on-hold','wc-processing') AND
                    pw_posts.post_status NOT IN ('trash')
                    Group BY pw_woocommerce_order_items.order_item_name ORDER BY total_amount DESC";
                    $coupon_amount[$order_items->order_id]='';
                    $get_coupons= $wpdb->get_results($get_coupons);
                    foreach($get_coupons as $coupon_items){
                        $sum_coupon+= $coupon_items->coupon_amount;
                    }
                }

                ////////////////////



				//Billing Country
				$country      	= $this->pw_get_woo_countries();
				$pw_table_value = isset($country->countries[$items->billing_country]) ? $country->countries[$items->billing_country]: $items->billing_country;
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $pw_table_value;
				$datatable_value.=("</td>");
				
				//Order Count
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->order_count;

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $num_sale+=$items->order_count;

				$datatable_value.=("</td>");
				
				//Amount
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $net_sale+=$items->total_amount;
				$datatable_value.=("</td>");

                //COUPON
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $sum_coupon == 0 ? $this->price(0) : $this->price($sum_coupon);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $coupon_amnt+=$sum_coupon;
				$datatable_value.=("</td>");

                //SHIPPING
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $sum_order_shipping == 0 ? $this->price(0) : $this->price($sum_order_shipping);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $shipping_amnt+=$sum_order_shipping;
				$datatable_value.=("</td>");

                //PART REFUND
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $sum_refund_amnt == 0 ? $this->price(0) : $this->price($sum_refund_amnt);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $refund_amnt+=$sum_refund_amnt;
				$datatable_value.=("</td>");

                //PART REFUND
				$final_sale=($items->total_amount+$sum_order_shipping+$sum_coupon)-$sum_refund_amnt;
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= ($final_sale) == 0 ? $this->price(0) : $this->price($final_sale);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $final_amnt+=$final_sale;
				$datatable_value.=("</td>");
				
			$datatable_value.=("</tr>");
		}

		////ADDED IN VER4.0
		/// TOTAL ROW
		$table_name_total= $table_name;
		$this->table_cols_total = $this->table_columns_total( $table_name_total );
		$datatable_value_total='';

		$datatable_value_total.=("<tr>");
		$datatable_value_total.="<td>$country_count</td>";
		$datatable_value_total.="<td>$num_sale</td>";
		$datatable_value_total.="<td>".(($net_sale) == 0 ? $this->price(0) : $this->price($net_sale))."</td>";
		$datatable_value_total.="<td>".(($coupon_amnt) == 0 ? $this->price(0) : $this->price($coupon_amnt))."</td>";
		$datatable_value_total.="<td>".(($shipping_amnt) == 0 ? $this->price(0) : $this->price($shipping_amnt))."</td>";
		$datatable_value_total.="<td>".(($refund_amnt) == 0 ? $this->price(0) : $this->price($refund_amnt))."</td>";
		$datatable_value_total.="<td>".(($final_amnt) == 0 ? $this->price(0) : $this->price($final_amnt))."</td>";
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