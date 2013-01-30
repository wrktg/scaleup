<?php
define( 'SCALEUP_DIR',     dirname( __FILE__ ) );
define( 'SCALEUP_VER',     '0.1.0' );
define( 'SCALEUP_MIN_PHP', '5.2.4' );
define( 'SCALEUP_MIN_WP',  '3.4' );

/**
 * Base classes
 */
require_once( SCALEUP_DIR . '/classes/class-base.php' );
require_once( SCALEUP_DIR . '/classes/class-context.php' );
require_once( SCALEUP_DIR . '/classes/class-routable.php' );

include_once( SCALEUP_DIR . '/classes/class-addon.php' );
include_once( SCALEUP_DIR . '/classes/class-addons.php' );
include_once( SCALEUP_DIR . '/classes/class-form.php' );
include_once( SCALEUP_DIR . '/classes/class-form-field.php' );
include_once( SCALEUP_DIR . '/classes/class-view.php' );
include_once( SCALEUP_DIR . '/classes/class-views.php' );
include_once( SCALEUP_DIR . '/classes/class-schemas.php' );
include_once( SCALEUP_DIR . '/classes/class-schema-type.php' );
include_once( SCALEUP_DIR . '/classes/class-schema.php' );
include_once( SCALEUP_DIR . '/classes/class-app.php' );
include_once( SCALEUP_DIR . '/classes/class-app-server.php' );
include_once( SCALEUP_DIR . '/classes/class-templates.php' );
include_once( SCALEUP_DIR . '/classes/class-plugin-base.php' );
include_once( SCALEUP_DIR . '/functions.php' );
include_once( SCALEUP_DIR . '/template_tags.php' );

include_once( SCALEUP_DIR . '/addons/addon-loader.php' );

new ScaleUp_App_Server();
new ScaleUp_Templates();
new ScaleUp_Schemas();