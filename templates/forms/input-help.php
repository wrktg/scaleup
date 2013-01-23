<?php if ( has_form_field_attr( "help" ) ) : ?>
  <span class="help-block"><?php the_form_field_attr( "help" ) ?></span>
<?php endif; ?>

<?php if ( has_form_field_attr( "after_field_help" ) ) : the_form_field_attr( "after_field_help" ); endif; ?>