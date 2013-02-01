<?php
class ScaleUp_Schema_Type extends ScaleUp_Base {

  function __construct( $args ) {
    parent::__construct( $args );

    add_filter( 'registered_schema_types', array( $this, 'registered_schema_types' ) );
  }

  function registered_schema_types( $schemas ) {
    $schemas[ $this->get( 'id' ) ] = $this;
    return $schemas;
  }

}