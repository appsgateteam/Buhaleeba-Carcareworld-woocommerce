<script type="text/html" id="tmpl-wps-store-details">
	<div class="wps-store-details">

		<# if ( data.description ) { #>
		<span><b><?= __('Pick up Time', WPS_TEXTDOMAIN) ?></b><span class="colon">:</span></span> {{ data.description.value }} <br><br>
		<# } #>
		<# if ( data.city ) { #>
		<span><?= __('City', WPS_TEXTDOMAIN) ?><span class="colon">:</span></span> {{ data.city.value }} <br>
		<# } #>
		<# if ( data.phone ) { #>
		<span><?= __('Phone', WPS_TEXTDOMAIN) ?><span class="colon">:</span></span> {{ data.phone.value }} <br>
		<# } #>
		<# if ( data.address ) { #>
		<span><?= __('Address', WPS_TEXTDOMAIN) ?><span class="colon">:</span></span> {{ data.address.value }} <br>
		<# } #>
		<# if ( data.map ) { #>
		<iframe src="{{ data.map.value }}" frameborder="0"></iframe>
		<# } #>
	</div>
</script>