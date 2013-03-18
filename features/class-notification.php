<?php
class ScaleUp_Notification extends ScaleUp_Feature {

  function activation() {
    switch( $this->get( 'method' ) ) :
      case 'email':
        $this->add_filter( 'issue', array( $this, 'send_email' ) );
        break;
      default:
    endswitch;
  }

  function issue( $args ) {
    return $this->apply_filters( 'issue', $args );
  }

  function send_email( $args ) {

    $flat_args = apply_filters( 'scaleup_flatten_args', $args );

    $to        = apply_filters( 'scaleup_string_template', $this->get( 'to' ), $flat_args );
    $subject   = apply_filters( 'scaleup_string_template', $this->get( 'subject' ), $flat_args );
    $message   = apply_filters( 'scaleup_string_template', $this->get( 'message' ), $flat_args );

    $from      = apply_filters( 'scaleup_string_template', $this->get( 'from' ), $flat_args );
    $headers = array();
    if ( $from ) {
      $headers[] = "From: $from";
    }

    if ( wp_mail( $to, $subject, $message, $headers ) ) {
      $alert = $this->add( 'alert', array(
        'type'  => "success",
        'msg'   => "Email succesfully sent to $to",
      ));
    } else {
      $alert = $this->add( 'alert', array(
        'type'  => "warning",
        'msg'   => "Failed to send email to $to",
      ));
    }

    return $args;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'notification',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'notification', array(
  '__CLASS__'     => 'ScaleUp_Notification',
  '_plural'       => 'notifications',
  '_supports'     => array( 'alerts' ),
  '_duck_types'   => array( 'contextual' ),
) );