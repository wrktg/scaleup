<?php
class ScaleUp_Template extends ScaleUp_Feature {

  static function registration( $args ) {
    if ( isset( $args[ 'template' ] ) ) {
      $hook = "get_template_part_$args[template]";
      add_action( $hook, array( __CLASS__, 'get_template_part' ) );
    }
  }

  function get_template_part( $template ) {
    echo $template;
    

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'template',
  array(
    '__CLASS__'     => 'ScaleUp_Template',
    '_feature_type' => 'template',
    '_plural'       => 'templates',
    '_duck_types'   => array( 'global' ),
  ) );