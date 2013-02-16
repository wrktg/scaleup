<?php
/**
 * Plugin Name: ScaleUp
 */

define( 'SCALEUP_DIR', dirname( __FILE__ ) );
define( 'SCALEUP_VER', '0.5.0' );
define( 'SCALEUP_MIN_PHP', '5.2.4' );
define( 'SCALEUP_MIN_WP', '3.4' );

/**
 * Core & Parent classes
 */
require_once( SCALEUP_DIR . '/classes/class-scaleup.php' );
require_once( SCALEUP_DIR . '/classes/class-base.php' );
require_once( SCALEUP_DIR . '/classes/class-duck-type.php' );
require_once( SCALEUP_DIR . '/classes/class-feature.php' );
require_once( SCALEUP_DIR . '/classes/class-contextual.php' );

/**
 * Core Features
 */
include_once( SCALEUP_DIR . '/classes/class-site.php' );
include_once( SCALEUP_DIR . '/classes/class-form.php' );
include_once( SCALEUP_DIR . '/classes/class-form-field.php' );
include_once( SCALEUP_DIR . '/classes/class-schema.php' );
include_once( SCALEUP_DIR . '/classes/class-property.php' );
include_once( SCALEUP_DIR . '/classes/class-template.php' );

/**
 * Activate ScaleUp functionality within the site
 */
new ScaleUp();