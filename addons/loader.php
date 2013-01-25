<?php
include_once( dirname( __FILE__ ) . '/login/addon.php' );
include_once( dirname( __FILE__ ) . '/profile/addon.php' );

register_addon( 'login', 'ScaleUp_Login_Addon' );
register_addon( 'profile', 'ScaleUp_Profile_Addon' );