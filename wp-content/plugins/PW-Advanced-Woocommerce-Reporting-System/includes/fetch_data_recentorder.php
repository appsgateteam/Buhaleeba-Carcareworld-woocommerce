<?php

if ($file_used == "sql_table") {

    //GET POSTED PARAMETERS
    $request            = array();
    $start              = 0;
    $pw_from_date       = $this->pw_get_woo_requests('pw_from_date', null, true);
    $pw_to_date         = $this->pw_get_woo_requests('pw_to_date', null, true);
    $date_format        = $this->pw_date_format($pw_from_date);
    $pw_id_order_status = $this->pw_get_woo_requests('pw_id_order_status', null, true);
    $pw_order_status    = $this->pw_get_woo_requests('pw_orders_status', '-1', true);
    $pw_order_status    = "'" . str_replace(",", "','", $pw_order_status) . "'";

    ///////////HIDDEN FIELDS////////////
    $pw_hide_os       = $this->pw_get_woo_requests('pw_hide_os', '-1', true);
    $pw_publish_order = 'no';

    $data_format = $this->pw_get_woo_requests_links('date_format', get_option('date_format'), true);
    //////////////////////

    //ORDER SATTUS
    $pw_id_order_status_join   = '';
    $pw_order_status_condition = '';

    //ORDER STATUS
    $pw_id_order_status_condition = '';

    //DATE
    $pw_from_date_condition = '';

    //PUBLISH ORDER
    $pw_publish_order_condition = '';

    //HIDE ORDER STATUS
    $pw_hide_os_condition = '';

    $sql_columns = " 	  
			pw_posts.ID AS order_id, 
			DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date
			,pw_postmeta3.meta_value As 'total_amount'
			,pw_posts.post_status As order_status
		";

    $sql_joins = " {$wpdb->prefix}posts as pw_posts 
					LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_posts.ID";

    if (strlen($pw_id_order_status) > 0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all") {
        $pw_id_order_status_join = " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
    }

    $sql_condition = " pw_posts.post_type='shop_order'
		AND pw_postmeta3.meta_key='_order_total'";

    if (strlen($pw_id_order_status) > 0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all") {
        $pw_id_order_status_condition = " AND  term_taxonomy.term_id IN ({$pw_id_order_status})";
    }

    if ($pw_from_date != null && $pw_to_date != null) {
        $pw_from_date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
    }


    if (strlen($pw_publish_order) > 0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all") {
        $in_post_status             = str_replace(",", "','", $pw_publish_order);
        $pw_publish_order_condition = " AND  pw_posts.post_status IN ('{$in_post_status}')";
    }


    if ($pw_order_status && $pw_order_status != '-1' and $pw_order_status != "'-1'") {
        $pw_order_status_condition = " AND pw_posts.post_status IN (" . $pw_order_status . ")";
    }

    if ($pw_hide_os && $pw_hide_os != '-1' and $pw_hide_os != "'-1'") {
        $pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('" . $pw_hide_os . "')";
    }

    $sql_group_by = " GROUP BY pw_posts.ID";

    $sql_order_by = " Order By pw_posts.post_date DESC ";

    $sql = "SELECT $sql_columns
				FROM $sql_joins $pw_id_order_status_join
				WHERE $sql_condition
				$pw_id_order_status_condition $pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition 
				$sql_group_by $sql_order_by";

    //echo $sql;

} elseif ($file_used == "data_table") {


    ////ADDE IN VER4.0
    /// TOTAL ROWS VARIABLES
    $gross_amnt = $discount_amnt = $shipping_amnt = $shipping_tax_amnt =
    $order_tax_amnt = $total_tax_amnt = $part_refund_amnt = $order_count =
    $product_count = $product_qty = $total_rate = $product_amnt = $product_discount = $net_amnt = 0;

    foreach ($this->results as $items) {
        $index_cols = 0;
        //for($i=1; $i<=20 ; $i++){

        $order_id         = $items->order_id;
        $fetch_other_data = '';

        if ( ! isset($this->order_meta[$order_id])) {
            $fetch_other_data = $this->pw_get_full_post_meta($order_id);
        }

        //print_r($fetch_other_data);


        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $order_count++;

        $datatable_value .= ("<tr>");

        $pw_order_total = isset($fetch_other_data['order_total']) ? $fetch_other_data['order_total'] : 0;

        $order_shipping = isset($fetch_other_data['order_shipping']) ? $fetch_other_data['order_shipping'] : 0;

        $pw_cart_discount = isset($fetch_other_data['cart_discount']) ? $fetch_other_data['cart_discount'] : 0;

        $pw_order_discount = isset($fetch_other_data['order_discount']) ? $fetch_other_data['order_discount'] : 0;

        $total_discount = isset($fetch_other_data['total_discount']) ? $fetch_other_data['total_discount'] : ($pw_cart_discount + $pw_order_discount);


        //order ID
        $order_id = $items->order_id;

        //CUSTOM WORK - 15862
        if (is_array(__CUSTOMWORK_ID__) && in_array('15862', __CUSTOMWORK_ID__)) {
            $order_id = get_post_meta($order_id, '_order_number_formatted', true);
        }

        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $order_id;
        $datatable_value .= ("</td>");

        //Name
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $fetch_other_data['billing_first_name'] . ' ' . $fetch_other_data['billing_last_name'];
        $datatable_value .= ("</td>");

        //Email
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $fetch_other_data['billing_email'];
        $datatable_value .= ("</td>");

        //Date
        $date_format   = get_option('date_format');
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= date($date_format, strtotime($items->order_date));
        $datatable_value .= ("</td>");

        //Status
        $pw_table_value = $items->order_status;
        if ($pw_table_value == 'wc-completed') {
            $pw_table_value = '<span class="awr-order-status awr-order-status-' . sanitize_title($pw_table_value) . '" >' . ucwords(__($pw_table_value,
                    __PW_REPORT_WCREPORT_TEXTDOMAIN__)) . '</span>';
        } elseif ($pw_table_value == 'wc-refunded') {
            $pw_table_value = '<span class="awr-order-status awr-order-status-' . sanitize_title($pw_table_value) . '" >' . ucwords(__($pw_table_value,
                    __PW_REPORT_WCREPORT_TEXTDOMAIN__)) . '</span>';
        } else {
            $pw_table_value = '<span class="awr-order-status awr-order-status-' . sanitize_title($pw_table_value) . '" >' . ucwords(__($pw_table_value,
                    __PW_REPORT_WCREPORT_TEXTDOMAIN__)) . '</span>';
        }

        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= str_replace("Wc-", "", $pw_table_value);
        $datatable_value .= ("</td>");

        //Items
        $display_class   = '';
        $order_items_cnt = $this->pw_get_oi_count($items->order_id, 'line_item');
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= isset($order_items_cnt[$items->order_id]) ? $order_items_cnt[$items->order_id] : "";
        $datatable_value .= ("</td>");

        //Payment Method
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= isset($fetch_other_data['payment_method_title']) ? $fetch_other_data['payment_method_title'] : "";
        $datatable_value .= ("</td>");

        //Shipping Method
        $shipping_method = $this->pw_oin_list($items->order_id, 'shipping');
        $display_class   = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= isset($shipping_method[$items->order_id]) ? $shipping_method[$items->order_id] : "";
        $datatable_value .= ("</td>");

        //Order Currency
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= isset($fetch_other_data['order_currency']) ? $fetch_other_data['order_currency'] : "";
        $datatable_value .= ("</td>");

        //Gross Amt.
        $display_class  = '';
        $pw_table_value = ($pw_order_total + $total_discount) - ($fetch_other_data['order_shipping'] + $fetch_other_data['order_shipping_tax'] + $fetch_other_data['order_tax']);
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $gross_amnt      += $pw_table_value;
        $datatable_value .= ("</td>");

        //Order Discount Amt.
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_order_discount);
        $datatable_value .= ("</td>");

        //Cart Discount Amt.
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price(isset($fetch_other_data['cart_discount']) ? $fetch_other_data['cart_discount'] : 0);
        $datatable_value .= ("</td>");

        //Total Discount Amt.
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($total_discount);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $discount_amnt   += $total_discount;
        $datatable_value .= ("</td>");

        //Shipping Amt.
        $display_class = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($fetch_other_data['order_shipping']);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $shipping_amnt   += $fetch_other_data['order_shipping'];
        $datatable_value .= ("</td>");

        //Shipping Tax Amt.
        $display_class  = '';
        $pw_table_value = (isset($fetch_other_data['order_shipping_tax']) ? $fetch_other_data['order_shipping_tax'] : 0);
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $shipping_tax_amnt += $pw_table_value;
        $datatable_value   .= ("</td>");

        //Order Tax Amt.
        $display_class  = '';
        $pw_table_value = (isset($fetch_other_data['order_tax']) ? $fetch_other_data['order_tax'] : 0);
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $order_tax_amnt  += $pw_table_value;
        $datatable_value .= ("</td>");

        //Total Tax Amt.
        $display_class  = '';
        $pw_table_value = isset($fetch_other_data['total_tax']) ? $fetch_other_data['total_tax'] : ($fetch_other_data['order_tax'] + $fetch_other_data['order_shipping_tax']);
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $total_tax_amnt  += $pw_table_value;
        $datatable_value .= ("</td>");

        //Part Refund Amt.
        $display_class     = '';
        $order_refund_amnt = $this->pw_get_por_amount($items->order_id);
        $pw_table_value    = (isset($order_refund_amnt[$items->order_id]) ? $this->price($order_refund_amnt[$items->order_id]) : $this->price(0));
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= ($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $part_refund_amnt += isset($order_refund_amnt[$items->order_id]) ? $order_refund_amnt[$items->order_id] : 0;
        $datatable_value  .= ("</td>");
        $part_refund      = (isset($order_refund_amnt[$items->order_id]) ? $order_refund_amnt[$items->order_id] : 0);

        //Net Amt.
        $pw_table_value = isset($items->total_amount) ? ($items->total_amount) - $part_refund : 0;
        $display_class  = '';
        if ($this->table_cols[$index_cols++]['status'] == 'hide') {
            $display_class = 'display:none';
        }
        $datatable_value .= ("<td style='" . $display_class . "'>");
        $datatable_value .= $this->price($pw_table_value);

        ////ADDE IN VER4.0
        /// TOTAL ROWS
        $net_amnt        += $pw_table_value;
        $datatable_value .= ("</td>");

        $datatable_value .= ("</tr>");
    }

    ////ADDE IN VER4.0
    /// TOTAL ROWS
    $table_name_total       = $table_name;
    $this->table_cols_total = $this->table_columns_total($table_name_total);
    $datatable_value_total  = '';

    $datatable_value_total .= ("<tr>");
    $datatable_value_total .= "<td>$order_count</td>";
    $datatable_value_total .= "<td>" . (($gross_amnt) == 0 ? $this->price(0) : $this->price($gross_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($discount_amnt) == 0 ? $this->price(0) : $this->price($discount_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($shipping_amnt) == 0 ? $this->price(0) : $this->price($shipping_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($shipping_tax_amnt) == 0 ? $this->price(0) : $this->price($shipping_tax_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($order_tax_amnt) == 0 ? $this->price(0) : $this->price($order_tax_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($total_tax_amnt) == 0 ? $this->price(0) : $this->price($total_tax_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($part_refund_amnt) == 0 ? $this->price(0) : $this->price($part_refund_amnt)) . "</td>";
    $datatable_value_total .= "<td>" . (($net_amnt) == 0 ? $this->price(0) : $this->price($net_amnt)) . "</td>";
    $datatable_value_total .= ("</tr>");

} elseif ($file_used == "search_form") {
    ?>
    <form class='alldetails search_form_report' action='' method='post'>
        <input type='hidden' name='action' value='submit-form'/>
        <div class="row">

            <div class="col-md-6">
                <div class="awr-form-title">
                    <?php _e('From Date', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                <input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick"/>
            </div>

            <div class="col-md-6">
                <div class="awr-form-title">
                    <?php _e('To Date', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                <input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"/>

                <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                <input type="hidden" name="pw_orders_status[]" id="order_status"
                       value="<?php echo $this->pw_shop_status; ?>">
            </div>

        </div>

        <div class="col-md-12">
            <?php
            $pw_hide_os       = $this->otder_status_hide;
            $pw_publish_order = 'no';

            $data_format = $this->pw_get_woo_requests_links('date_format', get_option('date_format'), true);
            ?>
            <input type="hidden" name="list_parent_category" value="">
            <input type="hidden" name="pw_category_id" value="-1">
            <input type="hidden" name="group_by_parent_cat" value="0">

            <input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os; ?>"/>

            <input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format; ?>"/>

            <input type="hidden" name="table_names" value="<?php echo $table_name; ?>"/>
            <div class="fetch_form_loading search-form-loading"></div>
            <button type="submit" value="Search" class="button-primary"><i class="fa fa-search"></i>
                <span><?php echo __('Search', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
            <button type="button" value="Reset" class="button-secondary form_reset_btn"><i
                        class="fa fa-reply"></i><span><?php echo __('Reset Form',
                        __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
        </div>

    </form>
    <?php
}

?>