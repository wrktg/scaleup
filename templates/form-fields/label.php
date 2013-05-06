<?php if ( has_form_field_attr( "label" ) ):  ?>
  <label class="control-label" for="<?php echo get_form_field_attr( "id" ) ?>"><?php echo get_form_field_attr( "label" ) ?></label>
<?php endif;