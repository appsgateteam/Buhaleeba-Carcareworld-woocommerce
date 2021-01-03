<div class="container my_content awr-maincontainer">

    <div id="loader-wrapper">
        <div id="loader"></div>
        <div class="loader-image"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>

    </div>

    <div class="pw-topbar-wrapper" style="visibility: hidden">
        <div class="pw-logo-wrapper">
            <div class="pw-logo-cnt">
                <div id="pw-nav-icon1" class="">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="pw-logotext-cnt"><?php _e("Woocommerce Reporting",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></div>
        </div>

        <div class="pw-rightbar-wrapper">
            <div class="pw-righbar-icon" title="<?php _e("Extra Add-ons",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>">
                <i class="fa fa-puzzle-piece"></i>
                <div class="pw-topbar-submenu pw-fixed-submenu  bn-profile-menu">
					<?php include("addons_report.php"); ?>
                </div>
            </div>

            <div class="pw-righbar-icon" title="<?php _e("Send Request / Feedback",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>">
                <i class="fa fa-envelope-o"></i>
                <div class="pw-topbar-submenu pw-fixed-submenu bn-profile-menu">
					<?php include("report_form.php"); ?>
                </div>
            </div>

			<?php
			ob_start();
			$news_count=0;
			include("report_news.php");
			$news= ob_get_clean();
			?>
            <div class="pw-righbar-icon pw-rightbar-rss" >
                <i class="fa fa-rss"></i>
				<?php
				if($news_count>0){
					?>
                    <span class="pw-notify-count"><?php echo $news_count;?></span>
				<?php } ?>
                <div class="pw-topbar-submenu pw-fixed-submenu bn-profile-menu">
					<?php echo $news; ?>
                </div>
            </div>

            <div class="pw-righbar-icon pw-switch-wordpress">
                <a class="pw-switch-wordpress-a" href="<?php echo get_admin_url();?>" title="<?php _e("Switch to wordpress dashboard",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>">
                    <i class="fa fa-wordpress"></i>
                </a>
            </div>

            <div class="pw-righbar-icon pw-expand-icon" title="<?php _e("Expand Window",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>">
                <i class="fa fa-expand"></i>

            </div></div>
    </div>

	<?php

	$menu_html='';
	$our_menu=apply_filters( 'pw_report_wcreport_page_fetch_menu', $visible_menu );
	update_option("pw_report_menus",json_encode($our_menu));

	//print_r($our_menu);

	$basic_menu='';
	$tax_field_reports='';
	$more_reports='';
	$cross_menu='';
	$other_menu='';



	?>

    <div class="awr-allmenu-cnt" style="visibility:hidden">
        <div class="awr-allmenu-close"><i class="fa fa-times"></i></div>
        <div class="row">

            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="awr-allmenu-box">
                    <div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Basics',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $basic_menu; ?>
                </div>
            </div>

			<?php
			if($tax_field_reports!='')
			{
				?>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="awr-allmenu-box">
                        <div class="awr-menu-title"><i class="fa fa-random"></i><?php echo __('All Order with Taxonomies & Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
						<?php echo $tax_field_reports; ?>
                    </div>
                </div>
				<?php
			}
			?>

            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="awr-allmenu-box">
                    <div class="awr-menu-title"><i class="fa fa-files-o"></i><?php echo __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $more_reports; ?>
                </div>
            </div>

			<?php
			if($cross_menu!='')
			{
				?>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="awr-allmenu-box">
                        <div class="awr-menu-title"><i class="fa fa-random"></i><?php echo __('CrossTab',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
						<?php echo $cross_menu; ?>
                    </div>
                </div>
				<?php
			}
			?>

            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="awr-allmenu-box">
                    <div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Other',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<?php echo $other_menu; ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 awr-allmenu-footer">
                <h3><?php echo __('WOOCommerce Advance Reporting System',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></h3>
                <span>Powered By <a href="http://codecanyon.net/user/proword/portfolio">Proword</a></span>
            </div>
        </div><!--row -->
    </div>


	<?php
	$menu_html='';
	$included_menus='';
	if ($this->dashboard($this->pw_plugin_status)){
		$included_menus='';
	}else{
		$included_menus=array("dashboard","active_plugin");

		$this->our_menu = array(
			"logo" => array(
				"label" => '',
				"id" => "logo",
				"link" => '#',
				"icon" => __PW_REPORT_WCREPORT_URL__ . "/assets/images/logo.png",
				"mini_icon" => __PW_REPORT_WCREPORT_URL__ . "/assets/images/mini_logo.png",
			),

			"dashboard" => array(
				"label" => __('Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
				"id" => "dashboard",
				"link" => $pw_plugin_main_url,
				"icon" => "fa-bookmark",
			),
			"active_plugin" => array(
				"label" => __('Activate Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
				"id" => "active_plugin",
				"link" => "admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin",
				"icon" => "fa-check",
			)
		);

		//array_push($visible_menu[0]['childs'],$no_dashboard_menu);
	}

	//$our_menu=apply_filters( 'pw_report_wcreport_page_fetch_menu', $visible_menu );

	$parent=(isset($_GET['parent']) ? $_GET['parent']: "");
	$child=(isset($_GET['smenu']) ? $_GET['smenu']:"");
	$selected_menu=array("parent" => $parent , "smenu" => $child);

	$menu_html.=$this->pw_menu_generator($this->our_menu,'',$selected_menu);
	$menu_html_mini=$this->pw_menu_generator($this->our_menu,"mini",$selected_menu);
	update_option("pw_report_menus",json_encode($this->our_menu));

	?>
    <div class="awr-mini-menu" style="visibility: hidden">
		<?php echo $menu_html_mini;?>
    </div>

    <nav id="ml-menu" class="awr-menu"  style="visibility:hidden">
        <div class="awr-item">
            <a href="javascript:void(0);">
                <img src="<?php echo __PW_REPORT_WCREPORT_URL__ . "/assets/images/logo.png"; ?>" class="small image">
            </a>
        </div>
        <div class="awr-mainmenu-cnt">
            <div class="awr-mainmenu-list-cnt">
                <ul class="awr-mainmenu-list-ul">

					<?php echo $menu_html;?>

                </ul>
            </div><!--awr-mainmenu-list-cnt -->

        </div>


    </nav>

    <!-- Main container -->

    <div class="awr-content" style="visibility:hidden">

        <div class="pw-breadcrumb-wrapper">

			<?php
			$parent=(isset($_GET['parent']) ? $_GET['parent']: "");
			$child=(isset($_GET['smenu']) ? $_GET['smenu']:"");
			$page_title='';
			$breadcrumb='';
			$our_menu=$this->our_menu;
			//echo $parent.'-';
			//echo $child;
			//print_r($our_menu);
			if($parent==$child || $child=='')
			{
				$breadcrumb='
                                <i class="fa fa-caret-right"></i>
                                <div class="pw-section pw-active">'.(isset($our_menu[$parent]['label']) ? $our_menu[$parent]['label'] : "").'</div>';
				$page_title=(isset($our_menu[$parent]['label']) ? $our_menu[$parent]['label'] : "");
			}else{
				$breadcrumb='<i class="fa fa-caret-right"></i>
                                <div class="pw-section">'.(isset($our_menu[$parent]['label']) ? $our_menu[$parent]['label'] : "Active Plugin").'</div>
                                <i class="fa fa-caret-right"></i>
                                <div class="pw-section pw-active">'.(isset($our_menu[$parent]['childs'][$child]['label']) ? $our_menu[$parent]['childs'][$child]['label'] : "").'</div>';
				$page_title=isset($our_menu[$parent]['childs'][$child]['label']) ? $our_menu[$parent]['childs'][$child]['label'] : "";
			}

			/////////In EDIT Page////////
			if(isset($_GET['action']) || isset($_GET['id']))
				$page_title=str_replace("Add",__('Edit', __PW_WOO_BUNDLE_TEXTDOMAIN__),$page_title);

			?>

            <div class="pw-breadcrumb-cnt">
                <a class="pw-section" href="<?php echo get_admin_url();?>admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard"><i class="fa fa-home"></i>Home</a>
				<?php echo $breadcrumb; ?>

                <!--FOR INTELLIGENCE PAGES-->
                <div class="pw_intelligence_search_form"></div>
            </div>


        </div>

		<?php
		if(defined("__PW_PERMISSION_ADD_ON__"))
		{

			if(isset($_REQUEST['parent']) && isset($_REQUEST['smenu']))
			{
				$enable_parent_menu=$this->get_menu_capability($_REQUEST['parent']);
				$enable_sub_menu=$this->get_menu_capability($_REQUEST['smenu']);

				if($this->get_menu_capability($_REQUEST['parent']) && $this->get_menu_capability($_REQUEST['smenu']))
				{
					if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
						include($page);
					}else{
						$page='plugin_active.php';
						include($page);
					}
				}else{
					echo '
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-box awr-acc-box">
												<div class="awr-acc-icon">
												    <i class="fa fa-meh-o"></i>
												</div>
												<h3 class="awr-acc-title">'. __("Access Denied !",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h3>
												<div class="awr-acc-desc">'. __("You have no permisson !! Please Contact site Administrator",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->';
				}

			}elseif(isset($_REQUEST['parent']) && !isset($_REQUEST['smenu'])){

				if($this->get_menu_capability($_REQUEST['parent']))
				{
					if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
						include($page);
					}else{
						$page='plugin_active.php';
						include($page);
					}
				}else{
					echo '
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-box awr-acc-box">
												<div class="awr-acc-icon">
												    <i class="fa fa-meh-o"></i>
												</div>
												<h3 class="awr-acc-title">'. __("Access Denied !",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h3>
												<div class="awr-acc-desc">'. __("You have no permisson !! Please Contact site Administrator",__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->';
				}
			}

		}
		else{
			if ($this->dashboard($this->pw_plugin_status) || $page=='plugin_active.php' || $page=="dashboard_report.php"){
				include($page);
			}else{
				$page='plugin_active.php';
				include($page);
			}
		}


		//echo $parent;


		?>

        <!-- Ajax loaded content here -->
    </div>
</div>