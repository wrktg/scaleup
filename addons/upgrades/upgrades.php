<?php
class ScaleUp_Upgrades_Addon extends ScaleUp_Addon {

  function activation() {
    $context      = $this->get( 'context' );
    $context_name = $context->get( 'name' );
    $this->set( 'option_key', "{$context_name}_upgrades" );


    $upgrades_view  = $this->add_view( 'upgrades',  '/upgrades' );
    $upgrades_view->add_action( 'process',              array( $this, 'process' ) );
    $upgrades_view->add_action( 'load_template_data',   array( $this, 'load_template_data' ) );

  }

  /**
   * Process request
   *
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function process( $view, $request ) {

    if ( 'GET' == $request->method ) {
      return;
    }

    $return = ( object ) array( 'success' => false, 'data' => array() );

    $executed_upgrades = $this->get_executed_upgrades();

    if ( isset( $request->vars[ 'execute' ] ) ) {

      $context = $this->get( 'context' );

      $data = (object) array(
        'success' => true,
        'data'    => array(),
        'args'    => $request->vars,
        'alerts'  => array(),
      );

      foreach ( $request->vars[ 'execute' ] as $upgrade_name ) {
        if ( !in_array( $upgrade_name, $executed_upgrades ) ) {
          $upgrade = $context->get_feature( 'upgrade', $upgrade_name );
          if ( is_object( $upgrade ) ) {
            $upgrade->execute( $data );
          }
          if ( $data->success ) {
            $executed_upgrades[] = $upgrade_name;
            $saved = update_option( $this->get( 'option_key' ), $executed_upgrades );
          }
        }
      }

      $request->template_data = $data;
      $view->remove_action( 'template_redirect', array( $view, 'template_redirect' ) );
      $view->add_action( 'template_redirect',    array( $this, 'template_redirect' ) );
    }

  }


  /**
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function load_template_data( $view, $request ) {
    $form = $this->setup_form();
    switch( $request->method ) :
      case 'GET':
        $request->template_data[ 'applied_upgrades' ] =  $this->get_executed_upgrades();
      case 'POST':
        $form->process( $request->vars );
    endswitch;
  }

  /**
   * @param ScaleUp_View $view
   * @param ScaleUp_Request $request
   */
  function template_redirect( $view, $request ) {
    if ( $request->template_data ) {
      header('Content-Type: application/json');
      echo json_encode( $request->template_data );
    }

  }

  function setup_form() {

    $form = add_form( array(
      'name' => 'upgrades',
    ));

    /** @var $context ScaleUp_Feature */
    $context  = $this->get( 'context' );
    $upgrades = $context->get_features( 'upgrades' );

    $executed_upgrades = $this->get_executed_upgrades();

    if ( is_array( $upgrades ) && !empty( $upgrades ) ) {
      $options = array();
      /** @var $upgrade ScaleUp_Upgrade */
      foreach ( $upgrades as $name => $upgrade ) {
        if ( !in_array( $name, $executed_upgrades ) ) {
          $options[$name] = $name;
        }
      }
      add_form_field( $form, array(
        'name'     => 'execute',
        'type'     => 'checkbox',
        'label'    => 'Available Upgrades',
        'options'  => $options,
        'help'     => $upgrade->get( 'description' ),
      ));
      add_form_field( $form, array(
        'name'    => 'button',
        'type'    => 'button',
        'text'    => 'execute',
      ));
    } else {
      $form->add( 'alert', array(
        'type'  => 'info',
        'msg'   => "You don't have any upgrades yet."
      ));
    }

    return $form;
  }

  function get_executed_upgrades() {
    $option_key        = $this->get( 'option_key' );
    $executed_upgrades = get_option( $option_key );
    if ( false === $executed_upgrades ) {
      $executed_upgrades = array();
    }
    return $executed_upgrades;
  }

}

ScaleUp::register( 'addon', array( 'name' => 'upgrades', '__CLASS__' => 'ScaleUp_Upgrades_Addon' ) );

include( dirname( __FILE__ ) . '/classes/upgrade.php' );
include( dirname( __FILE__ ) . '/classes/upgrades.php' );
