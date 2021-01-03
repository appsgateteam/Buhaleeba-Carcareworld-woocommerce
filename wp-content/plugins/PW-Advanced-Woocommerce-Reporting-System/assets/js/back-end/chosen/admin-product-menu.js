function getEnhancedSelectFormatString() {
    return {
        'language': {
            errorLoading: function() {
                // Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
                return wc_enhanced_select_params.i18n_searching;
            },
            inputTooLong: function( args ) {
                var overChars = args.input.length - args.maximum;

                if ( 1 === overChars ) {
                    return wc_enhanced_select_params.i18n_input_too_long_1;
                }

                return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
            },
            inputTooShort: function( args ) {
                var remainingChars = args.minimum - args.input.length;

                if ( 1 === remainingChars ) {
                    return wc_enhanced_select_params.i18n_input_too_short_1;
                }

                return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
            },
            loadingMore: function() {
                return wc_enhanced_select_params.i18n_load_more;
            },
            maximumSelected: function( args ) {
                if ( args.maximum === 1 ) {
                    return wc_enhanced_select_params.i18n_selection_too_long_1;
                }

                return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
            },
            noResults: function() {
                return wc_enhanced_select_params.i18n_no_matches;
            },
            searching: function() {
                return wc_enhanced_select_params.i18n_searching;
            }
        }
    };
}
jQuery(document).ready(function () {

    jQuery(".chosen-select").chosen();

});