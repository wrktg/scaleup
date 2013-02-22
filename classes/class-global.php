<?php
class ScaleUp_Global extends ScaleUp_Duck_Type {

  /**
   * Add feature into global context
   *
   * @param ScaleUp_Feature $feature
   * @param array $args
   * @return ScaleUp_Feature|void
   */
  function duck_types( $feature, $args = array() ) {
    parent::duck_types( $feature, $args );

    $site = ScaleUp::get_site();
    /** @var $storage ScaleUp_Base */
    $plural = $feature->get( '_plural' );
    $features = $site->get( 'features' );
    if ( $features->has( $plural ) ) {
      $storage = $features->get( $plural );
    } else {
      $storage = new ScaleUp_Base();
      $features->set( $plural, $storage );
    }
    $storage->set( $feature->get( 'name' ), $feature );

    return $feature;
  }

}

ScaleUp::register_duck_type( 'global', array(
  '__CLASS__'     => 'ScaleUp_Global',
) );
ScaleUp::activate_duck_type( 'global' );