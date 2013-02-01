<?php
/**
 * ScaleUp_Views acts as a singleton class that references all other collections of views.
 * ScaleUp_Views operates in 2 modes: singleton for entire WP environment and collection of views for an application.
 */
class ScaleUp_Views {

  private static $_this;

  /**
   * Stores all instances of views
   *
   * @var array
   */
  private static $_global_views = null;

  /**
   * Contains views for an instance
   *
   * @var array
   */
  private $_views = array();

  /**
   * Contains reference to instance's container like an App
   *
   * @var null
   */
  private $_context = null;

  function __construct( $context = null ) {

    if ( isset( self::$_this ) ) {
      /**
       * means we're initializing ScaleUp_Views for an app as a view storage
       * set a reference to the app that includes these views
       */
      $this->_context = $context;

    } else {
      /**
       * being initialized the first time and $_storage will contain array of ScaleUp_Views instances
       */
      self::$_this = $this;
    }

  }

  /**
   * Returns singleton instance of this class.
   */
  public static function this() {
    return self::$_this;
  }

  public static function register_view( $slug, $url, $callbacks, $context = null, $args = array() ) {

    /**
     * Check if the base object has get_views method.
     * ScaleUp_App implements get_views function, which allows this class to register
     */
    if ( is_object( $context ) && method_exists( $context, 'get_views' ) ) {
      $views    = $context->get_views();
      $view     = $views->add_view( $slug, $url, $callbacks, $context, $args );
      $context->set_views( $views );
      return $view;
    }

    return false;
  }

  /**
   * ScaleUp_Views is a container of views and doesn't have a url prefix by itself therefore it either return's
   * WordPress's home url or gets url from context if its provided.
   *
   * @param null $context
   * @return string|void
   */
  static function get_url( $context = null ) {
    if ( is_null( $context ) )
      return home_url();

    if ( is_object( $context ) && method_exists( $context, 'get_url' ) )
      return $context->get_url();

    if ( is_string( $context ) ) // not sure when this would happen
      return $context;

    /**
     * @todo: if get_url was called with a context that could not be matched to anything then we should give an error
     */
    return null;
  }

  /**
   * Return view by specific name
   *
   * @param $name
   * @param null $context
   * @return ScaleUp_View|bool
   */
  static function get_view( $name, $context = null ) {

    if ( !is_null( $context ) && is_object( $context ) && method_exists( $context, 'get_view' ) )
      return $context->get_view( $name );

    /**
     * Lazy load global views
     */
    if ( is_null( self::$_global_views ) ) {
      self::$_global_views = apply_filters( 'register_view', array() );
      if ( isset( self::$_global_views[ $name ] ) )
        return self::$_global_views[ $name ];
    }

    return false;
  }

  /**
   * Register a url as a view for an App or Addon
   *
   * @param $slug
   * @param $url
   * @param $callbacks
   * @param null $context
   * @param $args
   * @return ScaleUp_View
   */
  function add_view( $slug, $url, $callbacks, $context = null, $args = null ) {
    $view = new ScaleUp_View( $slug, $url, $callbacks, $context, $args );
    $this->_views[] = $view;
    return $view;
  }

  /**
   * Register a url as WordPress view
   *
   * @param $url
   * @param $callbacks
   * @param $args
   * @return ScaleUp_View
   */
  public static function add_wp_view( $url, $callbacks, $args ) {
    self::$_wp_views[] = $view = new ScaleUp_View( $url, $callbacks, $args );
    return $view;
  }

}