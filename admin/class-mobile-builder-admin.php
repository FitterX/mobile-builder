<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rnlab.io
 * @since      1.0.0
 *
 * @package    Mobile_Builder
 * @subpackage Mobile_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mobile_Builder
 * @subpackage Mobile_Builder/admin
 * @author     Ngoc Dang <ngocdt@rnlab.io>
 */
class Mobile_Builder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The table name save in database.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $table_name The table name save in database.
	 */
	private $table_name;

	/**
	 * The api endpoint.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $namespace The api endpoint.
	 */
	private $namespace;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->table_name  = $wpdb->prefix . "mobile_builder_templates";
		$this->namespace   = $plugin_name . '/v' . intval( $version );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, 'https://cdnjs.rnlab.io/' . MOBILE_BUILDER_JS_VERSION . '/static/css/main.css', array(), MOBILE_BUILDER_JS_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, 'https://cdnjs.rnlab.io/' . MOBILE_BUILDER_JS_VERSION . '/static/js/main.js', array(
			'jquery',
			'media-upload'
		), MOBILE_BUILDER_JS_VERSION, true );
		wp_localize_script( $this->plugin_name, 'wp_rnlab_configs', array(
				'api_nonce' => wp_create_nonce( 'wp_rest' ),
				'api_url'   => rest_url( '' ),
			)
		);
	}

	/**
	 * Registers a REST API route
	 *
	 * @since 1.0.0
	 */
	public function add_api_routes() {
		$templates_endpoint = 'templates';

		register_rest_route( $this->namespace, $templates_endpoint, array(
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_templates' ),
			),
		) );

		register_rest_route( $this->namespace, $templates_endpoint, array(
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_templates' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );

		register_rest_route( $this->namespace, $templates_endpoint, array(
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_templates' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );

		register_rest_route( $this->namespace, $templates_endpoint, array(
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_templates' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
				'args'                => array(),
			),
		) );
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	public function admin_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_templates( $request ) {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM {$this->table_name}", OBJECT );

		return new WP_REST_Response( $results, 200 );
	}

	/**
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function add_templates( $request ) {
		global $wpdb;

		$data    = $request->get_param( 'data' );
		$results = $wpdb->insert(
			$this->table_name,
			$data
		);

		return new WP_REST_Response( $results, 200 );
	}

	/**
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function update_templates( $request ) {
		global $wpdb;

		$data  = $request->get_param( 'data' );
		$where = $request->get_param( 'where' );

		$results = $wpdb->update(
			$this->table_name,
			$data,
			$where
		);

		return new WP_REST_Response( $results, 200 );
	}

	/**
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function delete_templates( $request ) {
		global $wpdb;

		$where   = $request->get_param( 'where' );
		$results = $wpdb->delete(
			$this->table_name,
			$where
		);

		return new WP_REST_Response( $results, 200 );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the sidebar.
		 */

		$hook_suffix = add_menu_page(
			__( 'Mobile Builder', $this->plugin_name ),
			__( 'Mobile Builder', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-excerpt-view'
		);

		// Load enqueue styles and script
		add_action( "admin_print_styles-$hook_suffix", array( $this, 'enqueue_styles' ) );
		add_action( "admin_print_scripts-$hook_suffix", array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		?>
        <div id="wp-rnlab"></div><?php
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
			),
			$links
		);
	}


}
