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
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
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

		$sql = "SELECT users.ID,users.user_email,users.display_name,users.user_registered,usermeta.meta_value as user_role
				FROM {$wpdb->prefix}users as users";

		$sql.= " LEFT JOIN  {$wpdb->prefix}usermeta as usermeta ON users.ID=usermeta.user_id ";

		$sql .= " WHERE  ";

		//$sql .= " AND postmeta.meta_key = '_customer_user'";
		$sql.=" usermeta.meta_key='{$wpdb->prefix}capabilities' ";

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$sql.= " AND DATE(users.user_registered) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}


		$sql .= " GROUP BY  users.ID";

		$sql .= " ORDER BY users.user_registered desc";

		//echo $sql;

	}elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		    $index_cols=0;
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");


                //Billing Name
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $items->display_name;
                $datatable_value.=("</td>");

                //Billing Email
                $display_class='';
               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
                $datatable_value.=("<td style='".$display_class."'>");
                $datatable_value.= $this->pw_email_link_format($items->user_email,false);
                $datatable_value.=("</td>");

				//Billing First Name
				$display_class='';
				$table_value=$items->user_role;
				$table_value=unserialize($table_value);
				$table_value=array_keys($table_value);

                global $wp_roles;
                $u = get_userdata($items->ID);
                $role = array_shift($u->roles);
			    $table_value = $wp_roles->roles[$role]['name'];

			   	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $table_value;
				$datatable_value.=("</td>");


				$first_order=$this->pw_get_new_customer_role($items->ID);
				//print_r($first_order);
                if(count($first_order)>0){
	                foreach($first_order as $forder){
		                $display_class='';
		               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		                $datatable_value.=("<td style='".$display_class."'>");
		                $datatable_value.= '<a target="_blank" href="'.admin_url().'post.php?post='.$forder->order_id.'&action=edit">'.$forder->order_id.'</a>';
		                $datatable_value.=("</td>");

		                $display_class='';
		               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		                $datatable_value.=("<td style='".$display_class."'>");
		                $datatable_value.= $forder->order_date;
		                $datatable_value.=("</td>");

		                $display_class='';
		               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		                $datatable_value.=("<td style='".$display_class."'>");
		                $datatable_value.= $forder->order_total == 0 ? 0 : $this->price($forder->order_total);
		                $datatable_value.=("</td>");
	                }
                }else{
	                $display_class='';
	                $datatable_value.=("<td style='".$display_class."'>");
	                $datatable_value.= '';
	                $datatable_value.=("</td>");

	                $display_class='';
	               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
	                $datatable_value.=("<td style='".$display_class."'>");
	                $datatable_value.= '';
	                $datatable_value.=("</td>");

	                $display_class='';
	               	if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
	                $datatable_value.=("<td style='".$display_class."'>");
	                $datatable_value.= '';
	                $datatable_value.=("</td>");
                }


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

            </div>
            
                <div class="col-md-12">
                
                    <?php
                    	$pw_hide_os='trash';
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