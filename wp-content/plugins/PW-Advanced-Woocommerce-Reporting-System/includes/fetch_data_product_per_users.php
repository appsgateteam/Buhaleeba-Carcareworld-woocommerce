<?php

if($file_used=="sql_table")
{

	//GET POSTED PARAMETERS
	$request 			= array();
	$start				= 0;
	$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
	$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
	$date_format = $this->pw_date_format($pw_from_date);

	$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
	$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
	$tag_id 		= $this->pw_get_woo_requests('pw_tags_id','-1',true);
	$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
	$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
	//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
	$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

	//BRAND ADDONS
	$brand_id 		= $this->pw_get_woo_requests('pw_brand_id','-1',true);
	$pw_brand_prod_id_string = $this->pw_get_woo_pli_category($brand_id,$pw_product_id);

	///////////HIDDEN FIELDS////////////
	$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','-1',true);
	$pw_publish_order='no';

	$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
	//////////////////////
	//$category_id 				= "-1";
	if(is_array($category_id)){ 		$category_id		= implode(",", $category_id);}
	if(is_array($tag_id)){ 		$tag_id		= implode(",", $tag_id);}

	//BRANDS ADDON
	if(is_array($brand_id)){ 		$brand_id		= implode(",", $brand_id);}


	/////////CUSTOM TAXONOMY//////////
	$key=$this->pw_get_woo_requests('table_names','',true);

	$visible_custom_taxonomy=array();
	$post_name='product';

	$all_tax_cols=$all_tax_joins=$all_tax_conditions='';
	$custom_tax_cols=array();
	$all_tax=$this->fetch_product_taxonomies( $post_name );
	$current_value=array();
	if(defined("__PW_TAX_FIELD_ADD_ON__") && is_array($all_tax) && count($all_tax)>0){
		//FETCH TAXONOMY
		$i=10;
		foreach ( $all_tax as $tax ) {

			$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
			if($tax_status=='on'){


				$taxonomy=get_taxonomy($tax);
				$values=$tax;
				$label=$taxonomy->label;

				$translate=get_option($key.'_'.$tax."_translate");
				$show_column=get_option($key.'_'.$tax."_column");
				if($translate!='')
				{
					$label=$translate;
				}

				if($show_column=="on")
					$custom_tax_cols[]=array('lable'=>__($label,__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show');


				$visible_custom_taxonomy[]=$tax;

				${$tax} 		= $this->pw_get_woo_requests('pw_custom_taxonomy_in_'.$tax,'-1',true);

				/////////////////////////
				//APPLY PERMISSION TERMS
				$permission_value=$this->get_form_element_value_permission($tax,$key);
				$permission_enable=$this->get_form_element_permission($tax,$key);

				if($permission_enable && ${$tax}=='-1' && $permission_value!=1){
					${$tax}=implode(",",$permission_value);
				}
				/////////////////////////

				//echo(${$tax});

				if(is_array(${$tax})){ 		${$tax}		= implode(",", ${$tax});}

				$lbl_col=$tax."_cols";
				$lbl_join=$tax."_join";
				$lbl_con=$tax."_condition";


				${$lbl_col} ='';
				${$lbl_join} ='';
				${$lbl_con} = '';

				if(${$tax}  && ${$tax} != "-1") {
					${$lbl_col} = "
							
							,term_taxonomy$i.parent						AS term_parent";
				}

				$all_tax_cols=" ".${$lbl_col}." ";

				if(${$tax}  && ${$tax} != "-1") {
					${$lbl_join} = "
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships$i 	ON pw_term_relationships$i.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy$i 		ON term_taxonomy$i.term_taxonomy_id	=	pw_term_relationships$i.term_taxonomy_id
					";
				}

				$all_tax_joins.=" ".${$lbl_join}." ";

				if(${$tax}  && ${$tax} != "-1")
					${$lbl_con} = "AND term_taxonomy$i.taxonomy LIKE('$tax') AND term_taxonomy$i.term_id IN (".${$tax} .")";

				$all_tax_conditions.=" ".${$lbl_con}." ";

				$i++;
			}
		}
	}
	//////////////////


	//CATEGORY
	$category_id_cols='';
	$category_id_join='';
	$category_id_condition='';

	//TAG
	$tag_id_cols='';
	$tag_id_join='';
	$tag_id_condition='';

	//CATEGORY
	$brand_id_cols='';
	$brand_id_join='';
	$brand_id_condition='';
	$pw_brand_prod_id_string_condition='';

	//PRODUCT
	$pw_product_id_condition='';

	//DATE
	$pw_from_date_condition='';

	//PRODUCT STRING
	$pw_cat_prod_id_string_condition='';

	//ORDER STATUS
	$pw_order_status_condition='';
	$pw_id_order_status_join='';
	$pw_id_order_status_condition='';

	//PUBLISH STATUS
	$pw_publish_order_condition='';

	//HIDE ORDER STATUS
	$pw_hide_os_condition='';


	/////////////////////////
	//APPLY PERMISSION TERMS
	$key=$this->pw_get_woo_requests('table_names','',true);

	$category_id=$this->pw_get_form_element_permission('pw_category_id',$category_id,$key);
	$tag_id=$this->pw_get_form_element_permission('pw_tag_id',$category_id,$key);

	////ADDED IN VER4.0
	//BRANDS ADDONS
	$brand_id=$this->pw_get_form_element_permission('pw_brand_id',$brand_id,$key);

	$pw_product_id=$this->pw_get_form_element_permission('pw_product_id',$pw_product_id,$key);

	$pw_order_status=$this->pw_get_form_element_permission('pw_orders_status',$pw_order_status,$key);

	if($pw_order_status != NULL  && $pw_order_status != '-1')
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
	///////////////////////////


	$sql = " SELECT ";

	$sql_columns = "							
					pw_woocommerce_order_items.order_item_name		AS 'product_name'
					,pw_woocommerce_order_items.order_item_id		AS order_item_id
					,pw_woocommerce_order_itemmeta7.meta_value		AS product_id							
					,DATE(shop_order.post_date)					AS post_date
					";

	if($category_id  && $category_id != "-1") {

		$category_id_cols = "
					,pw_terms.term_id								AS term_id
					,pw_terms.name									AS term_name
					,term_taxonomy.parent						AS term_parent
				";
	}

	/////ADDED IN VER4.0
	/// PRODUCT TAG
	if($tag_id  && $tag_id != "-1") {

		$tag_id_cols = "
					,pw_terms_tag.term_id								AS term_id_tag
					,pw_terms_tag.name									AS term_name_tag
					,term_taxonomy_tag.parent						AS term_parent_tag
				";
	}

	//BRANDS ADDON
	if($brand_id  && $brand_id != "-1") {

		$brand_id_cols = "
					,pw_terms_brand.term_id								AS term_id
					,pw_terms_brand.name									AS term_name
					,term_taxonomy_brand.parent						AS term_parent
				";
	}

	//$sql .= " ,woocommerce_order_itemmeta.meta_value AS 'quantity' ,pw_woocommerce_order_itemmeta6.meta_value AS 'total_amount'";
	$sql_columns .= "$category_id_cols $tag_id_cols $brand_id_cols ,SUM(woocommerce_order_itemmeta.meta_value) AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value) AS 'total_amount' ";


	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_columns .= " ,SUM(woocommerce_order_itemmeta.meta_value * pw_woocommerce_order_itemmeta22.meta_value) AS 'total_cost'";
	}


	$sql_joins = "
					{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id ";

	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_joins .=	"	
            LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta22 ON pw_woocommerce_order_itemmeta22.order_item_id=pw_woocommerce_order_items.order_item_id ";
	}

	$sql_joins .=	"	
        LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id						
                ";

	if($category_id  && $category_id != "-1") {
		$category_id_join = " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
	}

	/////ADDED IN VER4.0
	/// PRODUCT TAG
	if($tag_id  && $tag_id != "-1") {
		$tag_id_join = " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_tag 	ON pw_term_relationships_tag.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy_tag 		ON term_taxonomy_tag.term_taxonomy_id	=	pw_term_relationships_tag.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_tag 				ON pw_terms_tag.term_id					=	term_taxonomy_tag.term_id";
	}


	//BRANDS ADDON
	if($brand_id  && $brand_id != "-1") {
		$brand_id_join = " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_brand 	ON pw_term_relationships_brand.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy_brand 		ON term_taxonomy_brand.term_taxonomy_id	=	pw_term_relationships_brand.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_brand 				ON pw_terms_brand.term_id					=	term_taxonomy_brand.term_id";
	}

	if($pw_id_order_status  && $pw_id_order_status != "-1") {
		$pw_id_order_status_join= " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
					LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
	}




	$sql_joins .= " $category_id_join $tag_id_join $brand_id_join $pw_id_order_status_join
					LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id";

	$sql_condition = "
					1*1
					AND woocommerce_order_itemmeta.meta_key	= '_qty'
					AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total'";

	//COST OF GOOD
	if($pw_show_cog=='yes'){
		$sql_condition .="
            AND pw_woocommerce_order_itemmeta22.meta_key	= '".__PW_COG_TOTAL__."' ";
	}

	$sql_condition .="	 
        AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'						
        AND shop_order.post_type					= 'shop_order'
        ";



	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition= " 
					AND (DATE(shop_order.post_date) BETWEEN STR_TO_DATE('" . $pw_from_date . "', '$date_format') and STR_TO_DATE('" . $pw_to_date . "', '$date_format'))";
	}

	if($pw_product_id  && $pw_product_id != "-1")
		$pw_product_id_condition = "
					AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_product_id .")";

	if($category_id  && $category_id != "-1")
		$category_id_condition = "
					AND pw_terms.term_id IN (".$category_id .")";

	////ADDED IN VER4.0
	/// PRODUCT TAG
	if($tag_id  && $tag_id != "-1")
		$tag_id_condition = "
					AND pw_terms_tag.term_id IN (".$tag_id .")";

	//BRANDS ADDON
	if($brand_id  && $brand_id != "-1")
		$brand_id_condition = "
					AND pw_terms_brand.term_id IN (".$brand_id .")";

	if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1")
		$pw_cat_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_cat_prod_id_string .")";

	////ADDED IN VER4.0
	//BRANDS ADDONS
	if($pw_brand_prod_id_string  && $pw_brand_prod_id_string != "-1")
		$pw_brand_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_brand_prod_id_string .")";

	if($pw_id_order_status  && $pw_id_order_status != "-1")
		$pw_id_order_status_condition = " 
					AND terms2.term_id IN (".$pw_id_order_status .")";


	if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
		$in_post_status		= str_replace(",","','",$pw_publish_order);
		$pw_publish_order_condition= " AND  shop_order.post_status IN ('{$in_post_status}')";
	}
	//echo $pw_order_status;
	if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
		$pw_order_status_condition= " AND shop_order.post_status IN (".$pw_order_status.")";

	if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
		$pw_hide_os_condition = " AND shop_order.post_status NOT IN ('".$pw_hide_os."')";

	$sql_group_by = " GROUP BY  pw_woocommerce_order_itemmeta7.meta_value";

	$sql_order_by = " ORDER BY total_amount DESC";

	$sql = "SELECT $sql_columns $all_tax_cols FROM $sql_joins $all_tax_joins WHERE 
							$sql_condition $pw_from_date_condition $pw_product_id_condition
							$category_id_condition $tag_id_condition $brand_id_condition $all_tax_conditions  $pw_cat_prod_id_string_condition
							$pw_brand_prod_id_string_condition $pw_id_order_status_condition $pw_publish_order_condition 
							$pw_order_status_condition $pw_hide_os_condition $sql_group_by $sql_order_by";


//	echo $sql;

	$this->table_cols =$this->table_columns($table_name);
	$array_index=3;
	if(is_array($custom_tax_cols))
		array_splice($this->table_cols,$array_index,0,$custom_tax_cols);

	///////////CHECK IF BRANDS ADD ON IS ENABLE///////////
	$brands_cols=array();
	if(__PW_BRAND_SLUG__){
		$brands_cols[]=array('lable'=>__PW_BRAND_LABEL__,'status'=>'show');
		array_splice($this->table_cols,$array_index,0,$brands_cols);
		$array_index++;
	}

	$array_index++;

	$users_cols=array();
	//$blogusers = get_users( array( 'fields' => array( 'display_name' ),'role'=>'customer' ) );
	$blogusers = get_users( array( 'fields' => array( 'ID','display_name','user_email' ),'role'=>'customer' ) );
    // Array of stdClass objects.
	foreach ( $blogusers as $user ) {
		//echo '<span>' . esc_html( $user->display_name ) . '</span>';
		$users_cols[]=array('lable'=>$user->display_name,'status'=>'show');
	}
	array_splice($this->table_cols,$array_index,0,$users_cols);


	////ADDE IN VER4.0
	/// COST OF GOOD
	//CHECK IF COST OF GOOD IS ENABLE
	if($pw_show_cog!='yes'){
		unset($this->table_cols[count($this->table_cols)-1]);
		unset($this->table_cols[count($this->table_cols)-1]);
	}

	//$this->table_cols = $columns;

}elseif($file_used=="data_table"){
	//print_r($this->results);

	/////////CUSTOM TAXONOMY////////
	$key=$this->pw_get_woo_requests('table_names','',true);
	$visible_custom_taxonomy=array();
	$post_name='product';
	$all_tax=$this->fetch_product_taxonomies( $post_name );
	$current_value=array();
	if(is_array($all_tax) && count($all_tax)>0){
		//FETCH TAXONOMY
		foreach ( $all_tax as $tax ) {
			$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
			$show_column=get_option($key.'_'.$tax.'_column');
			if($show_column=='on'){
				//if($tax_status=='on'){
				$visible_custom_taxonomy[]=$tax;
			}
		}
	}
	///////////////////////////


	////ADDE IN VER4.0
	/// TOTAL ROWS VARIABLES
	$product_count=$sales_qty=$total_amnt=$cog_amnt=$profit_amnt=0;


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
		$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
		$datatable_value.=("</td>");

		//Product Name
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= " <a href=\"".get_permalink($items->product_id)."\" target=\"_blank\">".get_the_title($items->product_id)."</a>";
		$datatable_value.=("</td>");

		//Categories
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."'>");
		$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
		$datatable_value.=("</td>");


		//Tags
//		$display_class='';
//		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
//		$datatable_value.=("<td style='".$display_class."'>");
//		$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_tag");
//		$datatable_value.=("</td>");

		///////////////CUSTOM TAXONOMY/////////////////
		if(defined("__PW_TAX_FIELD_ADD_ON__") &&  is_array($visible_custom_taxonomy)){
			foreach((array)$visible_custom_taxonomy as $tax){
				//Category
				$display_class='';
				if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.=$this->pw_get_cn_product_id($items->product_id,$tax);
				$datatable_value.=("</td>");
			}
		}
		////////////////////////////



		///////////CHECK IF BRANDS ADD ON IS ENABLE///////////
		if(__PW_BRAND_SLUG__){
			$display_class='';
			if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
			$datatable_value.= $this->pw_get_cn_product_id($items->product_id,__PW_BRAND_SLUG__);
			$datatable_value.=("</td>");
		}


		//Sale Qty.
		$display_class='';
		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
		$datatable_value.=("<td style='".$display_class."' >");
		$datatable_value.= $items->quantity;

		////ADDED IN VER4.0
		/// TOTAL ROWS
		$sales_qty+=$items->quantity;
		$datatable_value.=("</td>");



		$blogusers = get_users( array( 'fields' => array( 'ID','display_name','user_email' ),'role'=>'customer' ) );
		foreach ( $blogusers as $user ) {

			$type = 'total_row';$items_only = true; $id = $items->product_id;
			$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
			$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
			$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
			$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
			$pw_from_date=substr($pw_from_date,0,strlen($pw_from_date)-3);
			$pw_to_date=substr($pw_to_date,0,strlen($pw_to_date)-3);

			$params=array(
				"pw_from_date"=>$pw_from_date,
				"pw_to_date"=>$pw_to_date,
				"order_status"=>$pw_order_status,
				"pw_hide_os"=>'"trash"',
				"users"=>array($user->ID,$user->display_email),

			);

			$product_per_users= $this->pw_get_product_purchase_per_user($type,$items_only,$id,$params);

			$ss='';

			foreach($product_per_users as $items_user){
			    $ss=$items_user->total_amount == 0 ? $this->price(0) : /*$this->price($items_user->total_amount).' # '.*/$items_user->order_count;
            }


			$display_class='';
			if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
			$datatable_value.= $ss;
			$datatable_value.=("</td>");
		}




		//Current Stock
//		$display_class='';
//		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
//		$datatable_value.=("<td style='".$display_class."'>");
//		$datatable_value.= $this->pw_get_prod_stock_($items->order_item_id, $items->product_id);
//		$datatable_value.=("</td>");

		//Amount
		//global $WOOCS;
		//$res=$WOOCS->woocs_exchange_value('£568.11');

//		$display_class='';
//		if($this->table_cols[$index_cols++]['status']=='hide') $display_class='display:none';
//		$datatable_value.=("<td style='".$display_class."'>");
//		$datatable_value.= $items->total_amount == 0 ? $this->price(0) : $this->price($items->total_amount);
//
//		////ADDED IN VER4.0
//		/// TOTAL ROWS
//		$total_amnt+=$items->total_amount;
//		$datatable_value.=("</td>");

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
	$this->table_cols_total = $this->table_columns_total( $table_name_total );
	$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
	$datatable_value_total='';
	if($pw_show_cog!='yes'){
		////ADDE IN VER4.0
		/// COST OF GOOD
		unset($this->table_cols_total[count($this->table_cols_total)-1]);
		unset($this->table_cols_total[count($this->table_cols_total)-1]);
	}

	$datatable_value_total.=("<tr>");
	$datatable_value_total.="<td>$product_count</td>";
	$datatable_value_total.="<td>$sales_qty</td>";
	//$datatable_value_total.="<td>".(($total_amnt) == 0 ? $this->price(0) : $this->price($total_amnt))."</td>";
	if($pw_show_cog=='yes'){
		$datatable_value_total.="<td>".(($cog_amnt) == 0 ? $this->price(0) : $this->price($cog_amnt))."</td>";
		$datatable_value_total.="<td>".(($profit_amnt) == 0 ? $this->price(0) : $this->price($profit_amnt))."</td>";
	}
	$datatable_value_total.=("</tr>");

}elseif($file_used=="search_form"){
	?>
    <form class='alldetails search_form_report' action='' method='post' id="product_form">
        <input type='hidden' name='action' value='submit-form' />
        <div class="row">

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
			/////////////////
			//CUSTOM TAXONOMY
			$key=isset($_GET['smenu']) ? $_GET['smenu']:$_GET['parent'];
			$args_f=array("page"=>$key);
			echo $this->make_custom_taxonomy($args_f);
			?>

			<?php
			$col_style='';
			$permission_value=$this->get_form_element_value_permission('pw_category_id');
			if($this->get_form_element_permission('pw_category_id') ||  $permission_value!=''){
				if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
					$col_style='display:none';
				?>

                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
						<?php _e('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-tags"></i></span>
					<?php
					$args = array(
						'orderby'                  => 'name',
						'order'                    => 'ASC',
						'hide_empty'               => 1,
						'hierarchical'             => 0,
						'exclude'                  => '',
						'include'                  => '',
						'child_of'          		 => 0,
						'number'                   => '',
						'pad_counts'               => false

					);

					//$categories = get_categories($args);
					$current_category=$this->pw_get_woo_requests_links('pw_category_id','',true);

					$categories = get_terms('product_cat',$args);
					$option='';
					foreach ($categories as $category) {
						$selected='';
						//CHECK IF IS IN PERMISSION
						if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
							continue;
						/*if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
							$selected="selected";

						if($current_category==$category->term_id)
							$selected="selected";*/

						$option .= '<option value="'.$category->term_id.'" '.$selected.'>';
						$option .= $category->name;
						$option .= ' ('.$category->count.')';
						$option .= '</option>';
					}
					?>
                    <select name="pw_category_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
						<?php
						if($this->get_form_element_permission('pw_category_id') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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

			<?php
			$col_style='';
			$permission_value=$this->get_form_element_value_permission('pw_tags_id');
			if($this->get_form_element_permission('pw_tags_id') ||  $permission_value!='') {
				if ( ! $this->get_form_element_permission( 'pw_tags_id' ) && $permission_value != '' ) {
					$col_style = 'display:none';
				}
				?>

                <div class="col-md-6" style=" <?php echo $col_style; ?>">
                    <div class="awr-form-title">
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
                    <select name="pw_tags_id[]" multiple="multiple" size="5" data-size="5"
                            class="chosen-select-search">
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
			////ADDED IN VER4.0
			//BRANDS ADDONS
			$col_style='';
			$permission_value=$this->get_form_element_value_permission('pw_brand_id');
			if(__PW_BRAND_SLUG__ && ($this->get_form_element_permission('pw_brand_id') ||  $permission_value!='')){
				if(!$this->get_form_element_permission('pw_brand_id') &&  $permission_value!='')
					$col_style='display:none';
				?>

                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
						<?php echo __PW_BRAND_LABEL__;?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-tags"></i></span>
					<?php
					$args = array(
						'orderby'                  => 'name',
						'order'                    => 'ASC',
						'hide_empty'               => 1,
						'hierarchical'             => 0,
						'exclude'                  => '',
						'include'                  => '',
						'child_of'          		 => 0,
						'number'                   => '',
						'pad_counts'               => false

					);

					//$categories = get_categories($args);
					$current_category=$this->pw_get_woo_requests_links('pw_brand_id','',true);

					$categories = get_terms(__PW_BRAND_SLUG__,$args);
					$option='';
					foreach ($categories as $category) {
						$selected='';
						//CHECK IF IS IN PERMISSION
						if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
							continue;
						/*if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
							$selected="selected";

						if($current_category==$category->term_id)
							$selected="selected";*/

						$option .= '<option value="'.$category->term_id.'" '.$selected.'>';
						$option .= $category->name;
						$option .= ' ('.$category->count.')';
						$option .= '</option>';
					}
					?>
                    <select name="pw_brand_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
						<?php
						if($this->get_form_element_permission('pw_brand_id') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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


			$col_style='';
			$permission_value=$this->get_form_element_value_permission('pw_product_id');
			if($this->get_form_element_permission('pw_product_id') ||  $permission_value!=''){
				if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
					$col_style='display:none';
				?>

                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
						<?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
					$products=$this->pw_get_product_woo_data('all');
					$option='';
					$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
					//echo $current_product;

					foreach($products as $product){
						$selected='';
						if(is_array($permission_value) && !in_array($product->id,$permission_value))
							continue;
						if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
							$selected="selected";

						/* if($current_product==$product->id)
							 $selected="selected";*/
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
			$col_style='';
			$permission_value=$this->get_form_element_value_permission('pw_orders_status');
			if($this->get_form_element_permission('pw_orders_status') ||  $permission_value!=''){
				if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
					$col_style='display:none';
				?>

                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
						<?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-check"></i></span>
					<?php
					$pw_order_status=$this->pw_get_woo_orders_statuses();

					////ADDED IN VER4.0
					/// APPLY DEFAULT STATUS AT FIRST
					$shop_status_selected='';
					if($this->pw_shop_status)
						$shop_status_selected=explode(",",$this->pw_shop_status);

					$option='';
					foreach($pw_order_status as $key => $value){
						$selected="";
						//CHECK IF IS IN PERMISSION
						if(is_array($permission_value) && !in_array($key,$permission_value))
							continue;
						/*if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
							$selected="selected";*/

						////ADDED IN VER4.0
						/// APPLY DEFAULT STATUS AT FIRST
						if(is_array($shop_status_selected) && in_array($key,$shop_status_selected))
							$selected="selected";

						$option.="<option value='".$key."' $selected >".$value."</option>";
					}
					?>

                    <select name="pw_orders_status[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
						<?php
						if($this->get_form_element_permission('pw_orders_status') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                </div>
				<?php
			}
			?>

			<?php
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