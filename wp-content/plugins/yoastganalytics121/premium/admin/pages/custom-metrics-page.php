<div id="custommetrics" class="gatab">
<?php
echo '<h2>' . __( 'Custom metrics', 'ga-premium' ) . '</h2>';

// Showing checkbox for enabling/disabling adsense tracking
echo Yoast_GA_Admin_Form::input(
	'checkbox',
	__( 'Google metrics', 'yoast-google-analytics-premium', 'ga-premium' ),
	'track_adsense',
	null,
	__( 'This requires integration of your Analytics and AdSense account, for help, <a href="https://support.google.com/adsense/answer/94743?hl=en&ref_topic=23415" target="_blank">look here</a>.', 'yoast-google-analytics-premium', 'ga-premium' )
);

?>
</div>