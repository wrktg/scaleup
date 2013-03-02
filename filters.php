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
      $label = "$label<span class=\"asterisk\">&#42;</span>";
    }
  }
  return $label;
}
add_filter( 'scaleup_form_field_label', 'scaleup_form_field_label_asterisk', 10, 2 );