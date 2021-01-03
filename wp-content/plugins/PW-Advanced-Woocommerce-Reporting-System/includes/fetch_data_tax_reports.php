<?php
if ($file_used == "sql_table") {

    //GET POSTED PARAMETERS
    $start        = 0;
    $pw_from_date = $this->pw_get_woo_requests('pw_from_date', null, true);
    $pw_to_date   = $this->pw_get_woo_requests('pw_to_date', null, true);
    $date_format  = $this->pw_date_format($pw_from_date);

    $pw_sort_by  = $this->pw_get_woo_requests('sort_by', 'item_name', true);
    $pw_order_by = $this->pw_get_woo_requests('order_by', 'ASC', true);

    $pw_tax_group_by = $this->pw_get_woo_requests('pw_tax_groupby', 'tax_group_by_state', true);

    $pw_id_order_status = $this->pw_get_woo_requests('pw_id_order_status', null, true);
    $pw_order_status    = $this->pw_get_woo_requests('pw_orders_status', '-1', true);
    //$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
    $sort_by = $this->pw_get_woo_requests('sort_by', 'order_id', true);

    $pw_country_code = $this->pw_get_woo_requests('pw_countries_code', '-1', true);

    /*if($pw_country_code != NULL  && $pw_country_code != '-1')
    {
        $pw_country_code = "'".str_replace(",", "','",$pw_country_code)."'";
    }*/

    $state_code = $this->pw_get_woo_requests('pw_states_code', '-1', true);

    /*if($state_code != NULL  && $state_code != '-1')
    {
        $state_code = "'".str_replace(",", "','",$state_code)."'";
    }*/

    ///////////HIDDEN FIELDS////////////
    $pw_hide_os       = $this->pw_get_woo_requests('pw_hide_os', '-1', true);
    $pw_publish_order = 'no';

    /////////////////////////
    //APPLY PERMISSION TERMS
    $key = $this->pw_get_woo_requests('table_names', '', true);

    $pw_country_code = $this->pw_get_form_element_permission('pw_countries_code', $pw_country_code, $key);

    if ($pw_country_code != null && $pw_country_code != '-1') {
        $pw_country_code = "'" . str_replace(",", "','", $pw_country_code) . "'";
    }

    $state_code = $this->pw_get_form_element_permission('pw_states_code', $state_code, $key);

    if ($state_code != null && $state_code != '-1') {
        $state_code = "'" . str_replace(",", "','", $state_code) . "'";
    }

    $pw_order_status = $this->pw_get_form_element_permission('pw_orders_status', $pw_order_status, $key);

    if ($pw_order_status != null && $pw_order_status != '-1') {
        $pw_order_status = "'" . str_replace(",", "','", $pw_order_status) . "'";
    }

    ///////////////////////////

    $data_format = $this->pw_get_woo_requests_links('date_format', get_option('date_format'), true);
    //////////////////////

    //Start Date
    $pw_from_date_condition = "";

    //Order Status
    $pw_id_order_status_join      = "";
    $pw_id_order_status_condition = "";

    //Tax Group
    $pw_tax_group_by_join   = "";
    $tax_based_on_condition = "";

    //State Code
    $state_code_condition = "";

    //Coutry Code
    $pw_country_code_condition = "";

    //Order Status
    $pw_order_status_condition = "";

    //Hide Order
    $pw_hide_os_condition = "";

    $join_country = false;

    if (
        $pw_tax_group_by == "tax_group_by_city"
        || $pw_tax_group_by == "tax_group_by_state"
        || $pw_tax_group_by == "tax_group_by_country"
        || $pw_tax_group_by == "tax_group_by_zip"
        || $pw_tax_group_by == "tax_group_by_tax_name"
        || $pw_tax_group_by == "tax_group_by_city_summary"
        || $pw_tax_group_by == "tax_group_by_state_summary"
        || $pw_tax_group_by == "tax_group_by_country_summary"
        || ( ! empty($pw_country_code) and $pw_country_code != "-1")
    ) {
        $join_country = true;
    }

    $sql = "  SELECT
			
			SUM(woocommerce_order_itemmeta_tax_amount.meta_value)  				AS tax_amount,
			
			SUM(woocommerce_order_itemmeta_shipping_tax_amount.meta_value)  	AS shipping_tax_amount,
			
			woocommerce_order_items.order_id 									AS order_id, 
			
			woocommerce_order_items.order_item_name 							AS tax_rate_code, 
			
			woocommerce_tax_rates.tax_rate_name 								AS tax_rate_name, 
			
			woocommerce_tax_rates.tax_rate 										AS order_tax_rate
			
			,woocommerce_order_items.order_item_name							AS order_item_name
			
			,CONCAT(woocommerce_order_items.order_id,'-',woocommerce_order_items.order_item_name)	AS _group_column
			";

    if ($join_country) {
        $sql .= ", postmeta4.meta_value 								AS billing_country";
    }

    if ($pw_tax_group_by == "tax_group_by_state" || $pw_tax_group_by == "tax_group_by_state_summary") {
        $sql .= ", postmeta3.meta_value 								AS billing_state";
    }

    if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
        $sql .= ", postmeta5.meta_value 								AS tax_city";
        $sql .= ", postmeta3.meta_value 								AS billing_state";
    }

    if ($pw_tax_group_by == "tax_group_by_zip") {
        $sql .= ", postmeta6.meta_value 								AS billing_postcode";
    }

    switch ($pw_tax_group_by) {
        case "tax_group_by_zip":
            $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta6.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
            break;
        case "tax_group_by_city":
            $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta5.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
            break;
        case "tax_group_by_state":
            $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta3.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
            break;
        case "tax_group_by_country":
            $group_sql = ", CONCAT(postmeta4.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
            break;
        case "tax_group_by_tax_name":
            $group_sql = ", CONCAT(woocommerce_tax_rates.tax_rate_name,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate,'-',postmeta4.meta_value) as group_column";
            break;
        case "tax_group_by_tax_summary":
            $group_sql = ", CONCAT(woocommerce_tax_rates.tax_rate_name,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name) as group_column";
            break;
        case "tax_group_by_city_summary":
            $group_sql = ", CONCAT(postmeta4.meta_value,'',postmeta5.meta_value) as group_column";
            break;
        case "tax_group_by_state_summary":
            $group_sql = ", CONCAT(postmeta4.meta_value,'',postmeta3.meta_value) as group_column";
            break;
        case "tax_group_by_country_summary":
            $group_sql = ", CONCAT(postmeta4.meta_value) as group_column";
            break;
        default:
            $group_sql = ", CONCAT(woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate,'-',postmeta4.meta_value,'-',postmeta3.meta_value) as group_column";
            break;

    }

    $sql .= $group_sql;

    $sql .= " FROM {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items";

    $sql .= " LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=	woocommerce_order_items.order_id";

    if (($pw_id_order_status && $pw_id_order_status != '-1') || $sort_by == "status") {
        $sql .= " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
				
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";

        if ($sort_by == "status") {
            $sql .= " LEFT JOIN  {$wpdb->prefix}terms 				as terms 				ON terms.term_id					=	term_taxonomy.term_id";
        }
    }

    $sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_tax_amount ON woocommerce_order_itemmeta_tax_amount.order_item_id=woocommerce_order_items.order_item_id";

    $sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_shipping_tax_amount ON woocommerce_order_itemmeta_shipping_tax_amount.order_item_id=woocommerce_order_items.order_item_id";

    $sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_tax ON woocommerce_order_itemmeta_tax.order_item_id=woocommerce_order_items.order_item_id";

    $sql .= " LEFT JOIN  {$wpdb->prefix}woocommerce_tax_rates as woocommerce_tax_rates ON woocommerce_tax_rates.tax_rate_id=woocommerce_order_itemmeta_tax.meta_value";

    if ($join_country) {
        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta4 ON postmeta4.post_id=woocommerce_order_items.order_id";
    }

    if (
        $pw_tax_group_by == "tax_group_by_state"
        || $pw_tax_group_by == "tax_group_by_state_summary"
        || $pw_tax_group_by == "tax_group_by_city_summary"
        || $pw_tax_group_by == "tax_group_by_city"
        || ( ! empty($state_code) and $state_code != "-1"
        )) {
        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta3 ON postmeta3.post_id=woocommerce_order_items.order_id";
    }

    if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta5 ON postmeta5.post_id=woocommerce_order_items.order_id";
    }

    if ($pw_tax_group_by == "tax_group_by_zip") {
        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta6 ON postmeta6.post_id=woocommerce_order_items.order_id";
    }

    $sql_joins = $pw_tax_group_by_join;


    $sql .= " WHERE 1*1 AND woocommerce_order_items.order_item_type = 'tax'";

    $sql .= " AND posts.post_type='shop_order' ";

    $sql .= " AND woocommerce_order_itemmeta_tax.meta_key='rate_id' ";

    $sql .= " AND woocommerce_order_itemmeta_tax_amount.meta_key='tax_amount' ";

    $sql .= " AND woocommerce_order_itemmeta_shipping_tax_amount.meta_key='shipping_tax_amount' ";


    if ($join_country) {
        $sql .= " AND postmeta4.meta_key='_shipping_country'";
    }
    if ($pw_tax_group_by == "tax_group_by_state" || $pw_tax_group_by == "tax_group_by_state_summary" || $pw_tax_group_by == "tax_group_by_city_summary"
        || $pw_tax_group_by == "tax_group_by_city" || ( ! empty($state_code) and $state_code != "-1")) {
        $sql .= " AND postmeta3.meta_key='_shipping_state'";
    }

    if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
        $sql .= " AND postmeta5.meta_key='_shipping_city'";
    }
    if ($pw_tax_group_by == "tax_group_by_zip") {
        $sql .= " AND postmeta6.meta_key='_shipping_postcode'";
    }

    if ($pw_id_order_status && $pw_id_order_status != '-1') {
        $sql .= " AND term_taxonomy.term_id IN (" . $pw_id_order_status . ")";
    }

    if ($state_code and $state_code != '-1') {
        $sql .= " AND postmeta3.meta_value IN (" . $state_code . ")";
    }

    if ($pw_country_code and $pw_country_code != '-1') {
        $sql .= " AND postmeta4.meta_value IN (" . $pw_country_code . ")";
    }

    if ($pw_order_status && $pw_order_status != '-1' and $pw_order_status != "'-1'") {
        $sql .= " AND posts.post_status IN (" . $pw_order_status . ")";
    }

    if ($pw_hide_os && $pw_hide_os != '-1' and $pw_hide_os != "'-1'") {
        $sql .= " AND posts.post_status NOT IN ('" . $pw_hide_os . "')";
    }

    if ($pw_from_date != null && $pw_to_date != null) {
        $sql .= " AND (DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format'))";
    }

    $sql .= "  GROUP BY order_id, order_item_name ";

    $sql .= "  ORDER BY order_id ASC";


    if (
        $pw_tax_group_by == "tax_group_by_city_summary"
        || $pw_tax_group_by == "tax_group_by_state_summary"
        || $pw_tax_group_by == "tax_group_by_country_summary") {
        $join_country = false;

        if (
            $pw_tax_group_by == "tax_group_by_city"
            || $pw_tax_group_by == "tax_group_by_state"
            || $pw_tax_group_by == "tax_group_by_country"
            || $pw_tax_group_by == "tax_group_by_zip"
            || $pw_tax_group_by == "tax_group_by_tax_name"
            || $pw_tax_group_by == "tax_group_by_city_summary"
            || $pw_tax_group_by == "tax_group_by_state_summary"
            || $pw_tax_group_by == "tax_group_by_country_summary"
            || ( ! empty($country_code) and $country_code != "-1")
        ) {
            $join_country = true;
        }

        $sql = "  SELECT posts.ID AS order_id";

        $sql .= ", order_tax.meta_value AS tax_amount";

        $sql .= ", order_shipping_tax.meta_value AS shipping_tax_amount";

        if ($join_country) {
            $sql .= ", postmeta4.meta_value 								AS billing_country";
        }

        if ($pw_tax_group_by == "tax_group_by_state" || $pw_tax_group_by == "tax_group_by_state_summary") {
            $sql .= ", postmeta3.meta_value 								AS billing_state";
        }

        if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
            $sql .= ", postmeta5.meta_value 								AS tax_city";
            $sql .= ", postmeta3.meta_value 								AS billing_state";
        }

        if ($pw_tax_group_by == "tax_group_by_zip") {
            $sql .= ", postmeta6.meta_value 								AS billing_postcode";
        }

        switch ($pw_tax_group_by) {
            case "tax_group_by_zip":
                $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta6.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
                break;
            case "tax_group_by_city":
                $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta5.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
                break;
            case "tax_group_by_state":
                $group_sql = ", CONCAT(postmeta4.meta_value,'-',postmeta3.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
                break;
            case "tax_group_by_country":
                $group_sql = ", CONCAT(postmeta4.meta_value,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate) as group_column";
                break;
            case "tax_group_by_tax_name":
                $group_sql = ", CONCAT(woocommerce_tax_rates.tax_rate_name,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate,'-',postmeta4.meta_value) as group_column";
                break;
            case "tax_group_by_tax_summary":
                $group_sql = ", CONCAT(woocommerce_tax_rates.tax_rate_name,'-',lpad(woocommerce_tax_rates.tax_rate,3,'0'),'-',woocommerce_order_items.order_item_name) as group_column";
                break;
            case "tax_group_by_city_summary":
                $group_sql = ", CONCAT(postmeta4.meta_value,'',postmeta5.meta_value) as group_column";
                break;
            case "tax_group_by_state_summary":
                $group_sql = ", CONCAT(postmeta4.meta_value,'',postmeta3.meta_value) as group_column";
                break;
            case "tax_group_by_country_summary":
                $group_sql = ", CONCAT(postmeta4.meta_value) as group_column";
                break;
            default:
                $group_sql = ", CONCAT(woocommerce_order_items.order_item_name,'-',woocommerce_tax_rates.tax_rate_name,'-',woocommerce_tax_rates.tax_rate,'-',postmeta4.meta_value,'-',postmeta3.meta_value) as group_column";
                break;

        }

        $sql .= $group_sql;

        $sql .= " FROM {$wpdb->prefix}posts as posts";

        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as order_tax ON order_tax.post_id=posts.ID";

        $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as order_shipping_tax ON order_shipping_tax.post_id=posts.ID";

        if (($pw_id_order_status && $pw_id_order_status != '-1') || $sort_by == "status") {
            $sql .= " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
				
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";

            if ($sort_by == "status") {
                $sql .= " LEFT JOIN  {$wpdb->prefix}terms 				as terms 				ON terms.term_id					=	term_taxonomy.term_id";
            }
        }

        if ($join_country) {
            $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta4 ON postmeta4.post_id=posts.ID";
        }

        if (
            $pw_tax_group_by == "tax_group_by_state"
            || $pw_tax_group_by == "tax_group_by_state_summary"
            || $pw_tax_group_by == "tax_group_by_city_summary"
            || $pw_tax_group_by == "tax_group_by_city"
            || ( ! empty($state_code) and $state_code != "-1"
            )) {
            $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta3 ON postmeta3.post_id=posts.ID";
        }

        if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
            $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta5 ON postmeta5.post_id=posts.ID";
        }

        if ($pw_tax_group_by == "tax_group_by_zip") {
            $sql .= " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta6 ON postmeta6.post_id=posts.ID";
        }

        $sql .= " WHERE 1*1 ";

        $sql .= " AND posts.post_type='shop_order' ";

        $sql .= " AND order_tax.meta_key='_order_tax' ";

        $sql .= " AND order_shipping_tax.meta_key='_order_shipping_tax' ";


        if ($join_country) {
            $sql .= " AND postmeta4.meta_key='_shipping_country'";
        }
        if ($pw_tax_group_by == "tax_group_by_state" || $pw_tax_group_by == "tax_group_by_state_summary" || $pw_tax_group_by == "tax_group_by_city_summary"
            || $pw_tax_group_by == "tax_group_by_city" || ( ! empty($state_code) and $state_code != "-1")) {
            $sql .= " AND postmeta3.meta_key='_shipping_state'";
        }

        if ($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary") {
            $sql .= " AND postmeta5.meta_key='_shipping_city'";
        }
        if ($pw_tax_group_by == "tax_group_by_zip") {
            $sql .= " AND postmeta6.meta_key='_shipping_postcode'";
        }


        if ($pw_id_order_status && $pw_id_order_status != '-1') {
            $sql .= " AND term_taxonomy.term_id IN (" . $pw_id_order_status . ")";
        }

        if ($state_code and $state_code != '-1') {
            $sql .= " AND postmeta3.meta_value IN (" . $state_code . ")";
        }

        if ($pw_country_code and $pw_country_code != '-1') {
            $sql .= " AND postmeta4.meta_value IN (" . $pw_country_code . ")";
        }

        if ($pw_order_status && $pw_order_status != '-1' and $pw_order_status != "'-1'") {
            $sql .= " AND posts.post_status IN (" . $pw_order_status . ")";
        }

        if ($pw_hide_os && $pw_hide_os != '-1' and $pw_hide_os != "'-1'") {
            $sql .= " AND posts.post_status NOT IN ('" . $pw_hide_os . "')";
        }

        if ($pw_from_date != null && $pw_to_date != null) {
            $sql .= " AND (DATE(posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format'))";
        }

        $sql .= "  GROUP BY order_id ";

        $sql .= "  ORDER BY order_id ASC";
    }


//		echo $sql;

    $c = $pw_tax_group_by;


    if ($c == 'tax_group_by_city') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'billing_state',
                'lable'  => __('Tax State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array('id' => 'tax_city', 'lable' => __('Tax City', __PW_REPORT_WCREPORT_TEXTDOMAIN__), 'status' => 'show'),
            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_code',
                'lable'  => __('Tax Rate Code', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } elseif ($c == 'tax_group_by_state') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'billing_state',
                'lable'  => __('Tax State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_code',
                'lable'  => __('Tax Rate Code', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );


    } elseif ($c == 'tax_group_by_country') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_code',
                'lable'  => __('Tax Rate Code', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } elseif ($c == 'tax_group_by_zip') {

        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'billing_postcode',
                'lable'  => __('Tax Zip', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_code',
                'lable'  => __('Tax Rate Code', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } elseif ($c == 'tax_group_by_tax_name') {
        $columns = array(

            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'tax_rate_code',
                'lable'  => __('Tax Rate Code', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );


    } elseif ($c == 'tax_group_by_tax_summary') {
        $columns = array(

            array(
                'id'     => 'tax_rate_name',
                'lable'  => __('Tax Name', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );


    } elseif ($c == 'tax_group_by_city_summary') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'billing_state',
                'lable'  => __('Tax State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array('id' => 'tax_city', 'lable' => __('Tax City', __PW_REPORT_WCREPORT_TEXTDOMAIN__), 'status' => 'show'),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } elseif ($c == 'tax_group_by_state_summary') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'billing_state',
                'lable'  => __('Tax State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } elseif ($c == 'tax_group_by_country_summary') {
        $columns = array(

            array(
                'id'     => 'billing_country',
                'lable'  => __('Tax Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_count',
                'lable'  => __('Order Count', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_shipping',
                'lable'  => __('Shipping Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'gross_amount',
                'lable'  => __('Gross Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'order_total',
                'lable'  => __('Net Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'refund_order_total',
                'lable'  => __('Part Refund.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'show'
            ),
            array(
                'id'     => 'net_order_total',
                'lable'  => __('(Net- Refund) Amt.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )
        );

    } else {
        $columns = array(

            array(
                'id'     => 'order_tax_rate',
                'lable'  => __('Tax Rate', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'shipping_tax_amount',
                'lable'  => __('Shipping Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'tax_amount',
                'lable'  => __('Order Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            ),
            array(
                'id'     => 'total_tax',
                'lable'  => __('Total Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                'status' => 'currency'
            )

        );

    }

    $columns['refund_tax_amount']          = array(
        'id'     => 'total_tax',
        'lable'  => __("Refund Tax", '__PW_REPORT_WCREPORT_TEXTDOMAIN__'),
        'status' => 'currency'
    );
    $columns['refund_shipping_tax_amount'] = array(
        'id'     => 'total_tax',
        'lable'  => __("Refund Shipping Tax", '__PW_REPORT_WCREPORT_TEXTDOMAIN__'),
        'status' => 'currency'
    );
    $columns['total_tax_refund']           = array(
        'id'     => 'total_tax',
        'lable'  => __("Total Tax Refund", '__PW_REPORT_WCREPORT_TEXTDOMAIN__'),
        'status' => 'currency'
    );
    $columns['net_total_tax']              = array(
        'id'     => 'total_tax',
        'lable'  => __("Net Total Tax", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
        'status' => 'currency'
    );


    $this->table_cols = $columns;

} elseif ($file_used == "data_table") {

    $type            = 'limit_row';
    $pw_tax_group_by = $this->pw_get_woo_requests('pw_tax_groupby', 'tax_group_by_state', true);

    $pw_order_status = $this->pw_get_woo_requests('pw_orders_status', '-1', true);
    if ($pw_order_status != null && $pw_order_status != '-1') {
        $pw_order_status = "'" . str_replace(",", "','", $pw_order_status) . "'";
    }
    $pw_from_date = $this->pw_get_woo_requests('pw_from_date', null, true);
    $pw_to_date   = $this->pw_get_woo_requests('pw_to_date', null, true);

    $args = array(
        'pw_from_date' => $pw_from_date,
        'pw_to_date'   => $pw_to_date,
        'order_status' => $pw_order_status,
    );

    $order_items = $this->results;

    //print_r($order_items);
    $totals = 0;

    if (count($order_items) > 0) {
        $post_ids         = $this->pw_list_items_id($order_items, 'order_id');
        $tax_refund_items = $this->pw_fetch_refund_tax($type, $args);


        $shop_order_refund_id_items = $this->pw_refund_id_of_shop_order($type, $post_ids);

        $extra_meta_keys = array(
            'order_total',
            'order_shipping',
            'cart_discount',
            'order_discount',
            'order_tax',
            'order_shipping_tax'
        );

        $postmeta_datas = $this->pw_get_post_meta($post_ids, array(), $extra_meta_keys, 'total');

        $order_ids = array();
        foreach ($order_items as $key => $order_item) {
            $order_id      = $order_item->order_id;
            $_group_column = $order_item->_group_column;

            $postmeta_data = isset($postmeta_datas[$order_id]) ? $postmeta_datas[$order_id] : array();


            $refund_order_item = isset($tax_refund_items[$_group_column]) ? $tax_refund_items[$_group_column] : array();

            $order_items[$key]->refund_tax_amount          = isset($refund_order_item->refund_tax_amount) ? $refund_order_item->refund_tax_amount : 0;
            $order_items[$key]->refund_shipping_tax_amount = isset($refund_order_item->refund_shipping_tax_amount) ? $refund_order_item->refund_shipping_tax_amount : 0;

            $order_items[$key]->refund_tax_amount          = str_replace('-', '',
                $order_items[$key]->refund_tax_amount);
            $order_items[$key]->refund_shipping_tax_amount = str_replace('-', '',
                $order_items[$key]->refund_shipping_tax_amount);

            foreach ($postmeta_data as $postmeta_key => $postmeta_value) {
                $order_items[$key]->{$postmeta_key} = $postmeta_value;
            }
            $order_items[$key]->order_total    = isset($order_items[$key]->order_total) ? $order_items[$key]->order_total : 0;
            $order_items[$key]->order_shipping = isset($order_items[$key]->order_shipping) ? $order_items[$key]->order_shipping : 0;

            $order_items[$key]->cart_discount  = isset($order_items[$key]->cart_discount) ? $order_items[$key]->cart_discount : 0;
            $order_items[$key]->order_discount = isset($order_items[$key]->order_discount) ? $order_items[$key]->order_discount : 0;
            $order_items[$key]->total_discount = ($order_items[$key]->cart_discount + $order_items[$key]->order_discount);

            $order_items[$key]->order_tax          = isset($order_items[$key]->order_tax) ? $order_items[$key]->order_tax : 0;
            $order_items[$key]->order_shipping_tax = isset($order_items[$key]->order_shipping_tax) ? $order_items[$key]->order_shipping_tax : 0;


            $order_items[$key]->gross_amount = ($order_items[$key]->order_total + $order_items[$key]->total_discount) - ($order_items[$key]->order_shipping + $order_items[$key]->order_shipping_tax + $order_items[$key]->order_tax);
            $order_ids[$order_id]            = $order_id;


            $refund_postmeta_data = isset($shop_order_refund_id_items[$order_id]) ? $shop_order_refund_id_items[$order_id] : array();

            foreach ($refund_postmeta_data as $postmeta_key => $postmeta_value) {
                $order_items[$key]->{$postmeta_key} = $postmeta_value;
            }

            $order_items[$key]->refund_order_total = isset($order_items[$key]->refund_order_total) ? $order_items[$key]->refund_order_total : 0;

            $order_items[$key]->net_order_total  = $order_items[$key]->order_total + $order_items[$key]->refund_order_total;
            $order_items[$key]->total_tax_refund = $order_items[$key]->refund_tax_amount + $order_items[$key]->refund_shipping_tax_amount;

            $order_items[$key]->total_tax     = $order_items[$key]->tax_amount + $order_items[$key]->shipping_tax_amount;
            $order_items[$key]->net_total_tax = $order_items[$key]->total_tax - $order_items[$key]->total_tax_refund;
        }

        $order_ids = array();

        $lists = array();
        foreach ($order_items as $key => $order_item) {
            $group_column = $order_item->group_column;

            if (isset($lists[$group_column])) {
                $lists[$group_column]->order_total        = $lists[$group_column]->order_total + $order_items[$key]->order_total;
                $lists[$group_column]->order_shipping     = $lists[$group_column]->order_shipping + $order_items[$key]->order_shipping;
                $lists[$group_column]->cart_discount      = $lists[$group_column]->cart_discount + $order_items[$key]->cart_discount;
                $lists[$group_column]->order_discount     = $lists[$group_column]->order_discount + $order_items[$key]->order_discount;
                $lists[$group_column]->total_discount     = $lists[$group_column]->total_discount + $order_items[$key]->total_discount;
                $lists[$group_column]->order_tax          = $lists[$group_column]->order_tax + $order_items[$key]->order_tax;
                $lists[$group_column]->order_shipping_tax = $lists[$group_column]->order_shipping_tax + $order_items[$key]->order_shipping_tax;
                $lists[$group_column]->total_tax          = $lists[$group_column]->total_tax + $order_items[$key]->total_tax;
                $lists[$group_column]->gross_amount       = $lists[$group_column]->gross_amount + $order_items[$key]->gross_amount;

                $lists[$group_column]->order_tax = $lists[$group_column]->order_tax + $order_items[$key]->order_tax;

                $lists[$group_column]->tax_amount          = $lists[$group_column]->tax_amount + $order_items[$key]->tax_amount;
                $lists[$group_column]->shipping_tax_amount = $lists[$group_column]->shipping_tax_amount + $order_items[$key]->shipping_tax_amount;


                $lists[$group_column]->refund_tax_amount          = $lists[$group_column]->refund_tax_amount + $order_items[$key]->refund_tax_amount;
                $lists[$group_column]->refund_shipping_tax_amount = $lists[$group_column]->refund_shipping_tax_amount + $order_items[$key]->refund_shipping_tax_amount;
                $lists[$group_column]->total_tax_refund           = $lists[$group_column]->total_tax_refund + $order_items[$key]->total_tax_refund;

                $lists[$group_column]->order_count = $lists[$group_column]->order_count + 1;

                $lists[$group_column]->refund_order_total = $lists[$group_column]->refund_order_total + $order_items[$key]->refund_order_total;

                $lists[$group_column]->net_order_total = $lists[$group_column]->net_order_total + $order_items[$key]->net_order_total;

                $lists[$group_column]->net_total_tax = $lists[$group_column]->net_total_tax + $order_items[$key]->net_total_tax;

            } else {

                $lists[$group_column] = new stdClass();

                $lists[$group_column]->order_count        = 1;
                $lists[$group_column]->order_total        = $order_items[$key]->order_total;
                $lists[$group_column]->order_shipping     = $order_items[$key]->order_shipping;
                $lists[$group_column]->cart_discount      = $order_items[$key]->cart_discount;
                $lists[$group_column]->order_discount     = $order_items[$key]->order_discount;
                $lists[$group_column]->total_discount     = $order_items[$key]->total_discount;
                $lists[$group_column]->order_tax          = $order_items[$key]->order_tax;
                $lists[$group_column]->order_shipping_tax = $order_items[$key]->order_shipping_tax;
                $lists[$group_column]->total_tax          = $order_items[$key]->total_tax;
                $lists[$group_column]->gross_amount       = $order_items[$key]->gross_amount;

                $lists[$group_column]->order_tax           = $order_items[$key]->order_tax;
                $lists[$group_column]->tax_amount          = $order_items[$key]->tax_amount;
                $lists[$group_column]->shipping_tax_amount = $order_items[$key]->shipping_tax_amount;
                $lists[$group_column]->total_tax           = $order_items[$key]->total_tax;

                $lists[$group_column]->refund_tax_amount          = $order_items[$key]->refund_tax_amount;
                $lists[$group_column]->refund_shipping_tax_amount = $order_items[$key]->refund_shipping_tax_amount;
                $lists[$group_column]->total_tax_refund           = $order_items[$key]->total_tax_refund;


                $lists[$group_column]->tax_rate_code    = isset($order_items[$key]->tax_rate_code) ? $order_items[$key]->tax_rate_code : '';
                $lists[$group_column]->tax_rate_name    = isset($order_items[$key]->tax_rate_name) ? $order_items[$key]->tax_rate_name : '';
                $lists[$group_column]->order_tax_rate   = isset($order_items[$key]->order_tax_rate) ? $order_items[$key]->order_tax_rate : '';
                $lists[$group_column]->billing_country  = isset($order_items[$key]->billing_country) ? $order_items[$key]->billing_country : '';
                $lists[$group_column]->billing_state    = isset($order_items[$key]->billing_state) ? $order_items[$key]->billing_state : '';
                $lists[$group_column]->billing_postcode = isset($order_items[$key]->billing_postcode) ? $order_items[$key]->billing_postcode : '';
                $lists[$group_column]->tax_city         = isset($order_items[$key]->tax_city) ? $order_items[$key]->tax_city : '';

                $lists[$group_column]->group_column       = $group_column;
                $lists[$group_column]->refund_order_total = $order_items[$key]->refund_order_total;
                $lists[$group_column]->net_order_total    = $order_items[$key]->net_order_total;
                $lists[$group_column]->net_total_tax      = $order_items[$key]->net_total_tax;

            }
        }
        $order_items = $lists;

        foreach ($order_items as $key => $order_item) {
            $order_items[$key]->refund_tax_amount          = -$order_items[$key]->refund_tax_amount;
            $order_items[$key]->refund_shipping_tax_amount = -$order_items[$key]->refund_shipping_tax_amount;
            $order_items[$key]->total_tax_refund           = -$order_items[$key]->total_tax_refund;
        }
    }
//die(print_r($order_items));

    if (is_array($order_items) && count($order_item) > 0) {
        $datatable_value .= $this->pw_get_maincontent_grid($order_items, $pw_tax_group_by);
    }


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
            </div>

            <div class="col-md-6">
                <div class="awr-form-title">
                    <?php _e('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-globe"></i></span>
                <?php
                $country_data = $this->pw_get_paying_woo_state('shipping_country');

                $option = '';
                //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                //echo $current_product;

                foreach ($country_data as $country) {
                    $selected = '';

                    $option .= "<option $selected value='" . $country->id . "' >" . $country->label . " </option>";
                }
                ?>

                <select name="pw_countries_code[]" multiple="multiple" size="5" data-size="5"
                        class="chosen-select-search">

                    <option value="-1"><?php _e('Select All', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>

                    <?php
                    echo $option;
                    ?>
                </select>

            </div>


            <div class="col-md-6">
                <div class="awr-form-title">
                    <?php _e('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-map"></i></span>
                <?php
                $state_code = '-1';
                //$state_data = $this->pw_get_paying_woo_state('billing_state','billing_country');
                $state_data = $this->pw_get_paying_woo_state('shipping_state', 'shipping_country');
                //print_r($state_data);
                $option = '';
                //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                //echo $current_product;

                foreach ($state_data as $state) {
                    $selected = '';
                    $option   .= "<option $selected value='" . $state->id . "' >" . $state->label . " </option>";
                }
                ?>

                <select name="pw_states_code[]" multiple="multiple" size="5" data-size="5" class="chosen-select-search">

                    <option value="-1"><?php _e('Select All', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>

                    <?php
                    echo $option;
                    ?>
                </select>

            </div>


            <div class="col-md-6">
                <div class="awr-form-title">
                    <?php _e('Tax Group By', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                </div>
                <span class="awr-form-icon"><i class="fa fa-suitcase"></i></span>
                <select name="pw_tax_groupby" id="pw_tax_groupby" class="pw_tax_groupby">
                    <option value="tax_group_by_city"><?php _e('City', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_state" selected="selected"><?php _e('State',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_country"><?php _e('Country',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_zip"><?php _e('Zip Code',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_tax_name"><?php _e('Tax Name',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_tax_summary"><?php _e('Tax Sumary',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                        <m
                        /option>
                    <option value="tax_group_by_city_summary"><?php _e('City Summary',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_state_summary"><?php _e('State Summary',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                    <option value="tax_group_by_country_summary"><?php _e('Country Summary',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                </select>

            </div>

            <?php
            $col_style        = '';
            $permission_value = $this->get_form_element_value_permission('pw_orders_status');
            if ($this->get_form_element_permission('pw_orders_status') || $permission_value != '') {

                if ( ! $this->get_form_element_permission('pw_orders_status') && $permission_value != '') {
                    $col_style = 'display:none';
                }
                ?>

                <div class="col-md-6" style=" <?php echo $col_style; ?>">
                    <div class="awr-form-title">
                        <?php _e('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <?php
                    $pw_order_status = $this->pw_get_woo_orders_statuses();

                    ////ADDED IN VER4.0
                    /// APPLY DEFAULT STATUS AT FIRST
                    $shop_status_selected = '';
                    if ($this->pw_shop_status) {
                        $shop_status_selected = explode(",", $this->pw_shop_status);
                    }

                    $option = '';
                    foreach ($pw_order_status as $key => $value) {
                        $selected = '';
                        //CHECK IF IS IN PERMISSION
                        if (is_array($permission_value) && ! in_array($key, $permission_value)) {
                            continue;
                        }

                        /*if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
                            $selected="selected";*/

                        ////ADDED IN VER4.0
                        /// APPLY DEFAULT STATUS AT FIRST
                        if (is_array($shop_status_selected) && in_array($key, $shop_status_selected)) {
                            $selected = "selected";
                        }

                        $option .= "<option value='" . $key . "' $selected >" . $value . "</option>";
                    }
                    ?>

                    <select name="pw_orders_status[]" multiple="multiple" size="5" data-size="5"
                            class="chosen-select-search">
                        <?php
                        if ($this->get_form_element_permission('pw_orders_status') && (( ! is_array($permission_value)) || (is_array($permission_value) && in_array('all',
                                        $permission_value)))) {
                            ?>
                            <option value="-1"><?php _e('Select All', __PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></option>
                            <?php
                        }
                        ?>
                        <?php
                        echo $option;
                        ?>
                    </select>
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                </div>
                <?php
            }
            ?>

        </div>

        <div class="col-md-12">
            <?php
            $pw_hide_os       = $this->otder_status_hide;
            $pw_publish_order = 'no';

            $data_format = $this->pw_get_woo_requests_links('date_format', get_option('date_format'), true);
            ?>
            <input type="hidden" name="list_parent_category" value="">
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