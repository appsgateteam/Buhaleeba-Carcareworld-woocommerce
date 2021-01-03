<?php
	
	if($file_used=="sql_table")
	{
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$date_format = $this->pw_date_format($pw_from_date);

		$pw_parent_tag_id		= $this->pw_get_woo_requests('pw_tags_id','-1',true);
		
		$pw_child_cat_id	= $this->pw_get_woo_requests('child_category_id','-1',true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);

		$pw_list_parent_cat			= $this->pw_get_woo_requests('list_parent_category',NULL,false);
		$category_id			= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_group_by_parent_cat			= $this->pw_get_woo_requests('group_by_parent_cat','-1',true);
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
		
		/////////////////////////
		//APPLY PERMISSION TERMS
		$key=$this->pw_get_woo_requests('table_names','',true);

		$pw_parent_tag_id=$this->pw_get_form_element_permission('pw_tags_id',$pw_parent_tag_id,$key);

		///////////////////////////
		
		///////////HIDDEN FIELDS////////////
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		 
				 
		//DATE
		$pw_from_date_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		$pw_id_order_status_join='';
		$pw_id_order_status_condition='';
		
		//CATEGORY
		$category_id_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		
		//PARENT CATEGORY
		$pw_parent_tag_id_condition='';
		
		//CHILD CATEGORY
		$pw_child_cat_id_condition='';
		
		//LIST PARENT CATEGORY
		$pw_list_parent_cat_condition='';
		
		//PUBLISH STATUS
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition='';
		
		$sql_columns = " 
		SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value) 				AS 'quantity',
		SUM(pw_woocommerce_order_itemmeta_tags.meta_value) 				AS 'total_amount' ,
		pw_woocommerce_order_items.order_item_name					AS 'product_name' ,
		pw_woocommerce_order_items.order_item_id					AS order_item_id , 
		woocommerce_order_itemmeta_product_id.meta_value		AS product_id , 							
		DATE(shop_order.post_date)								AS post_date , 
		pw_terms.name											AS tag_name , 
		pw_terms.term_id											AS tag_id";
		
		//COST OF GOOD
		if($pw_show_cog=='yes'){
			$sql_columns .= " ,SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value * pw_woocommerce_order_itemmeta22.meta_value) AS 'total_cost'";
		}
		
		$sql_joins= "{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items	
							LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.id=pw_woocommerce_order_items.order_id
							LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_qty ON pw_woocommerce_order_itemmeta_product_qty.order_item_id=pw_woocommerce_order_items.order_item_id
							LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_tags ON pw_woocommerce_order_itemmeta_tags.order_item_id=pw_woocommerce_order_items.order_item_id
							LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_product_id ON woocommerce_order_itemmeta_product_id.order_item_id=pw_woocommerce_order_items.order_item_id
							
							LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships_tags 	ON pw_term_relationships_tags.object_id	=	woocommerce_order_itemmeta_product_id.meta_value 
							LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy_tags 		ON pw_term_taxonomy_tags.term_taxonomy_id	=	pw_term_relationships_tags.term_taxonomy_id
							LEFT JOIN  {$wpdb->prefix}terms					as pw_terms 				ON pw_terms.term_id					=	pw_term_taxonomy_tags.term_id
							LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id
							";
		
		
		//COST OF GOOD
		if($pw_show_cog=='yes'){
			$sql_joins .=	"	
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta22 ON pw_woocommerce_order_itemmeta22.order_item_id=pw_woocommerce_order_items.order_item_id ";
		}
		

		
		$sql_condition = "  pw_woocommerce_order_itemmeta_product_qty.meta_key				= '_qty'
							AND pw_woocommerce_order_itemmeta_tags.meta_key			= '_line_total' 
							AND woocommerce_order_itemmeta_product_id.meta_key 	= '_product_id'						
							AND shop_order.post_type							= 'shop_order'
							AND pw_term_taxonomy_tags.taxonomy = 'product_tag'";
		

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition= " AND DATE(shop_order.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}

		if($pw_parent_tag_id  && $pw_parent_tag_id != "-1") {
			$category_id_condition= " AND pw_terms.term_id IN ({$pw_parent_tag_id}) ";
		}


		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND pw_posts.post_status IN (".$pw_order_status.")";

		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition= " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";

		//COST OF GOOD
		if($pw_show_cog=='yes'){	
			$sql_condition .="
			AND pw_woocommerce_order_itemmeta22.meta_key	= '".__PW_COG_TOTAL__."' ";
		}
		
		
		$sql_group_by='';

		$sql_group_by= " GROUP BY  pw_terms.term_id";

		$sql_order_by= "  ORDER BY total_amount DESC, quantity DESC, tag_id ASC";
		
		$sql = "SELECT $sql_columns FROM $sql_joins $pw_id_order_status_join WHERE $sql_condition
				$pw_id_order_status_condition $pw_parent_tag_id_condition $pw_child_cat_id_condition
				$pw_list_parent_cat_condition $pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition $category_id_condition
				$sql_group_by $sql_order_by
				";
		
		//echo $sql;
		
		$this->table_cols =$this->table_columns($table_name);
		//CHECK IF COST OF GOOD IS ENABLE
		
		
		if($pw_show_cog!='yes'){
			unset($this->table_cols[count($this->table_cols)-1]);
			unset($this->table_cols[count($this->table_cols)-1]);
		}
		
	}elseif($file_used=="data_table"){

		////ADDE IN VER4.0
		/// TOTAL ROWS VARIABLES
		$sales_qty=$category_count=$total_amnt=$cog_amnt=$profit_amnt=0;

		foreach($this->results as $items){
		    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");

                ////ADDE IN VER4.0
                /// TOTAL ROWS
                $category_count++;
						
				//Tag Name
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->tag_name;
				$datatable_value.=("</td>");
				
				//Quantity
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->quantity;

                    ////ADDE IN VER4.0
                    /// TOTAL ROWS
                    $sales_qty+=$items->quantity;
				$datatable_value.=("</td>");
				
				//Amount
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

                    ////ADDED IN VER4.0
                    /// TOTAL ROWS
                    $total_amnt+=$items->total_amount;
				$datatable_value.=("</td>");
				
				//COST OF GOOD
				$pw_show_cog= $this->pw_get_woo_requests('pw_show_cog',"no",true);	
				if($pw_show_cog=='yes'){
					$display_class='';
					/*$cog=get_post_meta($items->product_id,__PW_COG__,true);
					$cog*=$items->quantity;*/
					if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						//$datatable_value.= $cog == 0 ? $this->price(0) : $this->price($cog);
						$datatable_value.= $items->total_cost == 0 ? $this->price(0) : $this->price($items->total_cost);

                        ////ADDED IN VER4.0
                        /// TOTAL ROWS
                        $cog_amnt+=$items->total_cost;

					$datatable_value.=("</td>");
					
					if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						//$datatable_value.= $cog == 0 ? $this->price(0) : $this->price($cog);
						$datatable_value.= ($items->total_amount-$items->total_cost) == 0 ? $this->price(0) : $this->price($items->total_amount-$items->total_cost);

                        ////ADDED IN VER4.0
                        /// TOTAL ROWS
                        $profit_amnt+=($items->total_amount-$items->total_cost);

					$datatable_value.=("</td>");
				}
				
			$datatable_value.=("</tr>");
		}

		////ADDED IN VER4.0
		/// TOTAL ROW
		$table_name_total= $table_name;
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
		$this->table_cols_total = $this->table_columns_total( $table_name_total );
		$datatable_value_total='';
		if($pw_show_cog!='yes'){
			////ADDE IN VER4.0
			/// COST OF GOOD
			unset($this->table_cols_total[count($this->table_cols_total)-1]);
			unset($this->table_cols_total[count($this->table_cols_total)-1]);
		}

		$datatable_value_total.=("<tr>");
		$datatable_value_total.="<td>$category_count</td>";
		$datatable_value_total.="<td>$sales_qty</td>";
		$datatable_value_total.="<td>".(($total_amnt) == 0 ? $this->price(0) : $this->price($total_amnt))."</td>";
		if($pw_show_cog=='yes'){
			$datatable_value_total.="<td>".(($cog_amnt) == 0 ? $this->price(0) : $this->price($cog_amnt))."</td>";
			$datatable_value_total.="<td>".(($profit_amnt) == 0 ? $this->price(0) : $this->price($profit_amnt))."</td>";
		}
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

                <?php
                $col_style='';
                $permission_value=$this->get_form_element_value_permission('pw_tags_id');

                if($this->get_form_element_permission('pw_tags_id') ||  $permission_value!=''){

                    if ( ! $this->get_form_element_permission( 'pw_tags_id' ) && $permission_value != '' ) {
                        $col_style = 'display:none';
                    }
                    ?>

                    <div class="col-md-6" >
                        <div class=" awr-form-title">
                    <?php _e( 'Tags', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ); ?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-tags"></i></span>
                    <?php
                    $p_categories     = $this->pw_get_tag_products( 'product_tag' );
                    $option           = '';
                    $current_category = $this->pw_get_woo_requests_links( 'pw_tags_id', '', true );
                    //echo $current_product;

                    foreach ( $p_categories as $category ) {
                        $selected = '';

                        /*if(!$this->get_form_element_permission('pw_tags_id') &&  $permission_value!='')
                            $selected="selected";

                        if($current_category==$category->id)
                            $selected="selected";*/
                        $option .= "<option $selected value='" . $category->id . "' >" . $category->label . " </option>";
                    }

                    ?>
                    <select name="pw_tags_id[]" multiple="multiple" size="5" data-size="5" class="chosen-select-search">
                        <?php
                        if ( $this->get_form_element_permission( 'pw_tags_id' ) && ( ( ! is_array( $permission_value ) ) || ( is_array( $permission_value ) && in_array( 'all', $permission_value ) ) ) ) {
                            ?>
                            <option value="-1"><?php _e( 'Select All', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ); ?></option>
                            <?php
                        }
                        ?>

                        <?php
                        echo $option;
                        ?>
                    </select>
                    </div>

                    <?php
                }
            	if(__PW_COG__!=''){
				?>
				
					<div class="col-md-6">
						<div class="awr-form-title">
							<?php _e('SHOW JUST INCLUDE C.O.G & PROFIT',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                            <br />
                            <span class="description"><?php _e('Include just products with current Profit(Cost of good) plugin(Selected in Setting -> Add-on Settings -> Cost of Good).',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span>
						</div>
						
						<input name="pw_show_cog" type="checkbox" value="yes"/>
						
					</div>	
				<?php
					}
				?>
                
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