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
    $successful = false;

    if ( isset( $args[ 'ID' ][ 'value' ] ) ) {
      $this->set( 'ID', $args[ 'ID' ][ 'value' ] );
      $this->set( 'error', false ); // reset error flag

      $name = $this->get( 'name' );

      if ( isset( $args[ $name ] ) ) {
        $given = $args[ $name ];
        $this->load( $given );
        $successful = true;
        }
      }

    return $successful;
  }

  /**
   * Add this item to the taxonomy. Callback for ScaleUp_Item "create" & "update" hooks.
   *
   * @param array $args
   * @return array
   */
  function on_item_update( $args = array() ) {

    if ( $this->setup( $args ) ) {
      $ID       = $this->get( 'ID' );
      $value    = $this->get( 'value' );
      $taxonomy = $this->get( 'taxonomy' );
      $append   = $this->get( 'append' );
      $term = wp_set_post_terms( $ID, $value , $taxonomy, $append );
      if ( is_array( $term ) ) {
        /**
         * the term was created
         */
        $name   = $this->get( 'name' );
        $args[ $name ][ 'taxonomy_id' ] = $term;
        $this->set( 'id', $term );
      } elseif ( false === $term ) {
        ScaleUp::add_alert(
          array(
            'type'  => 'warning',
            'msg'   => 'Item could not be added to the taxonomy term because post id is invalid.',
            'wrong' => $ID,
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

    return $args;
  }

  /**
   * Load terms associated with the item and set the result to value property of this object
   *
   * @param   array $args
   * @return  array
   */
  function on_item_read( $args ) {

    if ( $this->setup( $args ) ) {
      $ID       = $this->get( 'ID' );
      $taxonomy = $this->get( 'taxonomy' );
      $terms    = wp_get_post_terms( $ID, $taxonomy );
      $name     = $this->get( 'name' );

      $args[ $name ][ 'value' ] = $terms;
      $this->set( 'value', $terms );
    }

    return $args;
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