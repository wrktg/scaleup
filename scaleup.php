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

/**
 * Duck types
 */
require( SCALEUP_DIR . '/duck-types/class-global.php' );
require( SCALEUP_DIR . '/duck-types/class-contextual.php' );

/**
 * Features
 */
include( SCALEUP_DIR . '/features/class-site.php' );
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