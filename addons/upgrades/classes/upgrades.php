<?php
class ScaleUp_Upgrades extends ScaleUp_Base {

  function __construct( $args = array() ) {
    parent::__construct( $args );

    $context = $this->get( '_context' );
    add_action( 'init', array( $this, 'init' ) );
  }

  /**
   * On context activation add each method in this class as an upgrade to the context item
   */
  function init() {

    $context = $this->get( '_context' );
    if ( !is_null( $context ) ) {
      $methods = $this->get_upgrades( get_class( $this ) );
      foreach ( $methods as $method ) {
        $context->add( 'upgrade', array(
          'name'        => $method[ 'name' ],
          'description' => $method[ 'description' ],
          'execute'     => array( $this, $method[ 'name' ] ),
        ) );
      }
    }

  }

  /**
   * Return array of methods for the declared class
   *
   * @param string $class
   * @return array
   */
  function get_upgrades( $class ) {

    $reflector    = new ReflectionClass( $class );
    $upgrades     = array();
    $lower_class  = strtolower( $class );
    foreach ( $reflector->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
      if ( strtolower( $method->class ) == $lower_class ) {
        $upgrade = array();
        $upgrade[ 'name' ] = $method->name;
        $upgrade[ 'description' ] = $method->getDocComment();
        $upgrades[] = $upgrade;
      }
    }

    return $upgrades;
  }

}