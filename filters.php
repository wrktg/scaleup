<?php
/**
 * Add asterisk to the end of a label of a required field.
 *
 * @param $label
 * @param $form_field ScaleUp_Form_Field
 * @return string
 */
function scaleup_form_field_label_asterisk( $label, $form_field ) {
  if ( $form_field->has( 'validation' ) && is_array( $form_field->get( 'validation' ) ) ) {
    $validations = $form_field->get( 'validation' );
    if ( is_array( $validations ) && in_array( 'required', $validations ) && !empty( $label ) ) {
      $label .= '<span class="asterisk">&#42;</span>';
    }
  }
  return $label;
}
add_filter( 'scaleup_form_field_label', 'scaleup_form_field_label_asterisk', 10, 2 );

/**
 * Normalize value and return the value in format that ScaleUp will be able to work with
 *
 * Possible $format values:
 *  'string'      - provided value is a string
 *  'args_string' - provided value is an args strings in format $key=$value,$key1=$value1
 *
 * @param string $value
 * @param string $format
 * @return array
 */
function scaleup_normalize_value( $value, $format = 'string' ) {
  switch ( $format ):
    case 'args_string':
      $value = wp_parse_args( $value );
      break;
    default:
      $value = array( 'value' => $value );
  endswitch;
  return $value;
}
add_filter( 'scaleup_normalize_value', 'scaleup_normalize_value', 10, 2 );

/**
 * Expand an associative $args array into an array of arrays with value keys
 *
 * array( 'hello' => 'world', 'h2' => 'w2' ) becomes array( 'hello' => array( 'value' => 'world' ), 'h2' => array( 'value' => 'w2' ) );
 *
 * @param array $args
 * @return array
 */
function scaleup_expand_args( $args = array() ) {

  $result = array();
  foreach ( $args as $key => $value ) {
    if ( !is_null( $value ) ) {
      $expanded = array(
        'value' => $value,
      );
      $result[ $key ] = $expanded;
    }
  }

  return $result;
}
add_filter( 'scaleup_expand_args', 'scaleup_expand_args' );

/**
 * Flatten the array and return it in requested format
 *
 * @param array $args
 * @param string $format ARRAY_A or ARRAY_N
 * @return array
 */
function scaleup_flatten_args( $args, $format = ARRAY_A ) {

  $output = array();

  foreach ( $args as $key => $value ) {
    if ( is_array( $value ) && isset( $value[ 'value' ] ) ) {
      $value = $value[ 'value' ];
    }
    switch ( $format ):
      case ARRAY_A:
        $output[ $key ] = $value;
        break;
      case ARRAY_N:
        $output[] = $value;
      break;
    endswitch;
  }

  return $output;
}
add_filter( 'scaleup_flatten_args', 'scaleup_flatten_args', 10, 2 );

/**
 * Replaces variables in string template that uses {variable_name} syntax.
 *
 * @param $template
 * @param $args
 * @return string
 */
function scaleup_string_template( $template, $args ) {

  $pattern   = $template;
  $len       = strlen( $pattern );
  $tokens    = array();
  $variables = array();
  $pos       = 0;
  preg_match_all( '#.\{(\w+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
  foreach ( $matches as $match ) {
    if ( $text = substr( $pattern, $pos, $match[ 0 ][ 1 ] - $pos ) ) {
      $tokens[ ] = array( 'text', $text );
    }

    $pos = $match[ 0 ][ 1 ] + strlen( $match[ 0 ][ 0 ] );
    $var = $match[ 1 ][ 0 ];

    // Use the character preceding the variable as a separator
    $separators = array( $match[ 0 ][ 0 ][ 0 ] );

    if ( $pos !== $len ) {
      // Use the character following the variable as the separator when available
      $separators[ ] = $pattern[ $pos ];
    }
    $regexp = sprintf( '[^%s]+', preg_quote( implode( '', array_unique( $separators ) ), '#' ) );

    $tokens[ ] = array( 'variable', $match[ 0 ][ 0 ][ 0 ], $regexp, $var );

    if ( in_array( $var, $variables ) ) {
      /**
       * @todo: Add error that variable can't be used twice
       */
    }

    $variables[ ] = $var;
  }

  if ( $pos < $len ) {
    $tokens[ ] = array( 'text', substr( $pattern, $pos ) );
  }

  $result = '';
  foreach ( $tokens as $token ) {
    if ( 'text' === $token[ 0 ] ) {
      // Text tokens
      $result .= $token[ 1 ];
    }
    if ( 'variable' === $token[ 0 ] ) {
      // Variable tokens
      $prefix = $token[ 1 ];
      if ( isset( $args[ $token[ 3 ] ] ) ) {
        $value = $args[ $token[ 3 ] ];
      } else {
        /**
         * @todo: return an error if args doesn't provide value for variable.
         */
        $value = '';
      }
      $result .= "$prefix$value";
    }
  }

  return $result;
}
add_filter( 'scaleup_string_template', 'scaleup_string_template', 10, 2 );