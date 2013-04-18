<?php
class ScaleUp_Template extends ScaleUp_Feature {

  private static $_activated = array();

  /**
   * Contains data object properties of which are exported into scope before template is included
   *
   * @var stdClass
   */
  var $_data;

  function activation() {

    $this->_data = new stdClass();

    $template = $this->get( 'template' );
    $tag      = "get_template_part_{$template}";
    $callback = array( 'ScaleUp_Template', 'get_template_part' );
    if ( !in_array( $tag, self::$_activated ) ) {
      add_action( $tag, $callback, 10, 2 );
      self::$_activated[] = $tag;
    }
  }

  /**
   * Callback function for ScaleUp_View->render action
   *
   * @param null $context
   * @param object $data
   */
  function render( $context = null, $data = null ) {

    $template = $this->get( 'template' );
    if ( empty( $template ) ) {
      $template = "/$template.php";
    }

    $paths = array(
      get_stylesheet_directory()  . $template,          // child theme
      get_template_directory()    . $template,          // parent theme
      $this->get( 'path' )        . $template,          // original
    );

    if ( is_null( $data ) ) {
      $data = $this->get( 'data' );
    }

    $found = false;
    foreach ( $paths as $path ) {
      if ( $found = file_exists( $path ) ) {
        $this->do_render( $path, $data );
        break;
      }
    }

    /** @var $found bool */
    if ( !$found ) {
      ScaleUp::add_alert( array(
        'type'  => 'warning',
        'msg'   => "Template $template not found.",
        'wrong' => $template,
        'debug' => true,
      ));
    }

  }

  /**
   * Execute render action and include the template at $path. $data object is exported into local scope.
   *
   * @param string $path
   * @param object $data
   */
  private function do_render( $path, $data ) {
    extract( get_object_vars( $data ), EXTR_REFS );
    $this->do_action( 'render' );
    include $path;
    $this->do_action( 'after' );
  }

  /**
   * Find template by $slug and $name and output it to the screen
   *
   * @callback get_template_part
   * @param string $name
   * @param string $slug
   */
  static function get_template_part( $slug, $name = null ) {

    $template_name = $slug;
    if ( !is_null( $name ) && !empty( $name ) ) {
      $template_name .= "-$name";
    }
    $template_name = ScaleUp::slugify( $template_name );

    $template = ScaleUp::get_template( $template_name );

    if ( $template ) {
      $template->render();
    }

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
        'template'      => null,
        'path'          => null,
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'template',
  array(
    '__CLASS__'   => 'ScaleUp_Template',
    '_plural'     => 'templates',
    '_duck_types' => array( 'global' ),
    '_supports'   => array( 'assets' ),
    '_bundled'    => array(
      'assets' => array(
        'bootstrap_base'   => array(
          'type' => 'style',
          'src'  => '/scaleup/templates/libraries/bootstrap/bootstrap-base.css',
        ),
      )
    )
  ) );