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
		add_filter( 'timber_context', array( $this, 'testimonial_content' ) );
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

	public function testimonial_content( $context ) {

		if ( is_home() || is_page( 'products' ) ) {
			$context['testimonials'] = Timber::get_posts( 'post_type=testimonial&numberposts=30' );
		}

		return $context;
	}
}

new GutterGlove();

function gg_contact_form() {
	echo do_shortcode( '[gravityform id="1" title="false" description="false" ajax="true" tabindex="100"]' );
}

function gg_pro_form() {
	echo do_shortcode( '[gravityform id="2" title="false" description="false" ajax="true" tabindex="100"]' );
}