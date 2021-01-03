<?php
	
	$pw_active_plugin = array(
		/*array(  
			'label'	=> __('Activation Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type',
			'type'  => 'select',
			'options' => array (  
				'one' => array (
					'label' => __('Direct Activate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),  
					'value' => 'direct'  
				),
				'two' => array (
					'label' => __('Via Proword Site',__PW_REPORT_WCREPORT_TEXTDOMAIN__),  
					'value' => 'proword'  
				)
			)
		),*/
//		array(
//			'label'	=> __('Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//			'desc'	=> __('Enter Your Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
//			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
//			'type'	=> 'text',
//
//		),
		array(  
			'label'	=> __('Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'type'	=> 'text',		
			/*'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','direct') 	
			)*/
		),

		array(
			'label'	=> __('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Valid Email.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email',
			'type'	=> 'text',
			/*'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','direct')
			)*/
		),

		/*array(  
			'label'	=> __('Active Via Proword.net',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Goto <a href="http://proword.net/verify-purchase-code/" target="_blank">Here</a> and after complete the form, copy the form result and paste in below field',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_proword',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_proword',
			'type'	=> 'notype',		
			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','proword') 	
			)
		),
		array(  
			'label'	=> __('File Path',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Paste the path',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file',
			'type'	=> 'text',		
			'dependency' => array(
				'parent_id' => array(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'),
				__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_type'	  => array('select','proword') 	
			)
		)*/
	);
    $text_return='';
	if (isset($_POST["update_settings"])) {
		// Do the saving

        $email=isset($_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email']) ? $_POST[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email']:"";

		foreach($_POST as $key=>$value){
			if(!isset($_POST[$key])){
				delete_option($key);  
				continue;
			}
			
			$old = get_option($key);  
			$new = $value; 
			if(!is_array($new)) 
			{
				
				/*if($key==__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_type')
				{
					$path_file=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_path_file';
					if(isset($_POST[$path_file]))
					{
						$url=$_POST[$path_file];
						$response = wp_remote_get(  $url );
				
						//Check for errors, if there are some errors return false 
						if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
							return false;
						}
  
						
						//Transform the JSON string into a PHP array 
						$result = json_decode( wp_remote_retrieve_body( $response ), true );
						$add_ons_status='';
						foreach($result as $add_ons){
							$add_ons_status[]=
								array(
									"item_name" => $add_ons['item_name'],
									"buyer" => $add_ons['buyer'],
									"created_at" =>$add_ons['created_at'],
									"licence" => $add_ons['licence'],
									"supported_until" => $add_ons['supported_until'],
								);	
						}
					}
					
					
					
					
				}*/
				
				if ($new && $new != $old) {  
					update_option($key, $new);  
				} elseif ('' == $new && $old) {  
					delete_option($key);  
				}
			}else{
				
				//die(print_r($new));
				
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
			
		global $pw_rpt_main_class;
		$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code';
		$pw_rpt_main_class->pw_plugin_status=get_option($field);


		$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email';
		$pw_rpt_main_class->email=get_option($field);


		$text='';


		$check_db = $pw_rpt_main_class->dashboard($pw_rpt_main_class->pw_plugin_status);

		//echo 'ddddddddddddddddddddd'.$pw_rpt_main_class->pw_plugin_status;
		//print_r($check_db);
		if ($pw_rpt_main_class->dashboard($pw_rpt_main_class->pw_plugin_status) && isset($check_db["verify-purchase"]["status"]) && $check_db["verify-purchase"]["status"]=='valid' && filter_var($pw_rpt_main_class->email, FILTER_VALIDATE_EMAIL)){
			$text=__('Congratulation, The Plugin has been Activated Successfully !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			//echo '1111111111111';
			?>
                <script>
                    jQuery(document).ready(function ($) {
                        setTimeout(function() {
                            $(".pw_active_ok").attr("style", "display:block !important");
                            $(".pw_active_email").attr("style", "display:none !important");
                        },500);
                    });
                </script>

            <?php
		}else if ((isset($check_db["verify-purchase"]["status"]) && $check_db["verify-purchase"]["status"]!='valid') || !filter_var($pw_rpt_main_class->email, FILTER_VALIDATE_EMAIL)){
			$text=__('Unfortunately, The Purchase code is Wrong, Please try Again !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			$text_return=$check_db["verify-purchase"]["status"];
			//echo '222222222222';
			?>
                <script>
                    jQuery(document).ready(function ($) {
                        setTimeout(function(){
                            $(".pw_active_error").attr("style","display:block !important");
                        },500);
                    });
                </script>

            <?php
		}
	}
	
    global $pw_rpt_main_class;
	$field_1=$pw_active_plugin[0];
	$field_2=$pw_active_plugin[1];
	
	$meta_1 = get_option($field_1['id']);  
	$meta_2 = get_option($field_2['id']);

    $text_ok=__('Congratulation, The Plugin has been Activated Successfully ! Move to ',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'<a href="admin.php?page='.$pw_rpt_main_class->pw_plugin_main_url.'">'.__("Dashboard",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</a>';
    $text_error=__('Unfortunately, The Purchase code is Wrong or Email is not Valid, Please try Again !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);

	$html= '
    <div class="wrap">
        <div class="row">
                <div class="col-xs-12">
                    <div class="awr-box">
                            <div class="awr-title">
                                <h3><i class="fa fa-shield"></i>'.__('Plugin Activate',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'  </h3>
                            </div><!--awr-title -->
                            <div class="awr-box-content" >
                                <div class="col-md-12">
                                    <div id="setting-error-settings_updated" class="updated settings-error pw_active_ok">
                                        <p><strong>'.$text_ok.'</strong></p>
                                    </div>
                                    
                                    <div id="setting-error-settings_updated" class="error pw_active_error">
                                        <p><strong>'.$text_error.'</strong></p>
                                        <p style="color: #f1c40f"><strong>'.$text_return.'</strong></p>
                                    </div>';
                                    global $pw_rpt_main_class;
                                    $field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_email';
                                    $pw_rpt_main_class->email=get_option($field);
                                    if(!filter_var($pw_rpt_main_class->email, FILTER_VALIDATE_EMAIL)) {
	                                    $html .= '
                                        <div id="setting-error-settings_updated" class="updated email-notice pw_active_email">
                                            <p><strong>' . __( 'Please set email for complete activation in Ver4.0', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ) . '</strong></p>
                                        </div>';
                                    }
                                    $html.= '  
                                </div>
                                
                                <form method="POST" action="" class="awr-setting-form">
                                        <input type="hidden" name="update_settings" value="Y" />
                                        <div class="col-md-6">
                                            <div class="awr-form-title"><label>'.$field_1['label'].'</label></div>
                                            
                                            <input type="text" name="'.$field_1['id'].'" id="'.$field_1['id'].'" class="'.$field_1['id'].'" value="'.$meta_1.'" >
                                            <br /><div class="description">'.$field_1['desc'].'</div>
                        
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="awr-form-title"><label>'.$field_2['label'].'</label></div>
                                            
                                            <input type="email" name="'.$field_2['id'].'" id="'.$field_2['id'].'" class="'.$field_2['id'].'" value="'.$meta_2.'" >
                                            <br /><div class="description">'.$field_1['desc'].'</div>
                        
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="awr-setting-submit" style="margin-top:20px">
                                                <button type="submit" value="Save settings" class="button-primary"><i class="fa fa-floppy-o"></i> <span>'.__('Save Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span></button>
                                                
                                            </div>
                                        </div>
                                </form>
                            </div>
                            
                    </div>
                </div>
        </div>
	</div>
	
	';
	
	echo $html;
?>