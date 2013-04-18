<?php
class ScaleUp_View extends ScaleUp_Feature {

  function activation( $view, $args ) {

    /**
     * Setup process pipeline
     */
    $this->add_action( 'process', array( $this, 'do_parse_query' )     , 20 );
    $this->add_action( 'process', array( $this, 'do_query_posts' )     , 30 );
    $this->add_action( 'process', array( $this, 'do_template_redirect'), 40 );

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

    /**
     * Look in ScaleUp::activation method for list of hooks that are hooked to process action
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
    $request->query->query( $request->query_vars );
    /*** @var $wp_the_query WP_Query **/
    global $wp_the_query;
    $wp_the_query = $request->query;
  }

  /**
   * Execute template_redirect action.
   *
   * This method executes template_data filter to allow the developer to data into the template.
   * The properties in the data object will be available in the template execution scope as variables.
   *
   * Code Example @see: https://gist.github.com/taras/5408564
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function do_template_redirect( $view, $request ) {
    /**
     * Hook to template_data filter to modify data that will be passed into the template
     */
    $request->template_data = $this->apply_filters( 'template_data', $request->template_data );
    $this->do_action( 'template_redirect', $request );
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
      $template->render( $this, $request->template_data );
    }

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
      'template'  => "/$name.php",
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
  '_supports'    => array( 'forms', 'templates' ),
  '_duck_types'  => array( 'contextual', 'routable' ),
  'exclude_docs' => true,
) );