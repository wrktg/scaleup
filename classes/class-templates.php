<?php
class ScaleUp_Templates {

  private static $_this;

  private static $_templates = array();

  private static $_posts = array();

  function __construct() {
    self::$_this = $this;
    add_action( 'template_include', array( $this, 'template_include' ) );
  }

  static function this() {
    return self::$_this;
  }

  static function template_include( $template ) {

    if ( ( is_single() || is_page() ) && self::$_this->has_template( get_the_ID() ) ) {
      $located_template = '';
      $scaleup_template = self::$_this->get_template( get_the_ID() );
      if ( $scaleup_template )
        $located_template = self::$_this->locate_template( $scaleup_template );
      if ( $located_template )
        $template = $located_template;
    }

    return $template;
  }

  /**
   * Callback function for get_template_part_$slug hook
   * Includes requested template
   *
   * @param $template_name
   */
  static function get_template_part( $template_name ) {

    $template = self::$_this->get( $template_name );

    if ( self::$_this->template_exists( $template ) ) {
      $template_path = self::$_this->locate_template( $template );
      include( $template_path );
    }

  }

  /**
   * Return template by name
   *
   * @param $template_name
   * @return array|false
   */
  function get( $template_name ) {

    if ( isset( $this->_templates[ $template_name ] ) ) {
      return $this->_templates[ $template_name ];
    }

    return false;
  }

  /**
   * Apply a specific template to a post
   *
   * @param $post_id int
   * @param $template
   */
  function apply( $post_id, $template ) {
    $this->_posts[ $post_id ] = $template;
  }

  /**
   * Return true if post with id $post_id has a ScaleUp Template
   *
   * @param $post_id int
   * @return bool
   */
  function has_template( $post_id ) {
    return ( !empty( self::$_this->_posts ) && in_array( $post_id, array_keys( self::$_this->_posts ) ) );
  }

  /**
   * Return template assigned to specific post_id
   *
   * To get the final template after applying Template Hierarchy, you must run the result of thing function on
   * $this->locate_template function. eg: $this->locate_template( $this->get_template( $post_id ) )
   *
   * @param $post_id
   * @return array|false array containing path and template or false if not found
   */
  function get_template( $post_id ) {
    if ( isset( self::$_this->_posts[ $post_id ] ) ) {
      $template = self::$_this->_posts[ $post_id ];
      $template = self::$_this->_templates[ $template ];

      return $template;
    }

    return false;
  }

  /**
   * Returns path to template after applying the Template Hierarchy
   *
   * Template Hierarchy:
   *      1. Child theme template
   *      2. Parent theme template
   *      3. Original template
   *
   * @param $template array path to located template
   * @return string
   */
  function locate_template( $template ) {

    $theme_template = $template;

    // check if template exists in child theme directory
    if ( is_child_theme() ) {
      $theme_template[ 'path' ] = get_stylesheet_directory();
      if ( $this->template_exists( $theme_template ) ) {
        return $this->make_path( $theme_template );
      }
    }

    // check if template exists in parent theme directory
    $theme_template[ 'path' ] = get_template_directory();
    if ( $this->template_exists( $theme_template ) ) {
      return $this->make_path( $theme_template );
    }

    return $this->make_path( $template );
  }

  /**
   * Register a template at specified path to ScaleUp Templates
   * @param $path string absolute path prefix to $template ( without trailing slash )
   * @param $template_name string prefix with / forward slash
   */
  function register( $path, $template_name ) {
    $this->_templates[ $template_name ] = array(
      'path' => $path,
      'template' => $template_name
    );
    add_action( "get_template_part_$template_name", array( $this, 'get_template_part' ) );
  }

  /**
   * Combine template path and template name and return complete path
   *
   * @param $template array with elements path and template
   * @return string absolute path to the template
   */
  private function make_path( $template ) {

    $path = '';
    if ( !is_array( $template ) )
      return $path;

    if ( !isset( $template[ 'path' ] ) && !isset( $template[ 'template' ] ) )
      return $path;

    return $template[ 'path' ] . $template[ 'template' ];
  }

  /**
   * Return true if template exists, otherwise return false
   *
   * @param $template array with path element and template element
   * @return bool
   */
  private function template_exists( $template ) {
    $template_path = $this->make_path( $template );

    return ( !empty( $template_path ) && file_exists( $template_path ) );
  }

}