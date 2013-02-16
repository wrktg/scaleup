<?php
class ScaleUp_Template extends ScaleUp_Feature {

  static function registration( $args ) {
    if ( isset( $args[ 'template' ] ) ) {
      $activated = ScaleUp::activate( 'template', $args );
      $hook      = "get_template_part_$args[template]";
      add_action( $hook, array( $activated, 'get_template_part' ) );
    }
  }

  /**
   * Callback for get_template_part function
   *
   * @param $template
   */
  function get_template_part( $template ) {

    // check if template exists in child theme directory
    if ( is_child_theme() && file_exists( get_stylesheet_directory() . $template ) ) {
      include( get_stylesheet_directory() . $template );
    } elseif ( file_exists( get_template_directory() . $template ) ) {
      include( get_template_directory() . $template );
    } else {
      include( $this->get( 'path' ) . $template );
    }

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