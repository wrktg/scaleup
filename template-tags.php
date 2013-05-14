<?php
if ( !function_exists( 'the_form' ) ) {
  /**
   * Output form by $name
   *
   * @param $name string
   */
  function the_form( $name ) {

    global $scaleup_form;

    $form = get_form( $name );

    if ( is_object( $form ) ) {
      $scaleup_form = $form;

      /**
       * Show alerts above the form
       */
      $scaleup_form->add_action( 'header', 'scaleup_form_header_alerts' );

      $scaleup_form->do_action( 'header' );
      ScaleUp::get_template_part( 'form' );
      $scaleup_form->do_action( 'footer' );
    }

  }
}

if ( !function_exists( 'the_form_attr' ) ) {
  /**
   * Output form attribute and its value
   *
   * @param $name
   */
  function the_form_attr( $name ) {

    $value = get_form_attr( $name );
    if ( !is_null( $value ) ) {
      echo "$name=\"$value\"";
    }

  }
}

if ( !function_exists( 'get_form_attr' ) ) {
  /**
   * Return value of a form attribute
   *
   * @param $attr
   * @return mixed|null
   */
  function get_form_attr( $attr ) {

    global $scaleup_form;

    if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'get' ) ) {
      /** @var $value mixed */
      $value = $scaleup_form->get( $attr );
    } else {
      $value = null;
    }

    return $value;
  }
}

if ( !function_exists( 'form_has_fields' ) ) {
  /**
   * Check if form has more fields and advance to the next available form field
   *
   * @return bool
   */
  function form_has_fields(){

    global $scaleup_form;

    $has_fields = false;

    if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'has_fields' ) ) {
      $has_fields = $scaleup_form->has_fields();
    }

    return $has_fields;
  }
}

if ( !function_exists( 'setup_form_field_data' ) ) {
  /**
   * Set form field by specific name into global scope
   */
  function setup_form_field_data( $name = null ) {

    global $scaleup_form, $scaleup_form_field;

    if ( is_object( $scaleup_form ) && method_exists( $scaleup_form, 'setup_field' ) ) {
      $scaleup_form->setup_field( $name );
      $scaleup_form_field = $scaleup_form->get_current_field();
    }

  }
}

if ( !function_exists( 'the_form_field' ) ) {
  /**
   * Setup field and include its template
   *
   * @param null $name
   */
  function the_form_field( $name = null ) {

    setup_form_field_data( $name );

    global $scaleup_form_field;

    $template_name = $scaleup_form_field->get( 'template' );
    ScaleUp::get_template_part( $template_name );

  }
}

if ( !function_exists( 'get_form_field_attr' ) ) {
  /**
   * Return value of a field attribute with name $attr
   *
   * @param $attr
   * @return string
   */
  function get_form_field_attr( $attr ) {

    global $scaleup_form_field;

    $value = null;

    if ( is_object( $scaleup_form_field ) && method_exists( $scaleup_form_field, 'get' ) ) {
      $value = apply_filters( "scaleup_form_field_{$attr}", $scaleup_form_field->get( $attr ), $scaleup_form_field );
    }

    return $value;
  }
}

if ( !function_exists( 'the_form_field_attr' ) ) {
  /**
   * Output field attribute and its value
   *
   * @param $attr
   */
  function the_form_field_attr( $attr ) {

    $value = get_form_field_attr( $attr );

    if ( !is_null( $value ) ) {
      echo "$attr=\"$value\"";
    }

  }
}

if ( !function_exists( 'has_form_field_attr' ) ) {
  /**
   * Return true if form field has a specific attribute
   *
   * @param $attr
   * @return bool
   */
  function has_form_field_attr( $attr ) {
    return !is_null( get_form_field_attr( $attr ) );
  }
}

if ( !function_exists( 'the_view' ) ) {
  /**
   * Output view based on provided args
   *
   * @param string  $name
   * @param string  $template_part
   * @param array   $args
   */
  function the_view( $name, $template_part = null, $args = array() ) {

    $default = array(
      'vars'          => array(),
      'template_part' => $template_part,
    );
    $args = wp_parse_args( $args, $default );

    $view = ScaleUp::get_view( $name );

    if ( $view ) {
      $view->render( $args[ 'vars' ], $args );
    } else {
      get_template_part( $name, $template_part );
    }

  }
}

if ( !function_exists( 'the_view_part' ) ) {
  /**
   * Output part of the current view
   *
   * @param string $template_part
   * @param array $args
   */
  function the_view_part( $template_part, $args = array() ) {

    $site = ScaleUp::get_site();
    if ( property_exists( $site, 'view' ) && is_object( $site->view ) ) {
      /*** @var $view ScaleUp_View */
      $view = $site->view;
      if ( is_object( $view ) && method_exists( $view, 'render_template_part' ) ) {
        $view->render_template_part( $template_part, $args );
      }
    }

  }
}