<?php
class ScaleUp_Feature extends ScaleUp_Base {

  var $_features;

  function __construct( $args ) {
    parent::__construct( $args );

    $this->_features = new ScaleUp_Base();

    $feature_type      = $this->get( '_feature_type' );
    $feature_type_args = ScaleUp::get_feature_type( $feature_type );
    $this->load( wp_parse_args( $args, $feature_type_args ) );

    if ( $this->is( 'contextual' ) && $this->has( 'context' ) && !is_null( $this->get( 'context' ) ) ) {
      $context = $this->get( 'context' );
    } else {
      $context = ScaleUp::get_site();
    }

    // this registers and activates features when developer instantiates a feature with new keyword
    // _activated args is used to prevent automatic registration and activation of features that are instantiated via
    // activate method
    if ( is_object( $context ) && !$context->is_registered( $feature_type, $this ) && !isset( $args[ '_activated' ] ) ) {
      $context->register( $feature_type, $this );
      $context->activate( $feature_type, $this );
    }

    if ( isset( $args[ '_activated' ] ) && $this->is( 'global' ) ) {
      $global = ScaleUp::get_site();
      $storage = $global->get( 'features' )->get( $this->get( '_plural' ) );
      $storage->set( $this->get( 'name' ), $this );
    }

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

    if ( property_exists( $this, $name ) && is_callable( $this->$name ) ) {
      $result = call_user_func( $this->$name, $this, $args );
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
   * Return feature
   *
   * @param $feature_type
   * @param $feature
   * @return null|ScaleUp_Feature
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
   * Register feature and return complete arguments array for this feature.
   * Registration is storing of feature's configuration array without instantiation.
   *
   * @param $feature_type
   * @param $args array
   * @return array|WP_Error
   */
  function register( $feature_type, $args ) {

    // make sure that its known feature
    if ( !ScaleUp::is_registered_feature_type( $feature_type ) ) {
      return new WP_Error( 'invalid-feature-type', sprintf( __( '%s is not a valid feature type.' ), $feature_type ) );
    }

    $feature_type_args = ScaleUp::get_feature_type( $feature_type );

    if ( is_array( $args ) ) {

      $args = wp_parse_args( $args, $feature_type_args );
      if ( isset( $args[ 'name' ] ) ) {
        $name = $args[ 'name' ];
      }
      $class = $args[ '__CLASS__' ];

    } else {

      $class = get_class( $args );

      if ( is_object( $args ) ) {

        /**
         * When registering an object that was instantiated manually without using activate() function
         */
        $name = $args->get( 'name' );
        if ( $args->has( '__CLASS__' ) ) {
          $class = $args->get( '__CLASS__' );
        }

      } else {

        return new WP_Error( 'invalid-args', sprintf( __( '%s is not a valid args type. ', $class ) ) );

      }

    }

    /**
     * @todo: Add validation here. Validation should happen based on '_requires' argument in $_feature_types declaration
     */
    if ( empty( $name ) ) {
      return new WP_Error( 'name-missing', __( 'Feature name is required.' ) );
    }

    $plural = $feature_type_args[ '_plural' ];

    if ( !$this->_features->has( $plural ) ) {
      $this->_features->set( $plural, new ScaleUp_Base() );
    }

    $storage = $this->_features->get( $plural );

    $storage->set( $name, $args );

    /**
     * Register feature in global scope if it is of 'global' duck type
     */
    if ( 'site' != $this->get( '_feature_type' ) && ScaleUp::is_duck_type( $feature_type, 'global' ) ) {
      $global = ScaleUp::get_site();
      $global->register( $feature_type, $args );
    }

    /**
     * Call registration function to allow feature specific code to be executed
     * During registration, the feature is not yet instantiated, therefore we must call the static method
     */
    if ( method_exists( $class, 'registration' ) ) {
      $args = $class::registration( $args );
    }

    /**
     * Execute register_feature hook to allow 3rd party plugins to execute code during registration
     */
    do_action( 'register_feature', $feature_type, $args );

    return $args;
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
   * @return ScaleUp_Feature|WP_Error
   */
  function activate( $feature_type, $args = array() ) {

    $object = null;

    // make sure that feature type is available
    if ( ScaleUp::is_registered_feature_type( $feature_type ) ) {

      $feature_type_args = ScaleUp::get_feature_type( $feature_type );
      // get plural name
      $plural = $feature_type_args[ '_plural' ];

      // create new feature container
      if ( !$this->_features->has( $plural ) ) {
        $this->_features->set( $plural, new ScaleUp_Base() );
      }

      // convinient object
      $storage = $this->_features->get( $plural );

      if ( is_object( $args ) ) {
      // feature was already instantiate it, we just need to store a reference to it in our internal storage
        if ( method_exists( $args, 'has' ) && $args->has( 'name' ) ) {
          $storage->set( $args->get( 'name' ), $args );
          $object = $args;
        }
      }

      if ( is_array( $args ) ) {
        if ( isset( $args[ '__CLASS__' ] ) ) {
          $class = $args[ '__CLASS__' ];
          if ( class_exists( $class ) ) {
            // set _activated to true to prevent automatical activation in Feature __construct
            $args[ '_activated' ] = true;
            /** @var $object ScaleUp_Feature */
            $object               = new $class( $args );
          } else {
            return new WP_Error( 'activation-failed', sprintf( __( '%s class does not exist.' ), $class ) );
          }
        }
      }
    }
    if ( is_object( $object ) ) {
      $this->apply_duck_types( $object );
      $object->add_support();
    }

    /**
     * Call activation method allowing feature specific actions after activation is complete
     */
    $this->activation();

    /**
     * Execute activate_feature hook which allows plugins to perform actions when a feature is activated
     */
    do_action( 'activate_feature', $feature_type, $object );

    return $object;
  }

  /**
   * Executes after activation is completed successfully
   */
  function activation() {

  }

  /**
   * Apply duck types to the feature
   *
   * @param $feature ScaleUp_Feature
   */
  function apply_duck_types( $feature ) {

    if ( $feature->has( '_duck_types' ) ) {
      $duck_types = $feature->get( '_duck_types' );
      foreach ( $duck_types as $duck_type_name ) {
        if ( ScaleUp::is_activated_duck_type( $duck_type_name ) ) {
          $duck_type = ScaleUp::get_duck_type( $duck_type_name );
          $duck_type->apply( $feature, $this );
        }
      }
    }

  }

  function add_support() {
    if ( $this->has( '_supports' ) && is_array( $this->get( '_supports' ) ) ) {
      $plural_feature_types = $this->get( '_supports' );
      foreach ( $plural_feature_types as $plural_feature_type ) {
        if ( $this->has( $plural_feature_type ) && is_array( $this->get( $plural_feature_type ) ) ) {
          $features           = $this->get( $plural_feature_type );
          $found_feature_type = ScaleUp::find_feature_type( '_plural', $plural_feature_type );
          $feature_type_args  = ScaleUp::get_feature_type( $found_feature_type );
          if ( is_array( $feature_type_args ) ) {
            $feature_type = $feature_type_args[ '_feature_type' ];
            foreach ( $features as $key => $value ) {
              if ( is_numeric( $key ) ) {
                /**
                 * $key is numeric if the element is not associative
                 * this might happen if you have an array of elements like addons => array( 'login', 'frontpage' )
                 * @todo: when item is a string without a key, we should look up args by name
                 */
                $feature_name = $value;
                $args         = array();
              } else {
                $feature_name = $key;
                if ( is_array( $value ) ) {
                  $args = $value;
                } else {
                  /**
                   * @todo: I don't think this scenario would happen because you can't have arguments as 1 element. ME think!
                   */
                  $args = array( $value );
                }
              }
              if ( !isset( $args[ 'name' ] ) ) {
                $args[ 'name' ] = $feature_name;
              }
              if ( $this->is_registered( $feature_type, $feature_name ) ) {
                $feature = $this->get_feature( $feature_type, $feature_name );
              } else {
                $site = ScaleUp::get_site();
                if ( $site->is_registered( $feature_type, $feature_name ) ) {
                  $feature = $site->get_feature( $feature_type, $feature_name );
                } else {
                  $feature = $this->register( $feature_type, $args );
                }
              }
              if ( isset( $feature ) ) {
                if ( is_object( $feature ) && method_exists( $feature, 'get_defaults' ) ) {
                  $defaults = $feature->get_defaults();
                } else {
                  $defaults = $feature;
                }
                $args = wp_parse_args( $args, $defaults );
                $activated = $this->activate( $feature_type, $args );
                $features  = $this->get( 'features' );
                $storage   = $features->get( $plural_feature_type );
                $storage->set( $feature_name, $activated );
              }
            }
          }
        }
      }
    }
  }

}