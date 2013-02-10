<?php
/**
 * Plugin Name: ScaleUp
 */
define( 'SCALEUP_DIR',     dirname( __FILE__ ) );
define( 'SCALEUP_VER',     '0.1.0' );
define( 'SCALEUP_MIN_PHP', '5.2.4' );
define( 'SCALEUP_MIN_WP',  '3.4' );

/**
 * Base classes
 */
require_once( SCALEUP_DIR . '/classes/class-scaleup.php' );
require_once( SCALEUP_DIR . '/classes/class-base.php' );
require_once( SCALEUP_DIR . '/classes/class-contextual.php' );

include_once( SCALEUP_DIR . '/classes/class-form.php' );
include_once( SCALEUP_DIR . '/classes/class-forms.php' );
include_once( SCALEUP_DIR . '/classes/class-form-field.php' );
include_once( SCALEUP_DIR . '/classes/class-schemas.php' );
include_once( SCALEUP_DIR . '/classes/class-schema-property.php' );
include_once( SCALEUP_DIR . '/classes/class-schema-type.php' );
include_once( SCALEUP_DIR . '/classes/class-schema.php' );
include_once( SCALEUP_DIR . '/classes/class-templates.php' );
include_once( SCALEUP_DIR . '/functions.php' );
include_once( SCALEUP_DIR . '/template_tags.php' );

new ScaleUp_Templates();
new ScaleUp_Schemas();
new ScaleUp_Forms();