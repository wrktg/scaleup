<?php
/**
 * @see http://php.net/manual/en/class.arrayaccess.php
 */
class ScaleUp_Schema extends ScaleUp_Base implements ArrayAccess {

  function offsetSet( $offset, $value ) {
      $this->set( $offset, $value );
  }

  function offsetExists( $offset ) {
    $property = "_$offset";
    return isset( $this->$property );
  }

  function offsetUnset( $offset ) {
    $property = "_$offset";
    unset( $this->$property );
  }

  function offsetGet( $offset ) {
    return $this->get( $offset );
  }

}