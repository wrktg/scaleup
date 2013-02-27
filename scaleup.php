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
require( SCALEUP_DIR . '/classes/class-scaleup.php' );
require( SCALEUP_DIR . '/classes/class-base.php' );
require( SCALEUP_DIR . '/classes/class-duck-type.php' );
require( SCALEUP_DIR . '/classes/class-feature.php' );
require( SCALEUP_DIR . '/classes/class-global.php' );
require( SCALEUP_DIR . '/classes/class-contextual.php' );

/**
 * Core Features
 */
include( SCALEUP_DIR . '/classes/class-site.php' );
include( SCALEUP_DIR . '/classes/class-form.php' );
include( SCALEUP_DIR . '/classes/class-form-field.php' );
include( SCALEUP_DIR . '/classes/class-schema.php' );
include( SCALEUP_DIR . '/classes/class-property.php' );
include( SCALEUP_DIR . '/classes/class-template.php' );
include( SCALEUP_DIR . '/classes/class-asset.php' );
include( SCALEUP_DIR . '/classes/class-alert.php' );

/**
 * API
 */
include( SCALEUP_DIR . '/functions.php' );
include( SCALEUP_DIR . '/template-tags.php' );

/**
 * Activate ScaleUp functionality within the site
 */
new ScaleUp();