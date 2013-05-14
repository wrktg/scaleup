<?php
class ScaleUp_Template extends ScaleUp_Feature {

  /**
   * Contains data object properties of which are exported into scope before template is included
   *
   * @var stdClass
   */
  protected $_data;

  /**
   * Prepend this string to the beginning of template name when creating template include path
   *
   * @var string
   */
  protected $_directory;

  /**
   * Callback function for ScaleUp_View->render action
   *
   * @param string        $template_part
   * @param ScaleUp_View  $context
   * @param array        $data
   */
  function render( $template_part = null, $data = null, $context = null ) {

    $template = $this->get( 'name' );

    if ( !is_null( $template_part ) ) {
      $template .= "-$template_part";
    }

    // replace underscores with dashes
    $template = str_replace( '_', '-', "/$template.php" );

    $directory = $this->get( 'directory' );
    if ( !empty( $directory ) ) {
      $directory = "/$directory";
    }

    $paths = array(
      get_stylesheet_directory()  . $directory . $template,          // child theme
      get_template_directory()    . $directory . $template,          // parent theme
      $this->get( 'path' )        . $directory . $template,          // original
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

    global $wp_query, $post;
    /**
     * TODO: When the query is a list of posts based on a tax query
     * TODO: is_post_type_archive will otherwise be set to true and
     * TODO: it will throw errors inside wp_title().
     */
    $wp_query->is_post_type_archive = false;

    include $path;
    $this->do_action( 'after' );
  }

  /**
   * Attempt to find templates directory relative to the path of the class of $object. If found, return the path
   * otherwise return null.
   *
   * @param ScaleUp_Feature $object
   * @return string|null
   */
  static function find_templates_dir( $object ) {

    $path = null;

    $rc = new ReflectionClass(get_class( $object ));
    $dir = dirname( $rc->getFileName() );

    $paths = array(
      $dir . '/templates',              // if app class is in root directory
      dirname( $dir ) . '/templates',   // if app class in /classes directory
    );

    foreach ( $paths as $maybe ) {
      if ( is_dir( $maybe ) ) {
        $path = $maybe;
        break;
      }
    }

    return $path;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'template',
        'template'      => null,
        'path'          => SCALEUP_DIR . '/templates',
        'directory'     => null,
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
