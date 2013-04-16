<?php
class ScaleUp_Args extends ArrayObject {

  function __construct( $args = array() ) {

    foreach ( $args as $prop => $value ) {
      $this->$prop = $value;
    }

  }

}