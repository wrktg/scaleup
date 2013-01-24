<?php

if ( !function_exists( 'get_form' ) ) {

  /**
   * Return form by specific name from the view or global scope
   *
   * @param null $name optional name
   * @param null $view optional view
   * @return bool|object
   */
  function get_form( $name = null, $view = null ) {

    if ( is_null( $name ) ) {           // name is not specified when using get_form in a template
      global $in_scaleup_form, $scaleup_form;
      if ( $in_scaleup_form && is_object( $scaleup_form ) )
        return $scaleup_form;
      else
        return false;
    }

    if ( !is_null( $view ) )
      return $view->get_form( $name );

    global $scaleup_view;

    if ( is_object( $scaleup_view ) && method_exists( $scaleup_view, 'get_form' ) )
      return $scaleup_view->get_form( $name );

    return false;
  }
}

if ( !function_exists( 'the_form' ) ) {

  /**
   * Setup form by name
   *
   * @param $name
   * @param null $view
   * @return bool
   */
  function the_form( $name, $view = null ) {

    $form = get_form( $name, $view );
    if ( $form && is_object( $form ) && method_exists( $form, 'the_form' ) ) {
      return $form->the_form();
    }
    return false;
  }
}

if ( !function_exists( 'the_form_attr' ) ) {

  /**
   * Output form attribute
   *
   * @param $name
   */
  function the_form_attr( $name ) {
    $value = get_form_attr( $name );
    if ( !empty( $value ) )
      echo "$name=\"$value\"";
  }

}

if ( !function_exists( 'get_form_attr' ) ) {

  /**
   * Return form attribute
   *
   * @param $name
   * @return mixed
   */
  function get_form_attr( $name ) {
    $form = get_form();
    if ( $form && method_exists( $form, 'get' ) ) {
      return $form->get( $name );
    }

    return null;
  }
}

if ( !function_exists( 'form_has_fields' ) ) {

  /**
   * Check if form has fields
   *
   * @return bool
   */
  function form_has_fields() {
    $form = get_form();
    if ( $form && method_exists( $form, 'has_fields' ) ) {
      return $form->has_fields();
    }

    return false;
  }
}

if ( !function_exists( 'the_form_field' ) ) {

  /**
   * Iterate the field index in The Form.
   * Retrieves the next field, sets up the field, sets the 'in the field' property to true.
   */
  function the_form_field() {
    $form = get_form();
    if ( $form && method_exists( $form, 'the_field' ) ) {
      $form->the_field();
    }
  }
}

if ( !function_exists( 'get_form_field' ) ) {

  /**
   * Return current field
   *
   * @return bool|object
   */
  function get_form_field() {

    global $in_scaleup_form_field;
    if ( $in_scaleup_form_field ) {
      global $scaleup_form_field;
      if ( is_object( $scaleup_form_field ) )
        return $scaleup_form_field;
    }

    return false;
  }

}

if ( !function_exists( 'get_form_field_attr' ) ) {

  /**
   * Return field's attribute by name
   *
   * @param $name
   * @return null
   */
  function get_form_field_attr( $name ) {

    $form_field = get_form_field();
    if ( $form_field && method_exists( $form_field, 'get' ) )
      return $form_field->get( $name );

    return null;
  }
}

if ( !function_exists( 'the_form_field_attr' ) ) {

  /**
   * Output value from field attribute
   *
   * @param $name
   * @return null
   */
  function the_form_field_attr( $name ) {
    $value = get_form_field_attr( $name );
    if ( !empty( $value ) )
      echo "$name=\"$value\"";
  }
}

if ( !function_exists( 'has_form_field_attr' ) ) {

  /**
   * Check if field has an attribute
   *
   * @param $name
   * @return bool
   */
  function has_form_field_attr( $name ) {
    return !is_null( get_form_field_attr( $name ) );
  }
}