<?php
/**
 * Creates linkable objects that are aware of what they're part of.
 */
class ScaleUp_Contextual extends ScaleUp_Base {

  var $_context;

  function get_defaults() {
    return wp_parse_args(
      array(
           'context' => null,
      ), parent::get_defaults()
    );
  }

  function get_context() {
    return $this->_context;
  }

}