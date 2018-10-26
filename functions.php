<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	});

	add_filter('template_include', function( $template ) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});

	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class GutterGlove extends Timber\Site {

	const VERSION = '0.1.0';

	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'cmb2_admin_init', array( $this, 'testimonial_meta' ) );
		add_action( 'cmb2_admin_init', array( $this, 'theme_options' ) );
		add_filter( 'timber_context', array( $this, 'template_context' ) );
		add_filter( 'timber_get_preview', array( $this, 'post_preview' ) );

		// auto increment
		add_action( 'admin_init', array( $this, 'increment_events' ) );
		add_action( 'gg_daily', array( $this, 'increment_daily' ) );
		add_action( 'gg_twicedaily', array( $this, 'increment_twicedaily' ) );
		add_action( 'gg_hourly', array( $this, 'increment_hourly' ) );

		parent::__construct();
	}

	/** This is where you can register custom post types. */
	public function register_post_types() {
		register_post_type( 'testimonial', array(
			'label' => 'Testimonials',
		    'menu_icon' => 'dashicons-star-filled',
		    'show_ui'   => true,
		    'supports'  => array( 'title', 'editor' ),
		) );
	}

	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	public function scripts() {
		wp_enqueue_script( 'gutterglove-vender', get_stylesheet_directory_uri() . '/dist/scripts/vendor.js', array(), self::VERSION, true );
		wp_enqueue_script( 'gutterglove', get_stylesheet_directory_uri() . '/dist/scripts/app.bundle.js', array( 'gutterglove-vender', 'jquery' ), self::VERSION, true );
		wp_enqueue_style( 'titillium', 'https://fonts.googleapis.com/css?family=Titillium+Web:400,700' );
		wp_enqueue_style( 'gutterglove', get_stylesheet_directory_uri() . '/dist/styles/main.css',  array() );
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5', array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	public function testimonial_meta() {
		$cmb = new_cmb2_box( array(
			'id'           => 'gg-testimonial',
			'title'        => __( 'Testimonials', 'plf-mu' ),
			'object_types' => array( 'testimonial' ), // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		) );
		$cmb->add_field( array(
			'name' => __( 'Name', 'plf-mu' ),
			'id'   => 'name',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'Location', 'plf-mu' ),
			'id'   => 'location',
			'type' => 'text',
		) );
	}

	public function template_context( $context ) {

		if ( is_front_page() || is_page( 'products' ) ) {
			$context['testimonials'] = Timber::get_posts( 'post_type=testimonial&numberposts=30' );
		}

		if ( is_front_page() ) {
			$context['review_count']  = number_format( self::get_option( 'review_count' ) );
			$context['guarded_count'] = number_format( self::get_option( 'guarded_count' ) );
			$context['feet_count']    = number_format( self::get_option( 'feet_count' ) );
		}

		return $context;
	}

	public function post_preview( $excerpt ) {
		return $excerpt;
	}

	/**
	 * Get site option
	 *
	 * @param null $key
	 * @param bool $default
	 *
	 * @return bool|mixed
	 * @author Tanner Moushey
	 */
	public static function get_option( $key = null, $default = false ) {
		if ( ! $options = get_option( 'gg_options' ) ) {
			return $default;
		}

		if ( ! $key ) {
			return $options;
		}

		if ( ! isset( $options[ $key ] ) ) {
			return $default;
		}

		return $options[ $key ];
	}

	/**
	 * Update site option
	 *
	 * @param $key
	 * @param $value
	 *
	 * @author Tanner Moushey
	 */
	public static function update_option( $key, $value ) {
		if ( ! $options = get_option( 'gg_options' ) ) {
			$options = [];
		}

		$options[ $key ] = $value;

		update_option( 'gg_options', $options );
	}
	
	public function theme_options() {
		/**
		 * Registers options page menu item and form.
		 */
		$cmb_options = new_cmb2_box( array(
			'id'           => 'gg_option_metabox',
			'title'        => esc_html__( 'Site Options', 'gutterglove' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'gg_options',
		) );

		/*
		 * Options fields ids only need
		 * to be unique within this box.
		 * Prefix is not needed.
		 */
		$cmb_options->add_field( array(
			'name'  => __( 'General Settings' ),
		    'type'  => 'title',
		    'id'    => 'general_title',
		) );

		$cmb_options->add_field( array(
			'name'            => __( 'Number of reviews', 'gutterglove' ),
			'desc'            => __( 'Enter the number of 5 star reviews to show on the home page', 'myprefix' ),
			'id'              => 'review_count',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'esc_cb'          => 'absint',
		) );

		$cmb_options->add_field( array(
			'name' => __( 'Homes Guarded', 'gutterglove' ),
			'type' => 'title',
			'id'   => 'test_title',
		) );

		$cmb_options->add_field( array(
			'name'            => __( 'Number of homes guarded', 'gutterglove' ),
			'desc'            => __( 'Enter the number of homes guarded to show on the home page.', 'gutterglove' ),
			'id'              => 'guarded_count',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'esc_cb'          => 'absint',
		) );

		$cmb_options->add_field( array(
			'name'            => __( 'Auto increment', 'gutterglove' ),
			'desc'            => __( 'How much to auto increment the number of homes guarded', 'gutterglove' ),
			'id'              => 'guarded_increment',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'esc_cb'          => 'absint',
		) );

		$cmb_options->add_field( array(
			'name'    => __( 'Increment Interval', 'gutterglove' ),
			'desc'    => __( 'How often should the homes guarded count auto increment?' ),
			'id'      => 'guarded_interval',
			'type'    => 'radio_inline',
			'options' => array(
				'hourly'     => __( 'Hourly', 'gutterglove' ),
				'twicedaily' => __( 'Twice Daily', 'gutterglove' ),
				'daily'      => __( 'Daily', 'gutterglove' ),
			),
			'default' => 'standard',
		) );

		$cmb_options->add_field( array(
			'name' => __( 'Feet Installed', 'gutterglove' ),
			'type' => 'title',
			'id'   => 'feet_title',
		) );

		$cmb_options->add_field( array(
			'name'            => __( 'Number of feet installed', 'gutterglove' ),
			'desc'            => __( 'Enter the number of feet installed to show on the home page.', 'gutterglove' ),
			'id'              => 'feet_count',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'esc_cb'          => 'absint',
		) );

		$cmb_options->add_field( array(
			'name'            => __( 'Auto increment', 'gutterglove' ),
			'desc'            => __( 'How much to auto increment the number of feet installed.', 'gutterglove' ),
			'id'              => 'feet_increment',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'esc_cb'          => 'absint',
		) );

		$cmb_options->add_field( array(
			'name'    => __( 'Increment Interval', 'gutterglove' ),
			'desc'    => __( 'How often should the feet installed count auto increment?' ),
			'id'      => 'feet_interval',
			'type'    => 'radio_inline',
			'options' => array(
				'hourly'     => __( 'Hourly', 'gutterglove' ),
				'twicedaily' => __( 'Twice Daily', 'gutterglove' ),
				'daily'      => __( 'Daily', 'gutterglove' ),
			),
			'default' => 'standard',
		) );

	}

	/**
	 * Schedule increment events
	 *
	 * @author Tanner Moushey
	 */
	public function increment_events() {

		if ( ! wp_next_scheduled( 'gg_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'gg_hourly' );
		}

		if ( ! wp_next_scheduled( 'gg_twicedaily' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'gg_twicedaily' );
		}

		if ( ! wp_next_scheduled( 'gg_daily' ) ) {
			wp_schedule_event( time(), 'hourly', 'gg_daily' );
		}

	}

	public function increment_hourly() {
		$this->auto_increment( 'hourly' );
	}

	public function increment_twicedaily() {
		$this->auto_increment( 'twicedaily' );
	}

	public function increment_daily() {
		$this->auto_increment( 'daily' );
	}

	public function auto_increment( $interval ) {
		if ( $interval == self::get_option( 'guarded_interval' ) ) {
			self::update_option( 'guarded_count', self::get_option( 'guarded_count' ) + self::get_option( 'guarded_increment' ) );
		}

		if ( $interval == self::get_option( 'feet_interval' ) ) {
			self::update_option( 'feet_count', self::get_option( 'feet_count' ) + self::get_option( 'feet_increment' ) );
		}
	}

}

new GutterGlove();

function gg_contact_form() {
	echo do_shortcode( '[gravityform id="1" title="false" description="false" ajax="true" tabindex="100"]' );
}

function gg_pro_form() {
	echo do_shortcode( '[gravityform id="2" title="false" description="false" ajax="true" tabindex="100"]' );
}