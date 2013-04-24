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
    $this->add_action( 'process', array( $this, 'do_parse_query' )        , 20 );
    $this->add_action( 'process', array( $this, 'do_query_posts' )        , 30 );
    $this->add_action( 'process', array( $this, 'do_load_template_data' ) , 40 );
    $this->add_action( 'process', array( $this, 'do_template_redirect')   , 50 );
    $this->add_action( 'process', array( $this, 'do_reset' )              , 60 );

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
  function process( $request ) {

    $site = ScaleUp::get_site();
    if ( property_exists( $site, 'view' ) && $this !== $site->view ) {
      $this->original_view = $site->view;
      $site->view = $this;
    }

    $this->last_request = $request;

    /**
     * By default, view's process action has 3 callbacks
     *  array( $this, do_parse_query ) at priority 20
     *  array( $this, do_query_posts ) at priority 30
     *  array( $this, do_template_redirect ) at priority 40
     */
    $this->do_action( 'process', $request );
  }

  /**
   * Executes parse_query action.
   *
   * parse_query is the first action that's executed when a view is being processed.
   * during parse_query action, $request->query_vars array is modified based on $request->vars.
   * Hook to this action and modify the $request->query_vars array.
   *
   * Code Example: @see: https://gist.github.com/taras/5408564
   *
   * @param $view
   * @param $request
   */
  function do_parse_query( $view, $request ) {
    /**
     * use $this->add_action( 'parse_query, 'your_callback' ) to hook your callback to this action.
     */
    $this->do_action( 'parse_query', $request );
  }

  /**
   * Executes query_posts action. During query_posts action, a new query is executed based on $request->query_vars.
   *
   * To override the default query_posts callback, unhook array( $this, 'query_posts' ) from 'query_posts' action and hook
   * your callback function.
   *
   * @param $view
   * @param $request
   */
  function do_query_posts( $view, $request ) {
    $this->do_action( 'query_posts', $request );
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
   * Execute load_template_data action.
   *
   * Hook to this action to execute a callback that is expected to populate $request->template_data array with data.
   * $request->template_data will be exported within local scope of the template include.
   *
   * Code Example @see: https://gist.github.com/taras/5408564
   *
   * @param $view
   * @param $request
   */
  function do_load_template_data( $view, $request ) {
    $this->do_action( 'load_template_data', $request );
  }

  /**
   * Execute template_redirect action.
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function do_template_redirect( $view, $request ) {
    $this->do_action( 'template_redirect', $request );
  }

  /**
   * Execute reset_query action
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function do_reset( $view, $request ) {
    $this->do_action( 'reset', $request );
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
    setup_postdata( $post );
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
    $template = $this->get_feature( 'template', $this->get( 'name' ) );
    if ( $template ) {
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

    $this->do_load_template_data( $this,  $request );
    $this->do_template_redirect(  $this,  $request );

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
    $this->process( $request );
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