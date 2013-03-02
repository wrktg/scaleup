<?php
/**
 * Output from by $name
 *
 * @param $name string
 */
function scaleup_the_form( $name ) {

  global $scaleup_form;

  $form = scaleup_get_form( $name );

  if ( !is_null( $form ) ) {
    $scaleup_form = $form;
    get_template_part( '/scaleup-form.php' );
  }

}

/**
 * Return an form by specific $name
 *
 * @param $name
 * @return null|ScaleUp_Form
 */
function scaleup_get_form( $name ) {

  $site = ScaleUp::get_site();

  /*** @var $form ScaleUp_Form */
  $form = $site->get_feature( 'form', $name );

  return $form;
}

/**
 * Output form attribute and its value
 *
 * @param $name
 */
function scaleup_the_form_attr( $name ) {

  $value = scaleup_get_form_attr( $name );
  if ( !is_null( $value ) ) {
    echo "$name=\"$value\"";
  }

}

/**
 * Return value of a form attribute
 *
 * @param $attr
 * @return mixed|null
 */
function scaleup_get_form_attr( $attr ) {

  global $scaleup_form;

  if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'get' ) ) {
    /** @var $value mixed */
    $value = $scaleup_form->get( $attr );
  } else {
    $value = null;
  }

  return $value;
}

/**
 * Check if form has more fields and advance to the next available form field
 *
 * @return bool
 */
function scaleup_form_has_fields(){

  global $scaleup_form;

  $has_fields = false;

  if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'has_fields' ) ) {
    $has_fields = $scaleup_form->has_fields();
  }

  return $has_fields;
}

/**
 * Set form field by specific name into global scope
 */
function scaleup_setup_form_field( $name = null ) {

  global $scaleup_form, $scaleup_form_field;

  if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'setup_field' ) ) {
    $scaleup_form->setup_field( $name );
    $scaleup_form_field = $scaleup_form->get_current_field();
  }

}

/**
 * Setup field and include its template
 *
 * @param null $name
 */
function scaleup_the_form_field( $name = null ) {

  scaleup_setup_form_field( $name );

  global $scaleup_form_field;

  $template_name = $scaleup_form_field->get( 'template' );

  $site = ScaleUp::get_site();
  $feature = $site->get_feature( 'template', $template_name );
  if ( is_object( $feature ) && $feature->has( 'template' ) ) {
    $template = $feature->get( 'template' );
    get_template_part( $template );
  }

}

/**
 * Return value of a field attribute with name $attr
 *
 * @param $attr
 * @return string
 */
function scaleup_get_form_field_attr( $attr ) {

  global $scaleup_form_field;

  $value = null;

  if ( is_object( $scaleup_form_field ) && method_exists( $scaleup_form_field, 'get' ) ) {
    $value = apply_filters( "scaleup_form_field_{$attr}", $scaleup_form_field->get( $attr ), $scaleup_form_field );
  }

  return $value;
}

/**
 * Output field attribute and its value
 *
 * @param $attr
 */
function scaleup_the_form_field_attr( $attr ) {

  $value = scaleup_get_form_field_attr( $attr );

  if ( !is_null( $value ) ) {
    echo "$attr=\"$value\"";
  }

}

/**
 * Return true if form field has a specific attribute
 *
 * @param $attr
 * @return bool
 */
function scaleup_has_form_field_attr( $attr ) {
  return !is_null( scaleup_get_form_field_attr( $attr ) );
}