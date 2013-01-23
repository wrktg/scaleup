<?php get_template_part( "/bootstrap-form/input-label" ) ?>

<?php if ( has_form_field_attr( "before_field" ) ) : the_form_field_attr( "before_field" ); endif; ?>

<textarea id="<?php the_form_field_attr( "id" ) ?>" class="<?php the_form_field_attr( "class" ) ?>" rows="<?php the_form_field_attr( "rows" ) ?>">
  <?php the_form_field_attr( "value" ) ?>
</textarea>

<?php if ( has_form_field_attr( "after_field" ) ) : the_form_field_attr( "after_field" ); endif; ?>

<?php get_template_part( "/bootstrap-form/input-help" ) ?>
