<?php
class ScaleUp_Global extends ScaleUp_Duck_Type {
}

ScaleUp::register_duck_type( 'global', array(
  '__CLASS__'     => 'ScaleUp_Global',
) );
ScaleUp::activate_duck_type( 'global' );