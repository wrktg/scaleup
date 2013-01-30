<?php
/**
 * @see http://php.net/manual/en/class.arrayaccess.php
 */
class ScaleUp_Schema extends ScaleUp_Base implements ArrayAccess {

  private $_updated = array();

  /**
   * During initialization, ScaleUp_Schema takes an array of properties as an argument.
   * It stores these arguments in internal storage to be used when an $this->update is executed.
   * This is done to differentiate between values that are provided by the user from those that are loaded during
   * initialization. This makes it possible to distinguish between fields that need to be updated when saving to the
   * database.
   *
   * @param array $properties
   */
  function __construct( $properties = array() ) {
    parent::__construct( $properties );
  }

  /**
   * Set local properties
   * @param $property_name
   * @param $value
   */
  function set( $property_name, $value ) {

    // if property is already an instantiated ScaleUp_Schema_Property
    if ( isset( $this->$property_name ) && is_object( $this->$property_name ) && method_exists( $this->$property_name, 'set' ) ) {
      $this->$property_name->set( 'value', $value );
      $this->_updated[] = $property_name;
      return;
    }

    if ( ScaleUp_Schemas::is_property( $property_name ) ) {
      $args = ScaleUp_Schemas::get_property( $property_name );
      if ( is_array( $args ) && !empty( $args ) ) {
        $property = new ScaleUp_Schema_Property( $args );
        $property->set( 'value', $value );
        $this->$property_name = $property;
        $this->_updated[] = $property_name;
        return;
      }
    }

    parent::set( $property_name, $value );
  }

  /**
   * Update properties
   *
   * @param $id
   */
  function update( $id ) {

    foreach ( $this->_updated as $property )
      if ( isset( $this->$property ) && is_object( $this->$property ) && method_exists( $this->$property, 'update' ) )
        $this->$property->update( $id );

  }

  function __get( $property_name ) {
    return $this->get( $property_name );
  }

  function __set( $property_name, $value ) {
    $this->set( $property_name, $value );
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