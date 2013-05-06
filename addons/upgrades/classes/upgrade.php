<?php
class ScaleUp_Upgrade extends ScaleUp_Feature {

  function activation() {
    if ( $this->has( 'execute' ) && is_callable( $this->get( 'execute' ) ) ) {
      $callable = $this->get( 'execute' );
      $this->add_action( 'execute', $callable );
    }
  }

  /**
   * Execute the upgrade with passed object
   *
   * @param object $args
   */
  function execute( $args ) {
    $this->do_action( 'execute', $args );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'upgrade',
      ), parent::get_defaults()
    );
  }

}

ScaleUp::register_feature_type( 'upgrade', array(
  '__CLASS__'     => 'ScaleUp_Upgrade',
  '_plural'       => 'upgrades',
  '_container'    => 'ScaleUp_Upgrades',
  '_duck_types'   => array( 'contextual' ),
));

ScaleUp::extend_feature_type( array(
  'app' => array(
    '_supports' => array( 'upgrades' ),
  ),
  'addon' => array(
    '_supports' => array( 'upgrades' ),
  )
));