<?php

function get_operation_select($fields){
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

$pw_report_options_part=array(

	array(
		'id' => 'pw_report_metaboxname_fields_options_general_setting',
		'title' => __('General Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-cogs"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_general_setting'
	),
	array(
		'id' => 'pw_report_metaboxname_fields_options_email_setting',
		'title' => __('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-at"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_email_setting'
	),
	array(
		'id' => 'pw_report_metaboxname_fields_options_search_form',
		'title' => __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-check-square-o"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_search_form'
	),
	array(
		'id' => 'pw_report_metaboxname_fields_options_projected',
		'title' => __('Target',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-bar-chart"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_projected'
	),
	array(
		'id' => 'pw_report_metaboxname_fields_options_translate',
		'title' => __('Translate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-language"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_translate'
	),
	array(
		'id' => 'pw_report_metaboxname_fields_options_license',
		'title' => __('License info',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'icon' => '<i class="fa fa-info"></i>',
		'variable' => 'pw_report_metaboxname_fields_options_license'
	)
);

//	if(!defined('__PW_TAX_FIELD_ADD_ON__') && !defined('__PW_PO_ADD_ON__') && !defined('__PW_BRANDS_ADD_ON__'))
//	{
//		unset($pw_report_options_part[2]);
//	}



//GENERAL SETTING
$pw_report_metaboxname_fields_options_general_setting = array(
	array(
		'label'	=> __('System Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_sys_search',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_sys_search',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __('Shop Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set default shop order status, Selected status will be used for calculating salse amount. Default statuses : completed, on-hold and processing',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'order_status',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'order_status',
		'type'	=>'order_status',
	),

	array(
		'label'	=> __('Hide Trash Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'otder_status_hide',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'otder_status_hide',
		'type'	=>'checkbox',
	),
	array(
		'label'	=> __('Invoice Logo',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Upload an image as Invoice Pdf logo',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'invoice_logo',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'invoice_logo',
		'type'	=>'upload',
	),
	array(
		'label'	=> __('Invoice Footer Text',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set footer text for invoice pdf',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'invoice_footer_text',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'invoice_footer_text',
		'type'	=>'text',
	),
	array(
		'label'	=> __('Dashboard Setting',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __('Dashboard Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('You can enable/disable dashboard and set another report as default report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status',
		'type'	=>'select',
		'options'	=>array(
			'one' => array(
				'label' => __('Enable, please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'true',
			),
			'two' => array(
				'label' => __('Disable, please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'false',
			),
		),
	),
	array(
		'label'	=> __('Alternative Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Choose which one of reports that you want to display insted dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_alt',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_alt',
		'type'	=>'reports',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','false')
		),
	),
	array(
		'label'	=> __('Dashboard Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Do you want to set customize date for dashboard search ?',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date',
		'type'	=>'select',
		'options'	=>array(
			'one' => array(
				'label' => __('No, I want use default date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'false',
			),
			'two' => array(
				'label' => __('Yes, please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'true',
			),
		),
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('From Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set from date for dashboard search',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'from_date',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'from_date',
		'ids'	=>'pwr_from_date',
		'type'	=>'datepicker',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status',__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true') ,
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date' => array('select','true') ,
		),
	),
	array(
		'label'	=> __('To Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set to date for dashboard search',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'to_date',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'to_date',
		'ids'	=>'pwr_to_date',
		'type'	=>'datepicker',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status',__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true') ,
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customize_date' => array('select','true') ,
		),
	),
	array(
		'label'	=> __('Disable Map in Dashboard ?',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'disable_map',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'disable_map',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Disable Charts in Dashboard ?',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'disable_chart',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'disable_chart',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Dashboard Box Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Recent Order table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Product table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_product_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_product_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Category table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_category_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_category_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Customer table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_customer_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_customer_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Billing Country table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_country_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_country_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top State Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top State Country table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Payment Gateway table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_gateway_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_gateway_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),
	array(
		'label'	=> __('Top Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Minimum page number for Top Coupon table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',
		'type'	=>'numeric',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status' => array('select','true')
		),
	),

);

//CUSTOM WORK - 12679
if(is_array(__CUSTOMWORK_ID__) && in_array('12679',__CUSTOMWORK_ID__)){
	$extra_cols=array();
	$extra_cols[]= array(
		'label'	=> __('Clinic Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set default shop order status, Selected status will be used for calculating salse amount. Default statuses : completed, on-hold and processing',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'clinic_type',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'clinic_type',
		'type'	=>'text',
	);
	array_splice($pw_report_metaboxname_fields_options_general_setting,3,0,$extra_cols);
}


//EMAIL SETTING
$pw_report_metaboxname_fields_options_email_setting = array(

	array(
		'label'	=> __('Active Email Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email',
		'type'	=>'checkbox',
	),

	array(
		'label'	=> __('Email Today Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'today_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'today_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Yesterday Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'yesterday_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'yesterday_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Current Week Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_week_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_week_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Last Week Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_week_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_week_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Current Month Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_month_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_month_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Last Month Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_month_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_month_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Current Year Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_year_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cur_year_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Last Year Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_year_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'last_year_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Till Today Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'till_today_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'till_today_email',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Email Total/Other/Today Summary Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'total_summary',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'total_summary',
		'type'	=>'checkbox',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),

	//CUSOM WORK - 4061
//	array(
//		'label'	=> __('Email Purchased Product by Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//		'desc'	=> __('Yes, Please',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'product_by_customer',
//		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'product_by_customer',
//		'type'	=>'checkbox',
//		'dependency' => array(
//			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
//			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
//		),
//	),

	array(
		'label'	=> __('Email Send To',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Receiver Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sendto_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sendto_email',
		'type'	=>'text',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('From Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Sender Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'from_name',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'from_name',
		'type'	=>'text',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('From Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Sender Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sendfrom_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sendfrom_email',
		'type'	=>'text',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
	array(
		'label'	=> __('Subject',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set Email Subject',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'subject_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'subject_email',
		'type'	=>'text',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),

	array(
		'label'	=> __('Email Schedule',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set the email schedule',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule',
		'type'	=>'select',
		'options'	=>array(
			'00' => array(
				'label' => __('Select One',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => '0',
			),
			'0' => array(
				'label' => __('Once Hourly',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_hourly',
			),
			'1' => array(
				'label' => __('Once Daily',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_daily',
			),
			'2' => array(
				'label' => __('Once Weekly',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_weekly',
			),
			'3' => array(
				'label' => __('Once Monthly',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_monthly',
			),
			'4' => array(
				'label' => __('Twice Hourly',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_twicehourly',
			),
			'5' => array(
				'label' => __('Twice Daily',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_twicedaily',
			),
			'6' => array(
				'label' => __('Twice Weekly',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'pw_schd_twiceweekly',
			)

		),
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),

	array(
		'label'	=> __('Optimize Email Format',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Check this fields, if your email is unformatted.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'optimize_email',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'optimize_email',
		'type'	=>'checkbox',
	),

	array(
		'label'	=> __('Test Mail',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('You should save the form before send test mail.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'test_email',
		'id'    => 'pw_rpt_test_email',
		'href'	=>'#',
		'type'	=>'link',
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email' => array('checkbox','true')
		),
	),
);



//ADD-ONS SETTING
$pw_brands_fields=array();
if(defined("__PW_BRANDS_ADD_ON__")){
	$pw_brands_fields=array(
		array(
			'label'	=> __("Brands Add-ons",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("Set the Brands add-ons settings.",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_brands_addons',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_brands_addons',
			'type'	=> 'notype',
		),
		array(
			'label'	=> __('Enable Brand Taxonomy',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Do you want to enable brand taxonomy ?',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand',
			'type'	=>'select',
			'options'	=>array(
				'1' => array(
					'label' => __('No, at all',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'no',
				),
				'2' => array(
					'label' => __('Yes, I want use Proword Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'yes_this',
				),
				'3' => array(
					'label' => __('Yes, I have Brands plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'yes_another',
				),

			)
		),
		array(
			'label'	=> __('Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Choose your "Brands" plugin name.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brands_plugin',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brands_plugin',
			'type'	=>'select',
			'options'	=>array(
				'1' => array(
					'label' => __('WooCommerce Brands By Woothemes',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'product_brand',
				),
				'2' => array(
					'label' => __('Ultimate WooCommerce Brands Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'product_brand',
				),
				'3' => array(
					'label' => __('YITH WOOCOMMERCE BRANDS ADD-ON',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'yith_product_brand',
				),
				'4' => array(
					'label' => __('Other Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					'value' => 'other',
				),
			),
			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand' => array('select','yes_another'),
			),
		),
		array(
			'label'	=> __('Taxonomy Slug',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set the taxonomy slug plugin. <br /><strong>Exp : </strong>WooCommerce Brands by Woothemes : product_brand ',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_slug',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_slug',
			'type'	=>'text',

			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand',__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brands_plugin'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand' => array('select','yes_another'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brands_plugin' => array('select','other')
			),
		),
		array(
			'label'	=> __('Brand Label',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set brand label.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_label',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_label',
			'type'	=>'text',

			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand' => array('select','yes_another','yes_this'),
			),
		),
		array(
			'label'	=> __('Brand Thumnail',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Show brands as thumbnail in grid column.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_thumb',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'brand_thumb',
			'type'	=>'checkbox',

			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_brand' => array('select','yes_this'),
			),
		)
	);
}

////ADDED IN VER4.0
/// PRODUCT OPTIONS CUSTOM FIELDS
$product_options_fields=array();
if(defined("__PW_PO_ADD_ON__") && defined("THEMECOMPLETE_EPO_VERSION")) {
	$product_options_fields=array(
		////ADDED IN VER4.0
		/// PRODUCT OPTIONS CUSTOM FIELDS
		array(
			'label'	=> __("Product Options Plugin",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("You can add fields generated by Product Options Plugin to Some Reports",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_sectio',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_sectio',
			'type'	=> 'notype',
		),
		array(
			'label' => '',
			'desc'  => __('Select your custom fields for serach query, This fields just will be displayed in All Orders Reort (Based on Taxonomies)',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_custom_fields',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_custom_fields',
			'type'  => 'tab_multi_side',
		),
		array(
			'label'	=> __("Checkout Options Plugin",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("You can add fields generated by Checkout Options Plugin to Some Reports",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_checkout_sectio',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_checkout_sectio',
			'type'	=> 'notype',
		),
		array(
			'label' => '',
			'desc'  => __('Select your custom fields for serach query, This fields just will be displayed in All Orders Reort (Based on Taxonomies)',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_checkout_custom_fields',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_checkout_custom_fields',
			'type'  => 'tab_multi_side_checkout',
		),
	);
}


////ADDED IN VER4.0
/// TAX CUSTOM FIELDS
$custom_tax_fields=array();
if(defined("__PW_TAX_FIELD_ADD_ON__")) {
	$custom_tax_fields=array(
		array(
			'label'	=> __("Custom Taxonomy & Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("In this section you can choose which WooCommerce Taxonomy and which Custom fields appear in Report.",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'type'	=> 'notype',
		),
		array(
			'label'	=> __('Custom Taxonmy',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> "",
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'type'	=> 'custom_search_items',
		),
		array(
			'label' => __('Custom Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'  => __('Select your custom fields for serach query, This fields just will be displayed in All Orders Reort (Based on Taxonomies)',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<br />'.__('<strong>Note : </strong> Individual fields are used for specific products , so you can set to display them in data table only, Not as filter.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields',
			'type'  => 'multi_side',
		),
	);
}


////ADDED IN VER4.1
/// PRODUCT OPTIONS CUSTOM FIELDS
$custom_int_fields=array();
if(defined("__PW_INT_REPORTS_ADD_ON__")) {
	$custom_int_fields=array(
		////ADDED IN VER4.0
		/// PRODUCT OPTIONS CUSTOM FIELDS
		array(
			'label'	=> __("INTELLIGENCE REPORTS - RFM - AUTOMATION",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("Customers who have purchased more recently, more frequently, and have spent more money, are likelier to buy again. But those who haven't, are less valuable for the company and therefore, likely to churn. By giving points, for various hierarchies you can easily find out who are your best customers.",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_sectio',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_sectio',
			'type'	=> 'notype',
		),

		array(
			'label'	=> __("Recency Points",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("How recently did the customer purchase?",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_recency_point',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_recency_point',
			'type'  => 'recency_numeric_int',
		),
		array(
			'label'	=> __("Frequency Points",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("How often do they purchase?",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_frequency_point',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_frequency_point',
			'type'  => 'frequency_numeric_int',
		),
		array(
			'label'	=> __("Monetary Value",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("How much do they spend?",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_monetary_point',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_monetary_point',
			'type'  => 'monetary_numeric_int',
		),
	);
}



//CUSTOM WORK - 12300
/// TICKERA CUSOTM FIELDS
$tickera_fields=array();
if(defined("__PW_TICKERA_ADD_ON__")) {
	$tickera_fields=array(
		array(
			'label'	=> __("Tickera Plugin",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __("You can add fields generated by Tickera Plugin to Some Reports",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_sectio',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_sectio',
			'type'	=> 'notype',
		),
		array(
			'label' => '',
			'desc'  => __('Select your custom fields for serach query, This fields just will be displayed in All Orders Reort.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_custom_fields',
			'name'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_custom_fields',
			'type'  => 'tab_multi_side_tickera',
		),
	);
}




$pw_report_metaboxname_fields_options_search_form = array(

//		array(
//			'label'	=> __("Currency Switcher - Multiple Currency",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//			'desc'	=> '',
//			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
//			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
//			'type'	=> 'notype',
//		),
//		array(
//			'label'	=> __('Enable Currency Swither',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//			'desc'	=> __('If you have "WooCommerce Currency Switcher" plugin, You price will be convert to your admin currency',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'currency_switcher',
//			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'currency_switcher',
//			'type'	=> 'checkbox',
//		),

  


	//COST OF GOOD
	array(
		'label'	=> __('Cost of Good',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_enable_cog',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_enable_cog',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __('Enable Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Do you want to enable custom field of cost of god ?',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',
		'type'	=>'select',
		'options'	=>array(
			'one' => array(
				'label' => __('No, at all',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'no',
			),
			'two' => array(
				'label' => __('Yes, I have Cost of Good plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'yes_another',
			),
//				'three' => array(
//					'label' => __('Yes, I want use this Cost of Good',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//					'value' => 'yes_this',
//				),
		)
	),


	array(
		'label'	=> __('Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Choose your "Cost of Goods/Profit" plugin name.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin',
		'type'	=>'select',
		'options'	=>array(
			'one' => array(
				'label' => __('WooCommerce Cost of Goods by Woothemes',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'woo_profit',
			),
			'two' => array(
				'label' => __('WooCommerce Profit of Sales Report by IndoWebKreasi',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				'value' => 'indo_profit',
			),
//				'three' => array(
//					'label' => __('Other Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//					'value' => 'other',
//				),
		),
		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog' => array('select','yes_another'),
		),
	),

	array(
		'label'	=> __('Custom field 1',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set the custom field of plugin. <br /><strong>Exp : </strong>WooCommerce Cost of Goods by Woothemes : _wc_cog_cost <br />WooCommerce Profit of Sales Report by IndoWebKreasi : _posr_cost_of_good',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field',
		'type'	=>'text',

		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog' => array('select','yes_another'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin' => array('select','other'),
		),
	),
	array(
		'label'	=> __('Custom field 2',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __('Set the total custom field of plugin. <br /><strong>Exp : </strong>WooCommerce Cost of Goods by Woothemes : _wc_cog_item_total_cost  <br />WooCommerce Profit of Sales Report by IndoWebKreasi : _posr_line_cog_total',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field_total',
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field_total',
		'type'	=>'text',

		'dependency' => array(
			'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog' => array('select','yes_another'),
			__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin' => array('select','other'),
		),
	),
);

if(defined("__PW_BRANDS_ADD_ON__")) {
	global $pw_rpt_main_class;
	$pw_rpt_main_class->array_insert( $pw_report_metaboxname_fields_options_search_form, $pw_brands_fields, 0 );
	//$menu_fields=$pw_rpt_main_class->array_insert_after("4",$pw_report_metaboxname_fields_options_search_form,"5",$pw_brands_fields);
}

if(defined("__PW_PO_ADD_ON__") && defined("THEMECOMPLETE_EPO_VERSION")) {
	global $pw_rpt_main_class;
	$pw_rpt_main_class->array_insert( $pw_report_metaboxname_fields_options_search_form, $product_options_fields, 0 );
	//$menu_fields=$pw_rpt_main_class->array_insert_after("2",$pw_report_metaboxname_fields_options_search_form,"3",$product_options_fields);
}

if(defined("__PW_TAX_FIELD_ADD_ON__") ) {
	global $pw_rpt_main_class;
	$pw_rpt_main_class->array_insert( $pw_report_metaboxname_fields_options_search_form, $custom_tax_fields, 0 );
	//$menu_fields=$pw_rpt_main_class->array_insert_after("2",$pw_report_metaboxname_fields_options_search_form,"3",$custom_tax_fields);
}


if(defined("__PW_INT_REPORTS_ADD_ON__") ) {
	global $pw_rpt_main_class;
	$pw_rpt_main_class->array_insert( $pw_report_metaboxname_fields_options_search_form, $custom_int_fields, 0 );
}

//CUSTOM WORK - 12300
if(defined("__PW_TICKERA_ADD_ON__")) {
	global $pw_rpt_main_class;
	$pw_rpt_main_class->array_insert( $pw_report_metaboxname_fields_options_search_form, $tickera_fields, 0 );
	//$menu_fields=$pw_rpt_main_class->array_insert_after("2",$pw_report_metaboxname_fields_options_search_form,"3",$tickera_fields);
}

//FETCH YEARS
global $wpdb;

$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date ASC LIMIT 1";
$results= $wpdb->get_results($order_date);

$first_date='';
if(isset($results[0]))
	$first_date=$results[0]->order_date;

if($first_date==''){
	$first_date= date("Y-m-d");
	$first_date=substr($first_date,0,4);
}else{
	$first_date=substr($first_date,0,4);
}

$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date DESC LIMIT 1";
$results= $wpdb->get_results($order_date);

$pw_to_date='';
if(isset($results[0]))
	$pw_to_date=$results[0]->order_date;

if($pw_to_date==''){
	$pw_to_date= date("Y-m-d");
	$pw_to_date=substr($pw_to_date,0,4);
}else{
	$pw_to_date=substr($pw_to_date,0,4);
}




$cur_year=date("Y-m-d");
$cur_year=substr($cur_year,0,4);

$option="";
for($year=($first_date-5);$year<($pw_to_date+10);$year++)
{
	$year_arr[$year]=array (
		'label'	=> $year,
		'value'	=> $year
	);
}


//SEARCH OPTION
$pw_report_metaboxname_fields_options_projected= array(
	array(
		'label' => __('Projected Sales Year',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'  => __('Choose Year',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year',
		'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year',
		'type'  => 'select_year' ,
		'options'	=> $year_arr,
	),
	array(
		'label'	=> __("Set Sales of monthes",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_year_sale',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_year_sale',
		'type'	=> 'notype',
	),
	array(
		'label'	=> "",
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes',
		'type'	=> 'monthes',
	),

);

//TRANSLATE
$pw_report_metaboxname_fields_options_translate= array(
	array(
		'label'	=> __("Set Your Translate(s)",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_translate',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __("Set Translate for January",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jan_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jan_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for February",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'feb_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'feb_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for March",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'mar_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'mar_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for April",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'apr_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'apr_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for May",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'may_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'may_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for June",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jun_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jun_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for July",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jul_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'jul_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for August",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'aug_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'aug_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for September",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sep_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'sep_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for October",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'oct_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'oct_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for November",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'nov_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'nov_translate',
		'type'	=> 'text',
	),
	array(
		'label'	=> __("Set Translate for December",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dec_translate',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dec_translate',
		'type'	=> 'text',
	),

);

//LICENSE INFO
$pw_report_metaboxname_fields_options_license= array(
	array(
		'label'	=> __("Plugin Info",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'plugin_info',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'plugin_info',
		'type'	=> 'notype',
	),
	array(
		'label'	=> __("Set Your Custom Work ID",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'desc'	=> __("Set Your Custom Work ID if You Ordered the Custom Work.",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customwork_id',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'customwork_id',
		'type'	=> 'text',
	),
	array(
		'label'	=> "",
		'desc'	=> "",
		'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'license',
		'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'license',
		'type'	=> 'text_info',
	),
);



if (isset($_POST["update_settings"])) {

//	print_r($_POST);

	// Do the saving
	if(!in_array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields',$_POST)){
		delete_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields');
	}


	foreach($_POST as $key=>$value){

		if(!isset($_POST[$key])){
			delete_option($key);
			continue;
		}

		$old = get_option($key);
		$new = $value;

		if(!is_array($new))
		{

			$original_args 					= array();
			$timestamp 						= time();

			//SAVE SCHEDULE IN SETTING
			if(isset($_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule'])){

				$schedule_activate_old			= get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email',0);
				$schedule_recurrence_old		= get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule',0);

				$schedule_activate				= isset($_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email']) ? $_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'active_email'] : 0;
				$schedule_recurrence			= isset($_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule']) ? $_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'email_schedule'] : 0;
				$schedule_hook_name				= __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'_schedule_mailing_sales_status_event';

				if($schedule_activate_old!='' && ($schedule_activate_old != $schedule_activate) or ($schedule_recurrence_old != $schedule_recurrence)){
					//echo "action";
					wp_unschedule_event( $timestamp, $schedule_hook_name, $original_args );
					wp_clear_scheduled_hook( $schedule_hook_name, $original_args );
				}

				if($schedule_activate == 'on'){
					if(strlen($schedule_recurrence) > 2){
						if (!wp_next_scheduled($schedule_hook_name)){
							wp_schedule_event($timestamp, $schedule_recurrence, $schedule_hook_name);
						}
					}
				}

			}

			if ($new && $new != $old) {
				update_option($key, $new);
			} elseif ('' == $new && $old) {
				delete_option($key);
			}
		}elseif($key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'order_status' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_custom_fields' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'po_checkout_custom_fields' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_recency_point' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_frequency_point' || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'int_monetary_point'
		        || $key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_custom_fields'){
			//print_r($_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'tickera_custom_fields']);
			if ($new && $new != $old) {
				update_option($key, $new);
			} elseif ('' == $new) {
				delete_option($key);
			}
		}
		else{

			$get_year=array_keys($value);
			$get_year=$get_year[0];

			foreach($value[$get_year] as $keys=>$vals){

				$old = get_option($key."_".$get_year."_".$keys);
				$new = $vals;

				if ($new && $new != $old) {
					update_option($key."_".$get_year."_".$keys, $new);
				} elseif ('' == $new && $old) {
					delete_option($key."_".$get_year."_".$keys);
				}

			}
		}
	}


	//SET THE COST OF GOOD CUSTOM FIELD
	/*$enable_cog=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',"no");
	if($enable_cog=='yes_another'){
		$cog_plugin=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin',"woo_profit");
		$profit_fields=array(
			'woo_profit' => array(
				'field' => '_wc_cog_cost',
				'total' => '_wc_cog_item_total_cost',
			),
			'indo_profit' => array(
				'field' => '_posr_cost_of_good',
				'total' => '_posr_line_cog_total',
			),

		);

		if($cog_plugin=='other'){
			$cog_field=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field',"_PW_COST_GOOD_FIELD");
			define ('__PW_COG__',$cog_field);

			$cog_field=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field_total',"_PW_COST_GOOD_FIELD");
			define ('__PW_COG_TOTAL__',$cog_field);
		}else{
			$cog_field=$profit_fields[$cog_plugin]['field'];
			define ('__PW_COG__',$cog_field);

			$cog_total=$profit_fields[$cog_plugin]['total'];
			define ('__PW_COG_TOTAL__',$cog_total);
		}

	}else if($enable_cog=='yes_this'){

		define ('__PW_COG__','_PW_COST_GOOD_FIELD');
		define ('__PW_COG_TOTAL__','_PW_COST_GOOD_ITEM_TOTAL_COST');
	}else{
		define ('__PW_COG__','');
		define ('__PW_COG_TOTAL__','');
	}*/


	/*		die("d");
	foreach($pw_report_options_part as $option_part){
		$this_part_variable=${$option_part['variable']};
		foreach ($this_part_variable as $field) {

			if(!isset($_POST[$field['id']])){
				delete_option($field['id']);
				continue;
			}

			$old = get_option($field['id']);
			$new = $_POST[$field['id']];
			if ($new && $new != $old) {
				update_option($field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_option($field['id']);
			}

		} // end foreach
	}*/
	?>
    <div id="setting-error-settings_updated" class="updated settings-error">
        <p><strong><?php echo __('Settings saved',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>.</strong></p>
    </div>
	<?php
}


$html= '<div class="wrap">
            <div class="row">    
			    <div class="col-xs-12">
			    <div class="awr-box">
			         <div class="awr-title">
                        <h3>
                            <i class="fa fa-cog"></i>
                        '.__('Woo Report Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'    
                        </h3>
                        
                                                
                    </div>
			         <div class="awr-box-content">
			         
                
                <form method="POST" action="" class=" awr-setting-form">
                    <input type="hidden" name="update_settings" value="Y" />
                    <input type="hidden" name="update_setting" value="NN" />
                    <div class="tabs tabsA tabs-style-underline"> 
                        <nav>
                            <ul>';
foreach($pw_report_options_part as $option_part){
	$html.='<li><a href="#'.$option_part['id'].'" class="">'.$option_part['icon'].' <span>'.$option_part['title'].'</span></a></li>';
}
$html.='
                            </ul>
                        </nav>
                        <div class="content-wrap">';


foreach($pw_report_options_part as $option_part){
	//TAB TITLE


	$html.= '<section id="'.$option_part['id'].'">';
	$html.= '<table class="form-table">';
	$this_part_variable=${$option_part['variable']};
	foreach ($this_part_variable as $field) {
		if(isset($field['dependency']))
		{
			$html.= pw_report_dependency($field['id'],$field['dependency']);
		}
		// get value of this field if it exists for this post
		$meta = get_option($field['id']);
		//echo $field['id'];
		//if($field['id'])
		// begin a table row with
		$extra_class='';
		if($field['type']=='notype')
			$extra_class='awr-setting-title';
		$html.= '<tr class="'.$field['id'].'_field '.$extra_class.'" > ';

		$cols='';
		if($field['type']=='custom_search_items' || $field['type']=='tab_multi_site'){
			$cols='colspan="2"';
		}else{
			//$html.= '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th> ';
		}
		$html.= '
					<td '.$cols.'>
					<div class="awr-form-title"><label for="'.$field['id'].'">'.$field['label'].'</label></div>';
		switch($field['type']) {

			case 'notype':
				$html.= '<span class="description">'.$field['desc'].'</span>';
				break;

			case 'text_info':

				if ($this->dashboard($this->pw_plugin_status)){
					$html.= '<h3>Plugin is Licensed !</h3>';

					$result=$this->dashboard($this->pw_plugin_status);

					$html.='<div style="border-left:5px solid #eee;padding:5px;line-height:20px;letter-spacing: 1px;"><strong>Plugin Name : </strong>'.(isset($result['verify-purchase']['item_name']) ?? "").'';
					$html.='<br /><strong>Buyer Id : </strong>'.(isset($result['verify-purchase']['buyer']) ?? "").'';
					$html.='<br /><strong>Purchase Date : </strong>'.(isset($result['verify-purchase']['created_at']) ?? "").'';
					$html.='<br /><strong>License Type : </strong>'.(isset($result['verify-purchase']['licence']) ?? "").'';
					$html.='<br /><strong>Supported Until : </strong>'.(isset($result['verify-purchase']['supported_until']) ?? "").'</div>';
				}
				break;

			case 'text':
				$html.= '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" value="'.$meta.'" />
							<br /><span class="description">'.$field['desc'].'</span>	';
				break;

			case 'link':
				$html.= '<a class="button-primary" href="'.$field['href'].'" id="'.$field['id'].'" ><i class="fa fa-envelope-o"></i>'.__('Click Here',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>
<div class="email_target"></div>
                        <span class="description">'.$field['desc'].'</span>';
				break;

			case 'radio':
				foreach ( $field['options'] as $option ) {
					$html.= '<input type="radio" name="'.$field['id'].'" class="'.$field['id'].'" value="'.$option['value'].'" '.checked( $meta, $option['value'] ,0).' '.$option['checked'].' /> 
										<label for="'.$option['value'].'">'.$option['label'].'</label><br><br>';
				}
				break;

			case 'checkbox':
				$html.= '
                                <div class="awr-slidecheck">
                                    <input type="hidden" name="'.$field['id'].'" value="off"/> <input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" '.checked( $meta, "on" ,0).' /> 
                                    <label for="'.$field['id'].'"></label>
                                </div>
								<span class="description">'.$field['desc'].'</span>';
				break;

			case 'order_status':
				$pw_order_status=$this->pw_get_woo_orders_statuses();
				$option='';
				foreach($pw_order_status as $key => $value){
					$selected='';

					if(is_array($meta) && in_array($key,$meta))
						$selected='SELECTED';
					$option.="<option $selected value='".$key."' >".$value."</option>";
				}

				$html.= ' 
								<select name="'.$field['id'].'[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">';
				$html.='<option value="-1">'.__('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
				$html.=$option;
				$html.='
							</select>
								<br /><span class="description">'.$field['desc'].'</span>';
				break;

			case 'select':
				$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">';
				foreach ($field['options'] as $option) {
					$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';
				}
				$html.= '</select><br /><span class="description">'.__($field['desc'],__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>';
				break;

			case 'datepicker':{
				$html.='<input name="'.$field['id'].'" id="'.$field['ids'].'" type="text"  class="datepick"  value="'.$meta.'" /><br /><span class="description">'.$field['desc'].'</span>';
			}
				break;

			case 'reports':
				{
					global $pw_rpt_main_class;

					$all_menu='';
					foreach($pw_rpt_main_class->our_menu as $roots){

						if($roots['id']=='logo') continue;

						if(!isset($roots['childs']))
							$all_menu.='<option value="'.$roots['link'].'" '.$selected.'>'.$roots['label'].'</option>';
						else{
							$all_menu.='<optgroup label="'.$roots['label'].'">';
							foreach($roots['childs'] as $childs) {

								$selected = '';
								if ( $meta == $childs['link'] ) {
									$selected = 'SELECTED';
								}
								$all_menu.='<option value="'.$childs['link'].'" '.$selected.'>'.$childs['label'].'</option>';
							}
							$all_menu.='</optgroup>';
						}

					}

					$html.= '
							<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">
								'.$all_menu.'
							</select>
							
							<br /><span class="description">'.$field['desc'].'</span>	';
				}
				break;

			case "custom_search_items":{
				$custom_tax_pages=array(
					array("details_tax_field" => __("Taxonomies All Order",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("product" => __("Product",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("variation_stock" => __("Variation Stock",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					/*array("prod_per_month" => __("Product/Month",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("prod_per_country" => __("Product/Country",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("prod_per_state" => __("Product/State",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("stock_list" => __("Stock List",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),*/

				);


				$html.='
								<div class="col-md-12 bhoechie-tab-container">
									<div class=" col-md-3 bhoechie-tab-menu menu_tax">
									  <div class="list-group">';
				$i=0;
				foreach($custom_tax_pages as $tab){
					foreach($tab as $key=>$value){
						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='
												<a href="javascript:void(0);" class="list-group-item '.$active.' text-center">
												  <div class="awr-label-'.$key.'">'.$value.'</div>
												</a>';
					}
				}
				$html.='
									  </div>
									</div>
									<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bhoechie-tab menu_tax_content">';

				$i=0;
				foreach($custom_tax_pages as $tab){

					foreach($tab as $key=>$value){

						if(in_array($key,array("prod_per_month","prod_per_state","prod_per_country")) && !defined("__PW_CROSSTABB_ADD_ON__")){
							$html.='<div class="bhoechie-tab-content '.$active.'"><center>';
							$html.= '<h4><center><i class="fa fa-4x fa-user-times"></i><br />'.__("'CrossTab Add-on' is needed! Please Purchase/Active it. <br />click ",__PW_REPORT_WCREPORT_TEXTDOMAIN__)."<a target='_blank' href='".admin_url()."admin.php?page=wcx_wcreport_plugin_addons_report&parent=addons'>".__("Here",__PW_REPORT_WCREPORT_TEXTDOMAIN__)."</a>".__(" For more info !",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</center></h4>';
							$html.='</div>';
							continue;
						}

						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='<div class="bhoechie-tab-content '.$active.'"><center>';

						$original_query = 'product';

						$post_name='product';
						$option_data='';
						$param_line=$value;

						$all_tax=$this->fetch_product_taxonomies( $post_name );
						$current_value=array();
						if(is_array($all_tax) && count($all_tax)>0){

							$post_type_label=get_post_type_object( $post_name );
							$label=$post_type_label->label ;

							//FETCH TAXONOMY
							foreach ( $all_tax as $tax ) {

								if(strpos($tax,"pa_")!== false)
									continue;

								$taxonomy=get_taxonomy($tax);
								$values=$tax;
								$label=$taxonomy->label;
								$attribute_taxonomies = wc_get_attribute_taxonomies();

								$pw_display_type='';

								$meta=get_option($field['id'].'_'.$key.'_'.$tax);
								$meta_column=get_option($key.'_'.$tax.'_column');
								$translate=get_option($key.'_'.$tax.'_translate');

								//echo $meta. "# ".$field['id'].'_'.$tax;
								$checked='';
								if ($meta=="on")
									$checked = ' checked="checked"';

								$checked_col='';
								if ($meta_column=="on")
									$checked_col = ' checked="checked"';


								$html .=' 
														<div class="full-lbl-cnt more-padding">
															<label class="full-label">
																<input type="hidden" data-input="post_type" id="pw_checkbox_'.$key.'_'.$tax.'" name="'.$field['id'].'_'.$key.'_'.$tax.'" class="pw_taxomomy_checkbox" value="off">
																<input type="checkbox" data-input="post_type" id="pw_checkbox_'.$key.'_'.$tax.'" name="'.$field['id'].'_'.$key.'_'.$tax.'" class="pw_taxomomy_checkbox" '.$checked.'>
																Enable "'.$label.'"
															</label>
															<br />
															
															<label class="full-label">
																<input type="hidden" data-input="post_type" id="pw_column_'.$key.'_'.$tax.'" name="'.$key.'_'.$tax.'_column" class="pw_taxomomy_checkbox" value="off">
																<input type="checkbox" data-input="post_type" id="pw_column_'.$key.'_'.$tax.'" name="'.$key.'_'.$tax.'_column" class="pw_taxomomy_checkbox" '.$checked_col.'>
																Show "'.$label.'" in Grid
															</label>
															<br />
															
															
															<input type="text" name="'.$key.'_'.$tax.'_translate" value="'.$translate.'"/><span class="description">'.__('Set Label, Leave blank to use from default label',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>
														</div>
														<br />
														';

							}
						}

						//$html.=$param_line.'<hr />';
						$html.='</center></div>';
					}
				}
				$html.='
									</div>
								</div>
						<script>
							jQuery(document).ready(function($) {
								$("div.menu_tax>div.list-group>a").click(function(e) {
									e.preventDefault();
									$(this).siblings("a.active").removeClass("active");
									$(this).addClass("active");
									var index = $(this).index();
									$("div.menu_tax_content>div.bhoechie-tab-content").removeClass("active");
									$("div.menu_tax_content>div.bhoechie-tab-content").eq(index).addClass("active");
								});
							});
						</script>';
			}
				break;


			case 'multi_side':
				{
					global $wpdb;
					$options='';
					$selected_options='';
					$selected_options_product='';
					$selected_options_order='';
					$selected_options_gravity='';
					$selected_options_gravity_arr=array();
					$selected_options_addons_arr=array();
					$selected_options_addons='';

					if(is_array($meta)){
						foreach($meta as $opt){
							//$selected_options.= '<option value="'.$opt.'" SELECTED>'.$opt.'</option>';
						}
					}
					//$types = $wpdb->get_results("SELECT meta_key FROM ".$wpdb->postmeta." GROUP BY meta_key", ARRAY_A);


					//IF GRAVITY ACTIVED
					if(defined("GRAVITY_MANAGER_URL"))
					{

						$types = $wpdb->get_results("SELECT display_meta FROM {$wpdb->prefix}gf_form_meta", ARRAY_A);
						//print_r($types);
						if ($types!=null && is_array($types)) {
							$options.= '<optgroup label="Gravity From Fields">';
							foreach($types as $type){
								//print_r($type['display_meta']);
								$form = json_decode( $type['display_meta'] );
								//print_r($form->fields[1]->label);
								foreach($form->fields as $fields){
									$value=($fields->label);
									$value=str_replace(" ","_",$value);
									$options.= '<option value="'.$value.'">'.$fields->label.'</option>';



									if(is_array($meta) && in_array($value,$meta)){

										$selected_options_gravity_arr[$value] = '<option value="'.$value.'" SELECTED>'.$fields->label.'</option>';
									}
								}
							}
							$options.='</optgroup>';
						}

					}

					if(is_array($selected_options_gravity_arr) && count($selected_options_gravity_arr)>0)
					{
						$selected_options_gravity = '<optgroup label="Gravity From Fields">'.implode("",$selected_options_gravity_arr).'</optgroup>';
					}

					//IF PRODUCT ADDON ACTIVED
					if(class_exists("WC_Product_Addons"))
					{

						$types = $wpdb->get_results("SELECT meta_value,post_id FROM {$wpdb->prefix}postmeta where meta_key='_product_addons'", ARRAY_A);

						//print_r($type);
						//print_r($meta);
						$k=0;
						if ($types!=null && is_array($types)) {
							$options.= '<optgroup label="Product AddOn Fields">';
							foreach($types as $type){
								//print_r(unserialize ($type['meta_value']));
								$form = unserialize ( $type['meta_value'] );
								foreach($form as $fields){
									$value=($fields['options']);
									$parent=str_replace(" ","___",$fields['name']);
									$parent_label=get_the_title($type['post_id']).' - '.$fields['name'].'('.$fields['type'].')';
//											if($fields['type']=='checkbox')
//												continue;
									//print_r($value);



									$parent=$type['post_id'].'_'.$parent;
									$options.= '<option value="'.$parent.'">'.$parent_label.'</option>';



									if(is_array($meta) && in_array($parent,$meta)){
										//echo $parent_label;
										//echo $k++;
										$selected_options_addons_arr[$parent]= '<option value="'.$parent.'" SELECTED>'.$parent_label.'</option>';
									}

								}
							}
							$options.='</optgroup>';
						}

					}
					if(is_array($selected_options_addons_arr) && count($selected_options_addons_arr)>0)
					{
						$selected_options_addons = '<optgroup label="Product AddOn Fields">'.implode("",$selected_options_addons_arr).'</optgroup>';
					}


					//PRODUCT CUSTOM FIELDS
					$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='product'  GROUP BY pw_postmeta.meta_key", ARRAY_A);

					if ($types!=null && is_array($types)) {
						$options.= '<optgroup label="Product Fields">';
						foreach($types as $k=>$v) {
//								  if ($this->selected==null || !in_array($v['meta_key'], $this->selected)) {
							$options.= '<option value="'.$v['meta_key'].'">'.$v['meta_key'].'</option>';
							if(is_array($meta) && in_array($v['meta_key'],$meta)){
								$selected_options_product.= '<option value="'.$v['meta_key'].'" SELECTED>'.$v['meta_key'].'</option>';
							}
							//							  }
						}
						$options.= '</optgroup>';

					}
					if($selected_options_product!='')
					{
						$selected_options_product = '<optgroup label="Product Fields">'.$selected_options_product.'</optgroup>';
					}


					//ORDER CUSTOM FIELDS
					$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='shop_order' GROUP BY pw_postmeta.meta_key", ARRAY_A);


					if ($types!=null && is_array($types)) {
						$options.= '<optgroup label="Order Fields">';
						foreach($types as $k=>$v) {
//								  if ($this->selected==null || !in_array($v['meta_key'], $this->selected)) {
							$options.= '<option value="'.substr($v['meta_key'],1).'">'.$v['meta_key'].'</option>';
							if(is_array($meta) && in_array(substr($v['meta_key'],1),$meta)){
								$selected_options_order.= '<option value="'.substr($v['meta_key'],1).'" SELECTED>'.$v['meta_key'].'</option>';
							}

							//							  }
						}
						$options.= '</optgroup>';
					}
					if($selected_options_order!='')
					{
						$selected_options_order = '<optgroup label="Order Fields">'.$selected_options_order.'</optgroup>';
					}


					$selected_options=$selected_options_gravity.$selected_options_addons.$selected_options_product.$selected_options_order;

					$html.='
							
								
								<div class="description">'.$field['desc'].'</div><br />
							<div class="row pw-twoside-list">
								<div class="col-xs-4">
									<select name="from" id="undo_redo" class="form-control" size="11" multiple="multiple">
										'.$options.'
									</select>
								</div>
								
								<div class="col-xs-4 pw-twoside-btns">
									<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
									<button type="button" id="undo_redo_rightAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
									<button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
									<button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
									<button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>
									<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>
									
								</div>
								
								<div class="col-xs-4">
									<select name="'.$field['id'].'[]"  id="undo_redo_to" class="form-control" size="11" multiple="multiple" style="height: 162px;
">'.$selected_options.'</select>
									<button type="button" id="translate_fields" class="btn btn-warning btn-block" style="background-color:#0DBF44;border-color: #06A036; border-radius: 4px; margin-top: 5px;">'.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</button>
								</div>
							</div>	
							
							<div class="row">
								<div class="col-md-12" style="margin-top: 20px;">
									<div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #e0e0e0;color: #666;margin-bottom: 15px;">
										'.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
									</div>
									
								
									<div class="col-xs-12 pw_awr_fields_translate">';

					//$operators=array("eq"=>__('EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),);



					if(is_array($meta)){
						foreach($meta as $opt){
							//$label=str_replace("_"," ",$opt);

							$id_fieldss=explode("_",$opt);
							$id_field=$id_fieldss[0];
							$label=str_replace($id_field."_","",$opt);
							$label=str_replace("_"," ",$label);

							$html.= '
												<div class="col-xs-12 pw-translate">
													<input type="hidden" name="'.$opt.'_column" placeholder="Label for '.$opt.'" value="off">
													<input type="checkbox" name="'.$opt.'_column" placeholder="Label for '.$opt.'" "'.checked("on",get_option($opt.'_column'),0).'"> '.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
													<br />
													<input type="text" name="'.$opt.'_translate" placeholder="Label for '.$label.'" value="'.get_option($opt.'_translate').'">
													<select name="'.$opt.'_operator">
														'.get_operation_select(get_option($opt.'_operator')).'
													</select>
												</div>	
												<br />
											';
						}
					}
					$html.='		
									</div>
								</div>
							</div>		

							<script type="text/javascript"> 
								"use strict";
								jQuery(document).ready(function($) {
									$("#undo_redo").multiselect();
									$("#translate_fields").click(function(){
										$("#undo_redo_to option").prop("selected", true);
										var data="";
										data=$(".custom_report_set_default_fields_field").find("input[name],select[name],textarea[name]").serialize()+"&field='.$field['id'].'";
										//confirm($(".custom_report_set_default_fields_field").find("input[name],select[name],textarea[name]").serialize());
										
										var pdata = {
														action: "pw_rpt_fetch_custom_fields",
														postdata: data,
													}
										
										$.ajax ({
											type: "POST",
											url : ajaxurl,
											data:  pdata,
											success : function(resp){
												$(".pw_awr_fields_translate").html(resp);
											}
										});
									});
								});	
							</script>	
							';
				}
				break;


			////ADDE IN VER4.0
			/// PRODUCT OPTIONS PLUGIN
			case "tab_multi_side":{

				global $wpdb;
				$options='';
				$selected_options='';
				$selected_options_product='';
				$selected_options_order='';
				$selected_options_gravity='';
				$selected_options_addons='';


				//Fetch ALL FIELDS (All fields have value - used in orders)
				$options_all='';
				$types = $wpdb->get_results("SELECT pw_itemmeta.meta_value as meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta as pw_itemmeta where pw_itemmeta.meta_key='_tmcartepo_data'", ARRAY_A);

				$fields_array=array();
				if ($types!=null) {
					$options_all.= '<optgroup label="Order Fields">';
					foreach($types as $v) {

						$data=unserialize($v['meta_value']);
						foreach($data as $fields){
							if(!in_array($fields['name'],$fields_array)){

								$fields_array[]=$fields['name'];
								$options_all.= '<option value="'.$fields['name'].'">'.$fields['section_label'].'</option>';
							}

						}
					}
					$options_all.= '</optgroup>';
				}

				$options_all='';

				//Fetch GLOBAL FIELDS
				$meta_tab=isset($meta['po_global_fields_select']) ? $meta['po_global_fields_select'] : "" ;
				$fields_type=array('textarea', 'textfield', 'selectbox', 'radiobuttons', 'checkboxes', 'upload', 'date', 'time', 'range', 'color');

				$options_global=array();
				$selected_options_global=array();
				$types = $wpdb->get_results("SELECT pw_post.post_title,pw_postmeta.meta_value as meta_value FROM {$wpdb->prefix}posts as pw_post 
                                            INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                            where pw_post.post_type='tm_global_cp' AND pw_post.post_status IN ('publish') 
                                            AND pw_postmeta.meta_key='tm_meta'", ARRAY_A);

				$fields_array=array();
				if ($types!=null) {
					$options_all.= '<optgroup label="'.__('Global Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'">';
					foreach($types as $v) {
						if(!$v['meta_value']) continue;
						$parent_id=$v['post_title'];
						//$options_global.= '<optgroup label="'.$v['post_title'].'">';

						$data=unserialize($v['meta_value']);
						foreach($fields_type as $f_type){
							//if($f_type=='upload') continue;
							if(isset($data['tmfbuilder'][$f_type.'_header_title'])){
								$i=0;
								foreach($data['tmfbuilder'][$f_type.'_header_title'] as $fields){
									if(!in_array($fields,$fields_array)){
										$value=$fields;
										$label=$data['tmfbuilder'][$f_type.'_header_title'][$i++];
										$fields_array[]=$fields;

										if(is_array($meta_tab) && in_array($value,$meta_tab)){
											$selected="SELECTED";
											if(isset($selected_options_global[$parent_id]))
												$selected_options_global[$parent_id].='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';
											else
												$selected_options_global[$parent_id]='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';

										}

										if(isset($options_global[$parent_id]))
											$options_global[$parent_id].= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';
										else
											$options_global[$parent_id]= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';


										$options_all.= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';
									}
								}
							}
						}

						//$options_global.= '</optgroup>';
					}
					$options_all.= '</optgroup>';


					$options_globals='';
					if(is_array($options_global)) {
						foreach ( $options_global as $key => $val ) {
							$options_globals .= '<optgroup label="' .  $key  . '">';
							$options_globals .= $val;
							$options_globals .= '</optgroup>';
						}
					}

					$selected_options_globals='';
					if(is_array($selected_options_global)) {
						foreach ( $selected_options_global as $key => $val ) {
							$selected_options_globals .= '<optgroup label="' .  $key  . '">';
							$selected_options_globals .= $val;
							$selected_options_globals .= '</optgroup>';
						}
					}

				}



				//Fetch INDIVIDUAL FIELDS
				$fields_type=array('textarea', 'textfield', 'selectbox', 'radiobuttons', 'checkboxes', 'upload', 'date', 'time', 'range', 'color');

				$meta_tab=isset($meta['po_individual_fields_select']) ? $meta['po_individual_fields_select'] : "" ;
				$options_individual=array();
				$selected_options_individual=array();

				$types = $wpdb->get_results("SELECT pw_post.ID,pw_post.post_title,pw_postmeta.meta_value as meta_value FROM {$wpdb->prefix}posts as pw_post 
                                            INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                            where pw_post.post_type='product' AND pw_post.post_status IN ('publish') 
                                            AND pw_postmeta.meta_key='tm_meta'", ARRAY_A);

				$fields_array=array();
				if ($types!=null) {
					$options_all.= '<optgroup label="'.__('Individual Fields - Builder',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'">';
					foreach($types as $v) {
						$parent_id=$v['post_title'];
						if(!$v['meta_value']) continue;
						//$options_individual.= '<optgroup label="'.$v['post_title'].'">';

						$data=unserialize($v['meta_value']);
						foreach($fields_type as $f_type){
							if(isset($data['tmfbuilder'][$f_type.'_internal_name'])){
								$i=0;
								foreach($data['tmfbuilder'][$f_type.'_internal_name'] as $fields){
									if(!in_array($fields,$fields_array)){
										$value=$fields;
										$label=$data['tmfbuilder'][$f_type.'_header_title'][$i++];
										$fields_array[]=$fields;

										$selected="";
										if(is_array($meta_tab) && in_array($value,$meta_tab)){
											$selected="SELECTED";
											if(isset($selected_options_individual[$parent_id]))
												$selected_options_individual[$parent_id].='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';
											else
												$selected_options_individual[$parent_id]='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';

										}

										if(isset($options_individual[$parent_id]))
											$options_individual[$parent_id].= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';
										else
											$options_individual[$parent_id]= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';

										$options_all.= '<option value="'.$value.'">'.$label.'-('.$f_type.')</option>';
									}
								}
							}
						}

						//$options_individual.= '</optgroup>';
					}
					$options_all.= '</optgroup>';



					$options_indiv='';
					if(is_array($options_individual)) {
						foreach ( $options_individual as $key => $val ) {
							$options_indiv .= '<optgroup label="' .  $key  . '">';
							$options_indiv .= $val;
							$options_indiv .= '</optgroup>';
						}
					}

					$selected_options_indiv='';
					if(is_array($selected_options_individual)) {
						foreach ( $selected_options_individual as $key => $val ) {
							$selected_options_indiv .= '<optgroup label="' .  $key  . '">';
							$selected_options_indiv .= $val;
							$selected_options_indiv .= '</optgroup>';
						}
					}

				}

				//Fetch ATTRIBUTE FIELDS
				$meta_tab=isset($meta['po_atttribute_fields_select']) ? $meta['po_atttribute_fields_select'] : "" ;
				$options_attr=array();
				$selected_options_attr=array();
				$types = $wpdb->get_results("SELECT pw_post.post_parent as parent_id, pw_postmeta.meta_value as meta_value 
                                            FROM {$wpdb->prefix}posts as pw_post 
                                            INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                            where pw_post.post_type='tm_product_cp' AND pw_post.post_status IN ('publish') 
                                            AND pw_postmeta.meta_key='tmcp_attribute'", ARRAY_A);


				$fields_array=array();
				if ($types!=null) {
					$options_all.= '<optgroup label="'.__('Attribute Fields - Normal',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'">';
					foreach($types as $v) {

						$parent_id=$v['parent_id'];


						$data=($v['meta_value']);
						if(!in_array($data,$fields_array)){
							$value=$data;
							$product=wc_get_product($parent_id);
							$attributes = $product->get_attributes();

							$label=$attributes[$value]['name'];
							$fields_array[]=$fields;


							if(is_array($meta_tab) && in_array($value,$meta_tab)){
								$selected="SELECTED";
								if(isset($selected_options_attr[$parent_id]))
									$selected_options_attr[$parent_id].='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';
								else
									$selected_options_attr[$parent_id]='<option value="'.$value.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';

							}else{
								if(isset($options_attr[$parent_id]))
									$options_attr[$parent_id].= '<option value="'.$value.'" >'.$label.'-('.$f_type.')</option>';
								else
									$options_attr[$parent_id]= '<option value="'.$value.'" >'.$label.'-('.$f_type.')</option>';
							}


						}

					}

					$options_attrib='';
					if(is_array($options_attr)) {
						foreach ( $options_attr as $key => $val ) {
							$options_attrib .= '<optgroup label="' . get_the_title( $key ) . '">';
							$options_attrib .= $val;
							$options_attrib .= '</optgroup>';
						}
					}


					$selected_options_attrib='';
					if(is_array($selected_options_attr)){
						foreach($selected_options_attr as $key=>$val){
							$selected_options_attrib.= '<optgroup label="'.get_the_title($key).'">';
							$selected_options_attrib.= $val;
							$selected_options_attrib.= '</optgroup>';
						}
					}


					$options_all.= '</optgroup>';
				}


				//$selected_options=$selected_options_gravity.$selected_options_addons.$selected_options_product.$selected_options_order;

				$args=array(
					"id" => "po_all_fields_select" ,
					"tab_id" => "po_all_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_all ,
					"selected" => "" ,
					"meta" => $meta ,
				);

				$args2=array(
					"id" => "po_global_fields_select" ,
					"tab_id" => "po_global_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_globals,
					"selected" => $selected_options_globals,
					"meta" => $meta ,
				);

				$args3=array(
					"id" => "po_individual_fields_select" ,
					"tab_id" => "po_individual_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_indiv ,
					"selected" => $selected_options_indiv ,
					"meta" => $meta ,
				);

				$args4=array(
					"id" => "po_atttribute_fields_select" ,
					"tab_id" => "po_atttribute_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_attrib ,
					"selected" => $selected_options_attrib ,
					"meta" => $meta ,
				);

				//print_r($meta);

				//$html_multi["po_all_fields"]=pw_generate_multi_side($args);
				$html_multi["po_global_fields"]=pw_generate_multi_side($args2); // You can use these fields as Filter
				$html_multi["po_individual_fields"]=pw_generate_multi_side($args3);
				$html_multi["po_atttribute_fields"]=pw_generate_multi_side($args4);

				$custom_tax_pages=array(
					//array("po_all_fields" => __("All Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("po_global_fields" => __("Global Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("po_individual_fields" => __("Individual Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("po_atttribute_fields" => __("Attribute Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
				);


				$html.='
                                <div class="col-md-12 bhoechie-tab-container">
									<div class=" col-md-12 bhoechie-tab-menu menu_po">
									  <div class="list-group">';
				$i=0;
				foreach($custom_tax_pages as $tab){
					foreach($tab as $key=>$value){
						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='
                                            <a href="javascript:void(0);" class="list-group-item '.$active.' text-center">
												  <div class="awr-label-'.$key.'">'.$value.'</div>
												</a>';
					}
				}
				$html.='
                                    </div>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab menu_po_content">';

				$i=0;
				foreach($custom_tax_pages as $tab){

					foreach($tab as $key=>$value){

						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='<div id="'.$key.'" class="bhoechie-tab-content '.$active.'">';

						$html.=$html_multi[$key];

						$html.='</div>';
					}
				}
				$html.='
                                    </div>
								</div>
								<script>
							jQuery(document).ready(function($) {
								$("div.menu_po>div.list-group>a").click(function(e) {
									e.preventDefault();
									$(this).siblings("a.active").removeClass("active");
									$(this).addClass("active");
									var index = $(this).index();
									$("div.menu_po_content>div.bhoechie-tab-content").removeClass("active");
									$("div.menu_po_content>div.bhoechie-tab-content").eq(index).addClass("active");
								});
							});
						</script>';
			}
				break;

			////ADDE IN VER4.0
			/// PRODUCT OPTIONS PLUGIN
			case "tab_multi_side_checkout":{

				global $wpdb;
				$options='';
				$selected_options='';
				$selected_options_product='';
				$selected_options_order='';
				$selected_options_gravity='';
				$selected_options_addons='';


				$options_all='';

				//Fetch GLOBAL FIELDS
				$meta_tab=isset($meta['po_checkout_global_fields_select']) ? $meta['po_checkout_global_fields_select'] : "" ;
				//print_r($meta_tab);
				$fields_type=array('textarea', 'textfield', 'selectbox', 'radiobuttons', 'checkboxes', 'upload', 'date', 'time', 'range', 'color');

				$options_global=array();
				$selected_options_global=array();
				$types = $wpdb->get_results("SELECT pw_post.post_title,pw_postmeta.meta_value as meta_value FROM {$wpdb->prefix}posts as pw_post 
                                            INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                            where pw_post.post_type='tm_eco_cp' AND pw_post.post_status IN ('publish') 
                                            AND pw_postmeta.meta_key='tm_meta'", ARRAY_A);

				$fields_array=array();
				if ($types!=null) {
					$options_all.= '<optgroup label="'.__('Global Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'">';
					foreach($types as $v) {
						if(!$v['meta_value']) continue;
						$parent_id=$v['post_title'];
						//$options_global.= '<optgroup label="'.$v['post_title'].'">';

						$data=unserialize($v['meta_value']);
						foreach($fields_type as $f_type){
							//if($f_type=='upload') continue;
							if(isset($data['tmfbuilder'][$f_type.'_header_title'])){
								$i=0;

								foreach($data['tmfbuilder'][$f_type.'_header_title'] as $fields){
									$element_id=$data['tmfbuilder'][ $f_type . '_uniqid' ][$i];
									$input_name_txt=$element_id;
									$input_name_txt=str_replace(".","_",$element_id);

									$label=$data['tmfbuilder'][$f_type.'_header_title'][$i++];
									if(!in_array($fields,$fields_array)){
										$value=$fields;


										$fields_array[]=$fields;

										if(is_array($meta_tab) && in_array($input_name_txt.'@'.$label,$meta_tab)){
											$selected="SELECTED";
											if(isset($selected_options_global[$parent_id]))
												$selected_options_global[$parent_id].='<option value="'.$input_name_txt.'@'.$label.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';
											else
												$selected_options_global[$parent_id]='<option value="'.$input_name_txt.'@'.$label.'" '.$selected.'>'.$label.'-('.$f_type.')</option>';

										}

										if(isset($options_global[$parent_id]))
											$options_global[$parent_id].= '<option value="'.$input_name_txt.'@'.$label.'">'.$label.'-('.$f_type.')</option>';
										else
											$options_global[$parent_id]= '<option value="'.$input_name_txt.'@'.$label.'">'.$label.'-('.$f_type.')</option>';


										$options_all.= '<option value="'.$input_name_txt.'@'.$label.'">'.$label.'-('.$f_type.')</option>';
									}
								}
							}
						}

						//$options_global.= '</optgroup>';
					}
					$options_all.= '</optgroup>';


					$options_globals='';
					if(is_array($options_global)) {
						foreach ( $options_global as $key => $val ) {
							$options_globals .= '<optgroup label="' .  $key  . '">';
							$options_globals .= $val;
							$options_globals .= '</optgroup>';
						}
					}

					$selected_options_globals='';
					if(is_array($selected_options_global)) {
						foreach ( $selected_options_global as $key => $val ) {
							$selected_options_globals .= '<optgroup label="' .  $key  . '">';
							$selected_options_globals .= $val;
							$selected_options_globals .= '</optgroup>';
						}
					}

				}


				//$selected_options=$selected_options_gravity.$selected_options_addons.$selected_options_product.$selected_options_order;

				$args=array(
					"id" => "po_all_fields_select" ,
					"tab_id" => "po_all_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_all ,
					"selected" => "" ,
					"meta" => $meta ,
				);

				$args2=array(
					"id" => "po_checkout_global_fields_select" ,
					"tab_id" => "po_checkout_global_fields" ,
					"field_id" => $field['id'] ,
					"options" => $options_globals,
					"selected" => $selected_options_globals,
					"meta" => $meta ,
				);


				//print_r($meta);

				//$html_multi["po_all_fields"]=pw_generate_multi_side($args);
				$html_multi["po_checkout_global_fields"]=pw_generate_multi_side($args2); // You can use these fields as Filter
				//$html_multi["po_individual_fields"]=pw_generate_multi_side($args3);
				//$html_multi["po_atttribute_fields"]=pw_generate_multi_side($args4);

				$custom_tax_pages=array(
					//array("po_all_fields" => __("All Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
					array("po_checkout_global_fields" => __("Global Fields",__PW_REPORT_WCREPORT_TEXTDOMAIN__)),
				);


				$html.='
                                <div class="col-md-12 bhoechie-tab-container">
									<div class=" col-md-12 bhoechie-tab-menu menu_po_checkout">
									  <div class="list-group">';
				$i=0;
				foreach($custom_tax_pages as $tab){
					foreach($tab as $key=>$value){
						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='
                                            <a href="javascript:void(0);" class="list-group-item '.$active.' text-center">
												  <div class="awr-label-'.$key.'">'.$value.'</div>
												</a>';
					}
				}
				$html.='
                                    </div>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab menu_po_checkout_content">';

				$i=0;
				foreach($custom_tax_pages as $tab){

					foreach($tab as $key=>$value){

						$active='';
						if($i==0)
							$active="active";
						$i++;
						$html.='<div id="'.$key.'" class="bhoechie-tab-content '.$active.'">';

						$html.=$html_multi[$key];

						$html.='</div>';
					}
				}
				$html.='
                                    </div>
								</div>
								<script>
							jQuery(document).ready(function($) {
								$("div.menu_po_checkout>div.list-group>a").click(function(e) {
									e.preventDefault();
									$(this).siblings("a.active").removeClass("active");
									$(this).addClass("active");
									var index = $(this).index();
									$("div.menu_po_checkout_content>div.bhoechie-tab-content").removeClass("active");
									$("div.menu_po_checkout_content>div.bhoechie-tab-content").eq(index).addClass("active");
								});
							});
						</script>';
			}
				break;


		    //CUSTOM WORK - 12300
			/// TICKERA PLUGIN
			case "tab_multi_side_tickera":{
				global $wpdb;
				$options_all='';
				$selected_options='';
				$fields = $wpdb->get_results("SELECT posts.ID as Form_Id,fposts.ID as Field_Id, fposts.post_title as Field_Name,fmeta.meta_value as Field_Type,fmeta1.meta_value as Field_Html_Name from {$wpdb->prefix}posts as posts LEFT JOIN {$wpdb->prefix}posts as fposts ON posts.ID=fposts.post_parent LEFT JOIN {$wpdb->prefix}postmeta as fmeta ON fposts.ID=fmeta.post_id LEFT JOIN {$wpdb->prefix}postmeta as fmeta1 ON fposts.ID=fmeta1.post_id WHERE posts.post_type='tc_forms' and fposts.post_type='tc_form_fields' AND fmeta.meta_key='field_type' AND fmeta1.meta_key='name'", ARRAY_A);
				//print_r($fields);
				$fields_array=array();
				if ($fields!=null) {
					foreach ( $fields as $v=>$tfield ) {
						//$data=unserialize($v['meta_value']);
						//foreach($v as $field) {
                        $fields_array[$tfield['Field_Id']]=$tfield['Field_Name'];
                        $type=$tfield['Field_Type'];
                        $type=explode("_",$type);
                        $type=" (".$type[1].")";

                        $options_all .= '<option value="' . $tfield['Field_Id'].'_'.$tfield['Field_Html_Name']. '">' . $tfield['Field_Name'] . $type . '</option>';
                        if(is_array($meta) && in_array($tfield['Field_Id'].'_'.$tfield['Field_Html_Name'],$meta))
                            $selected_options.='<option value="' . $tfield['Field_Id'].'_'.$tfield['Field_Html_Name'] . '" SELECTED>' . $tfield['Field_Name'] . $type . '</option>';

						//}
					}
				}

				$html.='
							
								
								<div class="description">'.$field['desc'].'</div><br />
							<div class="row pw-twoside-list">
								<div class="col-xs-4">
									<select name="from" id="undo_redo_tickera" class="form-control" size="11" multiple="multiple">
										'.$options_all.'
									</select>
								</div>
								
								<div class="col-xs-4 pw-twoside-btns">
									<button type="button" id="undo_redo_tickera_undo" class="btn btn-primary btn-block">undo</button>
									<button type="button" id="undo_redo_tickera_rightAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
									<button type="button" id="undo_redo_tickera_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
									<button type="button" id="undo_redo_tickera_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
									<button type="button" id="undo_redo_tickera_leftAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>
									<button type="button" id="undo_redo_tickera_redo" class="btn btn-warning btn-block">redo</button>
									
								</div>
								
								<div class="col-xs-4">
									<select name="'.$field['id'].'[]"  id="undo_redo_tickera_to" class="form-control" size="11" multiple="multiple" style="height: 162px;
">'.$selected_options.'</select>
									<button type="button" id="translate_fields_tickera" class="btn btn-warning btn-block" style="background-color:#0DBF44;border-color: #06A036; border-radius: 4px; margin-top: 5px;">'.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</button>
								</div>
							</div>	
							
							<div class="row">
								<div class="col-md-12" style="margin-top: 20px;">
									<div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #e0e0e0;color: #666;margin-bottom: 15px;">
										'.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
									</div>
									
								
									<div class="col-xs-12 pw_awr_fields_translate_tickera">';

				//$operators=array("eq"=>__('EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),);



				if(is_array($meta)){

				    //print_r($meta);

					foreach($meta as $opt){
						$label=explode("_",$opt,2);

						$id_field=$label[1];
						$filter=get_option($label[1]."_filter");
						$col=get_option($label[1]."_column");
						$translate=get_option($label[1]."_translate");
						$label=$fields_array[$label[0]];


						$html.= '
                                        
                                        <div class="col-xs-12 pw-translate_tickera">
                                            <input type="hidden" name="'.$id_field.'_column" placeholder="Label for '.$opt.'" value="off">
                                            <input type="checkbox" name="'.$id_field.'_column" placeholder="Label for '.$opt.'" "'.checked("on",$col,0).'">'.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
                                            $html.='
                                            <input type="hidden" name="'.$id_field.'_filter" placeholder="Label for '.$opt.'" value="off">
                                            <input type="checkbox" name="'.$id_field.'_filter" placeholder="Label for '.$opt.'" "'.checked("on",$filter,0).'">'.__("Display in Filter",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
                                        $html.='
                                            <br />
                                            <input type="text" name="'.$id_field.'_translate" placeholder="Label for '.$label.'" value="'.($translate).'">
                                            
                                        </div>
                                        
                                      	
                                        <br />
                                    ';
					}
				}
				$html.='		
									</div>
								</div>
							</div>		

							<script type="text/javascript"> 
								"use strict";
								jQuery(document).ready(function($) {
									$("#undo_redo_tickera").multiselect();
									$("#translate_fields_tickera").click(function(){
										$("#undo_redo_tickera_to option").prop("selected", true);
										var data="";
										data=$(".custom_report_tickera_custom_fields_field").find("input[name],select[name],textarea[name]").serialize()+"&field='.$field['id'].'";
										confirm($(".custom_report_tickera_custom_fields_field").find("input[name],select[name],textarea[name]").serialize());
										
										var pdata = {
														action: "pw_rpt_fetch_custom_fields_tickera",
														postdata: data,
													}
										
										$.ajax ({
											type: "POST",
											url : ajaxurl,
											data:  pdata,
											success : function(resp){
											    confirm(resp);
												$(".pw_awr_fields_translate_tickera").html(resp);
											}
										});
									});
								});	
							</script>	
							';
			}
				break;

			case 'select_year':
				{
					$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">';
					foreach ($field['options'] as $option) {
						$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';
					}
					$html.= '</select><br /><span class="description">'.__($field['desc'],__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>';

					$all_monthes=array();
					$months = array("January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");

					//	$html.=$first_date;$year<$pw_to_date;

					for($year=2010;$year<2025;$year++){

						foreach($months as $month){
							$all_monthes[$year][$month]=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$year.'_'.$month);
						}

					}
					//print_r($all_monthes);
					$html.='
								<script>
								
									var all_month='.json_encode($all_monthes).';
									
									var mS = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
									
									
									jQuery(document).ready(function($){
										var cur_year="";
										cur_year=$("#custom_report_projected_year").val();
										
										$("#custom_report_projected_year").change(function(){
											
											chg_year=$(this).val();
											var i=0
											$(".pwr_year_months").each(function(){
												input_name=$(this).attr("name");
												input_name=input_name.replace(cur_year,chg_year);
												$(this).attr("name",input_name);
												
												your_val="0";
												your_month=mS[i];
												if(all_month[chg_year][your_month])
													your_val=all_month[chg_year][your_month];
													
												$(this).val(your_val);
												i=i+1;
											});
										});
									});
								</script>
							';
				}
				break;

			case 'monthes':

				$first_date=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year',$first_date);

				foreach($months as $month){
					$value=get_option($field['id'].'_'.$first_date.'_'.$month,0);

					$html.= '
							<span><label><strong>'.$month.'</strong></label></span><input type="text" name="'.$field['id'].'['.$first_date.']['.$month.']" id="'.$field['id'].'" class="'.$field['id'].' pwr_year_months" value="'.$value.'"/><br />';
				}

				$html.='
							<br /><span class="description">'.$field['desc'].'</span>	';
				break;

			case 'numeric':
				$html.= '
							<input type="number" name="'.$field['id'].'"  id="'.$field['id'].'" value="'.($meta=='' ? "":$meta).'" size="30" class="width_170 '.$field['id'].'" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />
        ';
				$html.= '
								<br /><span class="description">'.$field['desc'].'</span>';
				break;

			case 'recency_numeric_int':

				$int_html='';
				for($i=5;$i>0;$i--){

					$title=($i).' '.__("Points - Less Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ';
					if($i==1)
						$title=($i).' '.__("Point &nbsp - Less Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ';

					$metas=isset($meta[$i])?$meta[$i]:"";

					$int_html.='
						        <label for="'.$field['id'].'['.$i.']" class="full-label">'.$title.'</label>
							    <input type="number" name="'.$field['id'].'['.$i.']"  id="'.$field['id'].'['.$i.']" value="'.($metas=='' ? "":$metas).'" size="30" class="width_170 '.$field['id'].'['.$i.']" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />&nbsp&nbsp '.__("Days",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<br />
						        ';
				}

				$html.= $int_html;
				break;

			case 'frequency_numeric_int':
				$int_html='';
				for($i=5;$i>0;$i--){

					$title=($i).' '.__("Points - More Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ';
					if($i==1)
						$title=($i).' '.__("Point &nbsp - More Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					$metas=isset($meta[$i])?$meta[$i]:"";

					$int_html.='
						        <label for="'.$field['id'].'['.$i.']" class="full-label">'.$title.'</label>
							    <input type="number" name="'.$field['id'].'['.$i.']"  id="'.$field['id'].'['.$i.']" value="'.($metas=='' ? "":$metas).'" size="30" class="width_170 '.$field['id'].'['.$i.']" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />&nbsp&nbsp '.__("Times",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<br />
						        ';
				}

				$html.= $int_html;
				break;

			case 'monetary_numeric_int':
				$current_currency=get_woocommerce_currency_symbol();
				$int_html='';
				for($i=5;$i>0;$i--){

					$title=($i).' '.__("Points - More Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__).' ';
					if($i==1)
						$title=($i).' '.__("Point &nbsp - More Than",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
					$metas=isset($meta[$i])?$meta[$i]:"";

					$int_html.='
						        <label for="'.$field['id'].'['.$i.']" class="full-label">'.$title.'</label>
							    <input type="number" name="'.$field['id'].'['.$i.']"  id="'.$field['id'].'['.$i.']" value="'.($metas=='' ? "":$metas).'" size="30" class="width_170 '.$field['id'].'['.$i.']" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />&nbsp&nbsp '.$current_currency.'<br />
						        ';
				}

				$html.= $int_html;
				break;


			case 'html_editor':
				{
					ob_start();

					$html.= '
								<p><span class="description">'.$field['desc'].'</span></p>
								<p class="form-field product_field_type" >';
					$editor_id =$field['id'];
					wp_editor(stripslashes($meta), $editor_id );
					$html.= ob_get_clean();
					$html.='</p>';
				}
				break;

			case "pw_pages":
				{
					$args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $meta,
						'echo'                  => 0,
						'name'                  => $field['id'],
						'id'                    => null, // string
						'show_option_none'      => __('Choose a Page',__PW_REPORT_WCREPORT_TEXTDOMAIN__), // string
						'show_option_no_change' => null, // string
						'option_none_value'     => null, // string
					);
					$html.=wp_dropdown_pages($args);
					$html.= '<br /><span class="description">'.$field['desc'].'</span>';
				}
				break;

			case 'posttype_seletc':
				{
					$output = 'objects';
					$args = array(
						'public' => true
					);
					$post_types = get_post_types( $args , $output);

					$html.='<select name="'.$field['id'].'[]" id="'.$field['id'].'" class="chosen-select-build-posttype" multiple="multiple"> ';
					$html.='<option value="" >'.__('Choose Post Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
					foreach ( $post_types  as $post_type ) {

						if ( $post_type->name != 'attachment' ) {
							$post_value=$post_type->name;
							$post_lbl=$post_type->labels->name;

							$selected='';
							if(is_array($meta) && in_array($post_value,$meta))
								$selected='SELECTED';

							$html.='<option value="'.$post_value.'" '.$selected.'>'.$post_lbl.' ('.$post_value.')</option>';
						}
					}

					$html.= '<br /><span class="description">'.$field['desc'].'</span>';
					$html.='</select>
							<script type="text/javascript">
								jQuery(document).ready(function(){
									var visible = true;
									setInterval(
									function()
									{
										if(visible)
											if(jQuery(".chosen-select-build-posttype").is(":visible"))
											{
												jQuery(".chosen-select-build-posttype").chosen();
											}
									}, 100);
								});
							</script>';
				}
				break;

			case 'all_search':
				{
					$html.='
							<select name="'.$field['name'].'" >
								<option value="">'.__('Choose Live Search',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
					global $pw_woo_ad_main_class,$wpdb;

					$args=array('post_type' => 'pw_report',
					            'post_status'=>'publish',
					);

					$my_query_archive = new WP_Query($args);

					if( $my_query_archive->have_posts()):
						while ( $my_query_archive->have_posts() ) : $my_query_archive->the_post();
							$id=get_the_ID();
							$html.= '<option value="'.$id.'" '.selected($id,$meta,0).'>'.get_the_title().'</option>';
						endwhile;
						wp_reset_query();
					endif;
					$html.='</select>';
					$html.= '<br /><span class="description">'.$field['desc'].'</span>';
				}
				break;


			case "colorpicker":

				$html.= '<div class="medium-lbl-cnt">
											<label for="'.$field['id'].'" class="full-label">'.$field['label'].'</label>
											<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" class="wp_ad_picker_color" value="'.$meta.'" data-default-color="#'.$meta.'">
										  </div>
									';
				$html.= '
							
							<br />';
				$html.= '<br /><span class="description">'.$field['desc'].'</span>';
				break;

			case 'icon_type':
				$html.= $meta;
				$html.= '<input type="hidden" id="'.$field['id'].'font_icon" name="'.$field['id'].'" value="'.$meta.'"/>';
				$html.= '<div class="'.$field['id'].' pw_iconpicker_grid" id="benefit_image_icon">';
				$html.= include(__PW_LIVESEARCH_ROOT_DIR__ .'/includes/font-awesome.php');
				$html.= '</div>';
				$html.= '<br /><span class="description">'.$field['desc'].'</span><br />';
				$output = '
							<script type="text/javascript"> 
								jQuery(document).ready(function(jQuery){';
				if ($meta == '') $meta ="fa-none";
				$output .= 'jQuery( ".'.$field['id'].' .'.$meta.'" ).siblings( ".active" ).removeClass( "active" );
									jQuery( ".'.$field['id'].' .'.$meta.'" ).addClass("active");';
				$output.='
									jQuery(".'.$field['id'].' i").click(function(){
										var val=(jQuery(this).attr("class").split(" ")[0]!="fa-none" ? jQuery(this).attr("class").split(" ")[0]:"");
										jQuery("#'.$field['id'].'font_icon").val(val);
										jQuery(this).siblings( ".active" ).removeClass( "active" );
										jQuery(this).addClass("active");
									});
								});
							</script>';
				$html.= $output;
				break;

			case 'upload':
				//wp_enqueue_media();
				$image = __PW_REPORT_WCREPORT_URL__.'/assets/images/pw-transparent.gif';
				$image='';
				if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium'); $image = $image[0]; }

				$html.= '<input name="'.$field['id'].'" id="'.$field['id'].'" type="hidden" class="custom_upload_image '.$field['id'].'" value="'.(isset($meta) ? $meta:'').'" /> 
							<img src="'.(isset($image) ? $image:'').'" class="custom_preview_image" alt="" width="150" height="150" />
							<input name="btn" class="awr_upload_image_button button" type="button" value="'.__('Choose Image',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'" /> 
							<button type="button" class="awr_search_remove_image_button button">Remove image</button>';
				break;

			case "default_archive_grid":
				{
					global $pw_woo_ad_main_class,$wpdb;

					$query_meta_query=array('relation' => 'AND');
					$query_meta_query[] = array(
						'key' => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'shortcode_type',
						'value' => "search_archive_page",
						'compare' => '=',
					);

					$args=array('post_type' => 'ad_woo_search_grid',
					            'post_status'=>'publish',
					            'meta_query' => $query_meta_query,
					);

					$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">
									<option>'.__('Choose Shorcode',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';

					$my_query_archive = new WP_Query($args);
					if( $my_query_archive->have_posts()):
						while ( $my_query_archive->have_posts() ) : $my_query_archive->the_post();
							$html.= '<option value="'.get_the_ID().'" '.selected($meta,get_the_ID(),0).'>'.get_the_title().'</option>';
						endwhile;
					endif;

					$html.= '</select>';
				}
				break;

			case "pw_sendto_form_fields":
				{
					$html.= '
							<label class="pw_showhide" for="displayProduct-price"><input name="'.$field['id'].'[name_from]" type="checkbox" '.(is_array($meta) && in_array("name_from",$meta) ? "CHECKED": "").' value="name_from" class="displayProduct-eneble">'.__('Name (From) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>
							
							<label class="pw_showhide" for="displayProduct-price"><input name="'.$field['id'].'[name_to]" type="checkbox" '.(is_array($meta) && in_array("name_to",$meta) ? "CHECKED": "").' value="name_to" class="displayProduct-eneble">'.__('Name (To) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>                            
											
							<label class="pw_showhide" for="displayProduct-star"><input name="'.$field['id'].'[email]" type="checkbox" '.(is_array($meta) && in_array("email",$meta) ? "CHECKED": "").' value="email" class="displayProduct-eneble">'.__('Email (To) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>                                    
														
							<label class="pw_showhide" for="displayProduct-metatag"><input name="'.$field['id'].'[description]" type="checkbox" '.(is_array($meta) && in_array("description",$meta) ? "CHECKED": "").' value="description">'.__('Description Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>
							';
				}
				break;

			case 'multi_select':
				{

					$html.= '<select name="'.$field['id'].'[]" id="'.$field['id'].'" style="width: 170px;" class="chosen-select-build" multiple="multiple">';
					foreach ($field['options'] as $option) {
						$selected='';
						if(is_array($meta) && in_array($option['value'],$meta))
							$selected='SELECTED';
						$html.= '<option '. $selected.' value="'.$option['value'].'">'.$option['label'].'</option>';
					}
					$html.= '</select><br /><span class="description">'.$field['desc'].'</span>';

					$html.= '			
							<script type="text/javascript"> 
								jQuery(document).ready(function(){
									var visible = true;
									setInterval(
										function()
										{
											if(visible)
												if(jQuery(".chosen-select-build").is(":visible"))
												{
													visible = false;
													jQuery(".chosen-select-build").chosen();
												}
									}, 100);
									
								});
							</script>
							';
				}
				break;

		}
	}
	$html.= '</table>';
	$html.= '</section>';
}

$html.= '</div><!--END TAB-->';

$html.= ' <div class="awr-setting-submit">
				<button type="submit" value="Save Settings" class="button-primary"><i class="fa fa-floppy-o"></i> <span>'. __("Save settings",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span></button>
			</div>
		</form>
		</div>
		</div><!--awr-box -->
	</div><!--col-xs-12 -->
	</div>	<!--row -->
	</div>
	
	<script type="text/javascript">
		function strpos(haystack, needle, offset) {
			var i = (haystack + "").indexOf(needle, (offset || 0));
			return i === -1 ? false : i;
		}
		
		jQuery(document).ready(function(){
			[].slice.call( document.querySelectorAll( ".tabsA" ) ).forEach( function( el ) {
				new CBPFWTabs( el );
			});
			
			
			////////////SHOW/HIDE CUSTOM FIELD SELECTION/////////////
			
			
			////////////END SHOW/HIDE CUSTOM FIELD SELECTION/////////////
			
		});	
	</script>
	';

echo $html;

////ADDED IN VER4.0
/// PRODUCT OPTIONS CUSTOM FIELDS
/*
 * Change value of options to slug : example : "Flag Color => Flag_Color" and save 3 fields for each one : slug_tranlate, slug_column, slug_filter
 * Note : slug_filter use just for global fields
*/

function pw_generate_multi_side($args){
	$id=$args['id'];
	$tab_id=$args['tab_id'];
	$main_field_id=$args['field_id'];
	$field_id=$args['field_id']."[".$id."]";
	$options=$args['options'];
	$selected_options=$args['selected'];

	$desc=__("Global Fields are used for all products, so you can set display them both in filters and as a column in a data table.",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<br /><strong>'.__("Note : ",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</strong>'.__("Upload fields could not display as filter",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	if($id!='po_global_fields_select' && $id!='po_checkout_global_fields_select'){
		$desc=__("Individual fields are used for specific products , so you can set to display them in data table only.",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
	}

	$meta=$args['meta'];
	$meta=isset($meta[$id]) ? $meta[$id] : "";

	$html_multi='
            <div class="row pw-twoside-list">
                                            
                <div class="col-xs-4">
                    <select name="from" id="'.$id.'" class="form-control" size="11" multiple="multiple">
                        '.$options.'
                    </select>
                </div>
                
                <div class="col-xs-4 pw-twoside-btns">
                    <button type="button" id="'.$id.'_undo" class="btn btn-primary btn-block">undo</button>
                    <button type="button" id="'.$id.'_rightAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
                    <button type="button" id="'.$id.'_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
                    <button type="button" id="'.$id.'_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" id="'.$id.'_leftAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>
                    <button type="button" id="'.$id.'_redo" class="btn btn-warning btn-block">redo</button>
                    
                </div>
                
                <div class="col-xs-4">
                    <select name="'.$field_id.'[]"  id="'.$id.'_to" class="form-control '.$tab_id.'" size="11" multiple="multiple" style="height: 162px;">'.$selected_options.'</select>
                    <button type="button" id="translate_fields_'.$id.'" class="btn btn-warning btn-block" style="background-color:#0DBF44;border-color: #06A036; border-radius: 4px; margin-top: 5px;">'.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</button>
                </div>
            </div>	
            
            <div class="row">
                <div class="col-md-12" style="margin-top:25px">
                    <div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #e0e0e0;color: #666;margin-bottom: 15px;">
                        '.__('Field`s Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
                    </div>
                    <div class="description">'.$desc.'</div>
                    <br />
                    
                
                    <div class="col-xs-12 pw_awr_fields_translate_'.$id.'">';

	if(is_array($meta)){
		foreach($meta as $opt){
			$label=str_replace("_"," ",$opt);

			if($id=='po_checkout_global_fields_select'){
				$exp= explode('@',$opt);
				$opt=$exp[0];
				$label=$exp[1];
				//str_replace("@"," ",$fields);
			}

			$input_name=str_replace(" ","_",$opt);
			$html_multi.= '
												<div class="col-xs-12 pw-translate">
													<input type="hidden" name="'.$input_name.'_column" placeholder="Label for '.$opt.'" value="off">
													<input type="checkbox" name="'.$input_name.'_column" placeholder="Label for '.$opt.'" "'.checked("on",get_option($input_name.'_column'),0).'">'.__("Display in Grid",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			if($id=='po_global_fields_select' || $id=='po_checkout_global_fields_select'){
				$html_multi.='
													<input type="hidden" name="'.$input_name.'_filter" placeholder="Label for '.$opt.'" value="off">
													<input type="checkbox" name="'.$input_name.'_filter" placeholder="Label for '.$opt.'" "'.checked("on",get_option($input_name.'_filter'),0).'">'.__("Display in Filter",__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			}

			$html_multi.='
													<br />
													<input type="text" name="'.$input_name.'_translate" placeholder="Label for '.$label.'" value="'.get_option($input_name.'_translate').'">
													
												</div>	
												<br />
											';
		}
	}
	$html_multi.='		
                </div>
            </div>
        </div>		
    
        <script type="text/javascript"> 
            "use strict";
            jQuery(document).ready(function($) {
                $("#'.$id.'").multiselect();
                $("#translate_fields_'.$id.'").click(function(){
                    $("#'.$id.'_to option").prop("selected", true);
                    var data="";
                    data=$("#'.$tab_id.'").find("input[name],select[name],textarea[name]").serialize()+"&field='.$main_field_id.'&id='.$id.'";
                    var pdata = {
                                    action: "pw_rpt_fetch_custom_fields_po",
                                    postdata: data,
                                }
                    
                    $.ajax ({
                        type: "POST",
                        url : ajaxurl,
                        data:  pdata,
                        success : function(resp){
                            
                            $(".pw_awr_fields_translate_'.$id.'").html(resp);
                        }
                    });
                });
            });	
        </script>	
        ';

	return $html_multi;
}


?>