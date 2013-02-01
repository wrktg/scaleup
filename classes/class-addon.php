<?php
class ScaleUp_Addon extends ScaleUp_Routable {

  var $_views;

  function get_defaults() {
    return wp_parse_args(
      array(
           'views'    => new ScaleUp_Views( $this ),
           'exclude_route'  => true,
      ), parent::get_defaults() );
  }

  function get_views() {
    return $this->_views;
  }

  function set_views( $views ) {
    $this->_views = $views;
  }

}