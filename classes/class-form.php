<?php
class ScaleUp_Form extends ScaleUp_Feature {

  static function scaleup_init() {

    /**
     * @todo: replace this with glob + template comments parameters
     */
    $form_templates = array(
      'form_button'       => '/forms/button.php',
      'form_checkbox'     => '/forms/checkbox.php',
      'form_confirmation' => '/forms/confirmation.php',
      'form'              => '/forms/form.php',
      'form_error'        => '/forms/form-error.php',
      'form_help'         => '/forms/help.php',
      'form_hidden'       => '/forms/hidden.php',
      'form_label'        => '/forms/label.php',
      'form_password'     => '/forms/password.php',
      'form_text'         => '/forms/text.php',
      'form_textarea'     => '/forms/textarea.php'
    );

    foreach ( $form_templates as $template_name => $template ) {
      $args = array(
        'name'      => $template_name,
        'template'  => $template,
        'path'      => SCALEUP_DIR . '/templates'
      );
      ScaleUp::register( 'template', $args );
    }

  }

  function get_defaults() {
    return wp_parse_args(
      array(
        '_feature_type' => 'form',
      ), parent::get_defaults()
    );
  }
}

ScaleUp::register_feature_type( 'form', array(
  '__CLASS__'     => 'ScaleUp_Form',
  '_feature_type' => 'form',
  '_plural'       => 'forms',
  '_supports'     => array( 'form_fields' ),
  'duck_types'    => array( 'contextual' ),
) );

add_action( 'scaleup_init', array( 'ScaleUp_Form', 'scaleup_init' ) );