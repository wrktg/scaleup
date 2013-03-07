<?php
class ScaleUp_Taxonomy extends ScaleUp_Feature {

  function activation() {
    $schema = $this->get( 'context' );
    $schema->add_action( 'on_item_create', array( $this, 'on_item_update' ) );
    $schema->add_action( 'on_item_read', array( $this, 'on_item_read' ) );
    $schema->add_action( 'on_item_update', array( $this, 'on_item_update' ) );
  }

  /**
   * Setup this object from arguments. Return true if setup was executed successfully otherwise return false.
   *
   * @param $args
   * @return bool
   */
  function setup( $args ) {

    $successful = true;

    if ( !$this->has( 'item_id' ) || is_null( $this->has( 'item_id' ) ) ) {
      $item_id = (int)$args[ 'id' ];
      $this->set( 'item_id', $item_id );
    }
    if ( isset( $args[ $this->get( 'name' ) ] ) ) {
      $given = $args[ $this->get( 'name' ) ];
      if ( is_array( $given ) ) {
        if ( isset( $given[ 'name' ] ) ) {
          $this->set( 'by', 'name' );
          $this->set( 'value', $given[ 'name' ] );
        } elseif ( isset( $given[ 'id' ] ) ) {
          $this->set( 'by', 'id' );
          $this->set( 'value', $given[ 'id' ] );
        } else {
          ScaleUp::add_alert(
            array(
              'type'  => 'warning',
              'msg'   => 'Could not determine value that was passed to a ScaleUp Taxonomy CRUD function.',
              'debug' => true,
              'wrong' => $given,
            )
          );
          $successful = false;
        }
        if ( isset( $given[ 'append' ] ) ) {
          $this->set( 'append', $given[ 'append' ] );
        }
      } elseif ( is_int( $given ) ) {
        $this->set( 'by', 'id' );
        $this->set( 'value', $given );
      } elseif ( is_string( $given ) ) {
        $this->set( 'by', 'name' );
        $this->set( 'value', $given );
      } else {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => 'Could not determine value that was passed to a ScaleUp Taxonomy CRUD function.',
            'debug' => true,
            'wrong' => $given,
          )
        );
        $successful = false;
      }
    } else {
      /**
       * There is nothing to do, so let's return false
       */
      $successful = false;
    }

    return $successful;
  }

  /**
   * Add this item to the taxonomy.
   * This method is a callback for ScaleUp_Item "create" & "update" hooks.
   *
   * @param $schema
   * @param array $args
   */
  function on_item_update( $schema, $args = array() ) {
    $result  = null;
    $item_id = $this->get( 'item_id' );
    if ( $this->setup( $args ) && $item_id ) {
      $term = wp_set_post_terms( $item_id, $this->get( 'value' ), $this->get( 'taxonomy' ), $this->get( 'append' ) );
      if ( is_array( $term ) ) {
        /**
         * the term was created
         */
        $result = $term;
      } elseif ( false === $term ) {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => 'Item could not be added to the taxonomy term because post id is invalid.',
            'wrong' => $item_id,
            'debug' => true
          )
        );
      } elseif ( is_wp_error( $term ) ) {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => $term->get_error_messages( 'invalid_taxonomy' ),
            'wrong' => $term,
            'data'  => $term->get_error_data( 'invalid_taxonomy' ),
          )
        );
      }
    }

    $this->set( 'value', $result );
  }

  /**
   * Load terms associated with the item and set the result to value property of this object
   *
   * @param $schema
   * @param array $args
   */
  function on_item_read( $schema, $args = array() ) {
    $result = null;
    if ( $this->setup( $args ) ) {
      $item_id  = $this->get( 'item_id' );
      $taxonomy = $this->get( 'taxonomy' );
      $result   = wp_get_post_terms( $item_id, $taxonomy );
    }
    $this->set( 'value', $result );
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'by'            => 'id',
        'append'        => false,
        'output'        => ARRAY_A,
        'filter'        => 'raw',
        '_feature_type' => 'taxonomy',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'taxonomy', array(
  '__CLASS__'   => 'ScaleUp_Taxonomy',
  '_plural'     => 'taxonomies',
  '_duck_types' => array( 'global', 'contextual' ),
) );