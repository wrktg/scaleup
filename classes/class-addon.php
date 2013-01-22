<?php
class ScaleUp_Addon {

  protected $_views;

  protected $_base;

  function get_views() {
    return $this->_views;
  }

  function set_views( $views ) {
    $this->_views = $views;
  }

  function get_url() {
    if ( is_object( $this->_base ) && method_exists( $this->_base, 'get_url' ) ) {
      return $this->_base->get_url() . $this->_url;
    } else {
      return $this->_base . $this->_url;
    }
  }

}