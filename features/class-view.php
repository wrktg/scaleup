<?php
class ScaleUp_View extends ScaleUp_Feature {

  function activation() {
    $this->add_action( 'headers', array( $this, 'headers' ), 20 );
  }

  /**
   * Display view by executing a sequence of following actions
   *
   *  1. request  - handle request args
   *  2. headers  - send headers to the browser
   *  3. query    - setup & execute query
   *  4. data     - prepare data
   *  5. render   - render the data
   *
   * @param array $args
   */
  function display( $args = array() ) {

    /**
     * $args array is converted to an object to allow us to pass $args by reference
     */
    $args = new ScaleUp_Args( array(
      'request'   => $args,
      'headers'   => array(),
      'query'     => null,
      'data'      => null,
      'template'  => null,
    ));

    $this->do_action( 'request',  $args );
    $this->do_action( 'headers',  $args );
    $this->do_action( 'query',    $args );
    $this->do_action( 'data',     $args );
    $this->do_action( 'render',   $args );

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
    $template = ScaleUp::add_template( wp_parse_args( array(
      'name'      => $name,
      'path'      => null,
      'template'  => null,
    ), $args ) );

    $this->add_action( 'render', array( $template, 'render' ) );

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