<?php
class ScaleUp_Global extends ScaleUp_Duck_Type {

  /**
   * Add feature into global context
   *
   * @param ScaleUp_Feature $feature
   * @param null $context
   * @return ScaleUp_Feature|void
   */
  function apply( $feature, $context ) {
    $site = ScaleUp::get_site();
    $storage = $site->get( 'features' )->get( $feature->get( '_plural' ) );
    $storage->set( $feature->get( 'name' ), $feature );
  }

}

ScaleUp::register_duck_type( 'global', array(
  '__CLASS__'     => 'ScaleUp_Global',
) );
ScaleUp::activate_duck_type( 'global' );