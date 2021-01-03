jQuery(document).ready(function($){
    $('.colorPicker').wpColorPicker();

    $(document).ajaxComplete(function (event, xhr, settings) {
        var match;
        if (typeof settings.data === 'string'
        && /action=get-post-thumbnail-html/.test(settings.data)
        && xhr.responseJSON && typeof xhr.responseJSON.data === 'string') {
            match = /<img[^>]+src="([^"]+)"/.exec(xhr.responseJSON.data);
            if (match !== null) {
                $(".featured-image-woobadges").css("background-image","url("+match[1]+")");
            }
        }
    });


    $(document).on("input", "input[name='woobadges_opacity']", function(){
        $(".woobadges_opacity_value").html($(this).val());
    });

    $(document).on("input", "input[name='woobadges_zoomSingleProduct']", function(){
        $(".woobadges_zoom_value").html($(this).val());
    });
});