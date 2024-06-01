<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'botiga-woocommerce-style','botiga-bhfb','botiga-quick-view','botiga-style-min','botiga-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 12 );

// END ENQUEUE PARENT ACTION

//Rikkerdesign Support line
function add_support_to_admin_bar( $wp_admin_bar ) {
  if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $support_text = 'Komt u er niet helemaal uit? Bel dan 074-7002138';
    $args = array(
      'id' => 'custom-support',
      'title' => $support_text,
      'parent' => 'top-secondary',
      'meta' => array(
        'class' => 'custom-support-menu-item'
      )
    );
    $wp_admin_bar->add_node( $args );
  }
}
add_action( 'admin_bar_menu', 'add_support_to_admin_bar', 999 );

//Rikkerdesign login page
function wpb_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(https://rikkerdesign.nl/wp-content/uploads/2021/12/Logo-Groot-Top-1.png);
        height:140px;
        width:294px;
        background-size: 294px 140px;
        background-repeat: no-repeat;
        padding-bottom: 10px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'wpb_login_logo' );

function my_login_page_custom_bg_image() { ?>
<style type="text/css">
  body{
    background-image:url(https://rikkerdesign.nl/wp-content/uploads/2022/09/Achtergrond-Websites-scaled.jpg) !important;
    background-size:cover !important;
    background-position:center center !important;
  }
</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_page_custom_bg_image' );

function my_loginURL() {
    return 'https://www.rikkerdesign.nl';
}
add_filter('login_headerurl', 'my_loginURL');

function my_loginURLtext() {
    return 'Rikkerdesign';
}
add_filter('login_headertitle', 'my_loginURLtext');

add_filter( 'login_display_language_dropdown', '__return_false' );

function custom_login_styles() {
  wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login/login_styles.css' );
}
add_action( 'login_enqueue_scripts', 'custom_login_styles' );


//Rikkerdesign Admin panel
function custom_admin_bar_logo( $wp_admin_bar ) {
    $wp_admin_bar->add_node(
        array(
            'id'    => 'wp-logo',
            'title' => '<img src="' . get_stylesheet_directory_uri() . '/images/custom-icon.png" style="height: 30px; width: 30px;"/>',
            'href'  => home_url(),
            'meta'  => array(
                'class' => 'wp-logo',
                'title' => __('Home'),
            ),
        )
    );
}
add_action( 'admin_bar_menu', 'custom_admin_bar_logo', 11 );

// Add this to child theme functions.php to change the way reviews display in WooCommerce to not show full name.
// Details here: https://silicondales.com/tutorials/woocommerce/woocommerce-change-review-author-display-name-username/
// By Robin Scott for Silicon Dales
add_filter('get_comment_author', 'my_comment_author', 10, 1);

function my_comment_author( $author = '' ) {
// Get the comment ID from WP_Query
$comment = get_comment( $comment_ID );
if (!empty($comment->comment_author) ) {
if($comment->user_id > 0){
$user=get_userdata($comment->user_id);
$author=$user->first_name.' '.substr($user->last_name,0,1).'.'; // this is the actual line you want to change
} else {
$author = __('Anonymous');
}
} else {
$author = $comment->comment_author;
}

return $author;
}

// Register new status
function register_in_progress_order_status() {
  register_post_status( 'wc-in-progress', array(
      'label'                     => 'Wordt verwerkt',
      'public'                    => true,
      'show_in_admin_status_list' => true,
      'show_in_admin_all_list'    => true,
      'exclude_from_search'       => false,
      'label_count'               => _n_noop( 'In progress (%s)', 'In progress (%s)' )
  ) );
}
// Add custom status to order status list
function add_in_progress_to_order_statuses( $order_statuses ) {
  $new_order_statuses = array();
  foreach ( $order_statuses as $key => $status ) {
      $new_order_statuses[ $key ] = $status;
      if ( 'wc-processing' === $key ) {
          $new_order_statuses['wc-in-progress'] = 'Wordt verwerkt';
      }
  }
  return $new_order_statuses;
}
add_action( 'init', 'register_in_progress_order_status' );
add_filter( 'wc_order_statuses', 'add_in_progress_to_order_statuses' );

function   QuadLayers_change_order_status( $order_id ) {  
  if ( ! $order_id ) {return;}            
  $order = wc_get_order( $order_id );
  if( 'processing'== $order->get_status() ) {
      $order->update_status( 'wc-in-progress' );
  }
}
add_action('woocommerce_thankyou','QuadLayers_change_order_status');