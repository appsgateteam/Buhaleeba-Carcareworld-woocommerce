jQuery(document).ready(function($){
	
    $('.acau-color-field').wpColorPicker();
	
	$('#acau_custom_spinning_wheel').change(function() {		
		if (this.checked) {
			$('#acau_spinning_wheel_color').parents().eq(4).show();	
		} else {
			$('#acau_spinning_wheel_color').parents().eq(4).hide();			
		}
	});
		
});