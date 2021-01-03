<script src="https://cdn.jsdelivr.net/npm/rsvp@4/dist/rsvp.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/basket.js/0.5.2/basket.min.js"></script>
<script type="text/javascript">
const setupRichpanelMessengerConfiguration = function (properties) {
	if (properties) {
		window.richpanelSettings = properties['data']
	}
}

setupRichpanelMessengerConfiguration(<?php echo wp_json_encode($this->getRichpanelUserData()); ?>)

window.richpanel||(window.richpanel=[]),window.richpanel.q=[],mth=["track","debug","atr"],sk=function(e){return function(){a=Array.prototype.slice.call(arguments);a.unshift(e);window.richpanel.q.push(a)}};for(var i=0;mth.length>i;i++){window.richpanel[mth[i]]=sk(mth[i])}
<?php if (!empty($this->api_key) && $this->accept_tracking) : ?>
basket.require({ url: "https://<?php echo esc_html_e($this->tracking_endpoint_domain); ?>/j/<?php echo esc_html_e($this->api_key); ?>?version=<?php echo esc_html_e($this->integration_version); ?>" });
<?php endif ?>
</script>
