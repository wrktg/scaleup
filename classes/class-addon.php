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

}