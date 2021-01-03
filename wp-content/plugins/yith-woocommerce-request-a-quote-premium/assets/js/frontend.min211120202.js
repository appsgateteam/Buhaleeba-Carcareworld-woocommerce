jQuery(document).ready(function(h){"use strict";h("body"),h(".add-request-quote-button");var f=h(document).find(".widget_ywraq_list_quote, .widget_ywraq_mini_list_quote"),w="undefined"!=typeof ywraq_frontend&&ywraq_frontend.block_loader,c="undefined"!=typeof ywraq_frontend&&ywraq_frontend.allow_out_of_stock,l="undefined"!=typeof ywraq_frontend&&ywraq_frontend.allow_only_on_out_of_stock,t=(h(".yith-ywraq-item-remove"),h(".shipping_address")),e=h(".woocommerce-billing-fields"),d=document.location.href,a=h(document).find("#yith-ywrq-table-list");c="yes"==c,l="yes"==l,0<a.length&&ywraq_frontend.raq_table_refresh_check&&h.post(d,function(t){if(""!=t){var e=h("<div></div>").html(t).find("#yith-ywrq-table-list");h("#yith-ywrq-table-list").html(e.html()),h(document).trigger("ywraq_table_reloaded")}}),h.fn.yith_ywraq_variations=function(){var t=h(document).find("form.variations_form");if(t.length&&void 0!==t.data("product_id")){var n=t.data("product_id").toString().replace(/[^0-9]/g,""),o=h(".add-to-quote-"+n).find(".yith-ywraq-add-button"),r=o.find("a.add-request-quote-button"),d=h(".yith_ywraq_add_item_product-response-"+n),s=h(".yith_ywraq_add_item_response-"+n),_=h(".yith_ywraq_add_item_browse-list-"+n),e=function(){r.show().addClass("disabled"),o.show().removeClass("hide").removeClass("addedd"),d.hide().removeClass("show"),s.hide().removeClass("show"),_.hide().removeClass("show"),(l&&c||l)&&r.hide()};t.on("found_variation",function(t,e){var a=""+h(".add-to-quote-"+n).attr("data-variation"),i=!0;d.hide().removeClass("show"),c?l&&e.is_in_stock&&(i=!1):e.is_in_stock||(i=!1),i?(r.show().removeClass("disabled"),o.show().removeClass("hide").removeClass("addedd")):(r.hide().addClass("disabled"),o.hide().removeClass("show").removeClass("addedd")),s.hide().removeClass("show"),_.hide().removeClass("show"),-1!==a.indexOf(""+e.variation_id)&&i?(r.hide(),s.show().removeClass("hide"),_.show().removeClass("hide")):i&&(r.show().removeClass("disabled"),o.show().removeClass("hide").removeClass("addedd"),s.hide().removeClass("show"),_.hide().removeClass("show"))}),t.on("reset_data",function(t){e()}),e()}},h(".variations_form").each(function(){h(this).yith_ywraq_variations()}),h(document).on("qv_loader_stop",function(t){h(".variations_form").each(function(){h(this).yith_ywraq_variations()})}),h.fn.yith_ywraq_refresh_button=function(){var t=h('[name|="product_id"]').val();h(".add-to-quote-"+t).find("a.add-request-quote-button").parents(".yith-ywraq-add-to-quote");if(!h('[name|="variation_id"]').length)return!1},h.fn.yith_ywraq_refresh_button();var m=!1;h(document).on("click",".add-request-quote-button",function(t){t.preventDefault();var e=h(this),a=e.closest(".yith-ywraq-add-to-quote"),i="ac",n="";if(e.hasClass("outofstock")?window.alert(ywraq_frontend.i18n_out_of_stock):e.hasClass("disabled")&&window.alert(ywraq_frontend.i18n_choose_a_variation),!(e.hasClass("disabled")||e.hasClass("outofstock")||m)){if(h(".grouped_form").length){var o=0;if(h(".grouped_form input.qty").each(function(){o=Math.floor(h(this).val())+o}),0==o)return void alert(ywraq_frontend.select_quantity)}if(void 0===(n=e.closest(".cart").length?e.closest(".cart"):a.siblings(".cart").first().length?a.siblings(".cart").first():h(".composite_form").length?h(".composite_form"):h(".cart:not(.in_loop)"))[0]||"function"!=typeof n[0].checkValidity||n[0].checkValidity()){if(0<e.closest("ul.products").length)var r="",d=(s=e.closest("li.product").find("a.add_to_cart_button")).data("product_id");else{r=e.closest(".product").find('input[name="add-to-cart"]');var s=e.closest(".product").find('input[name="product_id"]');d=e.data("product_id")||(s.length?s.val():r.val())}var _=void 0===d?e.data("product_id"):d;(i=n.serializefiles()).append("context","frontend"),i.append("action","yith_ywraq_action"),i.append("ywraq_action","add_item"),i.append("product_id",e.data("product_id")),i.append("wp_nonce",e.data("wp_nonce")),i.append("yith-add-to-cart",e.data("product_id"));var c=a.find("input.qty").val();if(0<c&&i.append("quantity",c),0<e.closest(".yith_wc_qof_button_and_price").length){var l=e.closest(".yith_wc_qof_button_and_price").find(".YITH_WC_QOF_Quantity_Cart").val();i.append("quantity",l)}var u=e.closest("li.product").find(".variations_form.in_loop"),y=!!u.length&&u.data("active_variation");return y&&(i.append("variation_id",y),u.find("select").each(function(){i.append(this.name,this.value)})),h(document).trigger("yith_ywraq_action_before"),!("undefined"!=typeof yith_wapo_general&&!yith_wapo_general.do_submit)&&(!("undefined"!=typeof ywcnp_raq&&!ywcnp_raq.do_submit)&&void(m=h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_action"),dataType:"json",data:i,contentType:!1,processData:!1,beforeSend:function(){e.after(' <img src="'+w+'" class="ywraq-loader" >')},complete:function(){e.next().remove()},success:function(t){"true"==t.result||"exists"==t.result?("yes"==ywraq_frontend.go_to_the_list?window.location.href=t.rqa_url:(h(".yith_ywraq_add_item_response-"+_).hide().addClass("hide").html(""),h(".yith_ywraq_add_item_product-response-"+_).show().removeClass("hide").html(t.message),h(".yith_ywraq_add_item_browse-list-"+_).show().removeClass("hide"),e.parent().hide().removeClass("show").addClass("addedd"),h(".add-to-quote-"+_).attr("data-variation",t.variations),f.length&&(f.ywraq_refresh_widget(),f=h(document).find(".widget_ywraq_list_quote, .widget_ywraq_mini_list_quote")),p()),h(document).trigger("yith_wwraq_added_successfully",[t])):"false"==t.result&&(h(".yith_ywraq_add_item_response-"+_).show().removeClass("hide").html(t.message),h(document).trigger("yith_wwraq_error_while_adding")),m=!1}})))}h('<input type="submit">').hide().appendTo(n).click().remove()}}),h.fn.serializefiles=function(){var t=h(this),i=new FormData;h.each(h(t).find("input[type='file']"),function(t,a){h.each(h(a)[0].files,function(t,e){i.append(a.name,e)})});var e=h(t).serializeArray(),a=!1;return h.each(e,function(t,e){"quantity"!=e.name&&!e.name.indexOf("quantity")||(a=!0),"add-to-cart"!=e.name&&i.append(e.name,encodeURIComponent(e.value))}),!1===a&&i.append("quantity",1),i},h.fn.ywraq_refresh_widget=function(){f.each(function(){var e=h(this),t=e.find(".yith-ywraq-list-wrapper"),a=e.find(".yith-ywraq-list"),i=e.find(".yith-ywraq-list-widget-wrapper").data("instance");h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_action"),data:i+"&ywraq_action=refresh_quote_list&action=yith_ywraq_action&context=frontend",beforeSend:function(){a.css("opacity",.5),e.hasClass("widget_ywraq_list_quote")&&t.prepend(' <img src="'+w+'" class="ywraq-loader">')},complete:function(){e.hasClass("widget_ywraq_list_quote")&&t.next().remove(),a.css("opacity",1)},success:function(t){e.hasClass("widget_ywraq_mini_list_quote")?e.find(".yith-ywraq-list-widget-wrapper").html(t.mini):e.find(".yith-ywraq-list-widget-wrapper").html(t.large),h(document).trigger("yith_ywraq_widget_refreshed")}})})},h(document).on("click",".yith-ywraq-item-remove",function(t){t.preventDefault();var e,a=h(this),o=a.data("remove-item"),i=a.parents(".ywraq-wrapper"),r=(h("#yith-ywraq-form"),i.find(".wpcf7-form")),d=i.find(".gform_wrapper"),s=a.data("product_id");e="context=frontend&action=yith_ywraq_action&ywraq_action=remove_item&key="+a.data("remove-item")+"&wp_nonce="+a.data("wp_nonce")+"&product_id="+s,h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_action"),dataType:"json",data:e,beforeSend:function(){a.find(".ajax-loading").css("visibility","visible")},complete:function(){a.siblings(".ajax-loading").css("visibility","hidden")},success:function(t){if(1===t){var e=h("[data-remove-item='"+o+"']").parents(".cart_item");if(e.hasClass("composite-parent")){var a=e.data("composite-id");h("[data-composite-id='"+a+"']").remove()}if(e.hasClass("yith-wapo-parent")){var i=e.find(".product-remove a").data("remove-item");h("[data-wapo_parent_key='"+i+"']").remove()}if(e.hasClass("ywcp_component_item")&&h("tr.ywcp_component_child_item").filter("[data-wcpkey='"+o+"']").remove(),e.hasClass("bundle-parent")){var n=e.data("bundle-key");h("[data-bundle-key='"+n+"']").remove()}e.remove(),0===h(".cart_item").length&&(r.length&&r.remove(),d.length&&d.remove(),h("#yith-ywraq-form, .yith-ywraq-mail-form-wrapper").remove(),h("#yith-ywraq-message").html(ywraq_frontend.no_product_in_list)),f.length&&(f.ywraq_refresh_widget(),f=h(document).find(".widget_ywraq_list_quote, .widget_ywraq_mini_list_quote")),p(),h(document).find('.hide-when-removed[data-product_id="'+s+'"]').hide(),h(document).find('.yith-ywraq-add-button[data-product_id="'+s+'"]').show(),h(document).trigger("yith_wwraq_removed_successfully")}else h(document).trigger("yith_wwraq_error_while_removing")}})});var s;0<h(".wpcf7-submit").closest(".wpcf7").length&&h(document).find(".ywraq-wrapper .wpcf7").each(function(){var t=h(this);t.find('input[name="_wpcf7"]').val()==ywraq_frontend.cform7_id&&(t.on("wpcf7:mailsent",function(){h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_order_action"),dataType:"json",data:{lang:ywraq_frontend.current_lang,action:"yith_ywraq_order_action",current_user_id:ywraq_frontend.current_user_id,context:"frontend",ywraq_order_action:"mail_sent_order_created"},success:function(t){""!=t.rqa_url&&(window.location.href=t.rqa_url)}})}),document.addEventListener("wpcf7mailsent",function(t){window.location.href=ywraq_frontend.rqa_url},!1))}),h("#yith-ywrq-table-list").on("change",".qty",function(){h(this).val()<=0&&h(this).val(1)}),h(document).bind("gform_confirmation_loaded",function(t,e){ywraq_frontend.gf_id==e&&(window.location.href=ywraq_frontend.rqa_url)}),ywraq_frontend.auto_update_cart_on_quantity_change&&h(document).on("click, change",".product-quantity input",function(t){void 0!==s&&s.abort();var e=h(this);if(void 0===(i=e.attr("name"))){var a=e.closest(".product-quantity").find(".input-text.qty"),i=a.attr("name"),n=a.val(),o=i.match(/[^[\]]+(?=])/g);e.hasClass("plus")&&n++,e.hasClass("minus")&&n--;var r="context=frontend&action=yith_ywraq_action&ywraq_action=update_item_quantity&quantity="+n+"&key="+o[0]}else r="context=frontend&action=yith_ywraq_action&ywraq_action=update_item_quantity&quantity="+(n=e.val())+"&key="+(o=i.match(/[^[\]]+(?=])/g))[0];s=h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_action"),dataType:"json",data:r,success:function(t){h.post(d,function(t){if(""!=t){var e=h("<div></div>").html(t).find("#yith-ywrq-table-list");h("#yith-ywrq-table-list").html(e.html()),h(document).trigger("ywraq_table_reloaded"),f.length&&(f.ywraq_refresh_widget(),f=h(document).find(".widget_ywraq_list_quote, .widget_ywraq_mini_list_quote"))}})}})});function p(){h(document).find(".ywraq_number_items").each(function(){var e=h(this),t=e.data("show_url"),a=e.data("item_name"),i=e.data("item_plural_name");h.ajax({type:"POST",url:ywraq_frontend.ajaxurl.toString().replace("%%endpoint%%","yith_ywraq_action"),data:"ywraq_action=refresh_number_items&action=yith_ywraq_action&context=frontend&item_name="+a+"&item_plural_name="+i+"&show_url="+t,success:function(t){e.replaceWith(t),h(document).trigger("ywraq_number_items_refreshed")}})})}0<t.length&&1==ywraq_frontend.lock_shipping&&(t.find("input").attr("readonly","readonly"),t.find("select").attr("readonly","readonly"),h(".woocommerce-checkout #shipping_country_field").css("pointer-events","none"),h(".woocommerce-checkout #shipping_state_field").css("pointer-events","none")),0<e.length&&1==ywraq_frontend.lock_billing&&(e.find("input").attr("readonly","readonly"),e.find("select").attr("readonly","readonly"),h(".woocommerce-checkout #billing_country_field").css("pointer-events","none"),h(".woocommerce-checkout #billing_state_field").css("pointer-events","none")),f.ywraq_refresh_widget(),p(),h(document).on("click","#ywraq_checkout_quote",function(t){h(document).find('input[name="payment_method"]').val("yith-request-a-quote"),h("#ywraq_checkout_quote").val(!0)})});