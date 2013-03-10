<?php
class ScaleUp_Post_Schema extends ScaleUp_Schema {

  function activation() {
    parent::activation();

    /** @var $context ScaleUp_Item */
    $item = $this->get( 'context' );
    $item->add_filter( 'create', array( $this, 'set_post_thumbnail' ) );
    $item->add_filter( 'read',   array( $this, 'get_post_thumbnail' ) );
    $item->add_filter( 'update', array( $this, 'set_post_thumbnail' ) );
  }

  /**
   * Create post from provided args
   *
   * @param   array $args
   * @return  array
   */
  function on_item_create( $args ) {

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

    $ID = wp_insert_post( $wp_post_args, true );

    if ( is_wp_error( $ID ) ) {
      ScaleUp::add_alert(
        array(
          'type'  => 'warning',
          'msg'   => $ID->get_error_message(),
          'debug' => true,
          'data'  => $ID
        )
      );
      $ID = null;
    } else {
      $this->set( 'ID', $ID );
      /**
       * Add ID to $args to inform all downstream features of the created post
       */
      $args[ 'ID' ] = apply_filters( 'scaleup_normalize_value', $ID );
      $args[ 'ID' ][ 'post' ] = $this;
    }

    return $args;
  }

  /**
   * Read post from the database
   *
   * @param   array $args
   * @return  array
   */
  function on_item_read( $args ) {
    if ( $this->setup( $args ) ) {
      $ID = $this->get( 'ID' );
      $post = get_post( $ID, ARRAY_A);
      if ( is_null( $post ) ) {
        $error = array(
          'type'  => 'error',
          'msg'   => "Could not load post with id: $ID",
          'debug' => true,
        );
        $this->add( 'alert', $error );
        /**
         * Since the item could not loaded then let's remove ID from args so nothing downstream will try to laod it
         */
        unset( $args[ 'ID' ] );
      } else {
        $this->load( $post );
      }
    }
    return $args;
  }


  /**
   * Update post with values from args
   *
   * @param array $args
   * @return array
   */
  function on_item_update( $args ) {

    if ( $this->setup( $args ) ) {
      /**
       * Only take args that are relevant post
       */
      $wp_post_args = array_intersect_key( $args, $this->get_defaults() );

      if ( sizeof( $wp_post_args ) > 1 ) {
        $ID = wp_update_post( $wp_post_args, true );

        if ( is_wp_error( $ID ) ) {
          $error = array(
            'type'  => 'error',
            'msg'   => $ID->get_error_message(),
            'debug' => true,
            'data'  => $ID,
          );
          $this->add( 'alert', $error );
        } else {
          $item = $this->get( 'context' );
          $item->add( 'alert', array(
            'type'  => 'info',
            'msg'   => "Item was updated."
          ));
          $args[ 'ID' ][ 'post' ] = $this;
        }
      }
    }

    return $args;
  }

  /**
   * Delete post
   *
   * @param   array $args
   * @return  array
   */
  function on_item_delete( $args ) {

    if ( $this->setup( $args ) ) {
      if ( isset( $args[ 'ID' ][ 'force_delete' ] ) ) {
        $force_delete = $args[ 'ID' ][ 'force_delete' ];
      } else {
        $force_delete = false;
      }
      $ID = $this->get( 'ID' );
      $item = $this->get( 'context' );
      if ( wp_delete_post( $ID, $force_delete ) ) {
        $item->add( 'alert', array(
          'type'  => 'info',
          'msg'   => "Deleted item with id: {$ID}.",
        ));
      } else {
        $error = array(
          'type'  => 'error',
          'msg'   => "Failed to delete item with id: {$args['id']}.",
        );
        $item->add( 'alert', $error );
        $this->add( 'alert', $error );
      }
    }

    return $args;
  }

  function set_post_thumbnail( $args ) {

    if ( $this->setup( $args ) && isset( $args[ 'post_thumbnail' ] ) ) {
      $ID = $this->get( 'ID' );
      $upload = $args[ 'post_thumbnail' ];

      $wp_upload_dir = wp_upload_dir();
      $attachment = array(
        'guid' => $wp_upload_dir[ 'url' ] . '/' . basename( $upload[ 'file' ] ),
        'post_mime_type' => $upload[ 'type' ],
        'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $upload[ 'file' ] ) ),
        'post_content' => '',
        'post_status' => 'inherit'
      );
      $id = wp_insert_attachment( $attachment, $upload[ 'file' ], $ID );
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attach_data = wp_generate_attachment_metadata( $id, $upload[ 'file' ] );
      wp_update_attachment_metadata( $id, $attach_data );
      $args[ 'post_thumbnail' ][ 'id' ] = $id;
    }

    return $args;
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