var base_url;

jQuery(function ($) {
    base_url = ysg_base_url;
    //ready
    $(document).ready(function () {
        $('body').on('click', '.js_show_hide_filters', function () {
            $('#sidebar-shop').slideToggle(function () {
                if ($('#sidebar-shop').is(':hidden')) {
                    $('.js_show_hide_filters span').html('Show filters')
                } else {
                    //show
                    $('.js_show_hide_filters span').html('hide filters')
                }
            });
        });
    });


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