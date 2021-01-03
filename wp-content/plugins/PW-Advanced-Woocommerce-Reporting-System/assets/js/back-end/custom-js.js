// PREVENT FROM CONFLICT WITH ANY $S
//$.noConflict();

function formatLabel(value, valueString, axis){
    valueString = params.woo_currency+valueString;
    return valueString;
}

function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results)
        return null;
    if (!results[2])
        return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

jQuery(window).load(function($){
    //jQuery("#awr_fullscreen_loading").hide();
    jQuery('.my_content').addClass('loaded');

    //jQuery(".awr-menu").show();
    jQuery(".awr-menu").css("visibility","visible");
    jQuery(".awr-mini-menu").css("visibility","visible");
    //jQuery(".awr-allmenu-cnt").show();
    jQuery(".awr-allmenu-cnt").css("visibility","visible");
    //jQuery(".awr-content").show();
    jQuery(".awr-content").css("visibility","visible");
    jQuery(".pw-topbar-wrapper").css("visibility","visible");


});

var loading_content='<div class="awr-loading-css"><div class="rect1"></div><div class="rect2"></div> <div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';

(function() {

})();

jQuery(document).ready(function($) {
    var menuEl = document.getElementById('ml-menu'),
        mlmenu = new MLMenu(menuEl, {
            // breadcrumbsCtrl : true, // show breadcrumbs
            // initialBreadcrumb : 'all', // initial breadcrumb text
            backCtrl : false, // show back button
            // itemsDelayInterval : 60, // delay between each menu item sliding animation
        });

    ////ADDED IN VER4.0
    /// NEW MENU
    $( ".awr-mainmenu-list-hassub a,.awr-mainmenu-list-hassub .awr-mainmenu-list-toggle" ).click(function(e) {

        element=$(this).parent();
        element.find( ".awr-mainmenu-list-sub" ).slideToggle( "slow", function(e) {
            // Animation complete.]
            if($(this).is(":visible"))
                element.find( ".awr-mainmenu-list-toggle" ).find("i").addClass( "fa-angle-down" ).removeClass("fa-angle-right");
            else
                element.find( ".awr-mainmenu-list-toggle" ).find("i").removeClass( "fa-angle-down" ).addClass("fa-angle-right");
        });
        //


    });


    ////ADDED IN VER4.0
    /// TOOLBAR ICONS
    $( ".pw-righbar-icon" ).click(function(e) {
        //confirm(e.target.class);
        if (e.target !== this && e.target.tagName !== 'I')
            return;
        if($(this).hasClass("pw-switch-wordpress")){
            href=$(this).find(".pw-switch-wordpress-a").attr('href');
            window.location=href;
            return true;
        }



        $(this).find('.pw-topbar-submenu').toggleClass('pw-show-submenu');
        $(this).toggleClass('pw-opened-icon');
        $(this).siblings(".pw-righbar-icon").removeClass("pw-opened-icon");
        if($(this).hasClass("pw-opened-icon")){


            //FOR RSS NOTIFICATION-UPDATE DATE
            if($(this).hasClass("pw-rightbar-rss")){
                var pdata = {
                    action: "pw_rpt_update_notification_date",
                    nonce: params.nonce,
                }

                $.ajax ({
                    type: "POST",
                    url: params.ajaxurl,
                    data: pdata,
                    success: function (resp) {
                        //confirm(resp);
                    }
                });
            }

            $(this).parents(".pw-rightbar-wrapper").find(".pw-topbar-submenu").each(function () {
                $(this).removeClass("pw-show-submenu");
            });
            $(this).find(".pw-topbar-submenu").addClass("pw-show-submenu");
        }else{
            console.log("CLOSE");
            $(this).parents(".pw-rightbar-wrapper").find(".pw-topbar-submenu").each(function () {
                $(this).removeClass("pw-show-submenu");
            });
        }

    });

    $(".pw_request_form_submit").click(function(e){
        e.preventDefault();
        //confirm($(".pw_request_form").serialize());
        var pdata = {
            action: "pw_rpt_request_form",
            postdata: $(".pw_request_form").serialize(),
            nonce: params.nonce,
        }

        $(".fetch_form_loading_request").html(loading_content);

        $.ajax ({
            type: "POST",
            url: params.ajaxurl,
            data: pdata,
            success: function (resp) {

                $(".pw_request_form_message").html(resp);
                $(".fetch_form_loading_request").html("");
            }
        });
    });


    $(".pw-expand-icon").click(function () {
        $(document).toggleFullScreen(true);
        if ($(this).find("i").hasClass("expand")) {
            $(this).find("i").removeClass("expand").addClass("compress");
        } else {
            $(this).find("i").removeClass("compress").addClass("expand");
        }
    });



    //////////REMOVE WORDPESS NOTICES//////////
    $(".error, .updated, .update-nag ").hide();

    /////////////BLOCK ICONS///////////////
    $(".awr-toggle-icon").click(function(){
        $(this).closest(".awr-box").find(".awr-box-content").slideToggle("fast");
        $(this).closest(".awr-box").find(".awr-box-content-form").slideToggle("fast");
    });


    ////ADDED IN VER4.0
    //////////////ADD MENU TO FAVORITE///////////////
    $(".awr-add-fav-icon").click(function () {

        var data_smenu=$(this).attr("data-smenu");

        var pdata = {
            action: "pw_rpt_add_fav_menu",
            postdata: "smenu="+data_smenu,
            nonce: params.nonce,
        }

        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            success : function(resp){
                //confirm(resp);
            }
        });
    });

    $(".awr-setting-icon , .awr-close-icon").hide();

    //Close Allmenu
    jQuery(document).keyup(function(e){
        if(e.which==27){
            jQuery(".awr-allmenu-cnt").hide();
        }
    });


    $('.awr-allmenu').click(function(){
        $('.awr-allmenu-cnt').fadeIn("fast")
    });
    $('.awr-allmenu-close').click(function(){
        $('.awr-allmenu-cnt').fadeOut("fast");
    });

    $(".awr-action--open").click(function(){
        $(".awr-menu").toggleClass('awr-opened-menu');
    });



    $(".menu__link").click(function(){
        //confirm("OK");
    });



    var url = document.URL;
    var hash = url.split("page=");
    if(hash[1]!='permission_report'){

        //var hash = url.substr(document.URL.indexOf('&parent=')+1)
        var hash = url.split("parent=") ;
        //confirm(hash[1]);
        var hash_arr=Array;
        hash_arr=hash[1].split("&smenu=");
        var time = 1000;

        $("a[data-submenu]").each(function(){
            if($(this).attr("data-submenu")==hash_arr[0])
            {
                $(this).simulateClick('click');
                $(this).addClass('menu__link--current');
            }
        });

        if(hash_arr.length>1)
        {
            clicked_menu=hash_arr[1];
        }else{
            clicked_menu=hash_arr[0];
        }

        $(".menu__link").each(function(){
            if($(this).attr("id")==clicked_menu)
            {
                $(this).addClass('menu__link--current');
            }
        });


        if(hash_arr.length==1)
        {
            /*$("a[data-submenu]").each(function(){
                if($(this).attr("data-submenu")==hash_arr[0])
                {
                    $(this).simulateClick('click');
                }
            });

            setTimeout(function(){
                $("a[data-submenu]").each(function(){
                    if($(this).attr("data-submenu")==hash_arr[1])
                    {
                        //this_e=$(this);
                        //
                            //confirm($(this).html());
                            $(this).simulateClick('click');
                        //
                        //$(this).delay(1000);
                    }
                });
            },time);*/
        }else/* if(hash_arr.length==1)*/
        {

            /*$("a[data-submenu]").each(function(){
                if($(this).attr("data-submenu")==hash_arr[1])
                {
                    $(this).simulateClick('click');
                    $(this).addClass('menu__link--current');
                }
            });	*/
        }
    }
});

jQuery.fn.simulateClick = function() {
    return this.each(function() {
        if('createEvent' in document) {
            var doc = this.ownerDocument,
                evt = doc.createEvent('MouseEvents');
            evt.initMouseEvent('click', true, true, doc.defaultView, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
            this.dispatchEvent(evt);
        } else {
            this.click(); // IE Boss!
        }
    });
}


jQuery( document ).ready(function( $ ) {


    if(params.advanced_pdf){
        ////ADDED IN VER4.0
        /// PDF GENERATOR

        //CHANGE LOADING POSITION
        $(".fetch_form_loading").attr("style","right:367px");

        //add pdf btn
        $('<button type="button" value="Reset" class="button-secondary form_pdf_btn"><i class="fa fa-file-pdf-o"></i><span>PDF</span></button>').insertAfter(".form_reset_btn");


        $(".form_pdf_btn").on("click",function (e) {

            e.preventDefault();

            var form_id;
            form_id=$(this).attr("id");

            var ford=$(".search_form_report").serialize();
            ford=ford.replace("table_name", "table_names");
            var pdata = {
                action: "pw_rpt_pdf_generator",
                postdata: ford,
                nonce: params.nonce,
            }


            var pdata = {
                action: "pw_rpt_pdf_generator",
                postdata: $(".search_form_report").serialize(),
                nonce: params.nonce,
            }

            $(".fetch_form_loading").html(loading_content);

            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                success : function(resp){

                    $("#target").html(resp);
                    $(".fetch_form_loading").html("");
                }
            });

        });
    }



    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();

    if(dd<10) {
        dd='0'+dd
    }

    if(mm<10) {
        mm='0'+mm
    }

    today = mm+'-'+dd+'-'+yyyy;


    /////////////////////////////////////
    //PAGES DATATABLE INITIALIZE
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
            function(m,key,value) {
                vars[key] = value;
            });
        return vars;
    }

    var delete_cookie = function(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    };


    function datatable_init(){
        //return true;
        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
            "currency-pre": function ( a ) {

                if(params.currency_format=='.'){
                    a = (a==="-") ? 0 : a.replace( /[^\d\-\,]/g, "" );
                }
                else if(params.currency_format==','){
                    a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
                }

                //FOR NEW VERSION OF WOOCOMMERCE
                a=a.replace(/-/g, '');

                return parseFloat( a );
            },

            "currency-asc": function ( a, b ) {
                return a - b;
            },

            "currency-desc": function ( a, b ) {
                return b - a;
            }
        } );

        var target_th=[];
        i=0;
        j=0;
        $('.datatable').find("thead").find("th").each(function(){
            //confirm($(this).attr("data-class"));
            if($(this).attr("data-class")==="currency")
            {
                //confirm(i);
                target_th[j]=i;
                j++;
            }
            i++;
        });

        //confirm(target_th);
        //target_th=target_th.join(',');

        /*var result = target_th.map(function (x) {
            return parseInt(x, 10);
        });*/


        var table_name=$("input[name='table_names']").val();

        ////ADDE IN VER4.0
        if(table_name===undefined){ return; }


        ////ADDED IN VER4.0
        var datatable_args={
            dom: 'Blfrtip',
            //"aaSorting": [],
            buttons: [
                {
                    extend: 'print',
                    text:      '<i class="fa fa-print"></i> Print',
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: '',
                    exportOptions: {
                        format: {
                            body: function ( data, row, column, node ) {

                                // // Strip $ from salary column to make it numeric

                                //FOR EXPORT THE CURRENCY COLUMN AS NUMBER
                                if(target_th.indexOf(column)!=-1){
                                    //return data.replace(/[^0-9\.]+/g,""); // keep . in price and remove symbol
                                    data=data.replace(/[^0-9\.\,]+/g,""); // keep . , in price and remove symbol
                                    return $.isNumeric(data.replace(',', '.')) ? data.replace(',', '.') : data;
                                    //return data;
                                }
                                //return data.replace(/<(?:.|\n)*?>/gm, '');
                                return data.replace(/<.*?>/ig, "");
                                //data.replace(params.woo_currency, '');

                            }
                        },
                        columns: ':visible'
                    },
                    title: table_name+'_export_'+today
                },
                {
                    extend:    'csvHtml5',
                    text:      '<i class="fa fa-file-text-o"></i> CSV',
                    titleAttr: '',
                    exportOptions: {
                        format: {
                            body: function ( data, row, column, node ) {

                                // // Strip $ from salary column to make it numeric

                                //FOR EXPORT THE CURRENCY COLUMN AS NUMBER
                                if(target_th.indexOf(column)!=-1){
                                    //return data.replace(/[^0-9\.]+/g,""); // keep . in price and remove symbol
                                    data=data.replace(/[^0-9\.\,]+/g,""); // keep . , in price and remove symbol
                                    return data;
                                }
                                //return data.replace(/<(?:.|\n)*?>/gm, '');
                                return data.replace(/<.*?>/ig, "");
                                //data.replace(params.woo_currency, '');

                            }
                        },
                        columns: ':visible'
                    },
                    title: table_name+'_export_'+today
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    text:      '<i class="fa fa-file-pdf-o"></i> PDF',
                    titleAttr: '',
                    exportOptions: {
                        columns: ':visible'
                    },
                    //message: 'PDF created by PDFMake with Buttons for DataTables.',
                    title: table_name+'_export_'+today
                }
            ],

            /*columnDefs: [ {
             targets: -1,
             visible: true
             } ]*/
            columnDefs: [
                { type: 'currency', targets: target_th }
            ]
        };

        ////ADDED IN VER4.0
        ///REMOVE ORDER FOR SOME GRIDS
        //'order_variation_analysis','order_product_analysis'
        var expt_table=['details','abandoned_cart','details_full','details_full_shipping','stock_summary_avg','details_brands','variation','customer_role_top_products','customer_role_bottom_products','details_product_options','details_order_country','details_tax_field','tax_reports','customer_analysis','product_variation_qty'];
        if(expt_table.indexOf(table_name)!==-1){
            //datatable_args["aaSorting"]=[];
            datatable_args["bSort"]=false;
        }

        ////ADDED IN VER4.0
        ///TOTAL ROWS - REMOVE SOME GRID OPTIONS : PAGE, ORDER, ...
        if(table_name.indexOf("total_row")!==-1){
            datatable_args["columnDefs"]=[];
        }


        var table = $('.datatable').DataTable(datatable_args);

        var fType;
        fType = getUrlVars()["smenu"];
        if(fType===undefined){
            fType = getUrlVars()['parent'];
        }
        json_str=getCookie(fType);
        if(json_str!=''){
            $('<div class="awr-selcol awr-remove-cookie dt-buttons-cooki" title="Clear Cookie"></div>').insertBefore(".awr-selcol");
        }


        $(".awr-selcol").find('input[type="checkbox"]', tableColumnToggler).each(function(){
            var iCol = parseInt($(this).attr("data-column"));
            var column = table.column( iCol );
            var fType;
            fType = getUrlVars()["smenu"];
            if(fType===undefined){
                fType = getUrlVars()['parent'];
            }

            json_str=getCookie(fType);
            if(json_str!=''){
                var arr = JSON.parse(json_str);
                /*$.each(arr, function( index, value ) {
                    confirm(value);
                });*/

                if($.inArray( iCol, arr )!==-1){
                    $(this).prop("checked",true);
                    column.visible(true );
                }else{
                    column.visible(false );
                    $(this).prop("checked",false);
                }
            }
        });


        var tableColumnToggler = $('.dropdown-menu');
        $('input[type="checkbox"]', tableColumnToggler).change(function (e) {

            var iCol = parseInt($(this).attr("data-column"));
            var column = table.column( iCol );
            column.visible( ! column.visible() );
            var fType;
            var checked_arr=new Array();
            $('input[type="checkbox"]', tableColumnToggler).each(function(){
                var iCol = parseInt($(this).attr("data-column"));

                if($(this).prop("checked")){
                    checked_arr.push(iCol);
                }

            });

            /*$.each(checked_arr, function( index, value ) {
                confirm(value);
            });*/

            var fType;
            fType = getUrlVars()["smenu"];
            if(fType===undefined){
                fType = getUrlVars()['parent'];
            }
            delete_cookie(fType);
            var json_str = JSON.stringify(checked_arr);
            setCookie(fType,json_str,5);


            /*json_str=getCookie(fType);
            var arr = JSON.parse(json_str);
            $.each(arr, function( index, value ) {
                confirm(value);
            });*/


        } );

        $(".awr-remove-cookie").click(function(){
            $(this).remove();
            var fType;
            fType = getUrlVars()["smenu"];
            if(fType===undefined){
                fType = getUrlVars()['parent'];
            }
            delete_cookie(fType);

            $('input[type="checkbox"]', tableColumnToggler).each(function(){

                var iCol = parseInt($(this).attr("data-column"));
                var column = table.column( iCol );
                var fType;
                fType = getUrlVars()["smenu"];
                if(fType===undefined){
                    fType = getUrlVars()['parent'];
                }

                json_str=getCookie(fType);
                if(json_str!=''){
                    var arr = JSON.parse(json_str);
                    /*$.each(arr, function( index, value ) {
                        confirm(value);
                    });*/

                    if($.inArray( iCol, arr )!==-1){
                        $(this).prop("checked",true);
                        column.visible(true );
                    }else{
                        column.visible(false );
                        $(this).prop("checked",false);
                    }
                }else{
                    column.visible(true );
                    $(this).prop("checked",true);
                }
            });
        });
    }

    /////////////////////////////////////
    //DASHBOARD DATATABLE INITIALIZE

    function switch_display_dashboard(data, type, table_name){

        var tableInfo=[];
        $("#awr-grid-chart-"+table_name+" tr").each(function(){

            var $td=$(this).find('td');

            if(table_name=='top_5_customer')
            {
                if($td.eq(2).text()=='')
                    return;

                tableInfo.push( {
                    label: ($td.eq(0).text()=='' ? "No Label":$td.eq(0).text()),
                    value: $td.eq(2).text(),
                } );
            }
            else{

                if($td.eq(1).text()=='')
                    return;

                tableInfo.push( {
                    label: ($td.eq(0).text()=='' ? "No Label":$td.eq(0).text()),
                    value: $td.eq(1).text(),
                } );
            }
        });

        target=type+"-"+table_name;
        //$("#"+target).addClass($("#awr-grid-chart-"+table_name).height());
        $("#"+target).addClass("awr-chart-show");

        if(type=="awr-pie-chart"){

            $("#"+target).html(loading_content);

            var chart = AmCharts.makeChart( target, {"type": "pie",
                "theme": "light",
                "dataProvider": tableInfo,
                "valueField": "value",
                "titleField": "label",
                "balloon":{
                    "fixedPosition":true
                }
            });

            $("#awr-pie-chart-"+table_name).show();
            $("#awr-bar-chart-"+table_name).hide();
            $("#awr-grid-chart-"+table_name).hide();

        }else if(type=="awr-bar-chart"){
            $("#"+target).html(loading_content);

            var chart = AmCharts.makeChart( target,
                {
                    "type": "serial",
                    "theme": "light",
                    "dataProvider": tableInfo,
                    "valueField": "value",
                    "titleField": "label",
                    "balloon":{
                        "fixedPosition":true
                    },
                    "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                    } ],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [ {
                        "balloonText": "[[label]]: <b>[[value]]</b>",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "value"
                    } ],
                    "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                    },
                    "categoryField": "label",
                    "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20,
                        "labelRotation": 45
                    },
                });

            $("#awr-pie-chart-"+table_name).hide();
            $("#awr-bar-chart-"+table_name).show();
            $("#awr-grid-chart-"+table_name).hide();

        }else if(type=="awr-grid-chart"){

            $("#awr-pie-chart-"+table_name).hide();
            $("#awr-bar-chart-"+table_name).hide();
            $("#awr-grid-chart-"+table_name).show();

        }
    }

    function datatable_init_dashboard(){
        var table = $('.datatable').DataTable( {
            paging: false,
            "searching": false,
            "ordering": false,
        } );

        $(".awr-title-icon").click(function(){

            //target=type+"-"+table_name;
            //$("#awr-grid-chart-sale_order_status").css("height","300");


            var table_data = $(this).closest(".awr-box").find('.datatable').tableToJSON();

            var swap_type='';

            table_name=$(this).attr("data-table");
            swap_type=$(this).attr("data-swap-type");

            $("#"+swap_type+"-"+ table_name).css("height","300");

            switch_display_dashboard(table_data, swap_type, table_name);

            $(this).siblings(".awr-title-icon").removeClass("awr-title-icon-active");
            $(this).addClass("awr-title-icon-active");
        });

    }

    datatable_init_dashboard();


    /////////////MENU- TOGGLE - KEEP RECENT STATUS /////////////
    pw_reporting_menu=getCookie("pw_reporting_menu");
    if(pw_reporting_menu!=''){
        setTimeout(function(){
            $('.pw-logo-cnt').trigger("click");
        },50);
    }

    $('.pw-logo-cnt').click(function () {

        $('#pw-nav-icon1').toggleClass('open');

        $(".awr-menu").toggleClass('awr-close-toggle');
        $(".awr-mini-menu").toggleClass('awr-opened-mini-menu');

        if ( $(".awr-content").hasClass('awr-nomargin') ){
            //setCookie("pw_reporting_menu","max",5);
            delete_cookie("pw_reporting_menu");
            $(".awr-content").toggleClass('awr-nomargin');
        }
        else {
            $(".awr-content").toggleClass('awr-nomargin');
            setCookie("pw_reporting_menu","min",5);
        }

    });


    ///////////////////////////////////////
    //AMAP CHART

    if($("html").find("#pwr_chartdiv_multiple").length)
    {
        var pw_from_date=$("#pwr_from_date_dashboard").val();
        var pw_to_date=$("#pwr_to_date_dashboard").val();

        var pdata = {
            action: "pw_rpt_fetch_chart",
            postdata: 'pw_from_date='+pw_from_date+"&pw_to_date="+pw_to_date,
            type : 'sales_chart',
            nonce: params.nonce,
        };
        var content_id='';
        content_id="pwr_chartdiv_multiple";

        //$("#"+content_id).html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
        $("#"+content_id).html(loading_content);
        //$("#"+content_id).html('<img src="'+params.address+'/assets/images/fa-loading-34.gif"></i>');

        function chart_init(theme_type){

            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                dataType: "json",
                success : function(resp){

                    //console.log(resp);


                    stt=JSON.stringify(resp);
                    f1=JSON.parse(stt)[0]; 	//MULTI
                    f2=JSON.parse(stt)[1];  //DAYS
                    f3=JSON.parse(stt)[2]; 	//WEEK- NOT USE
                    f4=JSON.parse(stt)[3];	//MONTH
                    f5=JSON.parse(stt)[4]; 	//PIE-TOP
                    //confirm(f4);


                    $("#"+content_id).html("");

                    //MULTIPLE CHART
                    /*var chart = AmCharts.makeChart( "pwr_chartdiv_multiple", {
                          type: "stock",
                           "theme": theme_type,

                          dataSets: [ {
                              title: "Sales by Months",
                              fieldMappings: [ {
                                fromField: "value",
                                toField: "value"
                              }, {
                                fromField: "volume",
                                toField: "volume"
                              } ],
                              dataProvider: chartData1,
                              categoryField: "date"
                            },

                            {
                              title: "Sales by Days",
                              fieldMappings: [ {
                                fromField: "value",
                                toField: "value"
                              }, {
                                fromField: "volume",
                                toField: "volume"
                              } ],
                              dataProvider: chartData2,
                              categoryField: "date"
                            },
                          ],

                          panels: [ {

                              showCategoryAxis: false,

                               valueAxes: [{

                                labelFunction : formatLabel
                              }],

                              title: "Value",
                              percentHeight: 70,

                              stockGraphs: [ {
                                id: "g1",
                                "fillAlphas": 0.4,
                                valueField: "value",
                                comparable: true,
                                //compareField: "value",
                                balloonText: "[[title]]:<b>"+params.woo_currency+"[[value]]</b>",
                                //compareGraphBalloonText: "[[title]]:<b>[[value]]</b>"
                              } ],


                            },

                            {
                              title: "Volume",
                              percentHeight: 30,
                              stockGraphs: [ {
                                valueField: "volume",
                                type: "column",
                                showBalloon: false,
                                fillAlphas: 1
                              } ],

                              stockLegend: {
                                periodValueTextRegular: "[[value.close]]"
                              }
                            }
                          ],

                          chartScrollbarSettings: {
                            graph: "g1"
                          },

                          chartCursorSettings: {
                            valueBalloonsEnabled: true,
                            fullWidth: true,
                            cursorAlpha: 0.1,
                            valueLineBalloonEnabled: true,
                            valueLineEnabled: true,
                            valueLineAlpha: 0.5,
                          },

                          periodSelector: {
                            position: "left",
                            periods: [ {
                              period: "MM",
                              selected: true,
                              count: 1,
                              label: "1 month"
                            }, {
                              period: "YYYY",
                              count: 1,
                              label: "1 year"
                            }, {
                              period: "YTD",
                              label: "YTD"
                            }, {
                              period: "MAX",
                              label: "MAX"
                            } ]
                          },

                          dataSetSelector: {
                            position: "left"
                          },

                      "export": {
                        "enabled": true,
                        //"position": "bottom-left"
                      }

                    } );*/

                    var chart = AmCharts.makeChart("pwr_chartdiv_multiple", {
                        "theme": "light",
                        "type": "serial",
                        "marginLeft": 60,
                        "dataProvider": f1,
                        "valueAxes": [{
                            "title": ""
                        }],
                        "graphs": [{
                            "balloonText": "Income in [[category]]:[[value]]",
                            "fillAlphas": 1,
                            "lineAlpha": 0.2,
                            "title": "Income",
                            "type": "column",
                            "valueField": "value"
                        }],
                        "depth3D": 20,
                        "angle": 30,
                        "rotate": true,
                        "categoryField": "date",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "fillAlpha": 0.05,
                            "position": "left"
                        },
                        "export": {
                            "enabled": true
                        }
                    });

                    //MONTH CHART
                    var chart = AmCharts.makeChart("pwr_chartdiv_month", {
                        "type": "serial",
                        "theme": theme_type,
                        "autoMargins": false,
                        "marginLeft": 50,
                        "marginRight": 8,
                        "marginLeft": 60,
                        "marginTop": 10,
                        "marginBottom": 26,
                        "balloon": {
                            "adjustBorderColor": false,
                            "horizontalPadding": 10,
                            "verticalPadding": 8,
                            "color": "#ffffff"
                        },

                        "dataProvider": f4,
                        "valueAxes": [{
                            "axisAlpha": 1,
                            "position": "left",
                            "tickLength": 0,
                            "labelFunction" : formatLabel
                        }],
                        "startDuration": 1,
                        "graphs": [{
                            "alphaField": "alpha",
                            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>"+params.woo_currency+"[[value]]</span> [[additional]]</span>",
                            "fillAlphas": 1,
                            "title": "date",
                            "type": "column",
                            "valueField": "value",
                            "dashLengthField": "dashLengthColumn"
                        }, {
                            "id": "graph2",
                            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>"+params.woo_currency+"[[value]]</span> [[additional]]</span>",
                            "bullet": "round",
                            "lineThickness": 3,
                            "bulletSize": 7,
                            "bulletBorderAlpha": 1,
                            "bulletColor": "#FFFFFF",
                            "useLineColorForBulletBorder": true,
                            "bulletBorderThickness": 3,
                            "fillAlphas": 0,
                            "lineAlpha": 1,
                            "title": "Total Sale ",
                            "valueField": "value"
                        }],
                        "categoryField": "date",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "tickLength": 0,
                            //"labelRotation": 45
                        },
                        "export": {
                            "enabled": true
                        }
                    });

                    //chart.valueAxes.labelFunction = formatLabel;

                    //DAYS CHART
                    var chart = AmCharts.makeChart("pwr_chartdiv_day", {
                        "type": "serial",
                        "theme": theme_type,
                        "marginRight": 40,
                        "marginLeft": 60,
                        "autoMarginOffset": 20,
                        "dataDateFormat": "YYYY-MM-DD",
                        "valueAxes": [{
                            "id": "v1",
                            "axisAlpha": 0,
                            "position": "left",
                            "ignoreAxisWidth":true,
                            "labelFunction" : formatLabel
                        }],
                        "balloon": {
                            "borderThickness": 1,
                            "shadowAlpha": 0
                        },
                        "graphs": [{
                            "id": "g1",
                            "balloon":{
                                "drop":true,
                                "adjustBorderColor":false,
                                "color":"#ffffff"
                            },
                            "bullet": "round",
                            "bulletBorderAlpha": 1,
                            "bulletColor": "#FFFFFF",
                            "bulletSize": 5,
                            "hideBulletsCount": 50,
                            "lineThickness": 2,
                            "title": "red line",
                            "useLineColorForBulletBorder": true,
                            "valueField": "value",
                            "balloonText": "<span style='font-size:18px;'>"+params.woo_currency+"[[value]]</span>"
                        }],
                        "chartScrollbar": {
                            "graph": "g1",
                            "oppositeAxis":false,
                            "offset":30,
                            "scrollbarHeight": 80,
                            "backgroundAlpha": 0,
                            "selectedBackgroundAlpha": 0.1,
                            "selectedBackgroundColor": "#888888",
                            "graphFillAlpha": 0,
                            "graphLineAlpha": 0.5,
                            "selectedGraphFillAlpha": 0,
                            "selectedGraphLineAlpha": 1,
                            "autoGridCount":true,
                            "color":"#AAAAAA"
                        },
                        "chartCursor": {
                            "pan": true,
                            "valueLineEnabled": true,
                            "valueLineBalloonEnabled": true,
                            "cursorAlpha":1,
                            "cursorColor":"#258cbb",
                            "limitToGraph":"g1",
                            "valueLineAlpha":0.2
                        },
                        "valueScrollbar":{
                            "oppositeAxis":false,
                            "offset":50,
                            "scrollbarHeight":10
                        },
                        "categoryField": "date",
                        "categoryAxis": {
                            "parseDates": true,
                            "dashLength": 1,
                            "minorGridEnabled": true
                        },
                        "export": {
                            "enabled": true
                        },

                        "dataProvider": f2,
                    });


                    //PIE CHART - TOP PRODUCTS
                    var chart = AmCharts.makeChart( "pwr_chartdiv_pie", {"type": "pie",
                        "theme": theme_type,
                        "dataProvider": f5,
                        "valueField": "value",
                        "titleField": "label",
                        "balloon":{
                            "fixedPosition":true
                        },
                        "export": {
                            "enabled": true
                        }
                    } );



                }
            });
        }
        chart_init("none");


        ////ADDED IN VER4.0
        /////////////////////////////
        //INTELLIGENCE REPORTS - CHARTS
        ////////////////////////////



        /////////////////////////////
        // CHARTS
        /////////////////////////////


        $(".pw_switch_chart_theme").click(function(e){
            e.preventDefault();
            var theme_type=$(this).attr("data-theme");
            chart_init(theme_type);

            switch(theme_type){
                case "none":

                    $("#pwr_chartdiv_day").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_month").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_multiple").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_pie").parent().css("background","#ffffff");
                    break;

                case "light":
                    $("#pwr_chartdiv_day").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_month").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_multiple").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_pie").parent().css("background","#ffffff");
                    break;

                case "dark":
                    $("#pwr_chartdiv_day").parent().css("background","#3f3f4f");
                    $("#pwr_chartdiv_month").parent().css("background","#3f3f4f");
                    $("#pwr_chartdiv_multiple").parent().css("background","#3f3f4f");
                    $("#pwr_chartdiv_pie").parent().css("background","#3f3f4f");
                    break;

                case "patterns":
                    $("#pwr_chartdiv_day").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_month").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_multiple").parent().css("background","#ffffff");
                    $("#pwr_chartdiv_pie").parent().css("background","#ffffff");
                    break;
            }

        });
    }
    //pw_int_chartdiv


    ////ADDED IN VER4.0
    /////////////////////////////
    //INTELLIGENCE REPORTS - CHARTS
    ////////////////////////////
    function pw_intelligence_chart() {

        var chart = AmCharts.makeChart("pw_int_sale_chartdiv", {
            "type": "serial",
            "theme": "light",
            "valueAxes": [{
                "stackType": "regular",
                "gridAlpha": 0.07,
                "position": "left",
                "labelFunction" : formatLabel,
            }],
            "graphs": [{
                "id": "g1",
                "fillAlphas": 0.4,
                "valueField": "value",
                "balloonText": "<div style='margin:5px; font-size:19px;'>"+params.woo_currency+"<b>[[value]]</b></div>"
            }],
            "chartScrollbar": {
                "graph": "g1",
                "scrollbarHeight": 20,
                "backgroundAlpha": 0,
                "selectedBackgroundAlpha": 0.1,
                "selectedBackgroundColor": "#888888",
                "graphFillAlpha": 0,
                "graphLineAlpha": 0.5,
                "selectedGraphFillAlpha": 0,
                "selectedGraphLineAlpha": 1,
                "autoGridCount": true,
                "color": "#AAAAAA"
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                "cursorPosition": "mouse"
            },
            "categoryField": "date",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true
            },
            // "export": {
            //     "enabled": true,
            //     "dateFormat": "MM/DD/YYYY"
            // },
            "dataProvider": chart_int_data
        });
    }

    function pw_intelligence_single_product_chart(){
        var chart = AmCharts.makeChart("pw_int_single_product_chartdiv", {
            "type": "serial",
            "addClassNames": true,
            "theme": "light",
            "autoMargins": false,
            "marginLeft": 8,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "balloon": {
                "adjustBorderColor": false,
                "horizontalPadding": 10,
                "verticalPadding": 8,
                "color": "#ffffff"
            },

            "dataProvider": pw_int_single_product_value,
            "valueAxes": [ {
                "axisAlpha": 0,
                "position": "left",
                "labelsEnabled": false
            } ],
            "startDuration": 1,
            "graphs": [ {
                "alphaField": "alpha",
                "balloonText": "<span style='font-size:12px;'>"+params.woo_currency+"[[value]] in [[category]]</span>",
                "fillAlphas": 1,
                "title": "Sale",
                "type": "column",
                "valueField": "income",
                "dashLengthField": "dashLengthColumn"
            }, {
                "id": "graph2",
                "balloonText": "<span style='font-size:12px;'>[[value]] Sale(s) in [[category]]</span>",
                "bullet": "round",
                "lineThickness": 3,
                "bulletSize": 7,
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": true,
                "bulletBorderThickness": 3,
                "fillAlphas": 0,
                "lineAlpha": 1,
                "title": "",
                "valueField": "expenses",
                "dashLengthField": "dashLengthLine"
            } ],
            "categoryField": "year",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelsEnabled": false
            },
            // "export": {
            //     "enabled": true
            // }
        } );
    }

    var currecnt_product_clicked='';
    function pw_intelligence_product_single() {

        $(".pw_int_products_single").on("click", function (e) {
            e.preventDefault();

            currecnt_product_clicked=$(this);

            product_id=$(this).attr("data-product-id");
            product_rank_type=$(this).attr("data-product-rank-type");
            product_rank_val=$(this).attr("data-product-rank-val");
            product_rank_title=$(this).attr("data-product-rank-title");

            //int_orders_products_arrs = JSON.stringify(int_orders_products_arr);

            var pdata = {
                action: "pw_rpt_fetch_single_product",
                postdata: $("#intelligence_top_product_chart").serialize()+'&product_id=' + product_id + '&product_rank_type=' + product_rank_type + '&product_rank_val=' + product_rank_val + '&product_rank_title=' + product_rank_title + '&total_products_amnt='+int_total_products_amnt+ '&total_products_refund_amnt='+int_total_refund_products_amnt,
                nonce: params.nonce,
            }

            currecnt_product_clicked.find(".awr-int-loading").show();
            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                success : function(resp){
                    currecnt_product_clicked.find(".awr-int-loading").hide();
                    $("#pw_rpt_fetch_single_product_html").html(resp);
                    $("#pw_rpt_fetch_single_product_html").show();
                    $("#pw_rpt_fetch_single_product_main").attr("style","display:none !important;");
                    $("html, body").animate({
                        scrollTop: 0
                    }, 600);

                    $(".pw_rpt_fetch_single_product_back").on("click", function (e) {
                        e.preventDefault();
                        $("#pw_rpt_fetch_single_product_html").attr("style","display:none !important;");
                        $("#pw_rpt_fetch_single_product_main").show();
                    });

                    $(".pw_rpt_fetch_single_product_next").on("click", function (e) {
                        e.preventDefault();
                        currecnt_product_clicked.next().click();
                    });

                    $(".pw_rpt_fetch_single_product_prev").on("click", function (e) {
                        e.preventDefault();
                        currecnt_product_clicked.prev().click();
                    });

                    //DISABLE NEXT/PREV AFTER CONTENT LOADED
                    if(currecnt_product_clicked.next("div").length == 0){$(".pw_rpt_fetch_single_product_next").addClass("pw_rpt_fetch_single_product_next_disabled");}
                    if(currecnt_product_clicked.prev("div").length == 0){$(".pw_rpt_fetch_single_product_prev").addClass("pw_rpt_fetch_single_product_prev_disabled");}

                    pw_intelligence_single_product_chart();

                    pw_intelligence_customer_single("pw_rpt_fetch_single_product_html");

                }
            });
        });


    }

    function pw_intelligence_top_product_chart() {
        var int_top_products_chart = AmCharts.makeChart("int_top_products_chart_args",{
            "type": "serial",
            "theme": "light",
            "marginRight":30,
            "legend": {
                "equalWidths": false,
                "periodValueText": "total: [[value.sum]]",
                "position": "top",
                "valueAlign": "left",
                "valueWidth": 100
            },
            "dataProvider": int_top_products_chart_args_data,
            "valueAxes": [{
                "stackType": "regular",
                "gridAlpha": 0.07,
                "position": "left",
                "labelFunction" : formatLabel,
            }],
            "graphs": int_top_products_chart_args_graph,
            "plotAreaBorderAlpha": 0,
            "marginTop": 10,
            "marginLeft": 0,
            "marginBottom": 0,
            "chartScrollbar": {},
            "chartCursor": {
                "cursorAlpha": 0
            },
            "categoryField": "date",

            // "export": {
            // 	"enabled": true
            // }
        });
    }

    var currecnt_customer_clicked='';
    function pw_intelligence_customer_single(page_id) {

        siblings='tr.pw_int_customers_single';
        if (page_id === undefined) {
            page_id ='pw_rpt_fetch_single_customer_main';
            siblings='div';
        }

        $(".pw_int_customers_single").on("click", function (e) {
            e.preventDefault();


            currecnt_customer_clicked=$(this);

            customer_id=$(this).attr("data-customer-id");
            customer_email=$(this).attr("data-customer-email");
            customer_segment=$(this).attr("data-customer-segment");

            var pdata = {
                action: "pw_rpt_fetch_single_customer",
                postdata: 'page_id='+page_id+'&customer_id=' + customer_id+'&customer_email=' + customer_email+'&customer_segment=' + customer_segment,
                nonce: params.nonce,
            }

            currecnt_customer_clicked.find(".awr-int-loading").show();
            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                success : function(resp){
                    currecnt_customer_clicked.find(".awr-int-loading").hide();
                    $("#pw_rpt_fetch_single_customer_html").html(resp);
                    $("#pw_rpt_fetch_single_customer_html").show();
                    //$("#"+page_id).attr("style","display:none !important;");
                    $("#"+page_id).hide();
                    $("html, body").animate({
                        scrollTop: 0
                    }, 600);

                    $(".pw_rpt_fetch_single_customer_back").on("click", function (e) {
                        e.preventDefault();
                        $("#pw_rpt_fetch_single_customer_html").attr("style","display:none !important;");
                        $("#"+page_id).show();
                    });

                    $(".pw_rpt_fetch_single_customer_next").on("click", function (e) {
                        e.preventDefault();
                        currecnt_customer_clicked.next().click();
                    });

                    $(".pw_rpt_fetch_single_customer_prev").on("click", function (e) {
                        e.preventDefault();
                        currecnt_customer_clicked.prev().click();
                    });

                    //DISABLE NEXT/PREV AFTER CONTENT LOADED
                    if(currecnt_customer_clicked.next(siblings).length == 0){$(".pw_rpt_fetch_single_customer_next").addClass("pw_rpt_fetch_single_customer_next_disabled");}
                    if(currecnt_customer_clicked.prev(siblings).length == 0){$(".pw_rpt_fetch_single_customer_prev").addClass("pw_rpt_fetch_single_customer_prev_disabled");}

                }
            });
        });


    }

    function pw_intelligence_customer_chart() {

        var chart = AmCharts.makeChart("pw_int_customer_chartdiv", {
            "type": "serial",
            "theme": "light",
            "dataProvider": chart_customer_int_data,
            "startDuration": 1,
            "graphs": [{
                "fillAlphas": 0.8,
                "fillColorsField": "color_p",
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "revenue",
                "title": "Revenue",
                //"labelText": "[[value]]",
                "clustered": false,
                "labelFunction": function(item) {
                    return params.woo_currency+ item.values.value;
                },
                "balloonFunction": function(item) {
                    return item.category + ": " + params.woo_currency+  item.values.value ;
                }
            }, {
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "fillColorsField": "color_n",
                "type": "column",
                "valueField": "refund",
                "title": "Refunded",
                //"labelText": "[[value]]",
                "clustered": false,
                "labelFunction": function(item) {
                    return params.woo_currency+ item.values.value;
                },
                "balloonFunction": function(item) {
                    return item.category + ": - " + params.woo_currency+ ((item.values.value)*(-1)) ;
                }
            }],
            "categoryField": "date",
            "categoryAxis": {
                "gridPosition": "start",
                "gridAlpha": 0.2,
                "axisAlpha": 0,
                "labelsEnabled":false,
            },
            "valueAxes": [ {
                "axisAlpha": 0,
                "position": "left",
                "labelsEnabled": false
            } ],
            "balloon": {
                "fixedPosition": true
            },
            "chartCursor": {
                "valueBalloonsEnabled": true,
                "cursorAlpha": 0.05,
                "fullWidth": false
            },

            // "export": {
            //     "enabled": true
            // }
        });
    }

    ////ADDED IN VER4.0
    /////////////////////////////
    //INTELLIGENCE REPORTS - CLICK ON CUSTOMER
    ////////////////////////////
    $(document).on("click",".pw_intelligence_note_text_save",function () {

        target=$(this).attr("data-target");
        id=$(this).attr("data-id");
        note_text=$('.pw_intelligence_note_text').val();

        var pdata = {
            action: "pw_rpt_int_add_note",
            postdata: 'id=' + id+"&target="+target+"&note_text="+note_text,
            nonce: params.nonce,
        }

        $.ajax({
            type: "POST",
            url: params.ajaxurl,
            data: pdata,
            success: function (resp) {
                $(".pw_intelligence_note_resp").html(resp);
                $(".pw_intelligence_note_resp").fadeIn('slow', function(){
                    $(".pw_intelligence_note_resp").delay(5000).fadeOut();
                });
            }
        });

    });


    ////ADDED IN VER4.0
    /////////////////////////////
    //INTELLIGENCE REPORTS - CLICK ON CUSTOMER
    ////////////////////////////
    var order_id='';

    function int_click_td(){


        $(".intelligence_customer_datatable").find("tr").click(function() {

            var row_id = $(this).find("td").eq(0).attr("data-order-id");
            //confirm("dfdf"+row_id);

            $(this).siblings("tr").removeClass("selected");
            $(this).addClass("selected");

            order_id=row_id;

            var pdata = {
                action: "pw_rpt_int_customer_details",
                postdata: 'row_id=' + row_id,
                nonce: params.nonce,
            }

            $(".awr-int-loading").show();

            //$("#chartdiv").html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
            //$("#chartdiv").html(</i>');

            $.ajax({
                type: "POST",
                url: params.ajaxurl,
                data: pdata,
                success: function (resp) {
                    //confirm(resp);
                    $(".awr-int-loading").hide();
                    $("#pw_intelligence_customer_details").html(resp);
                    //$(".pw_intelligence_change_order_status").attr("data-order-id",row_id);
                }
            });
        });
    }

    $(document).on("click",".pw_intelligence_change_order_status",function () {

        // var order_id=$(".pw_intelligence_change_order_status").attr("data-order-id");
        var new_status=$(".pw_intelligence_order_status").val();

        var pdata = {
            action: "pw_rpt_int_change_order_staus",
            postdata: 'order_id=' + order_id+"&status="+new_status,
            nonce: params.nonce,
        }

        $.ajax({
            type: "POST",
            url: params.ajaxurl,
            data: pdata,
            success: function (resp) {

                var pdata = {
                    action: "pw_rpt_int_customer_details",
                    postdata: 'row_id=' + order_id,
                    nonce: params.nonce,
                }

                //$("#chartdiv").html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
                //$("#chartdiv").html('<img src="'+params.address+'/assets/images/fa-loading-34.gif"></i>');

                $.ajax({
                    type: "POST",
                    url: params.ajaxurl,
                    data: pdata,
                    success: function (resp) {
                        //confirm(resp);
                        $("#pw_intelligence_customer_details").html(resp);
                        //$(".pw_intelligence_change_order_status").attr("data-order-id",row_id);
                    }
                });

                //confirm(resp);
                //$("#pw_intelligence_customer_details").html(resp);
            }
        });
    });

    /////////////////////////////
    //PRODUCT PAGE- CLICK ON PRODUCT ROWS
    ////////////////////////////

    if($("html").find("#pw_product_chart").length>0)
    {
        $(".button-primary,.button-secondary,.form_reset_btn").click(function(){
            pw_product_chart();
        });

        function pw_product_chart(){

            AmCharts.addInitHandler(function(chart) {
                if (chart.legend === undefined || chart.legend.truncateLabels === undefined)
                    return;

                // init fields
                var titleField = chart.titleField;
                var legendTitleField = chart.titleField+"Legend";

                // iterate through the data and create truncated label properties
                for(var i = 0; i < chart.dataProvider.length; i++) {
                    var label = chart.dataProvider[i][chart.titleField];
                    if (label.length > chart.legend.truncateLabels)
                        label = label.substr(0, chart.legend.truncateLabels-1)+'...'
                    chart.dataProvider[i][legendTitleField] = label;
                }

                // replace chart.titleField to show our own truncated field
                chart.titleField = legendTitleField;

                // make the balloonText use full title instead
                chart.balloonText = chart.balloonText.replace(/\[\[title\]\]/, "[["+titleField+"]]");

            }, ["pie"]);

            var pdata = {
                action: "pw_rpt_fetch_product_chart",
                postdata: $(".search_form_report").serialize() +'&type=pie',
                nonce: params.nonce,
            }


            $("#pw_product_chart").html(loading_content);

            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                dataType: "json",
                success : function(resp){

                    $("#pw_product_chart").html("");

                    var chart = AmCharts.makeChart("pw_product_chart", {
                        "type": "pie",
                        "startDuration": 0,
                        "theme": "light",
                        "addClassNames": true,
                        "legend":{
                            "position":"right",
                            "marginRight":100,
                            "autoMargins":false,
                            "truncateLabels": 15
                        },
                        "innerRadius": "30%",
                        "defs": {
                            "filter": [{
                                "id": "shadow",
                                "width": "200%",
                                "height": "200%",
                                "feOffset": {
                                    "result": "offOut",
                                    "in": "SourceAlpha",
                                    "dx": 0,
                                    "dy": 0
                                },
                                "feGaussianBlur": {
                                    "result": "blurOut",
                                    "in": "offOut",
                                    "stdDeviation": 5
                                },
                                "feBlend": {
                                    "in": "SourceGraphic",
                                    "in2": "blurOut",
                                    "mode": "normal"
                                }
                            }]
                        },
                        "dataProvider": resp,
                        "valueField": "value",
                        "titleField": "label",
                        "export": {
                            "enabled": true
                        }
                    });
                }
            });
        }
        pw_product_chart();
    }

    $(".pw_product_chart_click").on("click",function(){

        var row_id=$(this).data("product-id");
        var row_title=$(this).data("product-title");

        var pdata = {
            action: "pw_rpt_fetch_product_chart",
            postdata: $(".search_form_report").serialize() +'&row_id='+row_id+'&type=line',
            nonce: params.nonce,
        }

        $("#pw_product_chart").html(loading_content);
        $("#pw_product_chart_title").find("h3").html(row_title+" Chart");

        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            dataType: "json",
            success : function(resp){
                //console.log(resp);

                $("#pw_product_chart").html("");
                var chart = AmCharts.makeChart("pw_product_chart", {
                    "type": "serial",
                    "theme": "light",
                    "categoryField": "date",
                    "rotate": true,
                    "startDuration": 1,
                    "categoryAxis": {
                        "gridPosition": "start",
                        "position": "left"
                    },
                    "trendLines": [],
                    "graphs": [
                        {
                            "balloonText": "Open:[[value]]",
                            "fillAlphas": 0.8,
                            "id": "AmGraph-1",
                            "lineAlpha": 0.2,
                            "title": "Open",
                            "type": "column",
                            "valueField": "open"
                        },
                        {
                            "balloonText": "Abandoned:[[value]]",
                            "fillAlphas": 0.8,
                            "id": "AmGraph-2",
                            "lineAlpha": 0.2,
                            "title": "Abandoned",
                            "type": "column",
                            "valueField": "abandoned"
                        },
                        {
                            "balloonText": "Convert:[[value]]",
                            "fillAlphas": 0.8,
                            "id": "AmGraph-2",
                            "lineAlpha": 0.2,
                            "title": "Convert",
                            "type": "column",
                            "valueField": "convert"
                        }
                    ],
                    "guides": [],
                    "valueAxes": [
                        {
                            "id": "ValueAxis-1",
                            "position": "top",
                            "axisAlpha": 0
                        }
                    ],
                    "allLabels": [],
                    "balloon": {},
                    "titles": [],
                    "dataProvider": resp,
                    "export": {
                        "enabled": true
                    }

                });
            }
        });

    });
    function click_td(){
        $(".products_datatable").find("tr").click(function(){
            var row_id=$(this).find("td").eq(0).attr("data-order-id");

            var pdata = {
                action: "pw_rpt_fetch_chart",
                postdata: 'row_id='+row_id,
                nonce: params.nonce,
            }

            //$("#chartdiv").html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
            //$("#chartdiv").html('<img src="'+params.address+'/assets/images/fa-loading-34.gif"></i>');
            $("#chartdiv").html(loading_content);

            $.ajax ({
                type: "POST",
                url : params.ajaxurl,
                data:  pdata,
                dataType: "json",
                success : function(resp){
                    //confirm(resp);

                    $("#chartdiv").html("");
                    var chart = AmCharts.makeChart( "chartdiv", {
                        type: "stock",
                        //"theme": "none",
                        "theme": "chalk",

                        dataSets: [ {
                            title: "first data set",
                            fieldMappings: [ {
                                fromField: "value",
                                toField: "value"
                            }],
                            dataProvider: resp,
                            categoryField: "date"
                        },

                            {
                                title: "second data set",
                                fieldMappings: [ {
                                    fromField: "value",
                                    toField: "value"
                                }],
                                dataProvider: chartData2,
                                categoryField: "date"
                            },

                            {
                                title: "third data set",
                                fieldMappings: [ {
                                    fromField: "value",
                                    toField: "value"
                                }],
                                dataProvider: chartData3,
                                categoryField: "date"
                            },

                            {
                                title: "fourth data set",
                                fieldMappings: [ {
                                    fromField: "value",
                                    toField: "value"
                                } ],
                                dataProvider: chartData4,
                                categoryField: "date"
                            }
                        ],

                        panels: [ {

                            showCategoryAxis: false,
                            title: "Value",
                            percentHeight: 70,

                            stockGraphs: [ {
                                id: "g1",

                                valueField: "value",
                                comparable: true,
                                compareField: "value",
                                balloonText: "[[title]]:<b>[[value]]</b>",
                                compareGraphBalloonText: "[[title]]:<b>[[value]]</b>"
                            } ],

                            stockLegend: {
                                periodValueTextComparing: "[[percents.value.close]]%",
                                periodValueTextRegular: "[[value.close]]"
                            }
                        },


                        ],

                        chartScrollbarSettings: {
                            graph: "g1"
                        },

                        chartCursorSettings: {
                            valueBalloonsEnabled: true,
                            fullWidth: true,
                            cursorAlpha: 0.1,
                            valueLineBalloonEnabled: true,
                            valueLineEnabled: true,
                            valueLineAlpha: 0.5
                        },

                        periodSelector: {
                            position: "left",
                            periods: [ {
                                period: "MM",
                                selected: true,
                                count: 1,
                                label: "1 month"
                            }, {
                                period: "YYYY",
                                count: 1,
                                label: "1 year"
                            }, {
                                period: "YTD",
                                label: "YTD"
                            }, {
                                period: "MAX",
                                label: "MAX"
                            } ]
                        },

                        dataSetSelector: {
                            position: "left"
                        },

                        "export": {
                            //"enabled": true,
                            //"position": "bottom-left"
                        }

                    } );
                }
            });

        });
    }
    //click_td();

    $(".form_reset_btn").click(function(){
        $(".search_form_report")[0].reset();
        $('.search_form_report  input[type="text"]').val('');
    });

    ///////////////////////////////////////
    //SUBMIT FORM AND FETCH DASHBOARD DATATABLE
    $(".search_form_report_dashboard").submit(function(e){
        e.preventDefault();
        var form_id;
        form_id=$(this).attr("id");

        var pdata = {
            action: "pw_rpt_fetch_data_dashborad",
            postdata: $(".search_form_report_dashboard").serialize(),
            nonce: params.nonce,
        }

        //$(".fetch_form_loading").html('<i class="fa fa-circle-o-notch fa-pulse fa-2x"></i>');
        //$(".fetch_form_loading").html('<img src="'+params.address+'/assets/images/fa-loading-34.gif"></i>');
        $(".fetch_form_loading").html(loading_content);

        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            success : function(resp){

                $("#dashboard_target").html(resp);
                $(".fetch_form_loading").html("");

                if(form_id=="product_form")
                {
                    click_td();
                }
                if(form_id!="dashboard_form")
                {
                    datatable_init_dashboard();
                }
            }
        });
    });

    ////////////////////////////////////////
    //SUBMIT FORM AND FETCH PAGES DATATABLE
    //$("#int_search").html();
    var page = getParameterByName('smenu');
    if(page=='int_reports_sales' || page=='int_reports_products' || page=='int_reports_customers' || page=='int_reports_home' || page=='int_reports_transactions'){
        $('.search_form_report').prependTo('.pw_intelligence_search_form');
        $(".awr-box-content-form").parents(".row").eq(0).remove();
    }

    $(".search_form_report").submit(function(e){
        e.preventDefault();

        var form_id;
        form_id=$(this).attr("id");

        var ford=$(".search_form_report").serialize();
        ford=ford.replace("table_name", "table_names");
        var pdata = {
            action: "pw_rpt_fetch_data",
            postdata: ford,
            nonce: params.nonce,
        }


        var pdata = {
            action: "pw_rpt_fetch_data",
            postdata: $(".search_form_report").serialize(),
            nonce: params.nonce,
        }


        //EMPTY CONTROL FIELDS AFTER FETCH DATA AND READY FOR NEXT ACTION
        ////ADDED IN VER4.0
        /////////////////////////////
        //INTELLIGENCE REPORTS - CHARTS
        ////////////////////////////
        if(form_id=="intelligence_customer_chart"){
            $(".pw_rfm_segment_value").val('');
            $(".pw_rfm_segment_customer_ids").val('');
        }

        //$(".fetch_form_loading").html('<i class="fa fa-circle-o-notch fa-pulse fa-2x"></i>');
        //$(".fetch_form_loading").html('<img src="'+params.address+'/assets/images/fa-loading-34.gif"></i>');
        $(".fetch_form_loading").html(loading_content);

        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            success : function(resp){

                $("#target").html(resp);
                $(".fetch_form_loading").html("");

                //$('#popoverData').popover();
                //$('.popoverOption').popover({ trigger: "hover" });

                if(form_id=="product_form")
                {
                    click_td();
                }

                ////ADDED IN VER4.0
                /////////////////////////////
                //INTELLIGENCE REPORTS - CHARTS
                ////////////////////////////
                if(form_id=="intelligence_customer_datatable"){
                    //confirm(form_id);
                    int_click_td();
                    pw_intelligence_chart();
                    $main_content=$(".pw_int_heatmap_value").parents('.pw-box-padder').html();
                    $(".intelligence_customer_datatable").find("tr").eq(1).click();
                    $(".pw_int_heatmap_circle").mouseover(function(){
                        $(".pw_int_heatmap_value").html($(this).attr("data-value"));
                        $(".pw_int_heatmap_time").html($(this).attr("data-time"));
                        $(".pw_int_heatmap_onesale").html($(this).attr("data-onesale"));
                    });

                    //pw_int_heatmap_circle
                    $(".pw_int_heatmap_circle").mouseout(function(){
                        $(".pw_int_heatmap_value").parents('.pw-box-padder').html($main_content);
                    });

                    $('.datatable').DataTable( {
                        scrollY:        '75vh',
                        scrollCollapse: true,
                        paging:         false,

                        bSort:false
                    } );
                }
                if(form_id=="intelligence_top_product_chart"){
                    pw_intelligence_top_product_chart();
                    pw_intelligence_product_single();
                }

                if(form_id=="intelligence_customer_chart"){
                    pw_intelligence_customer_chart();
                    pw_intelligence_customer_single();
                    $(".pw_rfm_segment_td").click(function(){

                        //THIS VALUES WILL BE REMOVE ATER FETCH DATA - LINE 1751
                        $(".pw_rfm_segment_value").val($(this).attr("data-segment"));
                        $(".pw_rfm_segment_customer_ids").val(pw_rfm_segment_value[$(this).attr("data-segment")]);

                        $(".pw_rfm_segment_value").parents('form').submit();
                    });
                }


                if(form_id!="dashboard_form" && form_id!='intelligence_customer_datatable')
                {
                    datatable_init();
                }

                if(form_id=="dashboard_form")
                {
                    [].slice.call( document.querySelectorAll( ".tabsB" ) ).forEach( function( el ) {
                        new CBPFWTabs( el );
                    });
                }
            }
        });


    });

    /////////////////////////////////////
    //TEST EMAIL
    $("#pw_rpt_test_email").on("click",function(e){
        e.preventDefault();

        var pdata = {
            action: "pw_rpt_test_email",
            nonce: params.nonce,
        }


        //confirm("pw_livesearch_nonce-aas"+params.nonce);
        $(".email_target").html(loading_content);

        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            success : function(resp){
                //confirm(resp);
                $(".email_target").html(resp);
            }
        });

    });

    /////////////////////////////////////////////////
    // ADD DATEPICKER TO TEXTBOXES WITH .datepick CLASS
    if(typeof $('.datepick').datepicker !== 'undefined' && $.isFunction($('.datepick').datepicker))
    {
        /*$('.datepick').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });*/


        var daysToAdd = 0;
        var d = new Date();
        $("#pwr_from_date").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,

            /*
             onSelect: function (selected) {
             var dtMax = new Date(selected);
             -               dtMax.setDate(dtMax.getDate() - daysToAdd);
             +               dtMax.setDate(dtMax.getDate() - daysToAdd + 1);
             var dd = dtMax.getDate();
             var mm = dtMax.getMonth() + 1;
             var y = dtMax.getFullYear();
                 var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_to_date").datepicker("option", "minDate", dtFormatted);
             }
            */

            onSelect: function (selected) {
                var dtMax = new Date(selected);
                dtMax.setDate(dtMax.getDate() + daysToAdd);
                var dd = dtMax.getDate();
                var mm = dtMax.getMonth() + 1;
                var y = dtMax.getFullYear();
                var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_to_date").datepicker("option", "minDate", dtFormatted);
            }
        });

        $("#pwr_to_date").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,

            onSelect: function (selected) {
                var dtMax = new Date(selected);
                dtMax.setDate(dtMax.getDate() - daysToAdd);
                var dd = dtMax.getDate();
                var mm = dtMax.getMonth() + 1;
                var y = dtMax.getFullYear();
                var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_from_date").datepicker("option", "maxDate", dtFormatted)
            }
        });
        var currentDate = new Date();

        if($("#pwr_from_date").val()=='')
            $("#pwr_from_date").datepicker("setDate",currentDate);

        if($("#pwr_to_date").val()=='')
            $("#pwr_to_date").datepicker("setDate",currentDate);



        //CUSTOM WORK - 11899
        var daysToAdd = 0;
        var d = new Date();
        $("#pwr_from_date_delivery").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,

            /*
             onSelect: function (selected) {
             var dtMax = new Date(selected);
             -               dtMax.setDate(dtMax.getDate() - daysToAdd);
             +               dtMax.setDate(dtMax.getDate() - daysToAdd + 1);
             var dd = dtMax.getDate();
             var mm = dtMax.getMonth() + 1;
             var y = dtMax.getFullYear();
                 var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_to_date").datepicker("option", "minDate", dtFormatted);
             }
            */

            onSelect: function (selected) {
                var dtMax = new Date(selected);
                dtMax.setDate(dtMax.getDate() + daysToAdd);
                var dd = dtMax.getDate();
                var mm = dtMax.getMonth() + 1;
                var y = dtMax.getFullYear();
                var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_to_date_delivery").datepicker("option", "minDate", dtFormatted);
            }
        });

        $("#pwr_to_date_delivery").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,

            onSelect: function (selected) {
                var dtMax = new Date(selected);
                dtMax.setDate(dtMax.getDate() - daysToAdd);
                var dd = dtMax.getDate();
                var mm = dtMax.getMonth() + 1;
                var y = dtMax.getFullYear();
                var dtFormatted = y + '-' + mm + '-' + dd ;
                $("#pwr_from_date_delivery").datepicker("option", "maxDate", dtFormatted)
            }
        });

    }

    jQuery(".chosen-select-search").chosen();

    $(".search_form_report").submit();

    //ACTIVATE Main Menu
    $("#toplevel_page_wcx_wcreport_plugin_dashboard-parent-dashboard").addClass("awr-active-mainmenu");


    // SHOWING HIDDEN DEFAULT POSTBOXES
    $(".postbox").removeClass("hide");


    // MAKE PRINT PAGE FULL VIEW
    $(".DTTT_button_print").click(function() {
        $("#wpcontent").addClass("fullview");
        $("#wpcontent").prepend("<span class='checkprint hide'></span>");
    });

    // CHECK ESC BUTTON
    $(document).keyup(function(e) {
        if($("checkprint")){
            if (e.keyCode == 27){
                $("#wpcontent").removeClass("fullview");
                $("checkprint").remove();
            }
        }
    });


    ////ADDED IN VER4.0
    /// INVOICE ACTION
    $(".pw_pdf_invoice").on("click",function (e) {
        e.preventDefault();
        var order_id=$(this).attr("data-order-id");
        var pdata = {
            action : "pw_rpt_pdf_invoice",
            postdata : "order_id="+order_id,
            nonce : params.nonce,
        }

        var $this=$(this);
        //$(".email_target").html(loading_content);

        $this.find("i").removeClass("fa-file-text-o").addClass("fa-spinner fa-pulse");
        $this.parents('td').find(".fa-download").remove();
        $.ajax ({
            type: "POST",
            url : params.ajaxurl,
            data:  pdata,
            success : function(resp){
                //confirm(resp);
                $('<a href="'+resp+'" target="_blank"><i class="fa fa-download button "></i></a>').insertAfter($this);
                $this.find("i").removeClass("fa-spinner fa-pulse").addClass("fa-file-text-o");
            }
        });
    });


    ////ADDED IN VER4.0
    /// UPLOAD
    if ( ! jQuery('.custom_upload_image').val() )
    {
        jQuery('.awr_search_remove_image_button').hide();
    }

    // Uploading files
    var file_frame;

    jQuery(document).on( 'click', '.awr_upload_image_button', function( event ){

        event.preventDefault();

        formfield = jQuery(this).siblings('.custom_upload_image');
        preview = jQuery(this).siblings('.custom_preview_image');

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.downloadable_file = wp.media({
            title: 'Choose image',
            button: {
                text: 'Use image',
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();

            formfield.val( attachment.id );
            preview.attr('src', attachment.url );

            jQuery('.awr_search_remove_image_button').show();
        });

        // Finally, open the modal.
        file_frame.open();
    });

    jQuery(document).on( 'click', '.awr_search_remove_image_button', function( event ){

        formfield = jQuery(this).siblings('.custom_upload_image');
        preview = jQuery(this).siblings('.custom_preview_image');

        formfield.val('');
        preview.attr('src', '' );
        jQuery(this).siblings('.awr_search_remove_image_button').hide();
        return false;
    });







    jQuery(".player-select").select2({
        ajax: {
            // url: "https://raw.githubusercontent.com/kshkrao3/JsonFileSample/master/select2resp.json",
            url : params.ajaxurl,
            // dataType: 'html',
            dataType: 'json',
            type: "POST",
            //delay: 250,
            //data:  pdata,
            data: function(pw_params) {
                return {
                    action : "pw_chosen_ajax",
                    // postdata : "q="+pw_params.term+"&target_type="+ (product_type=='' ? :product_type),
                    postdata : "q="+pw_params.term+"&target_type="+ $(this).data("element-type"),
                    nonce : params.nonce,
                };
            },
            processResults: function(data, pw_params) {
               // confirm(data);
                // confirm(pw_params);
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                var resData = [];
                data.forEach(function(value) {
                    //if (value.LastName.indexOf(pw_params.term) != -1)
                    resData.push(value)
                })
                return {
                    results: $.map(resData, function(item) {
                        return {
                            text: item.label,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        //multiple : true,
        //allowClear: true,
        //minimumResultsForSearch: -1,
        placeholder: 'past your placeholder'
    });


});

//USE FOR VARIATION REPORT - DETECT THE PRODUCT TYPE FOR CHOSEN
// var product_type='';