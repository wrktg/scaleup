<?php if ( has_form_field_attr( "before_field_label" ) ) : the_form_field_attr( "before_field_label" ); endif; ?>

<?php if ( has_form_field_attr( "label" ) ) : ?>
  <label for="<?php the_form_field_attr( "id" ) ?>"><?php the_form_field_attr( "label") ?></label>
<?php endif ?>