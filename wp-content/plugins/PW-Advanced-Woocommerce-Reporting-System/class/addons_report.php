<?php
	//GET FROM XML
	$api_url='http://proword.net/xmls/Woo_Reporting/add-ons.php';
	
	$response = wp_remote_get(  $api_url );
				
	/* Check for errors, if there are some errors return false */
	if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
		return false;
	}
	
	/* Transform the JSON string into a PHP array */
	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	$add_ons_status=array();
	foreach($result as $add_ons){
		$add_ons_status[]=
			array(
				"id" => $add_ons['id'],
				"label" => $add_ons['label'],
				"desc" =>$add_ons['desc'],
				"link" => $add_ons['link'],
				"icon" => $add_ons['icon'],
				"folder_name" => $add_ons['folder_name'],
				"define_variable" => $add_ons['define_variable'],
			);	
	}
	
	
	echo '
	<div class="awr-news-header">
		<div class="awr-news-header-big">'. __("Reporting Add-ons",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
		<div class="awr-news-header-mini">'. __("Click For Buy Add-ons",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
			
	</div>';
	echo '<div class="row awr-addons-wrapper">';
	foreach($add_ons_status as $plugin){
		//IS ACTIVE
		$active=defined($plugin['define_variable']);
		
		//IS EXIST
		$my_plugin = WP_PLUGIN_DIR . '/' .$plugin['folder_name'];
		$exist=is_dir( $my_plugin );
		
		$label=$plugin['label'];
		$desc =$plugin['desc'];
		$icon = $plugin['icon'];
		$active_status='';
		$btn='';
		
		if($exist){
			if($active)
			{
				$active_status="awr-addones-active";
				$btn='<a class="awr-addons-btn" href="javascript:void(0);" ><i class="fa fa-check"></i>'.__('Activated',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			}else
			{
				$active_status="awr-addones-deactive";
				$btn='<a class="awr-addons-btn" href="'.admin_url()."plugins.php".'" target="_blank"><i class="fa fa-plug"></i>'.__('Active',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			
			}
		}else
		{
			$active_status="awr-addones-disable";
			$btn='<a class="awr-addons-btn" href="'.$plugin['link'].'" target="_blank"><i class="fa fa-shopping-cart"></i>'.__('Buy Now',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
			
			
			
		}
		
		//echo '<div style="background:'.$color.'"><div><h4>'.$label.'</h4></div>'.$text.'</div>';
		echo '
		   <div class="col-xs-12 col-md-6">		  
			  <div class="awr-addons-cnt '.$active_status.'">
				'.$icon.'
				<div class="awr-desc-content">	
					<h3 class="awr-addones-title">'.$label.'</h3>
					'.$btn.'
				</div>
				
			  </div>
		   </div>';
	}
	echo '</div>';
?>