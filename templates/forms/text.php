<?php get_template_part( "/bootstrap-form/input-label" ) ?>

<?php if ( has_form_field_attr( "before_field" ) ) : the_form_field_attr( "before_field" ); endif; ?>

  <input id="<?php the_form_field_attr( "id" ) ?>"
         type="<?php the_form_field_type() ?>"
         placeholder="<?php the_form_field_attr( "placeholder" ) ?>"
         class="<?php the_form_field_attr( "class" ) ?>">

<?php if ( has_form_field_attr( "after_field" ) ) : the_form_field_attr( "after_field" ); endif; ?>

<?php get_template_part( "/bootstrap-form/input-help" ) ?>