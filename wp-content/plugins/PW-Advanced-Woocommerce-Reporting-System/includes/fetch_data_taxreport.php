<?php
	
	if($file_used=="sql_table")
	{
		
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		
		
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status',"-1",true);
		$category_id		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id','-1',true);
		$pw_id_order_status	= $this->pw_get_woo_requests('pw_id_order_status','-1',true);	
		$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code','-1',true);
		$state_code 		= $this->pw_get_woo_requests('pw_states_code','-1',true);
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		
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
		
		//DATE
		$pw_from_date_condition='';
		
		//ORDER SATTUS
		$pw_id_order_status_join='';
		$pw_order_status_condition='';
		
		//COUTNRY
		$pw_country_code_join='';
		$pw_country_code_condition_1='';
		$pw_country_code_condition_2='';
		
		//STATE
		$state_code_join='';
		$state_code_condition_1='';
		$state_code_condition_2='';
		
		//ORDER STATUS
		$pw_id_order_status_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PUBLISH ORDER
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition ='';
		
		$sql_columns = "
		pw_posts.ID,
		SUM(pw_woocommerce_order_itemmeta_tax_amount.meta_value)  AS _order_tax,
		SUM(pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_value)  AS _shipping_tax_amount,
		
		SUM(pw_postmeta1.meta_value)  AS _order_shipping_amount,
		SUM(pw_postmeta2.meta_value)  AS _order_total_amount,
		COUNT(pw_posts.ID)  AS _order_count,
		
		pw_woocommerce_order_items.order_item_name as tax_rate_code, 
		pw_woocommerce_tax_rates.tax_rate_name as tax_rate_name, 
		pw_woocommerce_tax_rates.tax_rate as order_tax_rate, 
		
		pw_woocommerce_order_itemmeta_tax_amount.meta_value AS order_tax,
		pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_value AS shipping_tax_amount,
		pw_postmeta1.meta_value as order_shipping_amount,
		pw_postmeta2.meta_value as order_total_amount,
		pw_postmeta3.meta_value 		as billing_state,
		pw_postmeta4.meta_value 		as billing_country
		";
		
		$sql_columns .= ", CONCAT(pw_woocommerce_order_items.order_item_name,'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate,'-',pw_postmeta4.meta_value,'',pw_postmeta3.meta_value) as group_column";
		
		
		$sql_joins = "{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items";
				
		if(($pw_id_order_status  && $pw_id_order_status != '-1') || $pw_sort_by == "status"){
			$pw_id_order_status_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
			
			if($pw_sort_by == "status"){
				$pw_id_order_status_join .= " LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
			}
		}
			
		$sql_joins .= "$pw_id_order_status_join LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_tax ON pw_woocommerce_order_itemmeta_tax.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_tax_amount ON pw_woocommerce_order_itemmeta_tax_amount.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_shipping_tax_amount ON pw_woocommerce_order_itemmeta_shipping_tax_amount.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_tax_rates as pw_woocommerce_tax_rates ON pw_woocommerce_tax_rates.tax_rate_id=pw_woocommerce_order_itemmeta_tax.meta_value
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=	pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_woocommerce_order_items.order_id";
		
		if($pw_country_code and $pw_country_code != '-1')	
			$pw_country_code_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta5 ON pw_postmeta5.post_id=pw_posts.ID";
		
		if($state_code and $state_code != '-1')	
			$state_code_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_state ON pw_postmeta_billing_state.post_id=pw_posts.ID";
		
		$sql_joins.="$pw_country_code_join $state_code_join";
		
		$sql_condition = " pw_postmeta1.meta_key = '_order_shipping' AND pw_woocommerce_order_items.order_item_type = 'tax'
		AND pw_posts.post_type='shop_order' 
		AND pw_postmeta2.meta_key='_order_total'
		AND pw_woocommerce_order_itemmeta_tax.meta_key='rate_id'
		AND pw_woocommerce_order_itemmeta_tax_amount.meta_key='tax_amount'
		AND pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_key='shipping_tax_amount'
		AND pw_postmeta3.meta_key='_billing_state'
		AND pw_postmeta4.meta_key='_billing_country'";
		
		if($pw_id_order_status  && $pw_id_order_status != '-1') 
			$pw_id_order_status_condition = " AND term_taxonomy.term_id IN (".$pw_id_order_status .")";
		
		if($pw_country_code and $pw_country_code != '-1')	
			$pw_country_code_condition_1 = " AND pw_postmeta5.meta_key='_billing_country'";
		
		if($state_code and $state_code != '-1')		
			$state_code_condition_1 = " AND pw_postmeta_billing_state.meta_key='_billing_state'";
		
		if($pw_country_code and $pw_country_code != '-1')	
			$pw_country_code_condition_2 = " AND pw_postmeta5.meta_value IN (".$pw_country_code.")";
		
		if($state_code and $state_code != '-1')	
			$state_code_condition_2 = " AND pw_postmeta_billing_state.meta_value IN (".$state_code.")";
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";
			
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition = " AND (DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format'))";
		}	

		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";
		
		$sql_group_by = "  group by group_column";
	
		$sql_order_by = "  ORDER BY (pw_woocommerce_tax_rates.tax_rate + 0)  ASC";
		
		$sql = "SELECT $sql_columns
				FROM $sql_joins $pw_id_order_status_join
				WHERE $sql_condition
				$pw_id_order_status_condition $pw_country_code_condition_1 $state_code_condition_1
				$pw_country_code_condition_2 $state_code_condition_2
				$pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition  $pw_from_date_condition
				$sql_group_by $sql_order_by";
		
		//echo $sql;
		
	}
	elseif($file_used=="data_table"){

		////ADDE IN VER4.0
		/// TOTAL ROWS VARIABLES
		$tax_count=$order_count=$shipping_amnt=$gross_amnt=$net_amnt=$shipping_tax=$order_tax=$total_tax=0;
		$net_amnt_arr=array();
		foreach($this->results as $items){
		    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){

			////ADDE IN VER4.0
			/// TOTAL ROWS
            $tax_count++;

			$datatable_value.=("<tr>");
				//Tax Name
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->tax_rate_name;
				$datatable_value.=("</td>");
	
				//Tax Rate
				$pw_table_value=$items->order_tax_rate;
				
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $pw_table_value = sprintf("%.2f%%",$pw_table_value);
				$datatable_value.=("</td>");
				
				//Order Count
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->_order_count;

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $order_count+= $items->_order_count;
				$datatable_value.=("</td>");
				
				//Shipping Amt.
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->_order_shipping_amount);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $shipping_amnt+= $items->_order_shipping_amount;
				$datatable_value.=("</td>");
				
				//Gross Amt.
//				$display_class='';
//				$gross_value=$this->pw_get_number_percentage($items->_order_tax,$items->order_tax_rate);
//				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
//				$datatable_value.=("<td style='".$display_class."'>");
//					$datatable_value.= $this->price($gross_value);
//
//                    ////ADDE IN VER4.0
//                    /// TOTAL ROWS
//                    $gross_amnt+= $gross_value;
//				$datatable_value.=("</td>");
								
				//Net Amt.
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->order_total_amount);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS

	                    $net_amnt += $items->order_total_amount;
                    if(!isset($net_amnt_arr[$items->ID])) {
	                    $net_amnt_arr[$items->ID]='';
                    }

				$datatable_value.=("</td>");

				//Shipping Tax
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->_shipping_tax_amount);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $shipping_tax+= $items->_shipping_tax_amount;
				$datatable_value.=("</td>");
				
				//Order Tax
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->_order_tax);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $order_tax+= $items->_order_tax;
				$datatable_value.=("</td>");
				       
				//Total Tax
				$display_class='';
				$total_tax_value=$items->_shipping_tax_amount + $items->_order_tax;
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($total_tax_value);

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $total_tax+= $total_tax_value;
				$datatable_value.=("</td>");
										
			$datatable_value.=("</tr>");
		}
		//print_r($net_amnt_arr);

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$table_name_total= $table_name;
		$this->table_cols_total = $this->table_columns_total( $table_name_total );
		$datatable_value_total='';

		$datatable_value_total.=("<tr>");
		$datatable_value_total.="<td>$tax_count</td>";
		$datatable_value_total.="<td>$order_count</td>";
		$datatable_value_total.="<td>".(($shipping_amnt) == 0 ? $this->price(0) : $this->price($shipping_amnt))."</td>";
		//$datatable_value_total.="<td>".(($gross_amnt) == 0 ? $this->price(0) : $this->price($gross_amnt))."</td>";
		$datatable_value_total.="<td>".(($net_amnt) == 0 ? $this->price(0) : $this->price($net_amnt))."</td>";
		$datatable_value_total.="<td>".(($shipping_tax) == 0 ? $this->price(0) : $this->price($shipping_tax))."</td>";
		$datatable_value_total.="<td>".(($order_tax) == 0 ? $this->price(0) : $this->price($order_tax))."</td>";
		$datatable_value_total.="<td>".(($total_tax) == 0 ? $this->price(0) : $this->price($total_tax))."</td>";
		$datatable_value_total.=("</tr>");

		//print_r($net_amnt_arr);

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