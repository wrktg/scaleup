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

    if ( $feature->has( '_activate' ) ) {
      if ( in_array( 'global', $feature->get( '_activate' ) ) ) {
        $this->add_to_site( $feature );
      }
    } else {
      $this->add_to_site( $feature );
    }

    return $feature;
  }

  /**
   * Add feature to the global site
   *
   * @param ScaleUp_Feature $feature
   */
  function add_to_site( $feature ) {
    $site = ScaleUp::get_site();
    $plural = $feature->get( '_plural' );
    $features = $site->get( 'features' );
    if ( $features->has( $plural ) ) {
      $storage = $features->get( $plural );
    } else {
      $storage = new ScaleUp_Base();
      $features->set( $plural, $storage );
    }
    $storage->set( $feature->get( 'name' ), $feature );
  }

}

ScaleUp::register_duck_type( 'global', array(
  '__CLASS__'     => 'ScaleUp_Global',
) );
ScaleUp::activate_duck_type( 'global' );