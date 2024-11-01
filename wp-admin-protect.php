<?php
/*
Plugin Name: WP Admin Protect
Plugin URI: https://wordpress.org/plugins/wp-admin-protect/
Description: Protect your WP Admin from visitors, this plugin easily change your wp-login url to hide it from users.
Version: 2.6.0
Text Domain: wp-admin-protect
Domain Path: /lang
Author: Marcello Ruoppolo
Author URI: https://kloxstudios.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.htm
*/


if(!defined('ABSPATH'))exit;
class Wpap {
  private static $instance;

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {
    add_action( 'admin_menu', array($this,'wpap_menu_pages') );
    add_action( 'login_form_login',array($this,'wpap_protect_admin') );
    add_action( 'admin_enqueue_scripts', array($this,'wpap_enqueue_scripts') );
    add_action( 'plugins_loaded', array($this, 'wpap_languages') );
  }

  public function wpap_menu_pages() {
    add_menu_page( 'WP Admin Protect', 'WP Admin Protect', 'manage_options', 'wpap', 'Wpap::wpap_create_page', plugin_dir_url( __FILE__ ) . '/assets/images/wpap-icon.png', 50 );

    add_action( 'admin_init', array($this, 'wpap_register_settings') );
  }

  public function wpap_enqueue_scripts() {

    wp_register_style( 'wpap-style', plugin_dir_url( __FILE__ ) . 'assets/css/wpap-style.css', array(), '1.0.0', 'all' );
    wp_register_style( 'sts-grid', plugin_dir_url( __FILE__ ) . 'assets/css/simple-grid.css', array(), '1.0.0.', 'all' );
    wp_register_style( 'sts-icon', plugin_dir_url( __FILE__ ) . 'assets/css/icon-font.css', array(), '1.0.0', 'all' );

    wp_register_script( 'sts-fontawesome', 'https://use.fontawesome.com/eb411371dd.js', array(), '', true );

    wp_enqueue_style( 'wpap-style' );
    wp_enqueue_style( 'sts-grid' );
    wp_enqueue_style( 'sts-icon' );

    wp_enqueue_script( 'sts-fontawesome' );

  }

  public function wpap_register_settings() {
    register_setting( 'wpap-group', 'wpap-activated' );
    register_setting( 'wpap-group', 'wpap-url' );
    register_setting( 'wpap-group', 'wpap-minute' );
    register_setting( 'wpap-group', 'wpap-term' );

    add_settings_section( 'wpap-settings', '', 'Wpap::wpap_settings_section', 'wpap' );

    add_settings_section( $id, $title, $callback, $page );

    add_settings_field( 'wpap-activated', __( 'Enable Protection', 'wp-admin-protect' ), 'Wpap::wpap_enable_protection', 'wpap', 'wpap-settings' );
    //add_settings_field( 'wpap-minute', __( 'Enable Minute URL', 'wp-admin-protect' ), 'Wpap::wpap_enable_minute_protection', 'wpap', 'wpap-settings' );
    add_settings_field( 'wpap-url', __( 'Redirect URL', 'wp-admin-protect' ), 'Wpap::wpap_redirect_url', 'wpap', 'wpap-settings' );
    add_settings_field( 'wpap-term', __( 'Term you want to use on your URL', 'wp-admin-protect' ), 'Wpap::wpap_term', 'wpap', 'wpap-settings' );
  }

  public function wpap_settings_section() {}

  public function wpap_create_page() {
    include('templates/settings.php');
  }

  public function wpap_redirect_url() {
    $wpap_url = get_option( 'wpap-url' );
    if($wpap_url != ''):
    echo "<input type='url' name='wpap-url' id='wpap-url' placeholder='" . __( 'Insert the URL here', 'wp-admin-protect' ) . "' value='" . $wpap_url . "'>";
  else:
    echo "<input type='url' name='wpap-url' id='wpap-url' placeholder='" . __( 'Insert the URL here', 'wp-admin-protect' ) . "'>";
  endif;
    echo '<p>' . __( 'URL you want to redirect the user that tries to access your WP Admin', 'wp-admin-protect' ) . '</p>';
  }  
  public function wpap_term() {
    $wpap_term = get_option( 'wpap-term' );
    if($wpap_term != ''):
    echo "<input type='text' name='wpap-term' id='wpap-term' placeholder='" . __( 'Insert the term here', 'wp-admin-protect' ) . "' value='" . $wpap_term . "'>";
  else:
    echo "<input type='text' name='wpap-term' id='wpap-term' placeholder='" . __( 'Insert the term here', 'wp-admin-protect' ) . "'>";
  endif;
    echo '<p>' . __( 'The term you want to insert on your url to access WP Admin. The URL must have this term to access WP Admin', 'wp-admin-protect' ) . '</p>';
  }
  public function wpap_enable_protection() {
    $wpap_enabled = get_option( 'wpap-activated' );
    if($wpap_enabled == 1){
      $wpap_checked = " checked";
    }else{
      $wpap_checked = "";
    }
    echo "<input type='checkbox' id='wpap-activated' name='wpap-activated'" . $wpap_checked . " value='1'>
    <label for='wpap-activated'><span></span>" . __( 'Check if you want to enable your protection', 'wp-admin-protect' ) . "</label>";
  }
  public function wpap_enable_minute_protection() {
    $wpap_minute = get_option( 'wpap-aminute' );
    if($wpap_minute == 1){
      $wpap_minute = " checked";
    }else{
      $wpap_minute = "";
    }
    echo "<label><input type='checkbox' id='wpap-minute' name='wpap-minute'" . $wpap_minute . " value='1'>" . __( 'Check if you want to enable the minute protection', 'wp-admin-protect' ) . "</label>";
  }

  public function wpap_languages() {
      load_plugin_textdomain( 'wp-admin-protect', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
  }
    
  public function wpap_protect_admin() {
    if ( get_option( 'wpap-activated' ) == 1 ):
      $term = get_option( 'wpap-term' );
      $url = get_option( 'wpap-url' );
      $this_page = basename($_SERVER['REQUEST_URI']);
      if (strpos($this_page, "?") !== false) $this_page = reset(explode("?", $this_page));
      if(basename($this_page) == 'wp-login.php'){

          if(!isset($_GET[$term])){

              header('Location:' . $url);
          }
      }
    endif;

  }

}

Wpap::getInstance();
