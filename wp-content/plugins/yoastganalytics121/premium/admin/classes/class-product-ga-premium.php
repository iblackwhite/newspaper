<?php

/**
 * Class Yoast_Product_GA_Premium
 *
 * @since 1.0
 */
class Yoast_Product_GA_Premium extends Yoast_Product {

	/**
	 * Contains the license manager object
	 *
	 * @var object Yoast_Plugin_License_Manager
	 */
	protected $license_manager;


	public function __construct() {

		parent::__construct(
			'https://yoast.com',
			'Google Analytics by Yoast Premium',
			plugin_basename( GAWP_FILE ),
			GA_YOAST_PREMIUM_VERSION,
			'https://yoast.com/wordpress/plugins/google-analytics/',
			'admin.php?page=yst_ga_extensions#top#licenses',
			'yoast-google-analytics-premium',
			'Yoast'
		);

		$this->setup_license_manager();

	}


	/**
	 * Setting up the license manager
	 *
	 * @since 3.0
	 */
	protected function setup_license_manager() {

		$license_manager = new Yoast_Plugin_License_Manager( $this );
		$license_manager->setup_hooks();

		add_filter( 'yst_ga_extension_status', array( $this, 'filter_extension_is_active' ), 10, 1 );
		add_action( 'yst_ga_show_license_form', array( $this, 'action_show_license_form' ) );

		$this->license_manager = $license_manager;
	}

	/**
	 * If extension is active, it should be check if its license is valid
	 *
	 * @since 3.0
	 *
	 * @param $extensions
	 *
	 * @return mixed
	 */
	public function filter_extension_is_active( $extensions ) {

		if ( $this->license_manager->license_is_valid() ) {
			$extensions['ga_premium']->status = 'active';
		} else {
			$extensions['ga_premium']->status = 'inactive';
		}

		return $extensions;
	}

	/**
	 * This method will echo the license form for the extension
	 *
	 * @since 3.0
	 */
	public function action_show_license_form() {
		echo $this->license_manager->show_license_form( false );
	}

}