<?php
	function pw_show_news($add_ons_status,$type="new"){
		$html='';
		$recent_calss="";
		if($type=='recent') $recent_calss="awr-news-cnt-recent";
		foreach($add_ons_status as $plugin){
			
			$border='';
			if ($plugin === end($add_ons_status)){
				$border="border:0px";
			}
			
			$label=$plugin['label'];
			$desc =$plugin['desc'];
			$date =$plugin['date'];
			$active_status='';
			$btn='';
			
			$active_status="awr-news-active";
			$btn='';
			
			//echo '<div style="background:'.$color.'"><div><h4>'.$label.'</h4></div>'.$text.'</div>';
			$html .= '
				  <div class="awr-news-cnt '.$active_status.' '.$recent_calss.'" >
					<div class="awr-desc-content">	
						<h3 class="awr-news-title"><a class="" href="'.$plugin['link'].'" target="_blank">'.$label.'</a></h3>
						<div class="awr-news-date"><i class="fa fa-clock-o"></i>'.$date.'</div>
						<div class="awr-news-desc">'.$desc.'</div>
						'.$btn.'
					</div>
					
				  </div>';
		}
		return $html;
	}
	
	$read_date=get_option("pw_news_read_date");
	//$read_date='';

	//GET FROM XML
	$api_url='http://proword.net/xmls/Woo_Reporting/report-news.php';
	
//	$response = wp_remote_get(  $api_url );
//
//	/* Check for errors, if there are some errors return false */
//	if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
//		return false;
//	}
	
	/* Transform the JSON string into a PHP array */
	///$result = json_decode( wp_remote_retrieve_body( $response ), true );
    $result = '';
	$add_ons_status=array();
	$add_ons_status_old=array();
	$news_count=0;

	if($read_date=='' && is_array($result))
	{
		$i=0;
		
		foreach($result as $add_ons){
			
			if ($add_ons === reset($result)){
				//update_option("pw_news_read_date",$add_ons['date']);
			}
			
			$add_ons_status[]=
				array(
					"id" => $add_ons['id'],
					"date" => $add_ons['date'],
					"label" => $add_ons['label'],
					"desc" =>$add_ons['desc'],
					"link" => $add_ons['link'],
				);
			$news_count++;
		}
	}else if(is_array($result)){
		
		
		foreach($result as $add_ons){
			/*if ($add_ons === reset($result)){
				
			}*/
			
			if($read_date<$add_ons['date']){
				$add_ons_status[]=
				array(
					"id" => $add_ons['id'],
					"date" => $add_ons['date'],
					"label" => $add_ons['label'],
					"desc" =>$add_ons['desc'],
					"link" => $add_ons['link'],
				);
				$news_count++;
			}else{
				$add_ons_status_old[]=
					array(
						"id" => $add_ons['id'],
						"date" => $add_ons['date'],
						"label" => $add_ons['label'],
						"desc" =>$add_ons['desc'],
						"link" => $add_ons['link'],
					);
			}
		}
		//update_option("pw_news_read_date",$add_ons['date']);
	}
	
	

	echo '
	<div class="awr-news-cnt-wrap">
		<div class="awr-news-header">
			<div class="awr-news-header-big">'. __("All Notification",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
			<div class="awr-news-header-mini">'. __("Notification Center",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
			
		</div>
	';
		if(is_array($add_ons_status))
		{

			echo pw_show_news($add_ons_status);
			echo pw_show_news($add_ons_status_old,'recent');


		}else{



			if(is_array($add_ons_status)){
				echo '<div class="awr-news-cnt">'.__('There is no unread news, ',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<span class="awr-news-read-oldest">'.__('Show Oldest News !',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span></div>';
				echo '<div class="awr-news-oldest">'.pw_show_news($add_ons_status).'</div>';
			}else{
				echo '<div class="awr-news-cnt" ><div class="awr-desc-content"><h3 class="awr-news-title">'.__('There is no news !',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h3></div></div>';
			}
		}
	echo'	
	</div><!--wrap -->';

?>