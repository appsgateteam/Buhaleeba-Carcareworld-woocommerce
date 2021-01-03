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
		SUM(pw_postmeta1.meta_value) AS 'total_amount' 
		,pw_postmeta2.meta_value AS 'billing_email'
		,pw_postmeta3.meta_value AS 'billing_first_name'
		,Count(pw_postmeta2.meta_value) AS 'order_count'
		,pw_postmeta4.meta_value AS  customer_id";

    $sql_joins = "{$wpdb->prefix}posts as pw_posts
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_posts.ID";

    $sql_joins .= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_posts.ID";

    if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
        $pw_id_order_status_join= " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
    }
    $sql_condition = "
		pw_posts.post_type='shop_order'  
		AND pw_postmeta1.meta_key='_order_total' 
		AND pw_postmeta2.meta_key='_billing_email'  
		AND pw_postmeta3.meta_key='_billing_first_name'
		AND pw_postmeta4.meta_key='_customer_user'
		";

    if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
        $pw_id_order_status_condition = " AND  term_taxonomy.term_id IN ({$pw_id_order_status})";
    }

    if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
        $pw_from_date_condition= " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
    }
    if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
        $in_post_status		= str_replace(",","','",$pw_publish_order);
        $pw_publish_order_condition= " AND  pw_posts.post_status IN ('{$in_post_status}')";
    }


    if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
        $pw_order_status_condition= " AND pw_posts.post_status IN (".$pw_order_status.")";

    if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
        $pw_hide_os_condition= " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";

    $sql_group_by= "  GROUP BY  pw_postmeta2.meta_value";
    $sql_order_by="Order By total_amount DESC";

    $sql = "SELECT $sql_columns FROM $sql_joins $pw_id_order_status_join WHERE $sql_condition
				$pw_id_order_status_condition $pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition 
				$sql_group_by $sql_order_by
				";

}elseif($file_used=="data_table"){


    ////ADDE IN VER4.0
    /// TOTAL ROWS VARIABLES
    $result_count=$order_count=$total_amnt=0;

    foreach($this->results as $items){
        $index_cols=0;
        //for($i=1; $i<=20 ; $i++){

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $result_count++;

        $datatable_value.=("<tr>");

        //Billing First Name
        $display_class='';
        if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
        $datatable_value.=("<td style='".$display_class."'>");
        $datatable_value.= $items->billing_first_name;
        $datatable_value.=("</td>");

        //Billing Last Name
        $display_class='';
        if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
        $datatable_value.=("<td style='".$display_class."'>");
        $datatable_value.= get_user_meta( $items->customer_id, 'last_name', true );
        $datatable_value.=("</td>");

        //Billing Email
        $display_class='';
        if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
        $datatable_value.=("<td style='".$display_class."'>");
        $datatable_value.= $this->pw_email_link_format($items->billing_email,false);
        $datatable_value.=("</td>");

        //Order Count
        $display_class='';
        if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
        $datatable_value.=("<td style='".$display_class."'>");
        $datatable_value.= $items->order_count;

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $order_count+= $items->order_count;
        $datatable_value.=("</td>");

        //Amount
        $display_class='';
        if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
        $datatable_value.=("<td style='".$display_class."'>");
        $datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $total_amnt+= $items->total_amount;
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
    $datatable_value_total.="<td>$order_count</td>";
    $datatable_value_total.="<td>".(($total_amnt) == 0 ? $this->price(0) : $this->price($total_amnt))."</td>";
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