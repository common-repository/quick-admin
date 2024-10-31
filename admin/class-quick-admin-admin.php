<?php
require_once( ( plugin_dir_path(__FILE__) ) . 'includes/common.php');
/**
 * Quick Admin.
 *
 * @package   Quick_Admin_Admin
 * @author    Nilambar Sharma <nilambar@outlook.com>
 * @license   GPL-2.0+
 * @link      http://nilambar.net
 * @copyright 2014 Nilambar Sharma
 */
/**
 * Plugin class.
 *
 * @package Quick_Admin_Admin
 * @author  Nilambar Sharma <nilambar@outlook.com>
 */
class Quick_Admin_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	protected $options = array();

	protected $plugin_pages = array();

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Quick_Admin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$this->options = $plugin->get_options_array();

		$this->plugin_pages = array(
			'quick-admin_page_quick-admin-settings',
			'toplevel_page_quick-admin-links',
			);

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 */
		add_action( 'admin_init', array( $this, 'plugin_register_settings' ) );
		add_action( 'admin_init', array( $this, 'quick_admin_link_delete_action' ) );

    add_action( 'wp_ajax_nopriv_quick_admin_save_link', array( $this, 'qa_save_link' ) );
    add_action( 'wp_ajax_quick_admin_save_link', array( $this, 'qa_save_link' ) );

    add_action( 'wp_ajax_nopriv_quick_admin_get_link_list', array( $this, 'qa_get_link_list' ) );
    add_action( 'wp_ajax_quick_admin_get_link_list', array( $this, 'qa_get_link_list' ) );

    add_action( 'wp_ajax_nopriv_quick_admin_delete_link', array( $this, 'qa_delete_link' ) );
    add_action( 'wp_ajax_quick_admin_delete_link', array( $this, 'qa_delete_link' ) );

		if ( $this->options['qa_field_enable_quick_admin'] ) {
			// Only if Enable Quick Admin option is true

			add_action( 'wp_dashboard_setup', array( $this, 'qa_add_dashboard_widget' ) );

			add_action( 'admin_head', array( $this, 'qa_add_style_in_admin_head' ) );

			if ( $this->options['qa_field_enable_quick_links_in_admin_bar'] ) {
				add_action( 'admin_bar_menu' , array($this, 'qa_admin_add_admin_bar_links' ) , 1000 ) ;
			}
    }


	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if (in_array($screen->id, $this->plugin_pages)) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Quick_Admin::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

    wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		wp_enqueue_script('thickbox',null,array('jquery'));

		$screen = get_current_screen();

		if (in_array($screen->id, $this->plugin_pages)) {
			wp_enqueue_script( $this->plugin_slug . '-angular-script', plugins_url( 'assets/js/angular.min.js', __FILE__ ), array( 'jquery' ), Quick_Admin::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-qaapp-script', plugins_url( 'assets/js/qaapp.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-angular-script' ), Quick_Admin::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Quick_Admin::VERSION );
			$localized_arr = array(
				'admin_url' => admin_url(),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'plugin_url' => QUICK_ADMIN_URL,
				);
			wp_localize_script( $this->plugin_slug . '-qaapp-script', 'MyAjax', $localized_arr );
		}

	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Quick Admin', $this->plugin_slug ),
			__( 'Quick Admin', $this->plugin_slug ),
			'manage_options',
			'quick-admin-links',
			array( $this, 'display_quick_links_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'quick-admin-links',
			__( 'Quick Admin', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			'quick-admin-settings',
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Render the links page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_quick_links_page() {
		include_once( 'views/links.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'manage' => '<a href="' . admin_url( 'admin.php?page=quick-admin-links' ) . '">' . __( 'Manage links', $this->plugin_slug ) . '</a>'
			),
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=quick-admin-settings' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
   * Register plugin settings
	 *
	 * @since    1.0.0
	 */
  public function plugin_register_settings()
  {

    register_setting('qa-plugin-options-group', 'qa_plugin_options', array( $this, 'plugin_options_validate') );

    ////

		add_settings_section('general_settings', __( 'Quick Admin Settings', 'quick-admin' ) , array($this, 'plugin_section_general_text_callback'), 'quick-admin-general');

		add_settings_field('qa_field_enable_quick_admin', __( 'Enable Quick Admin', 'quick-admin' ), array($this, 'qa_field_enable_quick_admin_callback'), 'quick-admin-general', 'general_settings');

		add_settings_field('qa_field_enable_quick_links_in_admin_bar', __( 'Enable Quick Links in Admin bar', 'quick-admin' ), array($this, 'qa_field_enable_quick_links_in_admin_bar_callback'), 'quick-admin-general', 'general_settings');

		add_settings_field('qa_field_enable_quick_links_for_roles', __( 'Enable Quick Links for', 'quick-admin' ), array($this, 'qa_field_enable_quick_links_for_roles_callback'), 'quick-admin-general', 'general_settings');

  }

	/**
	 * Validate plugin setting options.
	 *
	 * @since    1.0.0
	 */
  function plugin_options_validate($input) {

		$input['qa_field_enable_quick_admin']              = ( isset( $input['qa_field_enable_quick_admin'] ) ) ? 1 : 0 ;
		$input['qa_field_enable_quick_links_in_admin_bar'] = ( isset( $input['qa_field_enable_quick_links_in_admin_bar'] ) ) ? 1 : 0 ;

  	return $input;
  }
  function plugin_section_general_text_callback() {
  	return;
	}

	/**
	 * Field Callback for qa_field_enable_quick_admin.
	 *
	 * @since    1.0.0
	 */
	function qa_field_enable_quick_admin_callback() {
		?>
		<input type="checkbox" name="qa_plugin_options[qa_field_enable_quick_admin]" value="1"
		<?php checked(isset($this->options['qa_field_enable_quick_admin']) && 1 == $this->options['qa_field_enable_quick_admin']); ?> />&nbsp;<?php _e("Enable",  'quick-admin' ); ?>
		<?php
	}

	/**
	 * Field Callback for qa_field_enable_quick_links_in_admin_bar.
	 *
	 * @since    1.0.0
	 */
	function qa_field_enable_quick_links_in_admin_bar_callback() {
		?>
		<input type="checkbox" name="qa_plugin_options[qa_field_enable_quick_links_in_admin_bar]" value="1"
		<?php checked(isset($this->options['qa_field_enable_quick_links_in_admin_bar']) && 1 == $this->options['qa_field_enable_quick_links_in_admin_bar']); ?> />&nbsp;<?php _e("Enable",  'quick-admin' ); ?>
		<?php
	}

	/**
	 * Field Callback for qa_field_enable_quick_links_for_roles.
	 *
	 * @since    1.0.0
	 */
	function qa_field_enable_quick_links_for_roles_callback() {
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		?>
		<?php foreach ($all_roles as $key => $role): ?>
			<p>
				<input type="checkbox" name="qa_plugin_options[qa_field_enable_quick_links_for_roles][<?php echo $key ?>]" value="1"
				<?php checked( isset($this -> options['qa_field_enable_quick_links_for_roles'][$key]) && 1 == $this -> options['qa_field_enable_quick_links_for_roles'][$key]); ?> />&nbsp;<?php echo esc_attr($role['name']); ?>
			</p>
		<?php endforeach ?>

		<?php
	}

	/**
	 * Action function for deleting quick link.
	 *
	 * @since    1.0.0
	 */
	function quick_admin_link_delete_action(){

		if ( isset( $_REQUEST['qamode'] ) && 'dellink' == $_REQUEST['qamode'] ) {
			if ( isset( $_REQUEST['qid'] ) && intval( $_REQUEST['qid'] ) > 0 ) {
				wp_delete_post( intval( $_REQUEST['qid'] ), true );
				add_action('admin_notices', array( $this, 'qa_add_deleted_admin_notice' ) );
			}
		}

	}

	/**
	 * Add quick links in admin bar.
	 *
	 * @since    1.0.0
	 */
	function qa_admin_add_admin_bar_links(){

		global $wp_admin_bar;
    if( !is_admin_bar_showing() ) return;

    $current_user = wp_get_current_user();
    $cur_user_roles = $current_user->roles;
    $available_roles = array_keys($this->options['qa_field_enable_quick_links_for_roles']);

    if ( ! array_intersect( $cur_user_roles, $available_roles ) ){
    	// not available for current user
    	return;
    }

    $args = array();
    $links_arr = $this->get_quick_links($args);

    $links_arr = apply_filters('qa_filter_links', $links_arr );

    // Add Parent Menu
    $argsParent=array(
        'id' => 'qa-main-link',
        'title' => 'Quick Admin',
        'href' => false
    );
    $wp_admin_bar->add_menu($argsParent);

    if ( ! empty( $links_arr ) ) {
    	foreach ($links_arr as $key => $link) {

    		$meta = array();
    		if ( isset( $link['open_new'] ) && 1 == $link['open_new'] ) {
    			$meta['target'] = '_blank';
    		}

		    $args=array(
		        'id' => $link['id'],
		        'parent' => 'qa-main-link',
		        'title' => $link['title'],
		        'href' => esc_url( $link['href'] ),
		        'meta' => $meta,
		    );
		    $wp_admin_bar->add_menu( $args );

    	} // end foreach
    } // end if

	}

	/**
	 * Add dashboard widget.
	 *
	 * @since    1.0.0
	 */
	public function qa_add_dashboard_widget() {

    $current_user = wp_get_current_user();
    $cur_user_roles = $current_user->roles;
    $available_roles = array_keys($this->options['qa_field_enable_quick_links_for_roles']);

    if ( ! array_intersect( $cur_user_roles, $available_roles ) ){
    	// not available for current user
    	return;
    }

		wp_add_dashboard_widget(
			'qa_links_widget',
			__( 'Quick Admin', 'quick-admin' ),
			array( $this, 'qa_links_widget_callback' )
    );
	}

	/**
	 * Add styles in admin head.
	 *
	 * @since    1.0.0
	 */
	function qa_add_style_in_admin_head(){

		?>
		<style>
		.quick-admin-dashboard-links-list{
			list-style-type: none;
			list-style-position: inside;
		}
		.quick-admin-dashboard-links-list .quick-admin-dashboard-link-tag{
			font-size: 14px;
		}
		.quick-admin-dashboard-links-list .dashicons{
			color: #0074a2;
		}
		</style>
		<?php

	}

	/**
	 * Add dashboard widget callback function.
	 *
	 * @since    1.0.0
	 */
	public function qa_links_widget_callback() {

    $args = array();
    $links_arr = $this->get_quick_links($args);

    $links_arr = apply_filters('qa_filter_links', $links_arr );

    if ( ! empty( $links_arr )) {
    	echo '<ul class="quick-admin-dashboard-links-list">';
	    foreach ($links_arr as $key => $link) {

	    	$target = ( isset( $link['open_new'] ) && 1 == $link['open_new'] ) ? ' target="_blank" ' : '' ;

	    	echo '<li class="quick-admin-dashboard-link-item">';
	    	echo '<div class="dashicons dashicons-arrow-right"></div><a class="quick-admin-dashboard-link-tag" href="' . esc_url( $link['href'] ) . '" title="' . esc_attr($link['title']) . '" '. $target.' >';
	    	echo esc_attr($link['title']);
	    	echo '</a>';
	    	echo '</li>';

	    } // end foreach
    	echo '</ul>';
    } // end if
    else{
    	echo __( 'No links added yet.', 'quick-admin' ). '&nbsp;';
    	$manage_url = add_query_arg( array( 'page' => 'quick-admin-links' ), admin_url('admin.php') );
    	$manage_link = sprintf('%shere%s',
    		'<a href="' . $manage_url . '">',
    		'</a>'
    		);

    	echo sprintf( __('Click %s to manage Quick Links.','quick-admin'), $manage_link );

    }

	}

	/**
	 * Save form.
	 *
	 * @since    1.0.0
	 */
	public function qa_save_link() {

		$output = array();
		$output['success'] = 1;

		$qa_link_id = intval( $_REQUEST['qa_link_id'] ) ;
		$qa_title = sanitize_text_field( $_REQUEST['qa_title'] ) ;
		$qa_url = esc_url( $_REQUEST['qa_url'] ) ;
		$qa_open_new = esc_attr( $_REQUEST['qa_open_new'] ) ;
		$qa_form_action = esc_attr( $_REQUEST['qa_form_action'] ) ;
		$qa_menu_order = absint(esc_attr( $_REQUEST['qa_menu_order'] ) );

		if ( empty($qa_title) || empty($qa_url) ) {
			$output['success'] = 0;
			wp_send_json( $output );
		}

		$form_mode = ( isset( $qa_link_id ) && -1 == $qa_link_id ) ? 'add' : 'edit' ;
		if ('edit' == $form_mode && intval($_REQUEST['qa_link_id']) > 0 ) {
		  // Update mode
			$data = array(
				'ID'           => $qa_link_id,
				'post_title'   => $qa_title,
				'post_content' => $qa_url,
				'menu_order'   => $qa_menu_order,
				);
			wp_update_post( $data );
			if ($qa_open_new) {
				update_post_meta($qa_link_id, '_qa_link_open_new', $qa_open_new);
			}
			else{
				delete_post_meta($qa_link_id, '_qa_link_open_new');
			}
			$output['id']   = $qa_link_id;
			$output['data'] = $data;
			wp_send_json( $output );

		}
		else{
			// Insert mode
			$data = array(
				'post_title'   => $qa_title,
				'post_content' => $qa_url,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'qa_link',
				'menu_order'   => $qa_menu_order,
			);
			$qa_link_id = wp_insert_post( $data );
			if ($qa_open_new) {
				update_post_meta($qa_link_id, '_qa_link_open_new', $qa_open_new);
			}

			$output['id']   = $qa_link_id;
			$output['data'] = $data;
			wp_send_json( $output );

		}
    exit();

	}

	/**
	 * Fetch quick links.
	 *
	 * @since    1.0.0
	 */
	protected function get_quick_links( $params = array() ){
		$links_arr = array();

		$defaults = array(
			'method' => 'array',
			);
		$params = wp_parse_args( $params, $defaults );

		$args = array(
		  'post_type'      => 'qa_link',
		  'orderby'        => 'menu_order',
		  'posts_per_page' => -1,
		  );
		$all_links = get_posts( $args );
		if ( empty( $all_links ) ) {
			return;
		}
		$cnt=0;
		foreach ($all_links as $key => $link) {
			$links_arr[$cnt]['id']         = 'qa-link-'. $link->ID;
			$links_arr[$cnt]['link_id']    = $link->ID;
			$links_arr[$cnt]['title']      = $link->post_title;
			$links_arr[$cnt]['menu_order'] = $link->menu_order;
			$links_arr[$cnt]['href']       = strip_tags($link->post_content);
			$qa_link_open_new              = get_post_meta($link->ID,'_qa_link_open_new', true);
			$links_arr[$cnt]['open_new']   = ( 1 == $qa_link_open_new ) ? 1 : 0 ;
			$cnt++;
		}

		if ( 'array' == $params['method'] ) {
			return $links_arr;
		}
		else if ( 'json' == $params['method'] ) {
			$output            = array();
			$output['success'] = 1;
			$output['data']    = $links_arr;
			wp_send_json( $output );
		}



	}

	/**
	 * Admin notice for link delete.
	 *
	 * @since    1.0.0
	 */
	function qa_add_deleted_admin_notice(){
		?>
    <div class="updated">
        <p><?php _e( 'Link deleted successfully', 'quick-admin' ); ?> !</p>
    </div>
    <?php

	}


	function qa_get_link_list(){

		return $this->get_quick_links(array('method'=>'json'));
		die;

	}
	function qa_delete_link(){

		$link_id = $_REQUEST['link_id'];

		$output = array();
		$output['success'] = 1;

		if ( intval( $link_id ) < 0 ) {
			$output['success'] = 0;
			wp_send_json( $output );
		}

		$t = wp_delete_post($link_id);
		$output['link_id'] = $link_id;
		wp_send_json( $output );

	}




}
