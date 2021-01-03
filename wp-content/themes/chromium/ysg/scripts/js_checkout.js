
var base_url;
var bcurrent_cities = [];
var scurrent_cities = [];

jQuery(function ($) {
    base_url = ysg_base_url;
    //ready
    $(document).ready(function () {
    });

    //loaded
    $(window).load(function () {
        if ($('.js-ysg_selected_city').has('optgroup').size() <= 0) {
            doChangeCities({
                cstype: "billing_country"
            })
        }

        if ($('#billing_country').size() > 0 && $('#billing_country').val() != "") {
            var params = {};
            params['country_code'] = $('#billing_country').val();
            params['action'] = "get_cities_country";
            params['cstype'] = "billing_country";
            getCitiesAreasFromServer(params, doUpdateCurrentCities);
        }

        if ($('#shipping_country').size() > 0 && $('#shipping_country').val() != "") {
            var params = {};
            params['country_code'] = $('#shipping_country').val();
            params['action'] = "get_cities_country";
            params['cstype'] = "shipping_country";
            getCitiesAreasFromServer(params, doUpdateCurrentCities);
        }

        if ($('.js-ysg_selected_scity').has('optgroup').size() <= 0) {
            doChangeCities({
                cstype: "shipping_country"
            })
        }

        //
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

    //when country is changed
    $('body').on('change', '#shipping_country, #billing_country', function () {
        var params = {};
        params['country_code'] = $(this).val();
        params['action'] = "get_cities_country";
        params['cstype'] = $(this).attr('id');

        getCitiesAreasFromServer(params, returnCitiesList);
    });

    //when city is changed  -- show delivery date
    $('body').on('change', '#billing_city, #shipping_city', function () {
        setDeliveryDate($(this));
    });

    /**
     * 
     * @param {object} params 
     * @param {string} $returnFunction 
     */
    var getCitiesAreasFromServer = function (params, $returnFunction) {
        var submit_link = base_url + 'rnakdklake/';;
        sendToServer(submit_link, params, $returnFunction, 'get');
    }
    /**
    * @param {json} data
    * @returns boolean
    */
    var returnCitiesList = function (data) {
        if (data === "null" || typeof data === 'undefined' || data === null || data === "") {
            return;
        }

        if (data.error) {
            alert('<span class="error_message">' + data.error + '</span>');
            return;
        }

        if (data.success !== "") {
            doChangeCities(data);
            doUpdateCurrentCities(data);
        }
    };


    function doChangeCities(data) {
        if (typeof (data.cstype) != "undefined" && data.cstype == "shipping_country") {
            $class_m = ".js-ysg_selected_scity";
            $class_inh = ".js_shipping_city2";
            $classk = "#shipping_city_field";
        } else {
            $class_inh = ".js_billing_city2";
            $class_m = ".js-ysg_selected_city";
            $classk = "#billing_city_field";
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

    /**
     * @function doUpdateCurrentCities
     * updates the current billing / shipping cities
     * @param {array} cities 
     */
    var doUpdateCurrentCities = function (data) {
        if (data.cstype == "shipping_country") {
            scurrent_cities = data.all_areas;
            setDeliveryDate($('#shipping_city'));
        } else {
            bcurrent_cities = data.all_areas;
            setDeliveryDate($('#billing_city'));
        }
    }

    /**
     * @function setDeliveryDate
     * sets the delivery date based on element
     */
    var setDeliveryDate = function ($athis) {
        all_areas = [];
        $id = $athis.val();
        $('.js_delivery_date_text').remove();

        if ($athis.is('#billing_city')) {
            all_areas = bcurrent_cities;
            loopAreas(all_areas, $athis);
        } else {
            all_areas = scurrent_cities;
            loopAreas(all_areas, $athis);
        }
    }

    /**
     * @function loopAreas
     * loop through the areas and shows delivery date
     * @param {array} all_areas 
     */
    var loopAreas = function (all_areas, $athis) {
        $(all_areas).each(function (i) {
            if (all_areas[i].term_id == $id) {
                if (all_areas[i].delivery_day != "") {
                    html = "<label>Delivery Date : </label>";
                    html += "<span>" + all_areas[i].delivery_day + "</span>";
                    $athis.parent().append('<div class="js_delivery_date_text delivery_date">' + html + '</div>');
                    return false;
                } else {
                    return false;
                }
            }
        })
    }
});
