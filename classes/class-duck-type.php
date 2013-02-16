<?php
class ScaleUp_Duck_Type extends ScaleUp_Base implements ArrayAccess {

  /**
   * Add methods for this duck type into the object as properties as callbacks
   *
   * @param $feature
   */
  function apply( $feature ) {

    $methods = $this->get( 'methods' );
    foreach ( $methods as $method ) {
      if ( method_exists( $this, $method ) ) {
        $feature->$method = array( $this->get( '__CLASS__' ), $method );
      }
    }

    return $feature;
  }

  function offsetSet( $offset, $value ) {
    $this->set( $offset, $value );
  }

  function offsetExists( $offset ) {
    return isset( $this->$offset );
  }

  function offsetUnset( $offset ) {
    unset( $this->$offset );
  }

  function offsetGet( $offset ) {
    return $this->get( $offset );
  }

}