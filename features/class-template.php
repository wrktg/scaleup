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

    /**
     * This is only here for compatibility
     * @todo: remove this in the future.
     */
    if ( $this->get( 'template' ) ) {
      $name = $this->get( 'template' );
    } else {
      $name = $this->get( 'name' );
    }
    $tag      = "get_template_part_{$name}";
    if ( !in_array( $tag, self::$_activated ) ) {
      add_action( $tag, array( 'ScaleUp_Template', 'get_template_part' ), 10, 2 );
      self::$_activated[] = $tag;
    }
  }

  /**
   * Callback function for ScaleUp_View->render action
   *
   * @param string        $template_part
   * @param ScaleUp_View  $context
   * @param object        $data
   */
  function render( $template_part = null, $data = null, $context = null ) {

    $template = $this->get( 'name' );

    if ( !is_null( $template_part ) ) {
      $template .= "-$template_part";
    }

    // replace underscores with dashes
    $template = str_replace( '_', '-', "/$template.php" );


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
        $this->do_render( $path, $data, $template_part = null );
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
   * @param string $template_part that we're rendering
   */
  private function do_render( $path, $data, $template_part = null ) {
    if ( is_array( $data ) ) {
      extract( $data, EXTR_REFS & EXTR_PREFIX_IF_EXISTS, 'my_' );
    } elseif ( is_object( $data ) ) {
      extract( get_object_vars( $data ), EXTR_REFS & EXTR_PREFIX_IF_EXISTS, 'my_' );
    }

    $this->do_action( 'render' );
    include $path;
    $this->do_action( 'after' );
  }

  /**
   * Render template by specifying the name of the template and part that you'd like to render.
   *
   * This static function is a callback for get_template_part. get_template_part's arguments $slug and $name were renamed
   * to $template_name and $template_part_name because $slug and $name is just super confusing.
   *
   * @callback get_template_part
   * @param string $template_name
   * @param string $template_part_name
   */
  static function get_template_part( $template_name, $template_part_name = null ) {

    $template = ScaleUp::get_template( $template_name );

    if ( $template ) {
      $template->render( $template_part_name );
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
    '_supports'   => array( 'assets', 'template_parts' ),
    '_bundled'    => array(
      'assets' => array(
        'bootstrap_base'   => array(
          'type' => 'style',
          'src'  => '/scaleup/templates/libraries/bootstrap/bootstrap-base.css',
        ),
      )
    )
  ) );