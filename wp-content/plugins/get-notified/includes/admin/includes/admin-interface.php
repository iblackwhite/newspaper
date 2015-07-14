<?php 
/**
 * Renders a page to display options.
 */
 
function gn_admin_interface( $active_tab = '' ) {
	
	
	$active_tab = $active_tab ? $active_tab : 'gn_general_options';
	$active_tab_class = 'nav-tab-active';
	
?>
	
	<div class="wrap phpbaba_settings_page">
		<div id="gn_nonce" style="display: none;"><?php echo wp_create_nonce('gn_settings_nonce'); ?></div>
		<h2>Get Notified - Viral Facebook Notifications</h2>
		
		<?php settings_errors(); ?>

		<h2 class="nav-tab-wrapper">
		
			<?php $gn_admin_pages = gn_admin_pages(); ?>
				
			<?php foreach($gn_admin_pages as $k => $tab){ ?>
				
				<a href="?page=<?php echo $tab['slug']; ?>" class="nav-tab <?php echo $active_tab == $k ? $active_tab_class : ''; ?>"><?php echo $tab['name']; ?></a>
				
			<?php } ?>
		
		</h2>
		
		<div id="poststuff">

			<div class="metabox-holder columns-2" id="post-body">

				<div class="postbox-container" id="postbox-container-2">

					<div class="meta-box-sortables ui-sortable" id="advanced-sortables">

						<div class="postbox">
						
							<h3 class="hndle"><span>&nbsp;</span></h3>
							
							<div class="inside">
								
								<?php if( !gn_is_compatible_server() ){ ?>
									
									<div class="phpbaba_error_box">
										
										<p><b><?php _e( 'Notice:', 'get-notified' ); ?></b> <?php _e( 'Get Notified requires PHP 5.4 or greater.', 'get-notified' ); ?></p>
									
									</div>
									
								<?php }else{ ?>
									
									<form method="post" action="options.php">
									
										<?php 

											settings_fields( $active_tab );
											do_settings_sections( $active_tab );
											
										?>
										
										<table class="form-table">
										
											<tbody>

												<tr style="border-top: 1px dashed #DDDDDD;">
													<th style="padding: 15px 10px 0;"></th>
													<td style="text-align: right; padding: 15px 0 0 10px;"><input type="submit" value="<?php _e( 'Save Changes', 'get-notified' ); ?>" class="button button-primary" id="submit" name="submit"></td>
												</tr>
												
											</tbody>
											
										</table>
										
									</form>
								
								<?php } ?>
								
							</div>
							
						</div>

					</div>

				</div>
				
				<div class="postbox-container" id="postbox-container-1">
				
					<?php do_action( 'gn_settings_page_right_side', $active_tab ); ?>
					<?php do_action( 'phpbaba_settings_page_right_side', $active_tab ); ?>
					
				</div>
				
			</div><!-- /post-body -->

			<br class="clear">

		</div><!-- /poststuff -->
		
	</div><!-- /.wrap -->
<?php

}

?>