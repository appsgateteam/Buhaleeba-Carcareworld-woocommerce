<?php
	
	if($file_used=="sql_table")
	{
		$limit 				= $this->pw_get_woo_requests('limit',3,true);
		$p 					= $this->pw_get_woo_requests('p',1,true);				
		$page				= $this->pw_get_woo_requests('page',NULL);				
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status',"-1",true);

		$pw_stock_less_than		= $this->pw_get_woo_requests('pw_stock_less_than',"0",true);

		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$date_format = $this->pw_date_format($pw_from_date);
		$pw_product_type			= $this->pw_get_woo_requests('pw_product_type','simple',true);
		$pw_sort_by			= $this->pw_get_woo_requests('pw_sort_by','stock_valid_days',true);
		$pw_order_by			= $this->pw_get_woo_requests('pw_order_by','ASC',true);

		$date1 		= strtotime($pw_from_date);
		$date2 		= strtotime($pw_to_date);
		$datediff 	= $date2 - $date1;

		$difference = floor($datediff/(60*60*24));

		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

		///////////HIDDEN FIELDS////////////
		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////

		$sql_columns = " 
		pw_woocommerce_order_items.order_item_name 																AS order_item_name , 
		pw_woocommerce_order_itemmeta_product_id.meta_value 														AS product_id , 
		SUM(pw_woocommerce_order_itemmeta_qty.meta_value)															AS sales_quantity , 
		pw_postmeta_stock.meta_value																				AS current_stock_quantity , 
        SUM(pw_woocommerce_order_itemmeta_qty.meta_value)/$difference												AS avg_sales_quantity , 
		ROUND((pw_postmeta_stock.meta_value/(SUM(pw_woocommerce_order_itemmeta_qty.meta_value)/$difference)))			AS stock_valid_days";

		if($pw_product_type=='variation'){
		    $sql_columns.=" ,
		    pw_woocommerce_order_items.order_item_id AS order_item_id,
		    pw_woocommerce_order_itemmeta_variation_id.meta_value AS variation_id , 
		    pw_postmeta_manage_stock.meta_value AS manage_stock  ";
        }

		$sql_joins = " {$wpdb->prefix}woocommerce_order_items AS pw_woocommerce_order_items  
		LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = pw_woocommerce_order_items.order_id 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pw_woocommerce_order_itemmeta_qty ON pw_woocommerce_order_itemmeta_qty.order_item_id = pw_woocommerce_order_items.order_item_id 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pw_woocommerce_order_itemmeta_product_id ON pw_woocommerce_order_itemmeta_product_id.order_item_id = pw_woocommerce_order_items.order_item_id ";

		if($pw_product_type=='simple'){
		    $sql_joins.="
            LEFT JOIN {$wpdb->postmeta} AS pw_postmeta_stock ON pw_postmeta_stock.post_id = pw_woocommerce_order_itemmeta_product_id.meta_value 
            LEFT JOIN {$wpdb->postmeta} AS pw_postmeta_manage_stock ON pw_postmeta_manage_stock.post_id = pw_woocommerce_order_itemmeta_product_id.meta_value";
        }

		if($pw_product_type=='variation'){
		    $sql_joins.=" 
		    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pw_woocommerce_order_itemmeta_variation_id 	ON pw_woocommerce_order_itemmeta_variation_id.order_item_id 	= pw_woocommerce_order_items.order_item_id
		    LEFT JOIN {$wpdb->postmeta} AS pw_postmeta_manage_stock 	ON pw_postmeta_manage_stock.post_id 	= pw_woocommerce_order_itemmeta_variation_id.meta_value
		    LEFT JOIN {$wpdb->postmeta} AS pw_postmeta_stock 		ON pw_postmeta_stock.post_id 			= pw_woocommerce_order_itemmeta_variation_id.meta_value
		    ";
        }


		$sql_condition= " posts.post_type = 'shop_order' 
		AND pw_woocommerce_order_itemmeta_qty.meta_key 			= '_qty' 
		AND pw_woocommerce_order_itemmeta_product_id.meta_key 	= '_product_id'  
		AND pw_postmeta_manage_stock.meta_key 					= '_manage_stock'  
        AND pw_postmeta_stock.meta_key 							= '_stock' 
		AND pw_postmeta_manage_stock.meta_value 					= 'yes' 
		AND pw_postmeta_stock.meta_value > 0 
		AND LENGTH(pw_postmeta_stock.meta_value) >= 0 ";

		if($pw_product_type=='variation'){
		    $sql_condition.=" AND pw_woocommerce_order_itemmeta_variation_id.meta_key 	= '_variation_id' 
		    AND pw_woocommerce_order_itemmeta_variation_id.meta_value > 0 ";
        }


		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$sql_condition .= " AND DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}

		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$sql_condition.= " AND posts.post_status IN (".$pw_order_status.")";

		$sql_group_by= " GROUP BY product_id";
		if($pw_product_type=="variation") {
		    $sql_group_by= " GROUP BY pw_woocommerce_order_itemmeta_variation_id.meta_value";
        }
		$sql_order_by= " ORDER BY {$pw_sort_by} {$pw_order_by}";

		
		$sql = "SELECT $sql_columns
				FROM $sql_joins
				WHERE $sql_condition $sql_group_by $sql_order_by";
		
		//echo $sql;



		if($pw_product_type=="variation") {
			$columns=array(
                array('lable'=> __( "Product SKU", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Product Name", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Variation ID", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Product Variation", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Avg. Sales Qty.", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Current Stock Qty", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Stock Valid Days", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show'),
                array('lable'=> __( "Till Date", __PW_REPORT_WCREPORT_TEXTDOMAIN__ ),'status'=>'show')
            );

			//CUSTOM WORK - 15862
			if(is_array(__CUSTOMWORK_ID__) && in_array('15862',__CUSTOMWORK_ID__)) {
				$custom_sku_cols[]=array('lable'=>'Variation Custom SKU','status'=>'show');
				array_splice($columns,3,0,$custom_sku_cols);
			}
		}
		else if($pw_product_type=="simple"){
			$columns=array(
                array('lable'=> __("Product SKU", 							__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
                array('lable'=> __("Product Name", 							__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
                array('lable'=> __("Avg. Sales Qty.", 						__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
                array('lable'=> __("Current Stock Qty", 						__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
                array('lable'=> __("Stock Valid Days", 						__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
                array('lable'=> __("Till Date", 						        __PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show')
            );
		}

		$this->table_cols = $columns;
		//CUSTOM WORK - 15862
		$custom_sku_cols=array();
		if(is_array(__CUSTOMWORK_ID__) && in_array('15862',__CUSTOMWORK_ID__)) {
			$custom_sku_cols[]=array('lable'=>'Custom SKU','status'=>'show');
			array_splice($this->table_cols,1,0,$custom_sku_cols);
		}

		
	}
	elseif($file_used=="data_table"){

		$pw_product_type			= $this->pw_get_woo_requests('pw_product_type','simple',true);

		////ADDE IN VER4.0
		/// TOTAL ROWS VARIABLES
		$product_count=$total_stock=0;

		foreach($this->results as $items){
		    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){

			////ADDE IN VER4.0
			/// TOTAL ROWS
			$product_count++;

			$datatable_value.=("<tr>");
				
				//Product SKU
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_product_sku( $items->product_id);
				$datatable_value.=("</td>");

                //CUSTOM WORK - 15862
                if(is_array(__CUSTOMWORK_ID__) && in_array('15862',__CUSTOMWORK_ID__)) {
                    //Custom SKU
                    $display_class = '';
                    $custom_sku    = get_post_meta( $items->product_id, 'jk_sku', true );
                    if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
                        $display_class = 'display:none';
                    }
                    $datatable_value .= ( "<td style='" . $display_class . "' data-product-id='" . $items->product_id . "'>" );
                    $datatable_value .= $custom_sku;
                    $datatable_value .= ( "</td>" );
                }

				//Product Name
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->order_item_name;
				$datatable_value.=("</td>");

                if($pw_product_type=="variation") {
	                //Variation Id
	                $display_class='';
	               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
	                $datatable_value.=("<td style='".$display_class."'>");
	                $datatable_value.= "#".$items->variation_id;
	                $datatable_value.=("</td>");

	                //CUSTOM WORK - 15862
	                if(is_array(__CUSTOMWORK_ID__) && in_array('15862',__CUSTOMWORK_ID__)) {
		                //Custom SKU
		                $display_class = '';
		                $custom_sku    = get_post_meta( $items->variation_id, 'custom_field', true );
		                if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
			                $display_class = 'display:none';
		                }
		                $datatable_value .= ( "<td style='" . $display_class . "'>" );
		                $datatable_value .= $custom_sku;
		                $datatable_value .= ( "</td>" );
	                }

	                //Product Variation
	                $pw_table_value= $this->pw_get_woo_variation($items->order_item_id);
	                $order_item_id			= ($items->order_item_id);
	                $attributes 							= $this->pw_get_variaiton_attributes('order_item_id','',$order_item_id);
	                $varation_string 						= isset($attributes['item_varation_string']) ? $attributes['item_varation_string'] : array();
	                $pw_table_value			= $varation_string[$order_item_id]['varation_string'];

	                $display_class='';
	               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
	                $datatable_value.=("<td style='".$display_class."'>");
	                $datatable_value.= $pw_table_value;
	                $datatable_value.=("</td>");
                }


				//AVG Sales Qty.
                $avg_sale_qty='';
                if($items->avg_sales_quantity < 1){
	                $avg_sale_qty = number_format($items->avg_sales_quantity,3);
                }else{
	                $avg_sale_qty = number_format($items->avg_sales_quantity,2);
                }
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $avg_sale_qty;
				$datatable_value.=("</td>");

                //Current Stock Qty.
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= number_format($items->current_stock_quantity);

                ////ADDE IN VER4.0
                /// TOTAL ROWS
                $total_stock+= $items->current_stock_quantity;

                $datatable_value.=("</td>");

                //Stock Valid Days.
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->stock_valid_days;
                $datatable_value.=("</td>");

				//Till date
			    $today_date 			= date("Y-m-d");
			    $today_date 	= strtotime($today_date);
			    $date_format		= get_option( 'date_format' );
			    $till_date='';
                if($items->stock_valid_days > 0 and $items->stock_valid_days < 8000){//80008
	                $till_date = date($date_format,strtotime(" + {$items->stock_valid_days} day", $today_date));
                }else{
	                $till_date = '';
                }
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $till_date;
				$datatable_value.=("</td>");
				
			$datatable_value.=("</tr>");
		}

		////ADDE IN VER4.0
		/// TOTAL ROWS
		$table_name_total= $table_name;
		$this->table_cols_total = $this->table_columns_total( $table_name_total );
		$datatable_value_total='';

		$datatable_value_total.=("<tr>");
		$datatable_value_total.="<td>$product_count</td>";
		$datatable_value_total.="<td>$total_stock</td>";
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
			            <?php _e('Order By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-sort-alpha-asc"></i></span>
                    <div class="row">
                        <div class="col-md-6">

                            <select name="pw_sort_by" id="sort_by" class="sort_by">
                                <option value="order_item_name" selected="selected"><?php _e('Product Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                                <option value="avg_sales_quantity"><?php _e('Avg. Quantity Sold',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                                <option value="current_stock_quantity"><?php _e('Current Stock Qty',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                                <option value="stock_valid_days"><?php _e('Stock Valid Days',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="pw_order_by" id="order_by" class="order_by">
                                <option value="ASC" selected="selected"><?php _e('Ascending',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                                <option value="DESC" ><?php _e('Descending',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
			            <?php _e('Product Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-check-square-o"></i></span>
                    <select name="pw_product_type" id="pw_product_type">
                        <option value="simple" selected="selected"><?php _e('Simple Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <option value="variation"><?php _e('Variation Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
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