<?php
class ScaleUp_View extends ScaleUp_Feature {

  /**
   * The original value of $GLOBALS['wp_query'] before query was modified
   * @var WP_Query
   */
  var $original_query = null;

  /**
   * The original value of $GLOBALS['post'] before view was rendered
   * @var WP_Post
   */
  var $original_post = null;

  /**
   * Last request that was executed in this view
   *
   * @var ScaleUp_Request
   */
  var $last_request = null;

  /**
   * View that was in $site->view before $this->process()
   *
   * @var ScaleUp_View
   */
  var $original_view = null;

  function activation( $view, $args ) {

    /**
     * Setup process pipeline
     */
    $this->add_action( 'reset', array( $this, 'reset_query' ) );
    $this->add_action( 'reset', array( $this, 'reset_post' ) );
    $this->add_action( 'reset', array( $this, 'reset_view' ) );

    // default callbacks
    $this->add_action( 'query_posts',       array( $this, 'query_posts' ) );
    $this->add_action( 'template_redirect', array( $this, 'template_redirect' ) );

    if ( !isset( $args[ 'template' ] ) && isset( $args[ 'templates_dir' ] ) ) {
      $this->add_template( $this->get( 'name' ), array(
        'templates_dir' => $args[ 'templates_dir' ]
      ));
    }

  }

  /**
   * Process $request object for this view
   *
   * @param ScaleUp_Request $request
   * @internal param array $args
   */
  function do_process( $request ) {

    $site = ScaleUp::get_site();
    if ( property_exists( $site, 'view' ) && $this !== $site->view ) {
      $this->original_view = $site->view;
      $site->view = $this;
    }

    $this->last_request = $request;

    $this->do_action( 'process',            $request );
    $this->do_action( 'parse_query',        $request );
    $this->do_action( 'query_posts',        $request );
    $this->do_action( 'load_template_data', $request );
    $this->do_action( 'template_redirect',  $request );
    $this->do_action( 'reset',              $request );

  }

  /**
   * Execute query on $request->query object and set the global $wp_the_query to $request->query.
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function query_posts( $view, $request ) {
    // set the global wp_query as backup
    $this->original_query = ( isset( $GLOBALS[ 'wp_query' ] ) ) ? $GLOBALS[ 'wp_query' ] : null ;
    $this->original_post  = ( isset( $GLOBALS[ 'post' ] ) )     ? $GLOBALS[ 'post' ] : null ;
    // Execute query with new query_vars
    if ( !empty( $request->query_vars ) ) {
      $request->query->query( $request->query_vars );
    }
    // set new query into global
    $GLOBALS['wp_query'] = $request->query;
  }

  /**
   * Set global $wp_query back to same state as before this view modified it
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function reset_query( $view, $request ) {
    $GLOBALS[ 'wp_query' ] = $this->original_query;
  }

  /**
   * Set global $post back to same state as before this view modified it
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function reset_post( $view, $request ) {
    $GLOBALS['post'] = $post = $this->original_post;
    if ( ! is_null( $post ) ) {
      setup_postdata( $post );
    }
  }

  /**
   * Set the original view into $site
   *
   * @param ScaleUp_View    $view
   * @param ScaleUp_Request $request
   */
  function reset_view( $view, $request ) {
    $site = ScaleUp::get_site();
    $site->view = $this->original_view;
  }

  /**
   * Render the template for this view
   *
   * @param ScaleUp_View    $view
   * @param ScaleUp_Request $request
   */
  function template_redirect( $view, $request ) {

    /*** @var $template ScaleUp_Template */
    if ( is_null( $this->get( 'template' ) ) ) {
      $template_name = $this->get( 'name' );
    } else {
      $template_name = $this->get( 'template' );
    }

    $template = $this->get_feature( 'template', $template_name );

    if ( $template ) {
      $template->view = $this;
      $template->render( $request->template_part, $request->template_data, $this );
    }

  }

  /**
   * Render a template_part while using the query in this view
   *
   * @param string $template_part
   * @param array $args
   */
  function render_template_part( $template_part, $args = array() ) {
    // check if this view previously processed a request
    if ( is_null( $this->last_request ) ) {
      // if not, create a new request with provided $template_part
      $request = new ScaleUp_Request(
        array(
          'template_part' => $template_part,
        )
      );
      $last_template_part = null;
    } else {
      $request = $this->last_request; // reuse
      $last_template_part = $request->template_part;
      $request->template_part = $template_part;
    }

    /**
     * Set into template data to make it available during template include
     */
    $request->template_data[ 'args' ] = $args;

    $this->do_action( 'load_template_data', $request );
    $this->do_action( 'template_redirect',  $request );

    if ( is_null( $last_template_part ) ) {
      unset( $request ); // cleanup
    } else {
      $request->template_part = $last_template_part; // reset the old template_part
    }

  }

  /**
   * Render this view from vars
   *
   * @param array  $vars
   * @param array  $args
   */
  function render( $vars = array(), $args = array() ) {
    $request = new ScaleUp_Request( $vars, $args );
    $this->do_process( $request );
  }

  /**
   * Add a template and return its instance.
   *
   * This function also hooks activated template to render action of this view.
   * When render is executed, the newly created template will be rendered.
   *
   * @param string $name
   * @param array $args
   * @return ScaleUp_Template|bool
   */
  function add_template( $name, $args = array() ) {

    if ( isset( $args[ 'templates_dir' ] ) ) {
      $path = $args[ 'templates_dir' ];
      unset( $args[ 'templates_dir' ] );
    } else {
      $path = null;
    }

    $template = ScaleUp::add_template( wp_parse_args( array(
      'name'      => $name,
      'path'      => $path,
    ), $args ) );

    return $template;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'url'           => '',
        '_feature_type' => 'view',
      ), parent::get_defaults() );
  }

}

ScaleUp::register_feature_type( 'view', array(
  '__CLASS__'    => 'ScaleUp_View',
  '_plural'      => 'views',
  '_supports'    => array( 'forms', 'templates', 'assets' ),
  '_duck_types'  => array( 'contextual', 'routable' ),
  'exclude_docs' => true,
) );
