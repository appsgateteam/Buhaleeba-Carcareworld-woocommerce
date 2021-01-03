<?php

	////ADDED IN VER4.0
	/// INTELLIGENCE REPORTS
	add_action('wp_ajax_pw_chosen_ajax', 'pw_chosen_ajax');
	add_action('wp_ajax_nopriv_pw_chosen_ajax', 'pw_chosen_ajax');
	function pw_chosen_ajax() {
		global $wpdb,$pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		global $wpdb;

		parse_str($_REQUEST['postdata'], $my_array_of_vars);

		$nonce = $_REQUEST['nonce'];

		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
				'success'=>'no-nonce',
				'products' => array()
			);
			print_r($arr);
			die();
		}

		//die(print_r($my_array_of_vars));

		global $pw_rpt_main_class;
		$data='';
		switch ($my_array_of_vars['target_type']){
			case 'simple_products':
				$data=$pw_rpt_main_class->pw_get_product_woo_data_chosen('simple',false,$my_array_of_vars['q']);
			break;

			case 'variable_products':
				$data=$pw_rpt_main_class->pw_get_product_woo_data_chosen('1',false,$my_array_of_vars['q']);
				break;

			case 'all_products':
				$data=$pw_rpt_main_class->pw_get_product_woo_data_chosen('0',false,$my_array_of_vars['q']);
				break;

			case 'customer';
				$data=$pw_rpt_main_class->pw_get_woo_customers_orders('shop_order','no',$my_array_of_vars['q']);
			break;
		}




		echo json_encode($data);

		die(0);


	}


	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	function pw_rpt_fetch_data() {
		global $wpdb;

		parse_str($_REQUEST['postdata'], $my_array_of_vars);

		$nonce = $_POST['nonce'];

		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);

		//echo $sql;

		//$products = $wpdb->get_results($sql);

		global $pw_rpt_main_class;

		//$table_name=$my_array_of_vars['table_name'];
		$table_name=$my_array_of_vars['table_names'];

		if($table_name=='int_reports_sales' || $table_name=='int_reports_products' || $table_name=='int_reports_customers' || $table_name=='int_reports_home' || $table_name=='int_reports_transactions')
		{
			$pw_rpt_main_class->table_html_intelligence($table_name,$my_array_of_vars);
		}else{
            $pw_rpt_main_class->table_html($table_name,$my_array_of_vars);
		}

		die();
	}


	////ADDED IN VER4.0
	/// INTELLIGENCE REPORTS
	add_action('wp_ajax_pw_rpt_int_customer_details', 'pw_rpt_int_customer_details');
	add_action('wp_ajax_nopriv_pw_rpt_int_customer_details', 'pw_rpt_int_customer_details');
	function pw_rpt_int_customer_details() {
		global $wpdb,$pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		global $pw_rpt_main_class;
		//print_r( $my_array_of_vars );
		$order_id=$my_array_of_vars['row_id'];
		include("Intelligence/fetch_data_int_reports_orders_details.php");

		die(0);
	}


	add_action('wp_ajax_pw_rpt_int_add_note', 'pw_rpt_int_add_note');
	add_action('wp_ajax_nopriv_pw_rpt_int_add_note', 'pw_rpt_int_add_note');
	function pw_rpt_int_add_note() {
		global $wpdb,$pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		global $pw_rpt_main_class;
		$id=$my_array_of_vars['id'];
		$target=$my_array_of_vars['target'];
		$note_text=$my_array_of_vars['note_text'];
		if($target=='order'){
			$order = new WC_Order($id);
			$order->add_order_note($note_text,0,true);
		}elseif ($target=='product'){
			update_post_meta($id,"_purchase_note",$note_text);
		}

		echo __('New note has been saved.',__PW_REPORT_WCREPORT_TEXTDOMAIN__);

		die(0);
	}

	add_action('wp_ajax_pw_rpt_int_change_order_staus', 'pw_rpt_int_change_order_staus');
	add_action('wp_ajax_nopriv_pw_rpt_int_change_order_staus', 'pw_rpt_int_change_order_staus');
	function pw_rpt_int_change_order_staus() {
		global $wpdb, $pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		//print_r($my_array_of_vars);

		$order_id=$my_array_of_vars['order_id'];
		$status=$my_array_of_vars['status'];
		$order = new WC_Order($order_id);
		$order->update_status($status, 'order_note');

		die(0);
	}


	add_action('wp_ajax_pw_rpt_fetch_single_customer', 'pw_rpt_fetch_single_customer');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_single_customer', 'pw_rpt_fetch_single_customer');
	function pw_rpt_fetch_single_customer() {
		global $wpdb, $pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		//print_r($my_array_of_vars);
		$page_id=$my_array_of_vars['page_id'];
		$customer_id=$my_array_of_vars['customer_id'];
		$customer_email=$my_array_of_vars['customer_email'];
		$customer_segment=$my_array_of_vars['customer_segment'];
		include("Intelligence/fetch_data_int_reports_customers_single.php");

		die(0);
	}


	add_action('wp_ajax_pw_rpt_fetch_single_product', 'pw_rpt_fetch_single_product');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_single_product', 'pw_rpt_fetch_single_product');
	function pw_rpt_fetch_single_product() {
		global $wpdb, $pw_rpt_main_class;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		//print_r($my_array_of_vars);
		$product_id=$my_array_of_vars['product_id'];
		$product_rank_type=$my_array_of_vars['product_rank_type'];
		$product_rank_val=$my_array_of_vars['product_rank_val'];
		$product_rank_title=$my_array_of_vars['product_rank_title'];

		$pw_from_date		  = $my_array_of_vars['pw_from_date'];
		$pw_to_date			= $my_array_of_vars['pw_to_date'];
		$total_products_amnt			= $my_array_of_vars['total_products_amnt'];
		$total_products_refund_amnt			= $my_array_of_vars['total_products_refund_amnt'];
//		$order_products			= $my_array_of_vars['orders_products_arr'];
//		$order_products=json_decode(stripslashes($order_products));
//		//print_r($order_products);

		include("Intelligence/fetch_data_int_reports_products_single.php");

		die(0);
	}

	////ADDED IN VER4.0
	/// PDF GENERATOR
	add_action('wp_ajax_pw_rpt_add_fav_menu', 'pw_rpt_add_fav_menu');
	add_action('wp_ajax_nopriv_pw_rpt_add_fav_menu', 'pw_rpt_add_fav_menu');
	function pw_rpt_add_fav_menu() {
		global $wpdb;

		parse_str( $_REQUEST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		global $pw_rpt_main_class;

		$current_user = wp_get_current_user();
		$user_info = $current_user->user_login;

		$smenu = $my_array_of_vars['smenu'];
		$current_fav_menus=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__."fav_menus_".$user_info);
		if(is_array($current_fav_menus) && in_array($smenu,$current_fav_menus)){

			if(($key = array_search($smenu, $current_fav_menus)) !== false) {
				unset($current_fav_menus[$key]);
			}

			update_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__."fav_menus_".$user_info,$current_fav_menus);
		}elseif(is_array($current_fav_menus)){
			array_push($current_fav_menus,$smenu);
			update_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__."fav_menus_".$user_info,$current_fav_menus);
		}else{
			update_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__."fav_menus_".$user_info,array($smenu));
		}
		echo $smenu;
		die(0);
	}

	function toAscii($str) {
		$clean = preg_replace("/[^a-zA-Z0-9/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[/_|+ -]+/", '-', $clean);

		return $clean;
	}
	////ADDED IN VER4.0
	/// PDF GENERATOR
	add_action('wp_ajax_pw_rpt_pdf_generator', 'pw_rpt_pdf_generator');
	add_action('wp_ajax_nopriv_pw_rpt_pdf_generator', 'pw_rpt_pdf_generator');
	function pw_rpt_pdf_generator() {
		global $wpdb;

		parse_str($_REQUEST['postdata'], $my_array_of_vars);

		$nonce = $_POST['nonce'];

		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
				'success'=>'no-nonce',
				'products' => array()
			);
			print_r($arr);
			die();
		}

		//echo $sql;

		//$products = $wpdb->get_results($sql);

		global $pw_rpt_main_class;

		$table_name=$my_array_of_vars['table_names'];


		$selected_cols=array("order_id","category_name","amount",'product_name','date','email');
		$pw_rpt_main_class->search_form_fields=$my_array_of_vars;
		$sql =$pw_rpt_main_class->fetch_sql($table_name,$my_array_of_vars);

		if(is_array($sql) && count($sql)>0 || $table_name=='projected_actual_sale'){

			//Solution 1
			//$html=$pw_rpt_main_class->table_html($table_name,$my_array_of_vars,"pdf");

			//Solution 2
			$datatable_value='';
			//$file_used="data_table";
			//$pw_rpt_main_class->result=$sql;
			//die(plugin_dir_path( __FILE__ )."/fetch_data_dashboard_report.php");
			$html=$pw_rpt_main_class->table_html_pdf($table_name,$my_array_of_vars,$selected_cols);

			//$html='<table class="display datatable" cellspacing="0" width="100%">'.$datatable_value.'</table>';
		}
		else
			$html= "No DatA !";

		echo ($html);
		die();
	}


	
	//FETCH CUSTOM FIELD IN SETTINGS
	function get_operation($fields){
		$operators=array(
			"Numeric" 	=> array(
							"eq"=>__('EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"neq"=>__('NOT EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"lt"=>__('LESS THEN',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"gt"=>__('MORE THEN',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"meq"=>__('EQUAL AND MORE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"leq"=>__('LESS AND EQUAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						),
			"String"	=>  array(
							"elike"=>__('EXACTLY LIKE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"like"=>__('LIKE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						),			
		);
		$operators_options='';
		foreach($operators as $key=>$value){
			$operators_options.='<optgroup label="'.$key.' operators">';
			foreach($value as $k=>$v){
				
				$selected="";
				if($fields==$k){
					$selected="SELECTED";
				}
				$operators_options.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
			}
			$operators_options.='</optgroup>';
		}
		return $operators_options;
	}
	
	add_action('wp_ajax_pw_rpt_fetch_custom_fields', 'pw_rpt_fetch_custom_fields');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_custom_fields', 'pw_rpt_fetch_custom_fields');
	function pw_rpt_fetch_custom_fields(){
		//print_r($_POST);
		$html='';
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		$field_id=$my_array_of_vars['field'];
		if(isset($my_array_of_vars[$field_id]))
		{
			$custom_fiels = $my_array_of_vars[$field_id];

			foreach($custom_fiels as $fields){
				$meta_column=isset($my_array_of_vars[$fields.'_column']) ? $my_array_of_vars[$fields.'_column'] : "";

				$meta_translate=isset($my_array_of_vars[$fields.'_translate']) ? $my_array_of_vars[$fields.'_translate'] : "";
				$meta_operator=isset($my_array_of_vars[$fields.'_operator']) ? $my_array_of_vars[$fields.'_operator'] : "";

				$label=str_replace("@"," ",$fields);


				$html.='
				<div class="col-xs-12 pw-translate">
					<input type="hidden" name="'.$fields.'_column" placeholder="Label for '.$fields.'" value="off">
					<input type="checkbox" name="'.$fields.'_column" placeholder="Label for '.$fields.'" "'.checked("on",$meta_column,0).'"> '.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
					<br />
					<input type="text" name="'.$fields.'_translate" placeholder="Label for '.$label.'" value="'.$meta_translate.'">
					<select name="'.$fields.'_operator">
						'.get_operation($meta_operator).'
					</select>
				</div>
				<br />';
			}
		}else{
			$html=__('Please add custom field to left site',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		}
		echo $html;
		
		die();
	}

	//CUSTOM WORK - 12300
	add_action('wp_ajax_pw_rpt_fetch_custom_fields_tickera', 'pw_rpt_fetch_custom_fields_tickera');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_custom_fields_tickera', 'pw_rpt_fetch_custom_fields_tickera');
	function pw_rpt_fetch_custom_fields_tickera(){
		//print_r($_POST);
		$html='';
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		$field_id=$my_array_of_vars['field'];
		$id=$my_array_of_vars['id'];

		if(isset($my_array_of_vars[$field_id]))
		{
			$custom_fiels = $my_array_of_vars[$field_id];
			//print_r($custom_fiels);
			foreach($custom_fiels as $fieldss){

				$fieldss_e=explode("_",$fieldss);
				$fieldss=$fieldss_e[0];

				global $wpdb;
				$other_fields = $wpdb->get_results("SELECT posts.post_title as Field_Name,fmeta.meta_value as Field_Html_Name from {$wpdb->prefix}posts as posts LEFT JOIN {$wpdb->prefix}postmeta as fmeta ON posts.ID=fmeta.post_id WHERE posts.post_type='tc_form_fields' AND fmeta.meta_key='name' AND posts.ID='$fieldss'", ARRAY_A);


//print_r($other_fields);
				//foreach ($fieldss as $fields){


					$label=$other_fields[0]['Field_Name'];
					$input_name=$other_fields[0]['Field_Html_Name'];

					$meta_column=isset($my_array_of_vars[$input_name.'_column']) ? $my_array_of_vars[$input_name.'_column'] : "";
					$meta_filter=isset($my_array_of_vars[$input_name.'_filter']) ? $my_array_of_vars[$input_name.'_filter'] : "";

					$meta_translate=isset($my_array_of_vars[$input_name.'_translate']) ? $my_array_of_vars[$input_name.'_translate'] : "";
					$meta_operator=isset($my_array_of_vars[$input_name.'_operator']) ? $my_array_of_vars[$input_name.'_operator'] : "";



					$html.='
					<div class="col-xs-12 pw-translate">
						<input type="hidden" name="'.$input_name.'_column" placeholder="Label for '.$label.'" value="off">
						<input type="checkbox" name="'.$input_name.'_column" placeholder="Label for '.$label.'" "'.checked("on",$meta_column,0).'">'.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
						$html.='
						<input type="hidden" name="'.$input_name.'_filter" placeholder="Label for '.$label.'" value="off">
						<input type="checkbox" name="'.$input_name.'_filter" placeholder="Label for '.$label.'" "'.checked("on",$meta_filter,0).'">'.__("Display in Filter",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					$html.='
						<br />
						<input type="text" name="'.$input_name.'_translate" placeholder="Label for '.$label.'" value="'.$meta_translate.'">
						
					</div>
					<br />';
				//}
			}
		}else{
			$html=__('Please add custom field to left site',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		}
		echo $html;

		die();
	}


	////ADDED IN VER4.0
	/// PRODUCT OPTIONS CUSTOM FIELDS
	add_action('wp_ajax_pw_rpt_fetch_custom_fields_po', 'pw_rpt_fetch_custom_fields_po');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_custom_fields_po', 'pw_rpt_fetch_custom_fields_po');
	function pw_rpt_fetch_custom_fields_po(){
		//print_r($_POST);
		$html='';
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		$field_id=$my_array_of_vars['field'];
		$id=$my_array_of_vars['id'];

		if(isset($my_array_of_vars[$field_id]))
		{
			$custom_fiels = $my_array_of_vars[$field_id];

			foreach($custom_fiels as $fieldss){

				foreach ($fieldss as $fields){

					$label=str_replace("@"," ",$fields);
					if($id=='po_checkout_global_fields_select'){
						$exp= explode('@',$fields);
						$fields=$exp[0];
						$label=$exp[1];
						//str_replace("@"," ",$fields);
					}
					$input_name=str_replace(" ","_",$fields);

					$meta_column=isset($my_array_of_vars[$input_name.'_column']) ? $my_array_of_vars[$input_name.'_column'] : "";
					$meta_filter=isset($my_array_of_vars[$input_name.'_filter']) ? $my_array_of_vars[$input_name.'_filter'] : "";

					$meta_translate=isset($my_array_of_vars[$input_name.'_translate']) ? $my_array_of_vars[$input_name.'_translate'] : "";
					$meta_operator=isset($my_array_of_vars[$input_name.'_operator']) ? $my_array_of_vars[$input_name.'_operator'] : "";



					$html.='
					<div class="col-xs-12 pw-translate">
						<input type="hidden" name="'.$input_name.'_column" placeholder="Label for '.$fields.'" value="off">
						<input type="checkbox" name="'.$input_name.'_column" placeholder="Label for '.$fields.'" "'.checked("on",$meta_column,0).'">'.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					if($id=='po_global_fields_select' || $id=='po_checkout_global_fields_select'){
						$html.='
						<input type="hidden" name="'.$input_name.'_filter" placeholder="Label for '.$fields.'" value="off">
						<input type="checkbox" name="'.$input_name.'_filter" placeholder="Label for '.$fields.'" "'.checked("on",$meta_filter,0).'">'.__("Display in Filter",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					}
					$html.='
						<br />
						<input type="text" name="'.$input_name.'_translate" placeholder="Label for '.$label.'" value="'.$meta_translate.'">
						
					</div>
					<br />';
				}
			}
		}else{
			$html=__('Please add custom field to left site',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		}
		echo $html;

		die();
	}
	
	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	function pw_rpt_fetch_data_dashborad() {
		global $wpdb;
		
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);
		
		//echo $sql;
		
		//$products = $wpdb->get_results($sql);
		
		global $pw_rpt_main_class;
        
		echo '
		<div class="awr-box">
			<div class="awr-title">
				<h3>
					<i class="fa fa-filter"></i>
					
				</h3>
			</div><!--awr-title -->
			<div class="awr-box-content">
				<div class="col-xs-12">
					<div class="awr-box">
						<div class="awr-box-content">					
							<div id="target">'.
									$pw_rpt_main_class->table_html("dashboard_report",$my_array_of_vars).'
							</div>
						</div>
					</div>
				</div>    
			</div>
		</div>		
        
        <div class="col-md-12">'.
            $pw_rpt_main_class->table_html("monthly_summary",$my_array_of_vars).'
        </div>
		';
		
		die();
	}


	//FETCH CHART DATA
	add_action('wp_ajax_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	function pw_rpt_fetch_chart() {
		
		global $wpdb;
		global $pw_rpt_main_class;
		
		parse_str($_POST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		$type = $_POST['type'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		$pw_from_date=$my_array_of_vars['pw_from_date'];
		$pw_to_date=$my_array_of_vars['pw_to_date'];
		$cur_year=substr($pw_from_date,0,4);
		
		$pw_hide_os=array('trash');
		$pw_shop_order_status=$pw_rpt_main_class->pw_shop_status;
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		
			
		/////////////////////////////
		//TOP PRODUCTS PIE CHART
		////////////////////////////
		$order_items_top_product=$pw_rpt_main_class->pw_get_dashboard_top_products_chart_pie($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		/////////////////////////////
		//SALE BY MONTHS
		////////////////////////////
		
		$order_items_months_multiple=$pw_rpt_main_class->pw_get_dashboard_sale_months_multiple_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_months=$pw_rpt_main_class->pw_get_dashboard_sale_months_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_days=$pw_rpt_main_class->pw_get_dashboard_sale_days_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_3d_months=$pw_rpt_main_class->pw_get_dashboard_sale_months_3d_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		//die($order_items_days);
		
		$order_items_week=$pw_rpt_main_class->pw_get_dashboard_sale_weeks_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$final_json=array();
		
		$currency_decimal=get_option('woocommerce_price_decimal_sep','.');
		$currency_thousand=get_option('woocommerce_price_thousand_sep',',');
		$currency_thousand=',';
		/////////////////////
		//SALE BY MONTH MULTIPLE CHART
		////////////////////
		
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_months_multiple as $key => $order_item) {
			$value  =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0);

			$pw_fetchs_data[$i]["date"]=substr($order_item->Month,0,10);
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = $pw_rpt_main_class->price_value($value);
			$pw_fetchs_data[$i]["volume"] = $pw_rpt_main_class->price_value($value);
			
			$i++;
			
		}
		//$final_json[]=($pw_fetchs_data);
		
		
		///////////////////////
		//MONTH FOR CHART
		////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_3d_months as $key => $order_item) {

			$value            =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0) ;

			$month=sprintf(__("%s",__PW_REPORT_WCREPORT_TEXTDOMAIN__), $order_item->Month);
			$pw_fetchs_data[$i]["date"]=$month.' '.$order_item->Year;

			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
				
			$pw_fetchs_data[$i]["value"] = $pw_rpt_main_class->price_value($value);
			$pw_fetchs_data[$i]["volume"] = $pw_rpt_main_class->price_value($value);
			
			$i++;			
		}
		$final_json[]=($pw_fetchs_data);	
		
		//////////////////
		//SALE BY DAYS
		//////////////////
		$item_dates = array();
		$item_data  = array();
		$pw_fetchs_data =array();
		$i=0;
		foreach ($order_items_days as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] = $item->TotalAmount;
			
			$value=  (is_numeric($item->TotalAmount) ?  number_format($item->TotalAmount,2):0);
			$pw_fetchs_data[$i]["date"] = trim($item->Date);
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = $pw_rpt_main_class->price_value($value);
			$pw_fetchs_data[$i]["volume"] = $pw_rpt_main_class->price_value($value);
			$i++;
		}
		$final_json[]=$pw_fetchs_data;
		
		////////////////////////////
		//SALE BY WEEK
		/////////////////////////////
		$item_dates = array();
		$item_data  = array();
		
		$weekarray = array();
		$timestamp = time();
		for ($i = 0; $i < 7; $i++) {
			$weekarray[] = date('Y-m-d', $timestamp);
			$timestamp -= 24 * 3600;
		}
		
		foreach ($order_items_week as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] = (is_numeric($item->TotalAmount) ?  number_format($item->TotalAmount,2):0);
		}
		
		$new_data = array();
		foreach ($weekarray as $date) {
			if (in_array($date, $item_dates)) {
				
				$new_data[$date] = $item_data[$date];
			} else {
				$new_data[$date] = 0;
			}
		}
		
		$pw_fetchs_data = array();
		$i         = 0;
		foreach ($new_data as $key => $value) {
			$pw_fetchs_data[$i]["date"] = $key;
			
			//$value=explode($currency_decimal,$value);
			//$value=$value[0];
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = (is_numeric($value) ? number_format($value,2):0) ;
			$pw_fetchs_data[$i]["volume"] =  (is_numeric($value) ? number_format($value,2):0) ;
			$i++;			
		}
		$final_json[]=array_reverse($pw_fetchs_data);
		
		///////////////////////
		//MONTH FOR CHART
		////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_months as $key => $order_item) {

			$value            =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0) ;

			$month=sprintf(__("%s",__PW_REPORT_WCREPORT_TEXTDOMAIN__), $order_item->Month);
			$pw_fetchs_data[$i]["date"]=$month;
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			//$value=$value[0];
			
			$pw_fetchs_data[$i]["value"] = $pw_rpt_main_class->price_value($value);
			$pw_fetchs_data[$i]["volume"] = $pw_rpt_main_class->price_value($value);
			
			$i++;			
		}
		$final_json[]=($pw_fetchs_data);		
		//die(print_r($pw_fetchs_data));
		
		///////////////////////////
		//	PIE CHART TOP PRODUCTS
		//////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_top_product as $items) {
			$pw_fetchs_data[$i]['label']=$items->Label;
			
			$value=(is_numeric($items->Value) ?  number_format($items->Value,2):0);
			$value=explode($currency_decimal,$value);
			$value=$value[0];
			$value=str_replace($currency_thousand,"",$value);
			$pw_fetchs_data[$i]['value']= $pw_rpt_main_class->price_value($value);
			
			$i++;
		}
		$final_json[]=($pw_fetchs_data);				
		
		//print_r($final_json);
			
		echo json_encode($final_json);	
		die();	
		
	}

	//ADDE IN 4.9
	//FETCH PRODUCT CHART DATA
	add_action('wp_ajax_pw_rpt_fetch_product_chart', 'pw_rpt_fetch_product_chart');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_product_chart', 'pw_rpt_fetch_product_chart');
	function pw_rpt_fetch_product_chart() {

		global $wpdb;
		global $pw_rpt_main_class;

		parse_str($_POST['postdata'], $my_array_of_vars);

		$nonce = $_POST['nonce'];

		$type = $_POST['type'];

		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
				'success'=>'no-nonce',
				'products' => array()
			);
			print_r($arr);
			die();
		}

//print_r($my_array_of_vars);

		$pw_from_date=$my_array_of_vars['pw_from_date'];
		$pw_to_date=$my_array_of_vars['pw_to_date'];
		$date_format = $pw_rpt_main_class->pw_date_format($pw_from_date);

		$pw_product_id=$my_array_of_vars['pw_product_id'];
		if(count($pw_product_id)>1)
			$pw_product_id  		= "'".str_replace(",","','",$pw_product_id)."'";
		else
			$pw_product_id  		='';

		$pw_order_status=$my_array_of_vars['pw_orders_status'];

		if(is_array($pw_order_status)) {
			$pw_order_status = implode( ",", $pw_order_status );
			$pw_order_status = str_replace( ",", "','", $pw_order_status ) ;
		}
		else
			$pw_order_status  		='';

		if($pw_order_status=='OandA'){
			$pw_order_status="Open','Abandoned";
		}

		//echo $pw_order_status;
		$cur_year=substr($pw_from_date,0,4);


		$pw_order_status_condition='';
		$pw_product_id_condition='';
		$date_condition='';
		$pw_order_status_join ='';
		if($pw_order_status  && $pw_order_status != "-1") {
			$pw_order_status_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 			ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 				ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
		}

		if($pw_order_status  && $pw_order_status != "-1") {
			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('" . $pw_order_status . "')";
		}

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$date_condition = " AND DATE(pw_posts.post_modified) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}

		//echo $pw_product_id;
		if($pw_product_id  && $pw_product_id != "-1") {
			$pw_product_id=explode(",",$pw_product_id);
			$pw_product_id_condition.=" AND (";
			$op=array();
			foreach($pw_product_id as $pid){
				$op[]=" meta.meta_value LIKE '%\"product_id\";i:$pid%' OR meta.meta_value LIKE '%\"variation_id\";i:$pid%'";
			}

			$pw_product_id_condition.=implode(" OR ",$op);

			$pw_product_id_condition.=' ) ';
		}

		$sql="Select DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition  $pw_product_id_condition ";
		//echo $sql;

		if($my_array_of_vars['type']=='line'){

			$pw_product_id=$my_array_of_vars['row_id'];
			if($pw_product_id  && $pw_product_id != "-1") {
				$pw_product_id=explode(",",$pw_product_id);
				$pw_product_id_condition.=" AND (";
				$op=array();
				foreach($pw_product_id as $pid){
					$op[]=" meta.meta_value LIKE '%\"product_id\";i:$pid%' OR meta.meta_value LIKE '%\"variation_id\";i:$pid%'";
				}

				$pw_product_id_condition.=implode(" OR ",$op);

				$pw_product_id_condition.=' ) ';
			}

			$pw_order_status_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 			ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 				ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";

			//MAIN
			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('open','Abandoned','Converted')";
			$sql_all="Select DATE_FORMAT(pw_posts.post_modified,'%M %Y' ) as modify_m,DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition  $pw_product_id_condition ";
			//echo $sql_all;

			//OPRN
			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('open')";
			$sql_open="Select DATE_FORMAT(pw_posts.post_modified,'%M %Y' ) as modify_m,DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition  $pw_product_id_condition ";


			//ABANDONED STATUS
			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('Abandoned')";
			$sql_abdn="Select DATE_FORMAT(pw_posts.post_modified,'%M %Y' ) as modify_m,DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition  $pw_product_id_condition ";

			//CONVERTED
			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('Converted')";
			$sql_convert="Select DATE_FORMAT(pw_posts.post_modified,'%M %Y' ) as modify_m,DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition  $pw_product_id_condition ";
			//echo $sql_convert;
		}

		//echo $sql;


		$final_json=array();

		$currency_decimal=get_option('woocommerce_price_decimal_sep','.');
		$currency_thousand=get_option('woocommerce_price_thousand_sep',',');
		$currency_thousand=',';
		/////////////////////
		//SALE BY MONTH MULTIPLE CHART
		////////////////////

		///////////////////////////
		//	PIE CHART TOP PRODUCTS
		//////////////////////////
		$pw_fetchs_data=array();
		$final_array=array();
		$i=0;

		if($my_array_of_vars['type']=='pie') {

			$order_items   = $wpdb->get_results( $sql );
			$product_array = array();
			foreach ( $order_items as $item ) {

				$items = ( unserialize( $item->pw_cartitems ) );

				foreach ( $items as $pitem ) {
					$_product = wc_get_product( $pitem['product_id'] );
					$pid      = $pitem['product_id'];
					if($pw_product_id  && $pw_product_id != "-1") {
						$pw_product_id = explode( ",", $pw_product_id );
						if(is_array($pw_product_id) && !in_array($pid,$pw_product_id)){
							continue;
						}
					}

					//echo $pid.'-';
					$sum                            = 0;
					$product_array[ $pid ]['title'] = $_product->get_title();
					$product_array[ $pid ]['sku']   = $_product->get_sku();
					$variation                      = '';
					if ( isset( $pitem['variation'] ) && count( $pitem['variation'] ) > 0 ) {
						$variation_data = wc_get_formatted_variation( $pitem['variation'], true );

						$variation = $variation_data;
					}
					$product_array[ $pid ]['variation'] = $variation;
					if ( isset( $product_array[ $pid ]['qty'] ) ) {
						$product_array[ $pid ]['qty'] += $pitem['quantity'];
					} else {
						$product_array[ $pid ]['qty'] = $pitem['quantity'];
					}

					$product_array[ $pid ]['rate'] = wc_price( $_product->get_price() );
					$sum                           = ( $pitem['quantity'] * $_product->get_price() );
					if ( isset( $product_array[ $pid ]['total'] ) ) {
						$product_array[ $pid ]['total'] += $sum;
					} else {
						$product_array[ $pid ]['total'] = $sum;
					}

				}

				$i = 0;

				foreach ( $product_array as $key => $pitem ) {
					$final_array[ $i ]['label'] = $pitem['title'];

					//$value=(is_numeric($items->Value) ?  number_format($items->Value,2):0);

					$final_array[ $i ]['value'] = $pitem['qty'];

					$i ++;
				}

			}
		}

		if($my_array_of_vars['type']=='line') {
			$pw_product_id=$my_array_of_vars['row_id'];
			$order_items_all   = $wpdb->get_results( $sql_all );
			$order_items_open   = $wpdb->get_results( $sql_open );
			$order_items_abdn   = $wpdb->get_results( $sql_abdn );
			$order_items_convert   = $wpdb->get_results( $sql_convert );

			$product_array_open=$product_array_abdn=$product_array_converted=array();

			//ALL
			$product_array = array();
			foreach ( $order_items_all as $item ) {

				$items = ( unserialize( $item->pw_cartitems ) );

				foreach ( $items as $pitem ) {
					$_product = wc_get_product( $pitem['product_id'] );
					$pid      = $pitem['product_id'];

					if($pw_product_id  && $pw_product_id != "-1") {
						if($pw_product_id!=$pid){
							continue;
						}
					}
					$sum                            = 0;
					$product_array[ $pid ]['date']   = $item->modify_m;
				}
			}
//print_r($product_array);


			//OPEN
			$product_array = array();
			foreach ( $order_items_open as $item ) {

				$items = ( unserialize( $item->pw_cartitems ) );

				foreach ( $items as $pitem ) {
					$_product = wc_get_product( $pitem['product_id'] );
					$pid      = $pitem['product_id'];

					if($pw_product_id  && $pw_product_id != "-1") {
						if($pw_product_id!=$pid){
							continue;
						}
					}

					$product_array[ $pid ]['date']   = $item->modify_m;
					if ( isset( $product_array[ $pid ]['qty'] ) ) {
						$product_array[ $pid ]['qty'] += $pitem['quantity'];
					} else {
						$product_array[ $pid ]['qty'] = $pitem['quantity'];
					}
				}

				$i = 0;

				foreach ( $product_array as $key => $pitem ) {
					if(isset($product_array_open[$pitem['date']]["value"]))
						$product_array_open[$pitem['date']]["value"]+= $pitem['qty'];
					else
						$product_array_open[$pitem['date']]["value"]= $pitem['qty'];
				}

			}

			//print_r($product_array_open);

			//ABANDINE
			$product_array = array();
			foreach ( $order_items_abdn as $item ) {

				$items = ( unserialize( $item->pw_cartitems ) );

				foreach ( $items as $pitem ) {
					$_product = wc_get_product( $pitem['product_id'] );
					$pid      = $pitem['product_id'];

					if($pw_product_id  && $pw_product_id != "-1") {
						if($pw_product_id!=$pid){
							continue;
						}
					}

					$product_array[ $pid ]['date']   = $item->modify_m;
					if ( isset( $product_array[ $pid ]['qty'] ) ) {
						$product_array[ $pid ]['qty'] += $pitem['quantity'];
					} else {
						$product_array[ $pid ]['qty'] = $pitem['quantity'];
					}
				}

				$i = 0;

				foreach ( $product_array as $key => $pitem ) {
					if(isset($product_array_abdn[$pitem['date']]["value"]))
						$product_array_abdn[$pitem['date']]["value"]+= $pitem['qty'];
					else
						$product_array_abdn[$pitem['date']]["value"]= $pitem['qty'];
				}

			}
			//print_r($product_array_abdn);

			//CONVERT
			$product_array = array();
			foreach ( $order_items_convert as $item ) {

				$items = ( unserialize( $item->pw_cartitems ) );

				foreach ( $items as $pitem ) {
					$_product = wc_get_product( $pitem['product_id'] );
					$pid      = $pitem['product_id'];

					if($pw_product_id  && $pw_product_id != "-1") {
						if($pw_product_id!=$pid){
							continue;
						}
					}

					$product_array[ $pid ]['date']   = $item->modify_m;
					if ( isset( $product_array[ $pid ]['qty'] ) ) {
						$product_array[ $pid ]['qty'] += $pitem['quantity'];
					} else {
						$product_array[ $pid ]['qty'] = $pitem['quantity'];
					}
				}

				$i = 0;

				foreach ( $product_array as $key => $pitem ) {
					if(isset($product_array_converted[$pitem['date']]["value"]))
						$product_array_converted[$pitem['date']]["value"]+= $pitem['qty'];
					else
						$product_array_converted[$pitem['date']]["value"]= $pitem['qty'];
				}

			}
			//print_r($product_array_converted);
		}

		$order_items_all   = $wpdb->get_results( $sql_all );
		//ALL
		$product_array_main = array();
		$i=0;
		foreach ( $order_items_all as $item ) {
			$product_array_main[$item->modify_m]   = 0;
			$i++;
		}

		//print_r($product_array_main);

		$i=0;
		foreach($product_array_main as $key=>$val){
			$keys=explode(" ",$key);
			$final_array[$i]['date']=$key;
			if(isset($product_array_open[$key])){
				$final_array[$i]['open']=$product_array_open[$key]['value'];
			}else{
				$final_array[$i]['open']=0;

			}

			if(isset($product_array_abdn[$key])){
				$final_array[$i]['abandoned']=$product_array_abdn[$key]['value'];
			}else{
				$final_array[$i]['abandoned']=0;

			}

			if(isset($product_array_converted[$key])){
				$final_array[$i]['convert']=$product_array_converted[$key]['value'];
			}else{
				$final_array[$i]['convert']=0;

			}
			$i++;
		}


		$final_json=($final_array);
		//print_r($final_json);
		echo json_encode($final_json);

		die();

	}

	////ADDED IN VER4.0
	/// DAILY EMAIL
	//CHECK MAIL
	add_action('wp_ajax_pw_rpt_test_email', 'pw_rpt_test_email');
	add_action('wp_ajax_nopriv_pw_rpt_test_email', 'pw_rpt_test_email');
	function pw_rpt_test_email() {

		global $wpdb;
		global $pw_rpt_main_class;

		//parse_str( $_POST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];


		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		global $pw_rpt_main_class;
		$result=$pw_rpt_main_class->wcx_send_email_schedule();

		if($result){
			echo '<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="awr-sum-item" style="background-color: #0DBF44;color: #fff">
							<h2>'.__('Your Test mail sent successfully',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h2>
						</div><!--awr-sum-item -->
					</div>';
		}else{
			echo '<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="awr-sum-item">
							<h2>'.__('Error in sending mail!',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h2>
						</div><!--awr-sum-item -->
					</div>';
		}

		die(0);
	}



	function pw_fetch_reports_core(){


		global $pw_rpt_main_class;


		if(($pw_rpt_main_class->email=="" || !filter_var($pw_rpt_main_class->email, FILTER_VALIDATE_EMAIL)) && isset($_GET["smenu"]) && $_GET["smenu"]!="wcx_wcreport_plugin_active_report"){
			header("location:".admin_url()."admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin");
			return false;
		}

		/////////////
		/// /// CHECK LICENSE PLUGIN
		/////////////
		$request_string = array(
			"body" => array(
				"action" => "insert_licensekey",
				"license-key" => $pw_rpt_main_class->license_key,
				"email" => $pw_rpt_main_class->email,
				"domain" => $pw_rpt_main_class->domain,
				"item-id" => $pw_rpt_main_class->item_valid_id,
			)
		);

		if($pw_rpt_main_class->license_key!="" && (filter_var($pw_rpt_main_class->email, FILTER_VALIDATE_EMAIL))){
			$response = wp_remote_post($pw_rpt_main_class->api_url, $request_string);

			if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
				return false;
			}
			$result = json_decode( wp_remote_retrieve_body( $response ), true );

			//$result=$result[0];
			if(isset($result["verify-purchase"]["status"]) && $result["verify-purchase"]["status"]=="valid"){
				$pw_rpt_main_class->pw_core_status=true;
				return $result;
			}
			else if(isset($result["verify-purchase"]["status"]) && $result["verify-purchase"]["status"]!="valid"){
				return $result;
			}
			else{
				return false;
			}
		}
	}




	////ADDED IN VER4.0
	/// SEND REQUEST FORM
	add_action('wp_ajax_pw_rpt_request_form', 'pw_rpt_request_form');
	add_action('wp_ajax_nopriv_pw_rpt_request_form', 'pw_rpt_request_form');
	function pw_rpt_request_form() {

		global $wpdb;
		global $pw_rpt_main_class;

		parse_str( $_POST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		$type = $_POST['type'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		global $pw_rpt_main_class;

		$subject_arr=array("request"=>__("Send a Request",__PW_REPORT_WCREPORT_TEXTDOMAIN__),"issue"=>__("Report an issue",__PW_REPORT_WCREPORT_TEXTDOMAIN__));
		$fullname=$my_array_of_vars['awr_fullname'];
		$email=$my_array_of_vars['awr_email'];
		$subject=$my_array_of_vars['awr_subject'];
		$subject=$subject!='' ? $subject_arr[$subject] : __("Email From Woo Reporting",__PW_REPORT_WCREPORT_TEXTDOMAIN__) ;
		$title=$my_array_of_vars['awr_title'];
		$content=$my_array_of_vars['awr_content'];
		$email_optimize 		= $pw_rpt_main_class->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'optimize_email','');

		$email_send_to = $pw_rpt_main_class->reformat_email_text('reporting_support@proword.net');
		$email_from_email = $pw_rpt_main_class->reformat_email_text($email);

		$date_format 					= get_option( 'date_format', "Y-m-d" );
		$time_format 					= get_option('time_format','g:i a');
		$reporte_created				= date_i18n($date_format." ".$time_format);

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		if($email_optimize)
			$headers .= 'From: '.$fullname.' <'.$email_from_email.'>'. "\r\n";
		else {

			$headers .= "From: =?UTF-8?B?" . base64_encode( $fullname ) . "?= <" . $email_from_email . ">" . "\r\n";
			$headers .= 'Content-Transfer-Encoding: 8bit';
		}

		$email_data =  $content. "<div style=\" padding-bottom:3px; width:520px; margin:auto; text-align:left;\"><strong>".__("Created Date/Time:",__PW_REPORT_WCREPORT_TEXTDOMAIN__)." "."</strong> {$reporte_created}</div>";

		$message = $email_data;
		$to		 = $email_send_to;

		$result = wp_mail( $to, "=?UTF-8?B?".base64_encode($subject).' - '.base64_encode($title)."?=", $message, $headers);

		if($result){
			echo '<span class="awr-req-result" style="background-color: #0DBF44">'. __("Your Email has been received. Thanks for contact.",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .' </span>';
		}else{
			echo '<span class="awr-req-result">'. __("Error in sending Email!",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .' </span>';
		}

		die(0);
	}

	////ADDED IN VER4.0
	/// UPDATE NOTIFICATION
	add_action('wp_ajax_pw_rpt_update_notification_date', 'pw_rpt_update_notification_date');
	add_action('wp_ajax_nopriv_pw_rpt_update_notification_date', 'pw_rpt_update_notification_date');
	function pw_rpt_update_notification_date() {
		global $wpdb;
		global $pw_rpt_main_class;
        die();
		parse_str( $_POST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		$type = $_POST['type'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}

		$read_date=get_option("pw_news_read_date");
		//$read_date='';

		//GET FROM XML
		$api_url='http://proword.net/xmls/Woo_Reporting/report-news.php';

		$response = wp_remote_get(  $api_url );

		/* Check for errors, if there are some errors return false */
		if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
			return false;
		}

		/* Transform the JSON string into a PHP array */
		$result = json_decode( wp_remote_retrieve_body( $response ), true );
		$add_ons_status='';
		$news_count=0;

		if($read_date=='' && is_array($result))
		{
			$i=0;

			foreach($result as $add_ons){

				if ($add_ons === reset($result)){
					update_option("pw_news_read_date",$add_ons['date']);
				}
			}
		}else if(is_array($result)){
			foreach($result as $add_ons){

			}
			update_option("pw_news_read_date",$add_ons['date']);
		}
		//echo $add_ons['date'];
		die();
	}

	////ADDED IN VER4.0
	/// INVOICE ACTION
	add_action('wp_ajax_pw_rpt_pdf_invoice', 'pw_rpt_pdf_invoice');
	add_action('wp_ajax_nopriv_pw_rpt_pdf_invoice', 'pw_rpt_pdf_invoice');
	function pw_rpt_pdf_invoice(){
		global $wpdb;
		global $pw_rpt_main_class;

		parse_str( $_POST['postdata'], $my_array_of_vars );

		$nonce = $_POST['nonce'];

		$type = $_POST['type'];

		if ( ! wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) ) {
			$arr = array(
				'success'  => 'no-nonce',
				'products' => array()
			);
			print_r( $arr );
			die();
		}
		$order_id=$my_array_of_vars['order_id'];

		global $pw_rpt_main_class;


		//////FETCH ORDER DETIALS AND PRODUCTS ITEM ///////

		$sql="SELECT DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') AS order_date, pw_woocommerce_order_items.order_id AS order_id, pw_woocommerce_order_items.order_item_name AS product_name,	pw_woocommerce_order_items.order_item_id	AS order_item_id, woocommerce_order_itemmeta.meta_value AS woocommerce_order_itemmeta_meta_value, (pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS sold_rate, (pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) AS product_rate, (pw_woocommerce_order_itemmeta4.meta_value) AS item_amount, (pw_woocommerce_order_itemmeta2.meta_value) AS item_net_amount, (pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) AS item_discount, pw_woocommerce_order_itemmeta2.meta_value AS total_price, count(pw_woocommerce_order_items.order_item_id) AS product_quentity, woocommerce_order_itemmeta.meta_value AS product_id ,pw_woocommerce_order_itemmeta3.meta_value AS 'product_quantity' ,pw_posts.post_status AS post_status ,pw_posts.post_status AS order_status FROM {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id	LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta2 ON pw_woocommerce_order_itemmeta2.order_item_id	= pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta3 ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta4 ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal' Where pw_posts.post_type = 'shop_order' AND woocommerce_order_itemmeta.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta2.meta_key='_line_total' AND pw_woocommerce_order_itemmeta3.meta_key='_qty' AND pw_woocommerce_order_items.order_id = $order_id AND pw_posts.post_status IN ('wc-processing','wc-on-hold','wc-completed') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_woocommerce_order_items.order_item_id
";

		global $wpdb;
		$result=$wpdb->get_results($sql);
		$itemss=$result[0];
		$order_items=$result;

		$pw_null_val=0;
		$_order_currency = get_post_meta($order_id, '_order_currency', true);

		foreach ( $order_items as $key => $order_item ) {

			$order_id								= $order_item->order_id;
			$order_items[$key]->billing_first_name  = '';//Default, some time it missing
			$order_items[$key]->billing_last_name  	= '';//Default, some time it missing
			$order_items[$key]->billing_email  		= '';//Default, some time it missing

			if(!isset($order_meta[$order_id])){
				$order_meta[$order_id]					= $pw_rpt_main_class->pw_get_full_post_meta($order_id);
			}

			//	die(print_r($order_meta[$order_id]));

			foreach($order_meta[$order_id] as $k => $v){
				$order_items[$key]->$k			= $v;
			}


			$order_items[$key]->order_total			= isset($order_item->order_total)		? $order_item->order_total 		: 0;
			$order_items[$key]->order_shipping		= isset($order_item->order_shipping)	? $order_item->order_shipping 	: 0;


			$order_items[$key]->cart_discount		= isset($order_item->cart_discount)		? $order_item->cart_discount 	: 0;
			$order_items[$key]->order_discount		= isset($order_item->order_discount)	? $order_item->order_discount 	: 0;
			$order_items[$key]->total_discount 		= isset($order_item->total_discount)	? $order_item->total_discount 	: ($order_items[$key]->cart_discount + $order_items[$key]->order_discount);


			$order_items[$key]->order_tax 			= isset($order_item->order_tax)			? $order_item->order_tax : 0;
			$order_items[$key]->order_shipping_tax 	= isset($order_item->order_shipping_tax)? $order_item->order_shipping_tax : 0;
			$order_items[$key]->total_tax 			= isset($order_item->total_tax)			? $order_item->total_tax 	: ($order_items[$key]->order_tax + $order_items[$key]->order_shipping_tax);

			$transaction_id = "ransaction ID";
			$order_items[$key]->transaction_id		= isset($order_item->$transaction_id) 	? $order_item->$transaction_id		: (isset($order_item->transaction_id) ? $order_item->transaction_id : '');
			$order_items[$key]->gross_amount 		= ($order_items[$key]->order_total + $order_items[$key]->total_discount) - ($order_items[$key]->order_shipping +  $order_items[$key]->order_shipping_tax + $order_items[$key]->order_tax );


			$order_items[$key]->billing_first_name	= isset($order_item->billing_first_name)? $order_item->billing_first_name 	: '';
			$order_items[$key]->billing_last_name	= isset($order_item->billing_last_name)	? $order_item->billing_last_name 	: '';
			$order_items[$key]->billing_name		= $order_items[$key]->billing_first_name.' '.$order_items[$key]->billing_last_name;


		}


		///die(print_r($order_items));

		$result=$order_items;
		$tax_names=$pw_subtotal=$table_products_row=$pw_net_amnt='';

		$i=0;
		//print_r($result);
		foreach($result as $items){
			$tax_names=$pw_rpt_main_class->pw_oin_list($order_id,'tax');
			$tax_names=isset($tax_names[$order_id]) ? $tax_names[$order_id] : "";

			$pw_table_value_rate = isset($items -> item_amount) ? $items -> item_amount : 0;
			$pw_subtotal+= $pw_table_value_rate == 0 ? $pw_null_val : $pw_table_value_rate;

			$order_refund_amnt= $pw_rpt_main_class->pw_get_por_amount($items -> order_id);
			$part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);
			$pw_table_value_net  = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
			$pw_net_amnt= $pw_table_value_net == 0 ? $pw_null_val : $pw_table_value_net;
			$pw_net_amnt= $pw_rpt_main_class->price($pw_net_amnt,array("currency" => $_order_currency),'multi_currency');

			$i++;
			$last_row_class="";
			if ($items === end($result))
				$last_row_class=" pw-last-tr ";

			$table_products_row.='
			<tr>
                <td width="10%" class="pw-in2-inv-tbl-tr-td '.$last_row_class.'">
                    <b>'.$i.'</b>
                </td >
                <td width="40%" class="pw-in2-inv-tbl-tr-td '.$last_row_class.'">
                    <b>'.$items->product_name.'</b>
                </td>
                <td width="18%" class="pw-in2-inv-tbl-tr-td '.$last_row_class.'">
                    <b>'.$pw_rpt_main_class->price($pw_table_value_rate,array("currency" => $_order_currency),'multi_currency').'</b>
                </td >
                <td width="14%" class="pw-in2-inv-tbl-tr-td '.$last_row_class.'">
                    <b>'.$items -> product_quantity.'</b>
                </td>
                <td width="18%" class="pw-in2-inv-tbl-tr-td '.$last_row_class.'">
                    <b>'.$pw_rpt_main_class->price(($pw_table_value_rate*$items -> product_quantity),array("currency" => $_order_currency),'multi_currency').'</b>
                </td>
            </tr>
			';

		}
		$pw_subtotal_amnt=$pw_subtotal;
		$pw_subtotal=$pw_rpt_main_class->price($pw_subtotal,array("currency" => $_order_currency),'multi_currency');


		$items = new WC_Order($order_id);
		$date_format		= get_option( 'date_format' );
		$order_date=date($date_format,strtotime($items->order_date));
		$customer_name=$items->billing_name;

		$country      	= $pw_rpt_main_class->pw_get_woo_countries();
		$customer_email=$items->billing_email;

		$billing_country = isset($country->countries[$items->billing_country]) ? $country->countries[$items->billing_country]: $items->billing_country;
		$billing_state = $pw_rpt_main_class->pw_get_woo_bsn($items->billing_country,$items->billing_state);
		$billing_city = $items->billing_city;
		$billing_address_1 = $items->billing_address_1;
		$billing_address_2 = $items->billing_address_2;
		$billing_phone = get_post_meta($order_id, '_billing_phone', true);//
		$billing_fname = get_post_meta($order_id, '_billing_first_name', true);//
		$billing_lname = get_post_meta($order_id, '_billing_last_name', true);//
		$billing_info= $billing_address_1 ."<br/>".$billing_address_2."<br />".$billing_city.', '.$billing_state.', '.$billing_country;

		$shipping_country = isset($country->countries[$items->shipping_country]) ? $country->countries[$items->shipping_country]: $items->shipping_country;
		$shipping_state = $pw_rpt_main_class->pw_get_woo_bsn($items->shipping_country,$items->shipping_state);
		$shipping_city = $items->shipping_city;
		$shipping_address_1 = $items->shipping_address_1;
		$shipping_address_2 = $items->shipping_address_2;
		$shipping_fname = get_post_meta($order_id, '_shipping_first_name', true);//
		$shipping_lname = get_post_meta($order_id, '_shipping_last_name', true);//
		$shipping_info= $shipping_address_1 ."<br/>".$shipping_address_2."<br />".$shipping_city.', '.$shipping_state.', '.$shipping_country;

		$shipping_method=$pw_rpt_main_class->pw_oin_list($items->order_id,'shipping');
		$shipping_method=isset($shipping_method[$items->order_id]) ? $shipping_method[$items->order_id] : "";

		$payment_method=isset($items->payment_method_title) ? $items->payment_method_title : "" ;

		$order_currency=isset($items->order_currency) ? $items->order_currency : "" ;

		$pw_coupon_code=$pw_rpt_main_class->pw_oin_list($items->order_id,'coupon');
		$pw_coupon_code=isset($pw_coupon_code[$items->order_id]) ? $pw_coupon_code[$items->order_id] : "";

		$tax_name=$pw_rpt_main_class->pw_oin_list($items->order_id,'tax');
		$tax_name=isset($tax_name[$items->order_id]) ? $tax_name[$items->order_id] : "";
		$order = new WC_Order($order_id);
		$tax_total = $order->get_total_tax();


		$pw_table_value = isset($items -> gross_amount) ? $items -> gross_amount : 0;
		$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
		$gross_amount= $pw_rpt_main_class->price($pw_table_value,array("currency" => $_order_currency),'multi_currency');

		$pw_table_value = isset($items -> order_discount) ? $items -> order_discount : 0;
		$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
		$order_discount=$pw_rpt_main_class->price($pw_table_value,array("currency" => $_order_currency),'multi_currency');

		$pw_table_value = isset($items -> total_price) ? $items -> total_price : 0;
		$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
		$net_amount= $pw_rpt_main_class->price($pw_table_value,array("currency" => $_order_currency),'multi_currency');
		$net_amount= $pw_rpt_main_class->price($order->get_total());

		$subtotal_amount= $pw_rpt_main_class->price($order->get_subtotal());

		$items_count = $pw_rpt_main_class->pw_get_oi_count($items->order_id,'line_item');
		$items_count=isset($items_count[$items->order_id]) ? $items_count[$items->order_id] : "";

		$date_format		= get_option( 'date_format' );
		$order_date=date($date_format,strtotime($items->order_date));


		$tax_name_rate = $pw_rpt_main_class->pw_get_tax_rate_name($order_id,'tax');
		$tax_name_rate_html='';
		$i=1;
		foreach($tax_name_rate as $taxrate){
			if($i==1)
				$tax_name_rate_html.='<p><b class="pw-sub-val-p-b">TAX :</b> '.$taxrate['name'].' - '.$taxrate['rate'].' - '.$pw_rpt_main_class->price($taxrate['amount']).'</p>';
			else
				$tax_name_rate_html.='<p><b>            </b> '.$taxrate['name'].' - '.$taxrate['rate'].' - '.$pw_rpt_main_class->price($taxrate['amount']).'</p>';
			$i++;
		}

		$order = wc_get_order( $order_id );
		$tax_name_rate_html='';$i=1;
		// Iterating through WC_Order_Item_Tax objects
		foreach( $order->get_items( 'tax' ) as $item_id => $item_tax ){
			## -- Get all protected data in an accessible array -- ##

			$tax_data = $item_tax->get_data(); // Get the Tax data in an array

			$item_tax_rate_code = $tax_data['rate_code'];
			$item_tax_rate_id = $tax_data['rate_id'];
			$item_tax_label = $tax_data['label'];
			$item_tax_total = $tax_data['tax_total']; // Tax total amount
			$item_tax_shipping_total = $tax_data['shipping_tax_total']; // Tax shipping total

			if($i==1)
				$tax_name_rate_html.='<p><b class="pw-sub-val-p-b">TAX :</b> '.$item_tax_label.' - '.$pw_rpt_main_class->price(($item_tax_total+$item_tax_shipping_total)).'</p>';
			else
				$tax_name_rate_html.='<p><b>            </b> '.$item_tax_label.' - '.$pw_rpt_main_class->price(($item_tax_total+$item_tax_shipping_total)).'</p>';
			$i++;
		}

		///////////


		require_once('TCPDF/config/tcpdf_config.php');
		require_once('TCPDF/tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

// set document information
		//$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 001');
		//$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// set font
		$pdf->SetFont('helvetica', '', 10);

// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------

// set default font subsetting mode
		//$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
		//$pdf->SetFont('arial', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
		//$pdf->SetMargins(10, 5, 10, true);
		$pdf->AddPage('P', 'A4');

// set text shadow effect
		//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

		$logo=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__."invoice_logo");
		$logo_td='';
		$first_col_td='';
		$second_col_td='';
		if($logo){
			$logo=wp_get_attachment_image_src($logo);
			$logo_td='<td width="20%">
	                    <img src="'.$logo[0].'"/>
	                </td>';
			$first_col_td='<p class="pw-inv-to-date"><b>'.$order_date.'</b></p>
                    <p class="pw-inv-to-date"><b>Order No : '.$order_id.'</b></p>';
		}else{
			$second_col_td='<td width="20%">
					<p class="pw-inv-to-date"><b>'.$order_date.'</b></p>
                    <p class="pw-inv-to-date"><b>Order No : '.$order_id.'</b></p>
                    </td>';
		}

		//// TRANLATE TITLE
		$inv_no=__("Invoice No",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$inv_date=__("Invoice Date",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$billing_title=__("BILLING DETALS",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$subtotal_title=__("Sub Total",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$tax_title=__("Tax",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$to_title=__("To",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$shipping_title=__("SHIPPING DETAILS",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$no_title=__("NO.",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$product_title=__("PRODUCT",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$unit_price_title=__("UNIT PRICE",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$qty_title=__("QTY.",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$total_title=__("TOTAL",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$total_ch_title=__("Total Charges",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$payment_title=__("PAYMENT METHOD",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		$thanks_title= get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'invoice_footer_text');

		$html = <<<EOD
		<style>
        .a4-page{
           
        }
        .pw-center-align{
            text-align: center;
        }
        .pw-right-align{
            text-align: right;
        }
        .pw-in2-header-tbl p,
        .pw-in2-inv-header-tbl p,
        .pw-in2-inv-tbl p
        {
            margin: 0 0 5px 0;
        }



        /*PAGE EEADER*/
        .pw-in2-header-tbl{
            width: 100%;
            margin-bottom: 30px;
        }
        .pw-in2-header-tbl td{
            vertical-align: top;
            font-size: 11px;
            color: #444;
            font-weight: bold;
        }
        .pw-in2-header-tbl .pw-inv-txt{
            text-transform: uppercase;
            font-size: 15px;
        }
        .pw-in2-header-tbl span{
            display: inline-block;
            color: #239ca7;
            margin-bottom: 10px;
        }




        /*INVOICE HEADER*/
        .pw-in2-inv-header-tbl{
            width: 100%;
            color: #333;
            border-spacing: 0;
            margin-bottom: 10px;
        }
        .pw-in2-inv-header-tbl .pw-first-tr td{
            border-bottom: 1px solid #333;
        }
        .pw-in2-inv-header-tbl tr.pw-first-tr p {
            font-size: 12px;
        }
        .pw-in2-inv-header-tbl tr.pw-last-tr td{
            border-bottom: 1px solid #333;
            line-height:20px;
        }

        .pw-in2-inv-header-tbl .pw-last-tr td{
            vertical-align: top;

        }
        .pw-in2-inv-header-tbl .pw-last-tr td p{
            font-size: 11px;
        }


        .pw-in2-inv-tbl{
            width: 100%;
            border-spacing: 0;

        }
        .pw-in2-inv-tbl tr th{
            color: #333;
            font-size: 13px;
            font-weight: bold;
            padding: 15px 10px;
            height: 30px;
            border-bottom: 2px solid #333;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .pw-in2-inv-tbl tr th:first-child{
            text-align: left;
        }

        .pw-in2-inv-tbl-tr-td{
            
            text-align: center;
            border-bottom: 1px solid #e4e6e5;
            line-height:35px;
        }
      
        .pw-in2-inv-tbl tbody tr:last-child td{
            border-bottom: 2px solid #333;
        }
        
        .pw-in2-inv-tbl tr td p{
            font-size: 13px;
            color: #333;
        }

        .pw-inv1-item-title{
            text-transform: capitalize;
        }
        .pw-in2-inv-tbl tr td p.pw-inv1-item-desc{
            font-size: 11px;
            color: #8c8c8c;
        }


        .pw-in2-address-tbl{
            width: 100%;
            font-size: 12px;
            color: #333;
            margin-top: 50px;
        }
        .pw-in2-inv-tbl > tfoot > tr td{
            padding: 0 0px 0 0;
        }
        .pw-in2-address-tbl tr td{
            padding: 0 10px 0 0!important;
            border-bottom: none;
        }
        .pw-in2-address-tbl .pw-inv2-pay-txt{
            margin-bottom: 10px;
        }
        .pw-in2-address-tbl .pw-inv2-term-txt{
            margin-top: 40px;
            text-transform: uppercase;
        }
        .pw-in2-inv-tbl tfoot tr td:last-child{
            vertical-align: top;
        }
        .pw-in2-inv-tbl tfoot .pw-sub-val{
            vertical-align: bottom;
        }
        .pw-sub-val-p-b {
            text-transform: uppercase;
            letter-spacing: 2px ;
            background-color: #333;
            /*padding: 0 5px;*/
            color: #fff;
            margin-right: 5px;
        }
        /*Total Table*/
        .pw-in2-total-tbl{
            width: 100%;
            border-spacing: 0;
            text-align: center;
        }
        .pw-in2-total-tbl .pw-total-title td{
             background-color: #333;
             text-align: center;
         }
        .pw-in2-total-tbl .pw-total-title td p{
            font-size: 15px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .pw-in2-total-tbl tr td{
            padding: 0px 0px 5px 15px!important;
            border-bottom: none;
        }
        .pw-in2-total-tbl tr.pw-total-value td p {
            font-size: 26px;
            text-align: center;
            letter-spacing: 3px;
        }

        .pw-in2-total-tbl  tr.pw-in2-disc td p{
            color: #219baa!important;
        }
        .pw-in2-total-tbl  tr.pw-in2-total td:first-child {
            background-color: #4c4d4f!important;
            text-transform: uppercase;
        }
        .pw-in2-total-tbl  tr.pw-in2-total td:last-child {
            background-color: #219baa!important;

        }
        .pw-in2-total-tbl  tr.pw-in2-total td p{
            color: #fff!important;
        }
        -in2-footer-tbl{
            width: 65%;
            border-spacing: 0;
            font-size: 12px;
            color: #555;
        }


        .pw-in2-footer-tbl h1{
            display: inline-block;
            font-size: 15px;
            border-top: 3px solid #333;
            text-transform: uppercase;
            padding-top: 5px;
        }
        .pw-border-botttom{
            border-bottom: 1px solid #e4e6e5;
        }
        .pw-last-tr{
        	border-bottom: 2px solid #333;
        }
        
        .pw-in2-total-tbl-tr-td{
            border-bottom: none;
        }

    </style>
<div class="a4-page">

    <!--INVOICE BODY -->
    <div class="pw-in2-body-cnt">
        <table class="pw-in2-header-tbl">
            <tr class="pw-first-tr">

                <td width="80%">
					<p class="pw-inv-to-date"><b>{$inv_no} :</b></p>
                    <p class="pw-inv-to-date"><b>{$inv_date} :</b></p>
                	{$first_col_td}
                </td>
                {$logo_td}
                {$second_col_td}
            </tr>
        </table>

        <table class="pw-in2-inv-header-tbl">

            <tr class="pw-first-tr">
                <td colspan="2" width="50%" >
                    <p><b>{$billing_title}</b></p>
                </td>
                <td colspan="2" width="50%" >
                    <p><b>{$shipping_title}</b></p>
                </td>
            </tr>
            <tr class="pw-last-tr">
                <td width="20%" class="pw-in2-inv-date">
                    <p><b>{$to_title} : </b>{$billing_fname} {$billing_lname}</p>
                </td>
                <td width="30%" class="pw-in2-inv-date">
                    <p>{$billing_info}</p>
                    <p>{$billing_phone}<br >
                       {$customer_email}</p>
                </td>
                <td width="20%" class="pw-in2-inv-date">
                    <p><b>To : </b>{$shipping_fname} {$shipping_lname}</p>
                </td>
                <td width="30%" class="pw-in2-inv-date">
                    <p>{$shipping_info}</p>
                </td>
            </tr>
            
            
        </table>
        <br/>
        <br/>

        <table class="pw-in2-inv-tbl">
            <thead>
            <tr>
                <th width="10%">{$no_title}</th>
                <th width="40%">{$product_title}</th>
                <th width="18%">{$unit_price_title}</th>
                <th width="14%">{$qty_title}</th>
                <th width="18%">{$total_title}</th>
            </tr>
            </thead>
            <tbody>
            {$table_products_row}
            
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="pw-sub-val">
                    <p><b class="pw-sub-val-p-b">{$subtotal_title}</b> {$pw_subtotal}</p>
                    {$tax_name_rate_html}
                </td>
                <td colspan="2">
                    <table class="pw-in2-total-tbl pw-in2-total-tbl-tr-td" style="padding:10px 0px 10px 15px!important;">

                        <tr class="pw-total-title" >
                            <td><p style="color: #fff;">{$total_ch_title}</p></td>
                        </tr>
                        <tr class="pw-total-value">
                            <td class="pw-last-tr"><p>{$pw_net_amnt}</p></td>
                        </tr>

                    </table>
                </td>
            </tr>
            </tfoot>
        </table>
        <table class="pw-in2-address-tbl">
            <tr>
                <td colspan="2">
                    <p class="pw-inv2-pay-txt"><b>{$payment_title} : {$payment_method}</b></p>
                </td>

            </tr>
            
        </table>
    </div>
    <!--INVOICE FOOTER -->
    <table  class="pw-in2-footer-tbl">
        <tr>
            <td>
                <h1>{$thanks_title}</h1>
            </td>
        </tr>

    </table>

</div>
EOD;

// Print text using writeHTMLCell()
		//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$pdf->MultiCell(0, 0, $html, 0, 'J', false, 1, '10', '10', true, 0, true, true, 0, 'T', false);
		//$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------
		$pdf->lastPage();
// Close and output PDF document
// This method has several options, check the source code documentation for more information.

		$upload_dir = wp_upload_dir();
		$user_dirname = $upload_dir['basedir'].'/Pw_Woo_Reporting_Invoices';
		if ( ! file_exists( $user_dirname ) ) {
			wp_mkdir_p( $user_dirname );
		}

		chdir($user_dirname);

		$my_file_name='invoice_'.$order_id.'_'.date('m-d-Y_hia').'.pdf';

		$pdf->Output($user_dirname.'/'.$my_file_name, 'F');
		$upload = wp_upload_dir();
		$user_dirname = $upload_dir['baseurl'].'/Pw_Woo_Reporting_Invoices';
		echo $user_dirname.'/'.$my_file_name;
		die();
	}
?>