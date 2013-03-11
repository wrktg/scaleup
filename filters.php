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