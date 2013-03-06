<?php
class ScaleUp_Contextual extends ScaleUp_Duck_Type {

  function duck_types( $feature, $args = array() ) {
    parent::duck_types( $feature, $args );
    if ( isset( $args[ 'context' ] ) && is_object( $args[ 'context' ] ) ) {
      $feature->set( 'context', $args[ 'context' ] );
    }

    return $feature;
  }

}
ScaleUp::register_duck_type( 'contextual', array(
  '__CLASS__'     => 'ScaleUp_Contextual',
) );
ScaleUp::activate_duck_type( 'contextual' );