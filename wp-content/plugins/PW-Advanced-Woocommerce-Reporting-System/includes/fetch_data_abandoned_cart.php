<?php
	if($file_used=="sql_table")
	{
		$request 			= array();
		$start				= 0;

		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$date_format = $this->pw_date_format($pw_from_date);

		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid',NULL,true);
		$txtProduct 		= $this->pw_get_woo_requests('txtProduct',NULL,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);


		////ADDED IN VER4.0
		//BRANDS ADDONS
		$brand_id 		= $this->pw_get_woo_requests('pw_brand_id','-1',true);

		$limit 				= $this->pw_get_woo_requests('limit',15,true);
		$p 					= $this->pw_get_woo_requests('p',1,true);

		$page 				= $this->pw_get_woo_requests('page',NULL,true);
		$order_id 			= $this->pw_get_woo_requests('pw_id_order',NULL,true);
		$pw_from_date 		= $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date 			= $this->pw_get_woo_requests('pw_to_date',NULL,true);

		$pw_txt_email 			= $this->pw_get_woo_requests('pw_email_text',NULL,true);

		$pw_txt_first_name		= $this->pw_get_woo_requests('pw_first_name_text',NULL,true);

		$pw_detail_view		= $this->pw_get_woo_requests('pw_view_details',"no",true);

		$pw_publish_order		= $this->pw_get_woo_requests('publish_order','no',true);//if publish display publish order only, no or null display all order

		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";

		$pw_paid_customer		= str_replace(",","','",$pw_paid_customer);
		//$pw_country_code		= str_replace(",","','",$pw_country_code);
		//$state_code		= str_replace(",","','",$state_code);
		//$pw_country_code		= str_replace(",","','",$pw_country_code);

		////ADDED IN V4.0
		$pw_variation_id		= $this->pw_get_woo_requests('pw_variation_id','-1',true);
		$pw_variation_only		= $this->pw_get_woo_requests('pw_variation_only','-1',true);
		$pw_variations=$pw_item_meta_key='';
		if($pw_variation_id != '-1' and strlen($pw_variation_id) > 0){

			$pw_variations = explode(",",$pw_variation_id);
			//$this->print_array($pw_variations);
			$var = array();
			$item_att = array();
			foreach($pw_variations as $key => $value):
				$var[] .=  "attribute_pa_".$value;
				$var[] .=  "attribute_".$value;
				$item_att[] .=  "pa_".$value;
				$item_att[] .=  $value;
			endforeach;
			$pw_variations =  implode("', '",$var);
			$pw_item_meta_key =  implode("', '",$item_att);
		}
		$pw_variation_attributes= $pw_variations;
		$pw_variation_item_meta_key= $pw_item_meta_key;

		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','"trash"',true);

		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

		///////////HIDDEN FIELDS////////////
		$pw_hide_os=$this->otder_status_hide;
		$pw_publish_order='no';
		$pw_order_item_name='';

		$pw_payment_method='';

		$pw_order_meta_key='';

		$data_format=$this->pw_get_woo_requests('date_format',get_option('date_format'),true);

		$amont_zero='';
		//////////////////////

		/////////////////////////
		//APPLY PERMISSION TERMS
		$key='all_orders';

		$pw_product_id=$this->pw_get_form_element_permission('pw_product_id',$pw_product_id,$key);

		$pw_order_status 			= $this->pw_get_woo_requests('pw_orders_status',NULL,true);
        if($pw_order_status=='OandA'){
            $pw_order_status="Open','Abandoned";
        }






		$pw_variations_formated='';

		if(strlen($pw_max_amount)<=0) $_REQUEST['max_amount']	= 	$pw_max_amount = '-1';
		if(strlen($pw_min_amount)<=0) $_REQUEST['min_amount']	=	$pw_min_amount = '-1';

		if($pw_max_amount != '-1' || $pw_min_amount != '-1'){
			if($pw_order_meta_key == '-1'){
				$_REQUEST['order_meta_key']	= "_order_total";
			}
		}

		$last_days_orders 		= "0";
		if(is_array($pw_id_order_status)){		$pw_id_order_status 	= implode(",", $pw_id_order_status);}
		if(is_array($category_id)){ 		$category_id		= implode(",", $category_id);}


		if(!$pw_from_date){	$pw_from_date = date_i18n('Y-m-d');}
		if(!$pw_to_date){
			$last_days_orders 		= apply_filters($page.'_back_day', $last_days_orders);//-1,-2,-3,-4,-5
			$pw_to_date = date('Y-m-d', strtotime($last_days_orders.' day', strtotime(date_i18n("Y-m-d"))));}

		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','order_id',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		///

		if($p > 1){	$start = ($p - 1) * $limit;}

		if($pw_detail_view == "yes"){
			$pw_variations_value		= $this->pw_get_woo_requests('variations_value',"-1",true);
			$pw_variations_formated = '-1';
			if($pw_variations_value != "-1" and strlen($pw_variations_value)>0){
				$pw_variations_value = explode(",",$pw_variations_value);
				$var = array();
				foreach($pw_variations_value as $key => $value):
					$var[] .=  $value;
				endforeach;
				$result = array_unique ($var);
				//$this->print_array($var);
				$pw_variations_formated = implode("', '",$result);
			}
			$_REQUEST['variations_formated'] = $pw_variations_formated;
		}


		//pw_first_name_text
		$pw_txt_first_name_cols='';
		$pw_txt_first_name_join = '';
		$pw_txt_first_name_condition_1 = '';
		$pw_txt_first_name_condition_2 = '';

		//pw_email_text
		$pw_txt_email_cols ='';
		$pw_txt_email_join = '';
		$pw_txt_email_condition_1 = '';
		$pw_txt_email_condition_2 = '';

		//SORT BY
		$pw_sort_by_cols ='';

		//CATEGORY
		$category_id_join ='';
		$category_id_condition = '';

		////ADDED IN VER4.0
		//BRANDS ADDONS
		$brand_id_join ='';
		$brand_id_condition = '';

		//ORDER ID
		$pw_id_order_status_join ='';
		$pw_id_order_status_condition = '';

		//COUNTRY
		$pw_country_code_join = '';
		$pw_country_code_condition_1 = '';
		$pw_country_code_condition_2 = '';

		//STATE
		$state_code_join= '';
		$state_code_condition_1 = '';
		$state_code_condition_2 = '';

		//PAYMENT METHOD
		$pw_payment_method_join= '';
		$pw_payment_method_condition_1 = '';
		$pw_payment_method_condition_2 = '';

		//POSTCODE
		$pw_billing_post_code_join = '';
		$pw_billing_post_code_condition= '';

		//COUPON USED
		$pw_coupon_used_join = '';
		$pw_coupon_used_condition = '';

		//VARIATION ID
		$pw_variation_id_join = '';
		$pw_variation_id_condition = '';

		////ADDED IN V4.0
		//VARIATION
		$pw_variation_item_meta_key_join='';
		$sql_variation_join='';
		$pw_show_variation_join='';
		$pw_variation_item_meta_key_condition='';
		$sql_variation_condition='';

		//VARIATION ONLY
		$pw_variation_only_join = '';
		$pw_variation_only_condition = '';

		//VARIATION FORMAT
		$pw_variations_formated_join = '';
		$pw_variations_formated_condition = '';

		//ORDER META KEY
		$pw_order_meta_key_join = '';
		$pw_order_meta_key_condition = '';

		//COUPON CODES
		$pw_coupon_codes_join = '';
		$pw_coupon_codes_condition = '';

		//COUPON CODE
		$pw_coupon_code_condition = '';

		//DATA CONDITION
		$date_condition = '';

		//ORDER ID
		$order_id_condition = '';

		//PAID CUSTOMER
		$pw_paid_customer_condition = '';

		//PUBLISH ORDER
		$pw_publish_order_condition_1 = '';
		$pw_publish_order_condition_2 = '';

		//ORDER ITEM NAME
		$pw_order_item_name_condition = '';

		//txt PRODUCT
		$txtProduct_condition = '';

		//PRODUCT ID
		$pw_product_id_condition = '';

		//CATEGORY ID
		$category_id_condition = '';

		//ORDER STATUS ID
		$pw_id_order_status_condition = '';

		//ORDER STATUS
		$pw_order_status_condition = '';
		$pw_order_status_join = '';

		//HIDE ORDER STATUS
		$pw_hide_os_condition = '';

        ////ADDED IN VER4.0
        /// COST OF GOOD
        $pw_show_cog_cols='';
        $pw_show_cog_join='';
        $pw_show_cog_condition='';



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


//		if($pw_order_status  && $pw_order_status != "-1") {
//			$pw_order_status_join = "
//				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 			ON pw_term_relationships.object_id		=	pw_posts.ID
//				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 				ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
//				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
//		}


		if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'")
			$pw_paid_customer_condition.= " AND pw_posts.post_author IN ('".$pw_paid_customer."')";


//		if($pw_order_status  && $pw_order_status != "-1")
//			$pw_order_status_condition = " AND term_taxonomy.taxonomy LIKE('shop_cart_status') AND pw_terms.slug IN ('".$pw_order_status ."')";

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$date_condition = " AND DATE(pw_posts.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format')";
		}




        $columns_total='';
		if($pw_detail_view=="yes"){

			$columns=array(
				array('lable'=>__('Title',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Owner',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Cart Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Last Online',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Cart Value',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				//CUSTOM WORK - 4187
				//array('lable'=>__('Trannsaction ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),

				array('lable'=>__('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Variation',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Qty.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),

				array('lable'=>__('Actions',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),

			);


		}else{

			$columns=array(
				array('lable'=>__('Title',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Owner',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Cart Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Last Online',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Cart Value',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Actions',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
			);

		}


		$columns=array_values($columns);
		$this->table_cols = $columns;

		$start = date('Y-m-d G:i:s', $pw_from_date." ");
		$end = date('Y-m-d G:i:s', $pw_to_date + 86400);

		$offset = get_option('gmt_offset');
		$timeout = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'abandoned_cart_timeout');;
		$where='';
		if($pw_order_status  && $pw_order_status != "-1"){
			if ($pw_order_status == "Open") {
				$where .= " AND pw_posts.post_modified > '".date('Y-m-d G:i:s', time() + ($offset * 3600) - $timeout)."'";
			} elseif ($pw_order_status =="Abandoned") {
				$where .= " AND pw_posts.post_modified < '".date('Y-m-d G:i:s', (time()  + ($offset * 3600)- $timeout))."'";
			}
		}

		$where = " AND pw_posts.post_modified > '" . $pw_from_date . "' AND pw_posts.post_modified < '" . $pw_to_date . "'";

		echo $where;


		$sql="Select DATE_FORMAT(pw_posts.post_modified,'%M %e, %Y %l:%i'	) as modify,pw_posts.id as id,pw_posts.post_author as author,meta.meta_value as  pw_cartitems from {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as meta ON 
pw_posts.ID=meta.post_id $pw_order_status_join where meta.meta_key='pw_cartitems' AND pw_posts.post_type='carts' $date_condition $pw_order_status_condition $pw_paid_customer_condition $pw_product_id_condition $where";

//echo $sql;

	}
	elseif($file_used=="data_table"){

		$order_items=$this->results;

		$new_order=false;
		if($first_order_id=='')
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}else if($first_order_id!=$items->order_id)
		{
			$first_order_id=$items->order_id;
			$new_order=true;
		}
		$pw_detail_view		= $this->pw_get_woo_requests('pw_view_details',"no",true);

		//print_r($order_items);

		if($pw_detail_view=="yes") {

			foreach ( $order_items as $item ) {

				$cart = new PW_Cart_Receipt();
				$cart->load_receipt($item->id);
				$cart->set_guest_details();
				//print_r($cart);

				$items = ( unserialize( $item->pw_cartitems ) );
				$sum=0;
				foreach ( $items as $pitem ) {
					$_product = wc_get_product($pitem['product_id']);
					$sum+=($pitem['quantity']*$_product->get_price());
				}

				//$items = ( unserialize( $pitem->pw_cartitems ) );
				$datatable_value .= ( "<tr>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= ( get_the_title($item->id ));
				$datatable_value .= ( "</td>" );

				//AUTHOR
				$author='';
				if($item->author==0)
					$author=__('Guest',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
				else
					$author=get_user_meta($item->author,'billing_first_name',true).' '.get_user_meta($item->author,'billing_last_name',true);
				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .=  $author;
				$datatable_value .= ( "</td>" );


				//Email

				$email='';
				if($item->author==0)
					$email=__('Not Available',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
				else
					$email=get_user_meta($item->author,'billing_email',true);

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .=  $email;
				$datatable_value .= ( "</td>" );


				//CART STATUS
				$show_custom_state = $cart->status();
				$filter_link = admin_url('edit.php?post_type=carts&status=' . $show_custom_state);
				$cart_status= __('<div class="awr_index_status"><mark class="awr_' . strtolower($show_custom_state) .'_index">'. __($show_custom_state, 'woocommerce_cart_reports') .'</mark></div>');


				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= $cart_status;
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= ( $item->modify );
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= wc_price($sum);
				$datatable_value .= ( "</td>" );


				//PRODUCTS COLUMNS
				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '';
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '';
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '';
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '';
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '';
				$datatable_value .= ( "</td>" );


				//ACTIONS
				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '<a href="'.esc_url( admin_url('post.php?post='. $item->id .'&action=edit') ).'">'.__('View Cart',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
				$datatable_value .= ( "</td>" );

				$datatable_value .= ( "</tr>" );


				$items = ( unserialize( $item->pw_cartitems ) );
				$sum=0;
				foreach ( $items as $pitem ) {

					$datatable_value .= ( "<tr>" );

					$_product = wc_get_product($pitem['product_id']);
//					$sum+=($pitem['quantity']*$_product->get_price());

					//MAIN ROW
					//PRODUCTS COLUMNS
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );


					//PRODUCTS COLUMNS
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= $_product->get_title();
					$datatable_value .= ( "</td>" );

					//SKU
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= $_product->get_sku();
					$datatable_value .= ( "</td>" );

					//VARIATION
					$variation='';
					if (isset( $pitem['variation'] ) && count( $pitem['variation'] ) > 0 ) {
						$variation_data = wc_get_formatted_variation( $pitem['variation'] , true );

						$variation = $variation_data;
					}
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= $variation;
					$datatable_value .= ( "</td>" );

					//QTY
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= $pitem['quantity'];
					$datatable_value .= ( "</td>" );

					//PRICE
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= wc_price($_product->get_price());
					$datatable_value .= ( "</td>" );

					//ACTIONS
					$display_class = '';
					if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
						$display_class = 'display:none';
					}
					$datatable_value .= ( "<td style='" . $display_class . "'>" );
					$datatable_value .= '';
					$datatable_value .= ( "</td>" );

					$datatable_value .= ( "</tr>" );
				}

			}
		}else{
			foreach ( $order_items as $item ) {

				$cart = new PW_Cart_Receipt();
				$cart->load_receipt($item->id);
				$cart->set_guest_details();
				//print_r($cart);

				$items = ( unserialize( $item->pw_cartitems ) );
				$sum=0;
				foreach ( $items as $pitem ) {
					$_product = wc_get_product($pitem['product_id']);
				    $sum+=($pitem['quantity']*$_product->get_price());
				}

				//$items = ( unserialize( $pitem->pw_cartitems ) );
				$datatable_value .= ( "<tr>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= ( get_the_title($item->id ));
				$datatable_value .= ( "</td>" );

				//AUTHOR
                $author='';
                if($item->author==0)
                    $author=__('Guest',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
                else
                    $author=get_user_meta($item->author,'billing_first_name',true).' '.get_user_meta($item->author,'billing_last_name',true);
				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .=  $author;
				$datatable_value .= ( "</td>" );


				//Email

				$email='';
				if($item->author==0)
					$email=__('Not Available',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
				else
					$email=get_user_meta($item->author,'billing_email',true);

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .=  $email;
				$datatable_value .= ( "</td>" );


				//CART STATUS
				$show_custom_state = $cart->status();
				$filter_link = admin_url('edit.php?post_type=carts&status=' . $show_custom_state);
				$cart_status= __('<div class="awr_index_status"><mark class="awr_' . strtolower($show_custom_state) .'_index">'. __($show_custom_state, 'woocommerce_cart_reports') .'</mark></div>');


				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= $cart_status;
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= ( $item->modify );
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= wc_price($sum);
				$datatable_value .= ( "</td>" );

				$display_class = '';
				if ( $this->table_cols[ $index_cols ++ ]['status'] == 'hide' ) {
					$display_class = 'display:none';
				}
				$datatable_value .= ( "<td style='" . $display_class . "'>" );
				$datatable_value .= '<a href="'.esc_url( admin_url('post.php?post='. $item->id .'&action=edit') ).'">'.__('View Cart',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
				$datatable_value .= ( "</td>" );

				$datatable_value .= ( "</tr>" );
			}
        }
	    //die();




	}elseif($file_used=="search_form"){
	?>
		<form class='alldetails search_form_report' action='' method='post'>
            <input type='hidden' name='action' value='submit-form' />

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Date From',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Date To',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"/>
                </div>
                <?php
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_product_id');
					if($this->get_form_element_permission('pw_product_id') ||  $permission_value!=''){

						if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
							$col_style='display:none';

				?>

                <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-gear"></i></span>
					<?php
                        $products=$this->pw_get_product_woo_data('all');
                        $option='';
                        $current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;

                        foreach($products as $product){
							$selected='';
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;

							/*if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
								$selected="selected";*/


                            if($current_product==$product->id)
                                $selected="selected";
                            $option.="<option $selected value='".$product -> id."' >".$product -> label." </option>";
                        }


                    ?>
                    <select name="pw_product_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_product_id') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
							{
						?>
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
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
				?>

                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-user"></i></span>
					<?php
                        $customers=$this->pw_get_woo_customers_orders();

                        $cust=$this->pw_dropdown_users();

                        $option='';
                        foreach($customers as $customer){
                            $option.="<option value='".$customer -> id."' >".$customer -> label." ($customer->counts)</option>";
                        }
                    ?>
                    <select name="pw_customers_paid[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $cust;
                        ?>
                    </select>

                </div>


                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Cart Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-sort-alpha-asc"></i></span>

                    <select name="pw_orders_status[]" id="pw_orders_status" class="pw_orders_status">
                        <option value="">Show All Carts  </option>
                        <option value="Open">Open  </option>
                        <option value="Converted">Converted  </option>
                        <option value="Abandoned">Abandoned  </option>
                        <option value="OandA">Open + Abandoned Carts  </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Show Order Item Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>

                    <input name="pw_view_details" type="checkbox" value="yes" checked/>

                </div>


           	 	<div class="col-md-12">
				<?php
                    $pw_hide_os=$this->otder_status_hide;
                    $pw_publish_order='no';
                    $pw_order_item_name='';
                    $pw_coupon_code='';
                    $pw_coupon_codes='';
                    $pw_payment_method='';

                    $pw_variation_only=$this->pw_get_woo_requests_links('pw_variation_only','-1',true);
                    $pw_order_meta_key='';

                    $data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);


                    $amont_zero='';

                ?>

                <input type="hidden" name="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
                <input type="hidden" name="publish_order" value="<?php echo $pw_publish_order;?>" />
                <input type="hidden" name="order_item_name" value="<?php echo $pw_order_item_name;?>" />
                <input type="hidden" name="coupon_code" value="<?php echo $pw_coupon_code;?>" />
                <input type="hidden" name="payment_method" value="<?php echo $pw_payment_method;?>" />


                <input type="hidden" name="date_format" value="<?php echo $data_format; ?>" />

                <input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
                <div class="fetch_form_loading search-form-loading"></div>
                <button type="submit" value="Search" class="button-primary"><i class="fa fa-search"></i> <span><?php echo __('Search',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
                <button type="button" value="Reset" class="button-secondary form_reset_btn"><i class="fa fa-reply"></i><span><?php echo __('Reset Form',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></span></button>
            </div>

        </form>

    <?php
	}

?>