<?php
/**
 * Class ScaleUp
 *
 * ScaleUp API
 */
class ScaleUp {

  static $_methods = array();

  /**
   * Register form to make it available on this site.
   *
   * @param $name
   * @param array $fields
   * @param array $args
   * @return array|WP_Error
   */
  static function register_form( $name, $fields = array(), $args = array() ) {
    $args[ 'name' ]   = $name;
    $args[ 'fields' ] = $fields;
    $form = ScaleUp_Forms::register_form( $args );
    do_action( 'register_form', $form );
    return $form;
  }

  /**
   * Return a form by a specific name
   *
   * @param $name
   * @return null|\ScaleUp_Form
   */
  static function get_form( $name ) {
    return ScaleUp_Forms::get_form( $name );
  }

  /**
   * Register a template located at $path + $template_name
   *
   * $template_name must start with forward slash / and may contain one or more directories.
   *
   * For example: /simple.php, /form/simple.php or /gravityforms/form/simple.php
   *
   * @param $path
   * @param $template_name
   */
  static function register_template( $path, $template_name ) {
    $scaleup_templates = ScaleUp_Templates::this();
    $scaleup_templates->register( $path, $template_name );
  }

  /**
   * Register a schema type against a custom post type.
   *
   * $post_type can be an already registered custom post type or a new post type.
   * If $post_type is a new post type then $args must contain post type arguments.
   * If $post_type is an existing post type then you can specify $args that will override the existing post type args.
   *
   * @see http://schema.org/ list of schema types
   * @param $schema_type string schema type
   * @param $post_type string
   * @param $args array of register_post_type arguments
   * @param $properties array override default behavior for schema's properties
   * @return mixed
   */
  static function register_schema( $schema_type, $post_type, $args, $properties ) {
    return ScaleUp_Schemas::register_schema( $schema_type, $post_type, $args, $properties );
  }

  /**
   * Register property and attach it to one or more schema types
   *
   * @param $property_name
   * @param $schema_types
   * @param $args
   * @return mixed
   */
  static function register_property( $property_name, $schema_types, $args ) {
    return ScaleUp_Schemas::register_property( $property_name, $schema_types, $args );
  }

  /**
   * Magic function to call registered methods
   *
   * @param $method
   * @param $args
   * @return mixed
   */
  static function __callStatic( $method, $args ) {
    if ( isset( self::$_methods[ $method ] ) ) {
      $callback = self::$_methods[ $method ];
      if ( is_callable( $callback ) ) {
        return call_user_func( $callback, $args );
      }
    }
  }

  /**
   * Register ScaleUp API method to specific callback methods
   *
   * @param $method
   * @param $callback
   */
  static function register( $method, $callback ) {
    if ( !isset( self::$_methods[ $method ] ) ) {
      self::$_methods[ $method ] = $callback;
    }
  }

  /**
   * Create post populated with schema properties.
   * $schema parameter is an associative array of schema properties and their values.
   *
   * @see http://schema.org/ list of schema types and their properties
   * @see http://codex.wordpress.org/Function_Reference/wp_insert_post post fields for $args
   * @param $properties
   * @param null $args
   * @return int|bool
   */
  static function create_post( $properties, $args = null ) {

    $default = array(
      'post_type'   => get_post_type_from_schema( $properties[ 'type' ] ),
      'post_status' => 'new',
    );

    $args = wp_parse_args( $args, $default );

    $args = add_magic_quotes( $args );

    /**
     * At this point, our intention is to create a post under any circumstances. This gives us the ability to create
     * custom field against it. We are providing just enough post_* for this to succeed.
     */
    $post_id = wp_insert_post( $args, true );

    if ( is_wp_error( $post_id ) ) {
      /**
       * In theory, this should not happen because wp_insert_post should always succeed. The only way that this would
       * not happen is if the DB is not available, but then we have bigger issues to fry.
       * @todo: Somehow log the error message. Not sure how yet.
       */
      return false;
    }

    update_post( $properties, $args );

    return $post_id;
  }

  /**
   * Update existing post with schema
   *
   * @see http://schema.org/ list of schema types and their properties
   * @see http://codex.wordpress.org/Function_Reference/wp_update_post post fields for $args
   * @param $properties array properties
   * @param null $args
   * @return bool|WP_Error
   */
  static function update_post( $properties, $args = null ) {

    if ( isset( $properties[ 'ID' ] ) && isset( $args[ 'ID' ] ) && $properties[ 'ID' ] != $args[ 'ID' ] ) {
      /**
       * Seriously? Make up your mind. A post can't be 2 posts at the same time... or can it? NO IT CAN'T!
       * @todo: throw some kind of a snarky message back at the developer for being silly
       */
      return false;
    }

    if ( isset( $properties[ 'ID' ] ) )
      $post_id = $properties[ 'ID' ];
    if ( isset( $args[ 'ID' ] ) )
      $post_id = $args[ 'ID' ];

    $default = array(
      'ID' => $post_id,
    );

    $args         = wp_parse_args( $args, $default );
    $args         = add_magic_quotes( $args );
    $post_type    = get_post_type( $post_id );
    $schema_type  = get_schema_type( $post_type );

    if ( !$schema_type )
      return new WP_Error( 'schema', sprintf( __( 'Schema type is not available for %s' ), $post_type ) );

    $result = wp_update_post( $args, true );

    if ( is_wp_error( $result ) ) {
      /**
       * not sure how this happened.
       * @todo: need some kind of developer feedback here.
       */
      return $result;
    }

    $schema = new ScaleUp_Schema();
    $schema->load( $properties );
    $schema->update( $post_id );

    return true;
  }

  /**
   *
   * @param $id
   * @param $property_name
   * @param $property_value
   * @param array $args
   * @return bool
   */
  static function update_property( $id, $property_name, $property_value, $args = array() ) {
    $property = new ScaleUp_Schema_Property( $property_name, $args );
    $property->set( 'value', $property_value );
    return $property->update( $id );
  }

  /**
   * Returns specific view either from global scope or the context
   *
   * @param $name
   * @param null $context
   * @return ScaleUp_View|bool
   */
  static function get_view( $name, $context = null ) {
    return ScaleUp_Views::get_view( $name, $context );
  }

  /**
   * Return a schema object with its properties.
   * Set $reference to false to get schema definition from reference
   *
   * @param $schema_type
   * @param bool $reference
   * @return ScaleUp_Schema|bool
   */
  static function get_schema( $schema_type, $reference = false ) {
    return ScaleUp_Schemas::get_schema( $schema_type, $reference );
  }

  /**
   * Replaces variables in string template that uses {variable_name} syntax.
   * For example, /profile/{username} with array( 'username' => 'taras') produces /profile/taras/
   *
   * @param $template
   * @param $args
   * @return string
   */
  static function string_template( $template, $args ) {

    $pattern   = $template;
    $len       = strlen( $pattern );
    $tokens    = array();
    $variables = array();
    $pos       = 0;
    preg_match_all( '#.\{(\w+)\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
    foreach ( $matches as $match ) {
      if ( $text = substr( $pattern, $pos, $match[ 0 ][ 1 ] - $pos ) ) {
        $tokens[ ] = array( 'text', $text );
      }

      $pos = $match[ 0 ][ 1 ] + strlen( $match[ 0 ][ 0 ] );
      $var = $match[ 1 ][ 0 ];

      // Use the character preceding the variable as a separator
      $separators = array( $match[ 0 ][ 0 ][ 0 ] );

      if ( $pos !== $len ) {
        // Use the character following the variable as the separator when available
        $separators[ ] = $pattern[ $pos ];
      }
      $regexp = sprintf( '[^%s]+', preg_quote( implode( '', array_unique( $separators ) ), '#' ) );

      $tokens[ ] = array( 'variable', $match[ 0 ][ 0 ][ 0 ], $regexp, $var );

      if ( in_array( $var, $variables ) ) {
        /**
         * @todo: Add error that variable can't be used twice
         */
      }

      $variables[ ] = $var;
    }

    if ( $pos < $len ) {
      $tokens[ ] = array( 'text', substr( $pattern, $pos ) );
    }

    $result = '';
    foreach ( $tokens as $token ) {
      if ( 'text' === $token[ 0 ] ) {
        // Text tokens
        $result .= $token[ 1 ];
      }
      if ( 'variable' === $token[ 0 ] ) {
        // Variable tokens
        $prefix = $token[ 1 ];
        if ( isset( $args[ $token[ 3 ] ] ) ) {
          $value = $args[ $token[ 3 ] ];
        } else {
          /**
           * @todo: return an error if args doesn't provide value for variable.
           */
          $value = '';
        }
        $result .= "$prefix$value";
      }
    }

    return $result;
  }

  /**
   * Return property reference by property name
   *
   * @param $property_name
   * @return array|bool
   */
  static function get_property_reference( $property_name ) {
    return ScaleUp_Schemas::get_property_reference( $property_name );
  }

  /**
   * Return post type registered for specific schema type
   *
   * @param $schema_type
   * @internal param $post
   * @return string
   */
  static function get_post_type_from_schema( $schema_type ) {
    return ScaleUp_Schemas::get_post_type( $schema_type );
  }

  /**
   * Return schema type that's registered for specific post type
   * @param $post_type
   * @return bool|string
   */
  static function get_schema_type( $post_type ) {
    return ScaleUp_Schemas::get_schema_type( $post_type );
  }

}