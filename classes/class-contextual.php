<?php
class ScaleUp_Contextual extends ScaleUp_Duck_Type {

}

ScaleUp::register_duck_type( 'contextual', array(
  '__CLASS__'     => 'ScaleUp_Contextual',
) );
ScaleUp::activate_duck_type( 'contextual' );