<?php
/**
 * Plugin Name: ScaleUp
 */

define( 'SCALEUP_DIR', dirname( __FILE__ ) );
define( 'SCALEUP_VER', '0.5.0' );
define( 'SCALEUP_MIN_PHP', '5.2.4' );
define( 'SCALEUP_MIN_WP', '3.4' );

/**
 * Core
 */
require( SCALEUP_DIR . '/core/class-scaleup.php' );
require( SCALEUP_DIR . '/core/class-base.php' );
require( SCALEUP_DIR . '/core/class-duck-type.php' );
require( SCALEUP_DIR . '/core/class-feature.php' );
require( SCALEUP_DIR . '/core/class-app-server.php' );

/**
 * Duck types
 */
require( SCALEUP_DIR . '/duck-types/class-global.php' );
require( SCALEUP_DIR . '/duck-types/class-contextual.php' );
include( SCALEUP_DIR . '/duck-types/class-routable.php' );

/**
 * Features
 */
include( SCALEUP_DIR . '/features/class-site.php' );
include( SCALEUP_DIR . '/features/class-app.php' );
include( SCALEUP_DIR . '/features/class-addon.php' );
include( SCALEUP_DIR . '/features/class-view.php' );
include( SCALEUP_DIR . '/features/class-item.php' );
include( SCALEUP_DIR . '/features/class-schema.php' );
include( SCALEUP_DIR . '/features/class-post-schema.php' );
include( SCALEUP_DIR . '/features/class-property.php' );
include( SCALEUP_DIR . '/features/class-taxonomy.php' );
include( SCALEUP_DIR . '/features/class-relationship.php' );
include( SCALEUP_DIR . '/features/class-form.php' );
include( SCALEUP_DIR . '/features/class-form-field.php' );
include( SCALEUP_DIR . '/features/class-template.php' );
include( SCALEUP_DIR . '/features/class-asset.php' );
include( SCALEUP_DIR . '/features/class-alert.php' );
include( SCALEUP_DIR . '/features/class-notification.php' );

/**
 * Addons
 */
function scaleup_init() {
  include( SCALEUP_DIR . '/addons/login/login.php' );
  include( SCALEUP_DIR . '/addons/profile/profile.php' );
  include( SCALEUP_DIR . '/addons/frontpage/frontpage.php' );
}
add_action( 'scaleup_init', 'scaleup_init' );

/**
 * API
 */
include( SCALEUP_DIR . '/functions.php' );
function scaleup_after_setup_theme() {
  include( SCALEUP_DIR . '/template-tags.php' );
  include( SCALEUP_DIR . '/filters.php' );
  include( SCALEUP_DIR . '/actions.php' );
}
add_action( 'after_setup_theme', 'scaleup_after_setup_theme' );

/**
 * Activate ScaleUp functionality within the site
 */
new ScaleUp();
new ScaleUp_App_Server();