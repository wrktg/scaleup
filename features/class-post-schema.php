<?php
class ScaleUp_Post_Schema extends ScaleUp_Schema {

  var $_error = false;

  function activation() {
    /** @var $context ScaleUp_Item */
    $context = $this->get( 'context' );
    $context->add_action( 'create', array( $this, 'create' ) );
    $context->add_action( 'read',   array( $this, 'read' ) );
    $context->add_action( 'update', array( $this, 'update' ) );
    $context->add_action( 'delete', array( $this, 'delete' ) );
  }

  /**
   * Create post from provided args
   *
   * @param $item ScaleUp_Item
   * @param array $args
   * @return bool
   */
  function create( $item, $args = array() ) {

    $success = false;

    /**
     * Only take args that are relevant post
     */
    $wp_post_args = array_intersect_key( $args, $this->get_defaults() );

    /**
     * load the values into the current schema
     */
    $this->load( $wp_post_args );

    $id = wp_insert_post( $wp_post_args, true );

    if ( is_wp_error( $id ) ) {
      ScaleUp::add_alert(
        array(
          'type'  => 'warning',
          'msg'   => $id->get_error_message(),
          'debug' => true,
          'data'  => $id
        )
      );
      $id = null;
    } else {
      $item->set( 'id', $id );
      $this->set( 'ID', $id );
      $success = true;
    }

    return $success;
  }

  /**
   * Read post from the database
   *
   * @param $item ScaleUp_Item
   * @param $id
   * @return bool
   */
  function read( $item, $id ) {
    $success = false;
    $post = get_post( $id, ARRAY_A);
    if ( is_null( $post ) ) {
      $error = array(
        'type'  => 'error',
        'msg'   => "Could not load post with id: $id",
        'debug' => true,
      );
      $this->add( 'alert', $error );
      $item->add( 'alert', $error );
    } else {
      $this->load( $post );
      $success = true;
    }
    return $success;
  }


  /**
   * Update post with values from args
   *
   * @param $item ScaleUp_Item
   * @param $args
   * @return bool
   */
  function update( $item, $args ) {

    $success = false;

    /**
     * If ID was passed but it doesn't match this post's ID then unset it
     */
    if ( isset( $args[ 'ID' ] ) && !is_null( $this->get( 'ID' ) ) && $this->get( 'ID') != $args[ 'ID' ] ) {
      unset( $args[ 'ID' ] );
    }

    /**
     * Only take args that are relevant post
     */
    $wp_post_args = array_intersect_key( $args, $this->get_defaults() );

    $id = wp_update_post( $wp_post_args, true );

    if ( is_wp_error( $id ) ) {
      $error = array(
        'type'  => 'error',
        'msg'   => $id->get_error_message(),
        'debug' => true,
        'data'  => $id,
      );
      $this->add( 'alert', $error );
      $item->add( 'alert', $error );
    } else {
      $item->add( 'alert', array(
        'type'  => 'info',
        'msg'   => "Item was updated."
      ));
      $success = true;
    }

    return $success;
  }

  /**
   * Delete post with id specified by $args[ 'id' ].
   * Set $args[ 'force_delete' ] to true if you want the post to skip the Trash.
   *
   * @param $item ScaleUp_Item
   * @param $args array
   * @return bool
   */
  function delete( $item, $args ) {

    $success = false;

    if ( !isset( $args[ 'id' ] ) && !is_null( $this->get( 'ID' ) ) ) {
      $args[ 'id' ] = $this->get( 'ID' );
    }

    if ( wp_delete_post( $args[ 'id' ], $args[ 'force_delete' ] ) ) {
      $item->add( 'alert', array(
        'type'  => 'info',
        'msg'   => "Deleted item with id: {$args['id']}.",
      ));
      $success = true;
    } else {
      $error = array(
        'type'  => 'error',
        'msg'   => "Failed to delete item with id: {$args['id']}.",
      );
      $item->add( 'alert', $error );
      $this->add( 'alert', $error );
    }

    return $success;
  }

  function get_defaults() {
    return wp_parse_args(
      array(
        'ID'                    => '',
        'post_author'           => '',
        'post_date'             => '',
        'post_date_gmt'         => '',
        'post_content'          => '',
        'post_title'            => '',
        'post_excerpt'          => '',
        'post_status'           => '',
        'comment_status'        => '',
        'ping_status'           => get_option( 'default_ping_status' ),
        'post_password'         => '',
        'post_name'             => '',
        'to_ping'               => '',
        'pinged'                => '',
        'post_modified'         => '',
        'post_modified_gmt'     => '',
        'post_content_filtered' => '',
        'post_parent'           => 0,
        'guid'                  => '',
        'menu_order'            => 0,
        'post_type'             => 'post',
        'post_mime_type'        => '',
        'comment_count'         => '',
      ), parent::get_defaults()
    );
  }

}

ScaleUp::register_schema(
  array(
    'name'      => 'post',
    '__CLASS__' => 'ScaleUp_Post_Schema',
  )
);