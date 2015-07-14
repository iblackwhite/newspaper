<?php
/**
Sanitize
*/

function gn_sanitize_html( $input ) {
	
	return wp_kses_post( $input );

}

?>