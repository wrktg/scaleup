<?php
include_once( dirname( __FILE__ ) . '/login/login.php' );
include_once( dirname( __FILE__ ) . '/profile/profile.php' );

register_addon( 'login', 'ScaleUp_Login_Addon' );
register_addon( 'profile', 'ScaleUp_Profile_Addon' );