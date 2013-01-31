<?php
/**
 * @see http://php.net/manual/en/class.arrayaccess.php
 */
class ScaleUp_Schema extends ScaleUp_Base implements ArrayAccess {

  protected $_updated = array();

  protected $_schema_type;


  /**
   * During initialization, ScaleUp_Schema takes an array of properties as an argument.
   * It stores these arguments in internal storage to be used when an $this->update is executed.
   * This is done to differentiate between values that are provided by the user from those that are loaded during
   * initialization. This makes it possible to distinguish between fields that need to be updated when saving to the
   * database.
   *
   * @param null $schema_type string
   * @param array $args array
   */
  function __construct( $schema_type = null, $args = array() ) {
    $this->_schema_type = $schema_type;
    if ( !is_null( $schema_type ) ) {
      $properties = ScaleUp_Schemas::get_properties( $schema_type );
      foreach ( $properties as $property_name => $args ) {
        $this->$property_name = new ScaleUp_Schema_Property( $property_name, $args );
        unset( $value );
      }
    }
  }

  /**
   * Read values from the database into each property in the schema
   *
   * @param $object_id
   * @param array $properties
   */
  function read( $object_id, $properties = array() ) {
    if ( empty( $properties ) ) {
      if ( isset( $this->_schema_type ) ) {
        $properties = ScaleUp_Schemas::get_properties( $this->_schema_type );
        if ( is_array( $properties ) ) {
          $properties = array_keys( $properties );
        } else {
          $properties = array();
        }
      }
    }
    foreach ( $properties as $property_name ) {
      if ( isset( $this->$property_name) && is_object( $this->$property_name ) && method_exists( $this->$property_name, 'read' ) ) {
        $this->$property_name->read( $object_id );
      }
    }
  }

  /**
   * Set local properties. Set $update to false if you do not want it to be recorded as changed.
   *
   * @param $property_name
   * @param $value bool
   * @param bool $update
   */
  function set( $property_name, $value, $update = true ) {

    // if property is already an instantiated ScaleUp_Schema_Property
    if ( isset( $this->$property_name ) && is_object( $this->$property_name ) && method_exists( $this->$property_name, 'set' ) ) {
      $this->$property_name->set( 'value', $value );
      if ( $update ) {
        $this->_updated[] = $property_name;
      }
      return;
    }

    if ( ScaleUp_Schemas::is_property( $property_name ) ) {
      if ( is_object( $value ) ) {
        $this->$property_name = $value;
        return;
      } else {
        $args = get_property_reference( $property_name );
        if ( is_array( $args ) && !empty( $args ) ) {
          $property = new ScaleUp_Schema_Property( $args );
          $property->set( 'value', $value );
          $this->$property_name = $property;
          if ( $update ) {
            $this->_updated[] = $property_name;
          }
          return;
        }
      }
    }

  }

  function get( $property_name ) {
    $value = null;
    if ( isset( $this->$property_name ) ) {
      $value = $this->$property_name;
    }
    return $value;
  }

  /**
   * Update properties
   *
   * @param $id
   */
  function update( $id ) {

    foreach ( $this->_updated as $property )
      if ( isset( $this->$property ) && is_object( $this->$property ) && method_exists( $this->$property, 'update' ) ) {
        $this->$property->update( $id );
      }

  }

  /**
   * Return array of properties in this schema instance
   *
   * @todo: Implement get_properties function
   * @return array|null
   */
  function get_properties() {
    $properties = null;

    return $properties;
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