<?php
class ScaleUp_Post_Schema extends ScaleUp_Schema {

  /**
   * Create post from provided args
   *
   * @param $item ScaleUp_Item
   * @param array $args
   * @return bool
   */
  function on_item_create( $item, $args = array() ) {

    $success = false;

    /**
     * Only take args that are relevant post
     */
    $wp_post_args = array_intersect_key( $args, $this->get_defaults() );

    $wp_post_args = wp_parse_args( $wp_post_args, array(
      'post_title'  => 'Automatically generated post',
      'post_content'=> 'placeholder content',
      'post_status' => 'draft',
    ));

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
  function on_item_read( $item, $id ) {
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
  function on_item_update( $item, $args ) {

    $success = false;

    /**
     * If ID was passed but it doesn't match this post's ID then unset it
     */
    if ( isset( $args[ 'ID' ] ) && 0 != (int) $this->get( 'ID' ) ) {
      unset( $args[ 'ID' ] );
    }

    /**
     * Only take args that are relevant post
     */
    $wp_post_args = array_intersect_key( $args, $this->get_defaults() );

    if ( sizeof( $wp_post_args ) > 1 ) {
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
    } else {
      /**
       * nothing to be done because there is only 1 parameter, which I assume is ID
       */
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
  function on_item_delete( $item, $args ) {

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
        'post_thumbnail'        => 0,
      ), parent::get_defaults()
    );
  }

  static function scaleup_init() {
    ScaleUp::register( 'schema',
      array(
        'name'      => 'post',
        '__CLASS__' => 'ScaleUp_Post_Schema',
      )
    );
  }

}
add_action( 'scaleup_init', array( 'ScaleUp_Post_Schema', 'scaleup_init' ) );