<?php
echo '
<div class="awr-news-header">
	<div class="awr-news-header-big">'. __("Request Form",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
	<div class="awr-news-header-mini">'. __("Send your request / issue for us",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</div>
</div>	
<div class="awr-request-form">
   
   <form action="" class="pw_request_form">
	    <div class="row">
	        <div class="col-md-12">
				<span class="awr-form-icon"><i class="fa fa-user"></i></span>
	            <input name="awr_fullname" id="awr_fullname" type="text" placeholder="'. __("Enter Full Name..",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'" >
	        </div>
	        
	        <div class="col-md-12">
				<span class="awr-form-icon"><i class="fa fa-envelope-o"></i></span>
	            <input name="awr_email" id="awr_email" type="text" placeholder="'. __("Enter Email.",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'" value="'.get_option("admin_email").'">
	        </div>
	        
	        <div class="col-md-12">
				<span class="awr-form-icon"><i class="fa fa-check"></i></span>
	            <select name="awr_subject" class="">
	                <option value="">'. __("Select Subject",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .' </option>
	                <option value="request">'. __("Send a Request",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</option>
	                <option value="issue">'. __("Report an issue",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</option>
				</select>
	        </div>
	        
	        <div class="col-md-12">
				<span class="awr-form-icon"><i class="fa fa-font"></i></span>
	            <input name="awr_title" id="awr_title" type="text" placeholder="'. __("Enter Title.",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'" >
	        </div>
	        
	        <div class="col-md-12">
	            <textarea name="awr_content" id="awr_content" placeholder="'. __("Enter Your request / issue...",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'"></textarea>
	        </div>
	        	
	        <div class="col-md-12">
				<div class="fetch_form_loading fetch_form_loading_request search-form-loading"></div>	
	            <button type="submit" value="Search" class="button-primary pw_request_form_submit"><i class="fa fa-reply"></i> <span>'. __("Send",__PW_REPORT_WCREPORT_TEXTDOMAIN__) .'</span></button>
	        </div>  
	        <div class="col-md-12 pw_request_form_message">
	        	
			</div>
	    </div>
   </form>
    	  
</div>';
?>