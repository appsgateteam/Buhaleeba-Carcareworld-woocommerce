<?php
	global $pw_rpt_main_class;

    if (!$pw_rpt_main_class->dashboard($pw_rpt_main_class->pw_plugin_status)){
        header("location:".admin_url()."admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin");
    }else {
	    $smenu=$_REQUEST['smenu'];
	    $fav_icon=' fa-star-o ';
	    if($pw_rpt_main_class->fetch_our_menu_fav($smenu)){
		    $fav_icon=' fa-star ';
	    }
	    ?>

        <div class="wrap">
            <div class="row">
                <div class="col-xs-12">
                    <div class="awr-box">
                        <div class="awr-title">
                            <h3>
                                <i class="fa fa-filter"></i><?php _e( 'Configuration', __PW_REPORT_WCREPORT_TEXTDOMAIN__ ); ?>
                            </h3>
                            <div class="awr-title-icons">
                                <div class="awr-title-icon awr-add-fav-icon" data-smenu="<?php echo $smenu;?>"><i class="fa <?php echo $fav_icon;?>"></i></div>
                                <div class="awr-title-icon awr-toggle-icon"><i class="fa fa-arrow-up"></i></div>
                                <div class="awr-title-icon awr-setting-icon"><i class="fa fa-cog"></i></div>
                                <div class="awr-title-icon awr-close-icon"><i class="fa fa-times"></i></div>
                            </div>
                        </div><!--awr-title -->
                        <div class="awr-box-content-form">
						    <?php
						    $table_name = 'dashboard_report';
						    $pw_rpt_main_class->search_form_html( $table_name );
						    ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="dashboard_target">
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
                                    <div id="target">
									    <?php $pw_rpt_main_class->table_html( "dashboard_report" ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
				    <?php
				    $table_name = 'monthly_summary';
				    $pw_rpt_main_class->table_html( $table_name );
				    ?>
                </div>
            </div>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                cb(moment().subtract(29, 'days'), moment());

                $('#reportrange').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

            });
        </script>
	    <?php
    }
        ?>