<?php
class ScaleUp {

  private static $_this;

  /**
   * Registered features
   * @var array
   */
  private static $_feature_types = array();

  private static $_duck_types = array();

  var $site;

  function __construct() {

    if ( isset( self::$_this ) ) {
      return new WP_Error( 'instantiation-error', 'ScaleUp class is a singleton and can only be instantiated once.' );
    }

    self::$_this = $this;
    $this->site  = new ScaleUp_Site( array( 'name' => 'WordPress' ) );

    add_action( 'scaleup_init', array( $this, '_activate_bundled' ) );
    add_action( 'init', array( $this, 'init' ) );

  }

  static function this() {
    return self::$_this;
  }

  function init() {
    do_action( 'scaleup_init' );
  }

  /**
   * Returns the global scope object for this class
   *
   * @return ScaleUp_Site
   */
  static function get_site() {
    return self::$_this->site;
  }

  /**
   * Add a form to this site & return created form
   *
   * @param array $args
   * @return bool|ScaleUp_Feature
   */
  static function add_form( $args ) {
    $site = ScaleUp::get_site();
    $form = $site->add( 'form', $args );

    return $form;
  }

  /**
   * Return form from this site
   *
   * @param string $name
   * @return bool|ScaleUp_Feature
   */
  static function get_form( $name ) {

    $site = ScaleUp::get_site();
    $form = $site->get_feature( 'form', $name );
    if ( is_null( $form ) ) {
      ScaleUp::add_alert( array(
        'type'  => 'warning',
        'msg'   => "Form called $form could not be found on this site.",
        'debug' => true,
        'wrong' => $form
      ) );
      $form = false;
    }

    return $form;
  }

  /**
   * Add form field to a form & return created form
   *
   * @param string|ScaleUp_Form $form
   * @param array $args
   * @return ScaleUp_Form_Field|bool
   */
  static function add_form_field( $form, $args ) {

    $form_field = false;

    if ( is_string( $form ) ) {
      $form = ScaleUp::get_form( $form );
    }

    if ( is_object( $form ) ) {
      $form_field = $form->add( 'form_field', $args );
      if ( false === $form_field ) {
        ScaleUp::add_alert( array(
          'type'  => 'warning',
          'msg'   => sprintf( "Form field could not be added to form called %s.", $form->get( 'name' ) ),
          'debug' => true,
          'wrong' => $args
        ) );
      }
    } else {
      ScaleUp::add_alert( array(
        'type'  => "warning",
        'msg'   => "Field could not be added to the form.",
        'debug' => true,
        'wrong' => $form
      ) );
    }

    return $form_field;
  }

  /**
   * Add a notification to a form
   *
   * @todo: implement form notifications
   *
   * @param string|ScaleUp_Form $form
   * @param array $args
   * @return ScaleUp_Notification|bool
   */
  static function add_form_notification( $form, $args ) {

    $notification = false;

    if ( is_string( $form ) ) {
      $form = ScaleUp::get_form( $form );
    }

    if ( is_object( $form ) ) {
      $notification = $form->add( 'notification', $args );
    } else {
      ScaleUp::add_alert( array(
        'type'  => "warning",
        'msg'   => "Notification could not be added to the form.",
        'debug' => true,
        'wrong' => $form
      ) );
    }

    return $notification;
  }

  /**
   * Add an alert to this site
   *
   * @param $args
   * @return ScaleUp_Alert|bool
   */
  static function add_alert( $args ) {
    $site = ScaleUp::get_site();
    $alert = $site->add( 'alert', $args );
    return $alert;
  }

  /**
   * Register schema with this site
   *
   * @param $args
   * @return array|bool
   */
  static function register_schema( $args ) {
    $site = ScaleUp::get_site();
    $schema = $site->register( 'schema', $args );
    return $schema;
  }

  /**
   * Create an item in the database from the passed arguments and return the created item
   *
   * @param $args
   * @return ScaleUp_Item
   */
  static function create_item( $args ) {
    $site = ScaleUp::get_site();
    /*** @var $item ScaleUp_Item */
    $item = $site->add( 'item', $args );
    $item->create( $args );

    $id = $item->get( 'id' );

    if ( is_int( $id ) && $id > 0 ) {
      /**
       * Let's replace item name in site's item feature storage with a property name that we can reference in the future
       */
      $new_id = "item_$id";
      $name   = $item->get( 'name' );
      $items  = $site->get_features( 'items' );
      /**
       * Remove reference to the old hashed name
       */
      unset( $items->$name );
      /**
       * Set new refence using item's id
       */
      $items->set( $new_id, $item );
    } else {
      $item = null;
      /**
       * @todo: an error to return
       */
    }

    return $item;
  }

  /**
   * Create an empty instance of an item ( the database is untouched )
   *
   * @param $args
   * @return ScaleUp_Item|bool
   */
  static function new_item( $args ) {
    $site = ScaleUp::get_site();

    return $site->add( 'item', $args );
  }

  /**
   * Get item by id
   *
   * @param int $id
   * @return ScaleUp_Item|bool
   */
  static function get_item( $id ) {

    $item = false;

    if ( $id > 0 ) {
      $property_name = "item_$id";
      $site          = ScaleUp::get_site();
      /** @var $items ScaleUp_Base */
      $items = $site->get_features( 'items' );
      $item  = $items->get( $property_name );
      if ( is_null( $item ) ) {
        /** @var $item ScaleUp_Item */
        $item = $site->add( 'item' );
        $item->read( $id );
      }
    } else {
      ScaleUp::add_alert( array(
        'type'  => 'warning',
        'debug' => true,
        'wrong' => $id,
        'msg'   => "Could not get the item because item id is invalid"
      ) );
    }

    return $item;
  }

  /**
   * Update item with values from $args
   *
   * @param $id
   * @param $args
   * @return bool
   */
  static function update_item( $id, $args = array() ) {

    $result = false;

    $item = ScaleUp::get_item( $id );

    if ( $item ) {
      $result = $item->update( $args );
    }

    return $result;
  }

  /**
   * Delete item with $id and return true on success or false on fail
   *
   * @param $id
   * @return bool
   */
  static function delete_item( $id ) {

    $success = true;

    /** @var $item ScaleUp_Item */
    $item = ScaleUp::get_item( $id );
    if ( $item ) {
      $success = $item->delete( $id );
    }

    return $success;
  }

  /**
   * Add template to the site to make it available to use with get_template_part
   *
   * @param $args
   * @return ScaleUp_Feature|bool
   */
  static function add_template( $args ) {
    $site = ScaleUp::get_site();
    $template = $site->add( 'template', $args );

    return $template;
  }

  /**
   * Register an asset to make it available to this site
   *
   * @param $args
   * @return array|bool
   */
  static function register_asset( $args ) {
    $site = ScaleUp::get_site();
    $asset_args = $site->register( 'asset', $args );

    return $asset_args;
  }


  /**
   * Make a feature type available.
   *
   * @param $feature_type string
   * @param $args array
   * @return array
   */
  static function register_feature_type( $feature_type, $args = array() ) {

    $default = array(
      '__CLASS__'     => 'ScaleUp_Feature',
      '_feature_type' => $feature_type,
      '_plural'       => "{$feature_type}s",
      '_container'    => "ScaleUp_Base",
      '_supports'     => array(),
      '_duck_types'   => array(),
      '_bundled'      => array(),
    );

    $args                                  = wp_parse_args( $args, $default );
    self::$_feature_types[ $feature_type ] = $args;

    return $args;
  }

  /**
   * Register duck type to make it available to ScaleUp
   *
   * @param $duck_type
   * @param array $args
   * @return array
   */
  static function register_duck_type( $duck_type, $args = array() ) {

    $default = array(
      '__CLASS__' => 'ScaleUp_Duck_Type',
      'duck_type' => $duck_type,
      'methods'   => array(),
    );

    if ( self::is_registered_duck_type( $duck_type ) ) {
      $args = self::get_duck_type( $duck_type );
    } else {
      $args                            = wp_parse_args( $args, $default );
      self::$_duck_types[ $duck_type ] = $args;
    }

    return $args;
  }

  /**
   * Return activated duck types
   *
   * @return array
   */
  static function get_duck_types() {
    return self::$_duck_types;
  }

  /**
   * Return registered feature types
   * @return array
   */
  static function get_feature_types() {
    return self::$_feature_types;
  }

  /**
   * Return true if duck type is registered, otherwise return false.
   *
   * *Note*: this function will also return true when the duck type is activated.
   *
   * @param $duck_type
   * @return bool
   */
  static function is_registered_duck_type( $duck_type ) {
    return isset( self::$_duck_types[ $duck_type ] );
  }

  /**
   * Return true if duck type was activated.
   *
   * @param $duck_type
   * @return bool
   */
  static function is_activated_duck_type( $duck_type ) {
    return self::is_registered_duck_type( $duck_type ) && is_object( self::get_duck_type( $duck_type ) );
  }

  /**
   * Return args array if duck_type is registered, object if its activated or null if its not registered.
   *
   * @param $duck_type
   * @return array|object|null
   */
  static function get_duck_type( $duck_type ) {

    $args = null;

    if ( self::is_registered_duck_type( $duck_type ) ) {
      $args = self::$_duck_types[ $duck_type ];
    }

    return $args;
  }

  /**
   * Return duck type object or null if duck type is not registered
   *
   * @param $duck_type string
   * @return object|null
   */
  static function activate_duck_type( $duck_type ) {

    $object = null;

    if ( self::is_activated_duck_type( $duck_type ) ) {
      // if already activated then return the activated object
      $object = ScaleUp::get_duck_type( $duck_type );
    } else {

      if ( self::is_registered_duck_type( $duck_type ) ) {
        // then activate the registered object
        $args = ScaleUp::get_duck_type( $duck_type );
        if ( class_exists( $args[ '__CLASS__' ] ) ) {
          $class                           = $args[ '__CLASS__' ];
          $object                          = new $class( $args );
          self::$_duck_types[ $duck_type ] = $object;
        }
      }

    }

    return $object;
  }

  /**
   * Return true if feature type has is of a specific duck type, otherwise return false
   *
   * @param $feature_type
   * @param $duck_type
   * @return bool
   */
  static function is_duck_type( $feature_type, $duck_type ) {

    $feature_type_args = ScaleUp::get_feature_type( $feature_type );

    return !is_null( $feature_type_args )
      && isset( $feature_type_args[ '_duck_types' ] )
      && is_array( $feature_type_args[ '_duck_types' ] )
      && in_array( $duck_type, $feature_type_args[ '_duck_types' ] );
  }

  /**
   * Check if a feature type is available
   *
   * @param $feature_type
   * @return bool
   */
  static function is_registered_feature_type( $feature_type ) {
    return isset( self::$_feature_types[ $feature_type ] );
  }

  /**
   * Return feature type by matching first args $key and $value.
   * $key can be __CLASS__, _plural or _feature_type
   *
   * @param $key
   * @param $value
   * @return string|null
   */
  static function find_feature_type( $key, $value ) {

    $found_feature_type = null;

    foreach ( self::$_feature_types as $feature_type => $args ) {
      if ( isset( $args[ $key ] ) && $args[ $key ] == $value ) {
        $found_feature_type = $feature_type;
        break;
      }
    }

    return $found_feature_type;
  }

  /**
   * Return args for a feature type
   *
   * @param $feature_type
   * @return array|null
   */
  static function get_feature_type( $feature_type ) {

    $args = null;

    if ( isset( self::$_feature_types[ $feature_type ] ) ) {
      $args = self::$_feature_types[ $feature_type ];
    }

    return $args;
  }

  static function register( $feature_type, $args ) {
    return self::$_this->site->register( $feature_type, $args );
  }

  static function activate( $feature_type, $args ) {
    return self::$_this->site->activate( $feature_type, $args );
  }

  /**
   * Extend feature types with passed arguments.
   *
   * @param array $args = array( $feature_type => $args )
   * @return array $array of modified feature types
   */
  static function extend_feature_type( $args = array() ) {

    $modified = array();

    foreach ( $args as $feature_type => $new ) {
      $feature_type_args = ScaleUp::get_feature_type( $feature_type );
      if ( is_array( $feature_type_args ) && is_array( $new ) ) {
        foreach ( $new as $key => $value ) {
          if ( isset( $feature_type_args[ $key ] ) ) {
            $old = $feature_type_args[ $key ];
            if ( is_array( $old ) ) {
              if ( is_array( $value ) ) {
                $old = array_merge( $old, $value );
              } else {
                $old[ ] = $value;
              }
            }
            $feature_type_args[ $key ] = $old;
          }
        }
        $modified[ $feature_type ]             = $feature_type_args;
        self::$_feature_types[ $feature_type ] = $feature_type_args;
      }
    }

    return $modified;
  }

  /**
   * Activate features that are bundled with feature types
   */
  function _activate_bundled() {
    $feature_types = self::$_feature_types;
    $site          = self::get_site();

    foreach ( $feature_types as $args ) {
      if ( isset( $args[ '_bundled' ] ) && !empty( $args[ '_bundled' ] ) ) {
        foreach ( $args[ '_bundled' ] as $plural_feature_type => $features ) {
          $feature_type = ScaleUp::find_feature_type( '_plural', $plural_feature_type );
          if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {
            foreach ( $features as $feature_name => $feature_args ) {
              $feature_args[ 'name' ] = $feature_name;
              $feature_args           = $site->register( $feature_type, $feature_args );
              $site->activate( $feature_type, $feature_args );
            }
          }
        }
      }
    }
  }

  /**
   * Returns a string with all spaces converted to underscores (by default), accented
   * characters converted to non-accented characters, and non word characters removed.
   *
   * @param string $string the string you want to slug
   * @param string $replacement will replace keys in map
   * @return string
   * @link http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::slug
   */
  static function slugify( $string, $replacement = '_' ) {
    $quotedReplacement = preg_quote( $replacement, '/' );

    $merge = array(
      '/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu'                      => ' ',
      '/\\s+/'                                                             => $replacement,
      sprintf( '/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement ) => '',
    );

    $transliteration = array(
      '/ä|æ|ǽ/'                         => 'ae',
      '/ö|œ/'                           => 'oe',
      '/ü/'                             => 'ue',
      '/Ä/'                             => 'Ae',
      '/Ü/'                             => 'Ue',
      '/Ö/'                             => 'Oe',
      '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/'           => 'A',
      '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/'         => 'a',
      '/Ç|Ć|Ĉ|Ċ|Č/'                     => 'C',
      '/ç|ć|ĉ|ċ|č/'                     => 'c',
      '/Ð|Ď|Đ/'                         => 'D',
      '/ð|ď|đ/'                         => 'd',
      '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/'             => 'E',
      '/è|é|ê|ë|ē|ĕ|ė|ę|ě/'             => 'e',
      '/Ĝ|Ğ|Ġ|Ģ/'                       => 'G',
      '/ĝ|ğ|ġ|ģ/'                       => 'g',
      '/Ĥ|Ħ/'                           => 'H',
      '/ĥ|ħ/'                           => 'h',
      '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/'           => 'I',
      '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/'           => 'i',
      '/Ĵ/'                             => 'J',
      '/ĵ/'                             => 'j',
      '/Ķ/'                             => 'K',
      '/ķ/'                             => 'k',
      '/Ĺ|Ļ|Ľ|Ŀ|Ł/'                     => 'L',
      '/ĺ|ļ|ľ|ŀ|ł/'                     => 'l',
      '/Ñ|Ń|Ņ|Ň/'                       => 'N',
      '/ñ|ń|ņ|ň|ŉ/'                     => 'n',
      '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/'         => 'O',
      '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/'       => 'o',
      '/Ŕ|Ŗ|Ř/'                         => 'R',
      '/ŕ|ŗ|ř/'                         => 'r',
      '/Ś|Ŝ|Ş|Š/'                       => 'S',
      '/ś|ŝ|ş|š|ſ/'                     => 's',
      '/Ţ|Ť|Ŧ/'                         => 'T',
      '/ţ|ť|ŧ/'                         => 't',
      '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
      '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
      '/Ý|Ÿ|Ŷ/'                         => 'Y',
      '/ý|ÿ|ŷ/'                         => 'y',
      '/Ŵ/'                             => 'W',
      '/ŵ/'                             => 'w',
      '/Ź|Ż|Ž/'                         => 'Z',
      '/ź|ż|ž/'                         => 'z',
      '/Æ|Ǽ/'                           => 'AE',
      '/ß/'                             => 'ss',
      '/Ĳ/'                             => 'IJ',
      '/ĳ/'                             => 'ij',
      '/Œ/'                             => 'OE',
      '/ƒ/'                             => 'f'
    );

    $map = $transliteration + $merge;

    return preg_replace( array_keys( $map ), array_values( $map ), $string );
  }
}