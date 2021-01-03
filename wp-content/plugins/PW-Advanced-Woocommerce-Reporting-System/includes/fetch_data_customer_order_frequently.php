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
		
		$sql_columns= "
            COUNT(DISTINCT posts.ID)									AS order_count,
            SUM(order_total.meta_value) 								AS total_amount,
            COUNT(DISTINCT postmeta_billing_email.meta_value)		AS customer_count,
            DATE_FORMAT(posts.post_date,'month_%Y%m') 				AS month_key,
            DATE_FORMAT(posts.post_date,'%Y-%m-01')					AS min_date,
            DATE_FORMAT(LAST_DAY(posts.post_date),'%Y-%m-%d')		AS max_date,
            postmeta_billing_email.meta_value						AS billing_email,
            CONCAT(DATE_FORMAT(posts.post_date,'month_%Y%m'),'-',postmeta_billing_email.meta_value)		AS group_column";


		$sql_joins = " 
            {$wpdb->posts} AS posts
            LEFT JOIN {$wpdb->postmeta} AS order_total ON order_total.post_id = posts.ID AND order_total.meta_key = '_order_total'
            LEFT JOIN {$wpdb->postmeta} AS postmeta_billing_email ON postmeta_billing_email.post_id = posts.ID AND postmeta_billing_email.meta_key = '_billing_email'";

		$sql_condition = " posts.post_type = 'shop_order' ";

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$sql_condition.= " AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}

		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND posts.post_status IN (".$pw_order_status.")";
			
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition= " AND posts.post_status NOT IN ('".$pw_hide_os."')";


		$sql_group_by= " GROUP BY group_column";

		$sql_order_by= " ORDER BY posts.post_date DESC";
		
		$sql = "SELECT $sql_columns FROM $sql_joins WHERE $sql_condition $pw_order_status_condition $pw_hide_os_condition
				$sql_group_by $sql_order_by	";
		
		//echo $sql;
		
	}elseif($file_used=="data_table"){

		foreach($this->results as $items) {
			$index_cols=0;

			$datatable_value.=("<tr>");
					
				//Months
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->month_name;
				$datatable_value.=("</td>");
				
				//Total Sale Amnt
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_sales_amount == 0 ? $this->price(0) : $this->price($items->total_sales_amount);
				$datatable_value.=("</td>");

				//Total Order Count
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_order_count;
				$datatable_value.=("</td>");

                //New Customer Count
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->repeat_customer_count;
                $datatable_value.=("</td>");

                //Repeat Customer Count
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->new_customer_count;
                $datatable_value.=("</td>");

                //New Customer Total
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->new_customer_sales_amount== 0 ? $this->price(0) : $this->price($items->new_customer_sales_amount);
                $datatable_value.=("</td>");

                //Repeat Customer Total
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->repeat_customer_sales_amount== 0 ? $this->price(0) : $this->price($items->repeat_customer_sales_amount);
                $datatable_value.=("</td>");


			$datatable_value.=("</tr>");
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

                <div class="col-md-6">
                    <div class="awr-form-title">
			            <?php _e('Min Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa fa-battery-4"></i></span>
                    <input name="pw_min_order_count" id="pw_min_order_count" type="text"/>
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