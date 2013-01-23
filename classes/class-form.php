<?php
class ScaleUp_Form {

  function __construct( $args ) {
    $this->_args = $args;
  }

  /**
   * Load arguments into the form.
   * Call this function when processing a POST request.
   *
   * @param $args
   */
  function load( $args ) {

  }

  /**
   * Set current form into global scope
   */
  function the_form() {
    global $form;
    $form = $this;
  }

}