<?php

if ( !function_exists( 'get_form' ) ) {

  /**
   * Return form by specific name
   *
   * @param null $name optional name
   * @return null|ScaleUp_Form
   */
  function get_form( $name = null ) {

    if ( is_null( $name ) ) {           // name is not specified when using get_form in a template
      global $in_scaleup_form, $scaleup_form;
      if ( $in_scaleup_form && is_object( $scaleup_form ) ) {
        return $scaleup_form;
      } else {
        return null;
      }
    }

    return ScaleUp::get_form( $name );
  }
}

if ( !function_exists( 'the_form' ) ) {

  /**
   * Output the form by a specific name.
   *
   * @param $name
   * @param array $args
   */
  function the_form( $name, $args = array() ) {

    $defaults = array(
      'echo'      => true,
      'template'  => '/forms/form.php'
    );

    $args = wp_parse_args( $args, $defaults );

    $form = get_form( $name );
    if ( !is_null( $form ) ) {
      $form->the_form();
      if ( $args[ 'echo' ] ) {
        get_template_part( $args[ 'template' ] );
      }
    }
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
    if ( !empty( $value ) ) {
      echo "$name=\"$value\"";
    }
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
    if ( is_object( $form ) && method_exists( $form, 'get' ) ) {
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
    if ( is_object( $form ) && method_exists( $form, 'has_fields' ) ) {
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
    if ( is_object( $form ) && method_exists( $form, 'the_field' ) ) {
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
    if ( is_object( $form_field ) && method_exists( $form_field, 'get' ) )
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
    $form_field = get_form_field();
    if ( is_object( $form_field ) && method_exists( $form_field, 'has' ) )
      return $form_field->has( $name );
    return false;
  }
}

if ( !function_exists( 'get_view_permalink' ) ) {
  /**
   * Return url if it could be generated
   *
   * @param $name
   * @param array $args
   * @return string|bool
   */
  function get_view_permalink( $name, $args = array() ) {
    $view = get_view( $name );
    if ( is_object( $view ) && method_exists( $view, 'get_url' ) ) {
      return $view->get_url( $args );
    }
  }
  return false;
}