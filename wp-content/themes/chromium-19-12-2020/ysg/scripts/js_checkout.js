
//var base_url = "http://127.0.0.1/carcareworld/";
var base_url = "https://store.thecarcareworld.com/";

jQuery(function ($) {

    var $cstype = "";

    $(window).load(function () {
        if ($('.js-ysg_selected_city').has('optgroup').size() <= 0) {
            $cstype = "billing_country";
            doChangeCities({})
        }

        if ($('.js-ysg_selected_scity').has('optgroup').size() <= 0) {
            $cstype = "shipping_country";
            doChangeCities({})
        }

        $('label[for="billing_city"], label[for="shipping_city"]').html('Emirates / Area <abbr class="required" title="required">*</abbr>');

        $('label[for="billing_city2"], label[for="shipping_city2"]').html('City / Area <abbr class="required" title="required">*</abbr>');

        $('body').on('update_checkout', function () {
            $('label[for="billing_city"], label[for="shipping_city"]').html('Emirates / Area <abbr class="required" title="required">*</abbr>');

            $('label[for="billing_city2"], label[for="shipping_city2"]').html('City / Area <abbr class="required" title="required">*</abbr>');
        });

    });


    $('body').on('change', '#shipping-pickup-store-select', function () {
        $("#shipping_pickupstore_id").val($("#shipping-pickup-store-select option:selected").attr('data-id'));
    });

    $('body').on('change', '#shipping_country, #billing_country', function () {
        //$('#billing_city_field, #shipping_city_field').css({ "display": "none" });
        $cstype = $(this).attr('id');
        //get cities
        $coutry_code = $(this).val();
        url = base_url + 'rnakdklake/';
        var params = {};
        params['country_code'] = $coutry_code;
        params['action'] = "get_cities_country";
        var submit_link = url;

        sendToServer(submit_link, params, returnCitiesList, 'get');
    });

    /**
    * @param {json} data
    * @returns boolean
    */
    var returnCitiesList = function (data) {

        if (data === "null" || typeof data === 'undefined' || data === null || data === "")
            return;

        if (data.error) {
            alert('<span class="error_message">' + data.error + '</span>');
            return;
        }

        if (data.success !== "") {
            doChangeCities(data);
        }
    };


    function doChangeCities(data) {
        $class_inh = ".js_billing_city2";
        $class_m = ".js-ysg_selected_city";
        $classk = "#billing_city_field";

        if ($cstype == "shipping_country") {
            $class_m = ".js-ysg_selected_scity";
            $class_inh = ".js_shipping_city2";
            $classk = "#shipping_city_field";
        }

        //if empty show city2
        if (typeof (data.html) === "undefined" || data.html == "") {
            //show city2 and hide city dropdown
            $($class_inh).css({ "display": "block" });
            $($classk).css({ "display": "none" });

            $($class_m).html('').append("").trigger('change');
        } else {
            $($class_inh).css({ "display": "none" });
            $($classk).css({ "display": "block" });
            //
            $($class_m).html('').append(data.html).trigger('change');
        }
    }

});


/**
 *	Sends request to the server
 *	@param url : the url link
 *	@param params : the parameters to send to server
 *	@param toCall : function to call after execution
 *	@param method : method either post/get
 *	
 * 	@return jsonData
 */
var sendToServer = function (url, params, toCall, method) {
    if (method !== null && method === "post")
        var result = jQuery.post(url, params);
    else
        var result = jQuery.get(url, params);

    result.success(function (data) {
        jsonData = jQuery.parseJSON(data);
        if (toCall !== null && toCall !== "" && (typeof toCall === "function"))
            toCall(jsonData);
    });

    result.error(function (data) {
        alert('<span class="red_text">An Error Occured while processing the information : ' + data.status + '</span>');
        //console.log('An Error Occured while processing the information : ' + JSON.stringify(data));
    });
};