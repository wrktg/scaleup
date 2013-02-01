<?php
class ScaleUp_Schemas_Addon extends ScaleUp_Addon {

  function get_defaults() {
    return wp_parse_args( array(
      'url' => 'schemas',
    ), parent::get_defaults() );
  }


  function initialize() {
    register_view( 'schemas', '', array( 'GET' => array( $this, 'show_schemas' ) ), $this );
    register_view( 'schema', '/{schema_type}', array( 'GET' => array( $this, 'show_schema' ) ), $this );
    register_template( dirname( __FILE__ ) . '/templates', '/schemas.php' );
    register_template( dirname( __FILE__ ) . '/templates', '/schema.php' );
  }

  function show_schemas( $args, $view ) {
    $schema_types = ScaleUp_Schemas::get_schema_types();
    $view->set( 'schema_types', $schema_types );
    get_template_part( '/schemas.php' );
  }

  function show_schema( $args, $view ) {

    if ( isset( $args[ 'schema_type' ] ) ) {
      $schema_type = $args[ 'schema_type' ];
      if ( ScaleUp_Schemas::is_registered( $schema_type ) ) {
        $schema = get_schema( $schema_type );
        $view->set( 'schema', $schema );
        get_template_part( '/schema.php' );
      } else {
        /**
         * @todo: redirect to 404 don't know how to do that yet.
         */
      }
    } else {
      $schemas_view = get_view( 'schemas' );
      wp_redirect( $schemas_view->get_url() );
    }

  }

}

if ( function_exists( 'register_addon' ) ) {

  register_addon( 'schemas',  'ScaleUp_Schemas_Addon' );

}