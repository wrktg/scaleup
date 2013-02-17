<?php
class ScaleUp_Contextual extends ScaleUp_Duck_Type {

  function apply( $feature, $context ) {
    parent::apply( $feature, $context );

    $feature->set( 'context', $context );
  }

}

ScaleUp::register_duck_type( 'contextual', array(
  '__CLASS__'     => 'ScaleUp_Contextual',
) );
ScaleUp::activate_duck_type( 'contextual' );