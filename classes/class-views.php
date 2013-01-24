<?php
/**
 * ScaleUp_Views acts as a singleton class that references all other collections of views.
 * ScaleUp_Views operates in 2 modes: singleton for entire WP environment and collection of views for an application.
 */
class ScaleUp_Views {

  private static $_this;

  /**
   * Used when creating arbitrary views for WordPress
   * @var array
   */
  private static $_wp_views;

  /**
   *
   * @var array
   */
  private $_views = array();

  private $_base;

  function __construct( $context = null ) {

    if ( isset( self::$_this ) ) {
      /**
       * means we're initializing ScaleUp_Views for an app as a view storage
       * set a reference to the app that includes these views
       */
      $this->_base = $context;

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

  public static function register_view( $base, $url, $callbacks, $args = array() ) {

    /**
     * Check if the base object has get_views method.
     * ScaleUp_App implements get_views function, which allows this class to register
     */
    if ( is_object( $base ) && method_exists( $base, 'get_views' ) ) {
      $views    = $base->get_views();
      $view_url = $base->get_url() . $url;
      $view     = $views->add_view( $view_url, $callbacks, $args );
      $base->set_views( $views );
      return $view;
    }

    if ( is_string( $base ) ) {
      self::add_wp_view( $base . $url, $callbacks, $args );
    }

    return false;
  }

  /**
   * Register a url as a view for an App or Addon
   *
   * @param $url
   * @param $callbacks
   * @param $args
   * @return ScaleUp_View
   */
  function add_view( $url, $callbacks, $args ) {
    $view = new ScaleUp_View( $url, $callbacks, $args );
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

  /**
   * Adopt a specified view into list of views
   *
   * @param $view
   */
  function adopt_view( $view ) {
    $this->_views[] = $view;
  }

}