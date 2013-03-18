<?php
class ScaleUp_Feature extends ScaleUp_Base {

  var $_features;

  function __construct( $args = array() ) {
    parent::__construct( $args );

    /**
     * create feature container
     */
    $this->_features = new ScaleUp_Base();
    /**
     * _feature_type comes from $this->get_default(), so it can only be ran after ScaleUp_Base instantiation
     */
    $feature_type = $this->get( '_feature_type' );
    /**
     * load attributes from feature type definition
     */
    if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {
      $feature_type_args = ScaleUp::get_feature_type( $feature_type );
      $this->load( $feature_type_args );
    }
    /**
     * apply duck type filters to
     */
    if ( $this->has( '_duck_types' ) ) {
      $duck_types = ( array )$this->get( '_duck_types' );
      foreach ( $duck_types as $duck_type ) {
        if ( ScaleUp::is_activated_duck_type( $duck_type ) ) {
          $duck_type = ScaleUp::get_duck_type( $duck_type );
          $this->add_filter( 'duck_types', array( $duck_type, 'duck_types' ) );
        }
        unset( $duck_type );
      }
    }

    $this->add_action( 'init', array( $this, 'init' ) );
    $this->add_action( 'init', array( $this, 'apply_duck_types' ) );
    $this->add_action( 'init', array( $this, 'register_features' ) );
    $this->add_action( 'init', array( $this, 'activate_features' ) );

    $this->add_action( 'activation', array( $this, 'activation' ) );

    $this->do_action( 'init', $args );
    $this->do_action( 'activation', $args );
  }

  function init() {

  }

  /**
   * Attempts to execute a callback function if instance has a property with the name that matches called the name of the called method.
   * This is used by duck typing mechanism to extend features with additional functionality.
   *
   * @param $name
   * @param array $args
   * @return mixed|null
   */
  function __call( $name, $args = array() ) {

    $result = null;

    if ( sizeof( $args ) == 1 ) {
      $args = array_pop( $args );
    }

    if ( property_exists( $this, $name ) && is_callable( $this->$name ) ) {
      $result = call_user_func( $this->$name, $this, $args );
    } elseif ( preg_match( '/^add_([a-zA-Z0-9_\x7f-\xff]*)$/', $name, $matches ) ) {
      $feature_type = $matches[ 1 ];
      $result = $this->add( $feature_type, $args );
    }

    return $result;
  }

  /**
   * Return true if instance is of specified duck type
   *
   * @param $duck_type
   * @return bool
   */
  function is( $duck_type ) {
    if ( $this->has( '_duck_types' ) ) {
      $duck_types = $this->get( '_duck_types' );

      return in_array( $duck_type, $duck_types );
    }

    return false;
  }

  /**
   * Check if a feature is registered. Feature can be an object, an array or a string.
   *
   * @param $feature_type
   * @param $feature ScaleUp_Feature|array|string
   * @return bool
   */
  function is_registered( $feature_type, $feature ) {
    return !is_null( $this->get_feature( $feature_type, $feature ) );
  }

  /**
   * Register and activate a feature type to the current feature
   *
   * @param $feature_type
   * @param array $args
   * @return ScaleUp_Feature|bool
   */
  function add( $feature_type, $args = array() ) {

    $result = false;

    if ( false !== ( $registered = $this->register( $feature_type, $args ) ) ) {
      if ( false !== ( $activated = $this->activate( $feature_type, $registered ) ) ) {
        if ( $activated->is( 'contextual' ) ) {
          $feature_type_args = ScaleUp::get_feature_type( $activated->get( '_feature_type' ) );
          $storage = $this->_features->get( $feature_type_args[ '_plural' ] );
          $storage->set( $activated->get( 'name' ), $activated );
        }
        $result = $activated;
      }
    }

    return $result;
  }

  /**
   * Return feature
   *
   * @param $feature_type
   * @param $feature
   * @return ScaleUp_Feature|null
   */
  function get_feature( $feature_type, $feature ) {

    if ( is_object( $feature ) && method_exists( $feature, 'get' ) ) {
      $name = $feature->get( 'name' );
    } else {
      if ( is_array( $feature ) ) {
        if ( isset( $feature[ 'name' ] ) ) {
          $name = $feature[ 'name' ];
        }
      } else {
        if ( is_string( $feature ) ) {
          $name = $feature;
        } else {
          return null;
        }
      }
    }

    // make sure that its a known feature
    if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {
      $feature_type_args = ScaleUp::get_feature_type( $feature_type );
      $plural            = $feature_type_args[ '_plural' ];
    } else {
      return null;
    }

    if ( $this->_features->has( $plural ) ) {
      $storage = $this->_features->get( $plural );

      return $storage->get( $name );
    }

    return null;
  }

  /**
   * Return associative array of specific kinds of features
   *
   * @param $plural_name string
   * @return array
   */
  function get_features( $plural_name ) {

    $return = array();

    $features = $this->get( 'features' );
    $storage  = $features->get( $plural_name );
    if ( is_object( $storage ) ) {
      $names = $storage->get_properties();
      foreach ( $names as $name ) {
        $return[ $name ] = $storage->get( $name );
      }
    }

    return $return;
  }

  /**
   * Return true if feature has features of specific kind
   *
   * @param $plural_name string
   * @return bool
   */
  function has_features( $plural_name ) {

    $features = $this->get( 'features' );

    return !is_null( $features->get( $plural_name ) );
  }

  /**
   * Register feature and return complete arguments array for this feature.
   * Registration is storing of feature's configuration array without instantiation.
   *
   * @param $feature_type
   * @param $args array|object
   * @return array|bool
   */
  function register( $feature_type, $args ) {

    $result = false;

    if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {

      /**
       * Get feature type declaration from ScaleUp
       */
      $feature_type_args = ScaleUp::get_feature_type( $feature_type );

      if ( is_array( $args ) ) {

        if ( !isset( $args[ 'name' ] ) || is_null( $args[ 'name' ] ) ) {
          /**
           * Give this object a name. This name is a hash, but gives this object some resemblance of identity
           * @see http://stackoverflow.com/questions/2254220/php-best-way-to-md5-multi-dimensional-array
           */
          $args[ 'name' ] = $this->_name_args( $args );
        }
        $name  = $args[ 'name' ];

        /**
         * Special situation when a feature is both global and contextual like a schema
         * The features in global scope are not instantiatable @todo: add code to make this configurable and enforced
         */
        if ( in_array( 'global', $feature_type_args[ '_duck_types' ] ) && in_array( 'contextual', $feature_type_args[ '_duck_types' ] ) ) {
          $site = ScaleUp::get_site();
          if ( $site->is_registered( $feature_type, $name ) && is_array( $site->get_feature( $feature_type, $name ) ) ) {
            $registered_feature_args = $site->get_feature( $feature_type, $name );
            $args = wp_parse_args( $args, $registered_feature_args );
          }
        }
        /**
         * Merge passed args with feature type declaration
         */
        $args = wp_parse_args( $args, $feature_type_args );

        $class = $args[ '__CLASS__' ];

      } elseif ( is_object( $args ) ) {

        $class = get_class( $args );

        if ( is_object( $args ) ) {
          /**
           * When registering an object that was instantiated manually without using activate() function
           */
          if ( $args->has( 'name' ) ) {
            $name = $args->get( 'name' );
          } else {
            $name = spl_object_hash( $args );
            $args->set( 'name', $name );
          }

        }

      } else {
        add_alert( 'warning', sprintf( "Passed invalid args value while attempting to register $feature_type to %s feature.", $this->get( 'name' ) ), array( 'debug' => true, 'wrong' => $args ) );
      }

      if ( method_exists( $class, 'registration' ) ) {
        $this->add_action( 'register', array( $class, 'registration' ) );
      }

      $plural = $feature_type_args[ '_plural' ];

      /**
       * Check if args were set via instance args
       */
      if ( $this->has( $plural ) ) {
        $instance_plural_args = $this->get( $plural );
        if ( isset( $instance_plural_args[ $name ] ) ) {
          $instance_args = $instance_plural_args[ $name ];
          $args          = wp_parse_args( $instance_args, $args );
        }
      }

      // create new feature container
      if ( !$this->has_container( $plural ) ) {
        // instantiate container from feature type args ( default is ScaleUp_Base )
        $this->add_container( $plural, $feature_type_args[ '_container' ] );
      }

      $storage = $this->_features->get( $plural );

      $storage->set( $name, $args );

      $this->do_action( 'register', $args );

      do_action( 'register_feature' );

      $result = $args;

    } else {
      ScaleUp::add_alert( array(
        'type'  => 'warning',
        'msg'   => 'Attempting to register a feature of unknown feature type.',
        'debug' => true,
        'wrong' => $feature_type
      ) );
    }

    return $result;
  }

  /**
   * Activation = Instantiation
   *
   * During activation, the object is populated with all its duck types and abilities that it supports.
   *
   * @see http://en.wikipedia.org/wiki/Duck_typing#In_PHP Duck Types in PHP
   * Duck Types are methods that are added to the object to match its abilities. duck types are specified via
   * _duck_types argument which takes an array of its types. Currently, the code supports 2 abilities: routable & contextual.
   * a "routable" object can be accesed via a url and has a get_url method which returns url of the object.
   * a "contextual" object can be nested inside of another object in which case it has a "context" property which points
   * the object's container.
   *
   * Support array specifies what kind of features this object supports. For example, an instance of Site supports Apps.
   * Futher, an App supports Addons, Views & Forms. This makes it possible to programmatically activate features of an
   * object. This activation happens recursively making it possible to instantiate deeply nested features.
   *
   * @param $feature_type
   * @param array|object $args
   * @return ScaleUp_Feature|bool
   */
  function activate( $feature_type, $args = array() ) {

    $result = null;

    // make sure that feature type is available
    if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {

      $feature_type_args = ScaleUp::get_feature_type( $feature_type );
      // get plural name
      $plural = $feature_type_args[ '_plural' ];

      // create new feature container
      if ( !$this->has_container( $plural ) ) {
        // instantiate container from feature type args ( default is ScaleUp_Base )
        $this->add_container( $plural, $feature_type_args[ '_container' ] );
      }

      // convenient object
      $storage = $this->_features->get( $plural );

      if ( is_object( $args ) ) {
        // feature was already instantiate it, we just need to store a reference to it in our internal storage
        if ( method_exists( $args, 'has' ) && $args->has( 'name' ) ) {
          $storage->set( $args->get( 'name' ), $args );
          $result = $args;
        }
      }

      if ( is_array( $args ) ) {
        if ( isset( $args[ '__CLASS__' ] ) ) {
          $class = $args[ '__CLASS__' ];
          if ( class_exists( $class ) ) {
            $args[ 'context' ] = $this;
            /** @var $result ScaleUp_Feature */
            $result = new $class( $args );
          } else {
            add_alert( 'warning', "Attempting to activate a feature with a class that doesn't exist.", array( 'debug' => true, 'wrong' => $class ) );
          }
        }
      }

      $this->do_action( 'activate', $result );

      do_action( 'activate_feature', $result );

    } else {
      add_alert( 'warning', "Attempting to activate a feature of unknown feature type.", array( 'debug' => true, 'wrong' => $feature_type ) );
    }

    return $result;
  }

  /**
   * Executes after activation is completed successfully
   */
  function activation() {

  }

  /**
   * @todo: Add proper docs to action and filter methods
   */

  /**
   * Add action to this feature
   *
   * @param $handle
   * @param null $callback
   * @param int $priority
   */
  function add_action( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    add_action( "{$object_hash}->{$handle}", $callback, $priority, 2 );
  }

  /**
   * Remove callback from this feature
   *
   * @param $handle
   * @param $callback
   * @param int $priority
   */
  function remove_action( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    remove_action( "{$object_hash}->{$handle}", $callback, $priority, 2 );
  }

  /**
   * Do action associated with this feature
   *
   * @param $handle
   * @param null $args
   */
  function do_action( $handle, $args = null ) {
    $object_hash = spl_object_hash( $this );
    do_action( "{$object_hash}->{$handle}", $this, $args );
  }

  /**
   * Add filter to this feature
   *
   * @param $handle
   * @param null $callback
   * @param int $priority
   */
  function add_filter( $handle, $callback = null, $priority = 10 ) {
    /**
     * @todo: remove this callback = null stuff
     */
    if ( is_null( $callback ) ) {
      $callback = array( $this, $handle );
    }
    $object_hash = spl_object_hash( $this );
    add_filter( "{$object_hash}->{$handle}", $callback, $priority );
  }

  /**
   * Remove filter for this feature
   *
   * @param $handle
   * @param $callback
   * @param int $priority
   */
  function remove_filter( $handle, $callback, $priority = 10 ) {
    $object_hash = spl_object_hash( $this );
    remove_filter( "{$object_hash}->{$handle}", $callback, $priority );
  }

  function apply_filters( $handle, $value ) {
    $object_hash = spl_object_hash( $this );
    return apply_filters( "{$object_hash}->{$handle}", $value );
  }

  function apply_duck_types( $feature, $args ) {
    $this->apply_filters( 'duck_types', $feature, $args );
  }

  /**
   * Register features that are passed via an args array
   *
   * @param $feature ScaleUp_Feature
   * @param $args array
   */
  function register_features( $feature, $args ) {
    if ( $this->has( '_supports' ) && is_array( $args ) ) {
      $supported = (array)$this->get( '_supports' );
      foreach ( $supported as $plural_feature_type ) {
        if ( isset( $args[ $plural_feature_type ] ) ) {
          $features     = (array)$args[ $plural_feature_type ];
          $feature_type = ScaleUp::find_feature_type( '_plural', $plural_feature_type );
          foreach ( $features as $key => $value ) {
            $feature_args = array();
            if ( is_numeric( $key ) ) {
              if ( is_array( $value ) ) {
                $feature_args[ 'name' ] = $this->_name_args( $value );
                $feature_args           = wp_parse_args( $feature_args, $value );
              } else {
                $feature_args[ 'name' ] = $value;
              }
            } else {
              $feature_args[ 'name' ] = $key;
              $feature_args           = wp_parse_args( $feature_args, $value );
            }
            $this->register( $feature_type, $feature_args );
          }
        }
      }
    }
  }

  function activate_features() {
    // check if feature supports features of its own
    if ( $this->has( '_supports' ) && is_array( $this->get( '_supports' ) ) ) {
      // get array of features that this feature supports
      $plural_feature_types = $this->get( '_supports' );
      foreach ( $plural_feature_types as $plural_feature_type ) {
        // support features are specified in plural, so lets get a corresponding singular feature_type
        $found_feature_type = ScaleUp::find_feature_type( '_plural', $plural_feature_type );

        // check if a feature with this feature type was already registered
        if ( $this->_features->has( $plural_feature_type ) ) {
          // get a convinience variable
          /**
           * @todo: change this to use instance variable instead of getter
           */
          $storage = $this->_features->get( $plural_feature_type );
          // get array of registered features for this feature type
          $features = $storage->get_properties();
          foreach ( $features as $feature_name ) {
            // activate registered features
            $activated = $this->activate( $found_feature_type, $this->get_feature( $found_feature_type, $feature_name ) );
            $storage->set( $feature_name, $activated );
          }
        }
      }
    }
  }

  /**
   * Create a feature container and return it
   *
   * @param string $plural feature
   * @param string $class container
   * @param array  $args
   * @return object
   */
  function add_container( $plural, $class, $args = array() ) {

    $default = array(
      'context' => $this,
    );

    if ( class_exists( $class ) ) {
      $container = new $class( wp_parse_args( $args, $default ) );
    } else {
      $container = new ScaleUp_Base( wp_parse_args( $args, $default ) );
      ScaleUp::add_alert( array(
        'type'  => "warning",
        'msg'   => "Failed to instantiate feature container because '$class' does not exist.",
        'wrong' => $class,
        'debug' => true,
      ));
    }

    $this->set_container( $plural, $container );

    return $container;
  }

  /**
   * Return a feature container by plural name
   *
   * @param $plural
   * @return mixed
   */
  function get_container( $plural ) {
    $features = $this->get( 'features' );
    $container = $features->get( $plural );
    return $container;
  }

  /**
   * Set container as container for feature
   *
   * @param $plural
   * @param $container
   */
  function set_container( $plural, $container ) {
    $features = $this->get( 'features' );
    $features->set( $plural, $container );
  }

  /**
   * Return true if current feature has a container for specified features, otherwise return false
   *
   * @param $plural
   * @return bool
   */
  function has_container( $plural ) {
    $features = $this->get( 'features' );
    $has = $features->has( $plural );
    return $has;
  }


  /**
   * Return args that can be used to instantiate an object in a similar state
   * @todo: build this more thoroughly
   *
   * @return mixed
   */
  function export() {
    $state = (array) $this;
    unset( $state[ '_features' ] );
    unset( $state[ '_context' ] );
    $feature_args = ScaleUp::get_feature_type( $this->get( '_feature_type' ) );
    foreach ( $feature_args as $key => $value ) {
      unset( $state[ "_$key" ] );
    }
    unset( $state[ '__args' ] );

    foreach ( $state as $key => $value ) {
      unset( $state[ $key ] );
      $key = trim( $key, '_' );
      $state[ $key ] = $value;
    }
    return $state;
  }

  /**
   * Return unique identifier for this args array
   *
   * @param $args
   * @return string
   */
  function _name_args( $args ) {
    return md5( json_encode( $args ) );
  }

}