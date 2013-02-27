<div class="control-group <?php echo ( scaleup_has_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">
    <input <?php scaleup_the_form_field_attr( "id" ); ?> <?php scaleup_the_form_field_attr( "name" ); ?> <?php scaleup_the_form_field_attr( "type" ); ?> <?php scaleup_the_form_field_attr( "placeholder" ); ?> <?php scaleup_the_form_field_attr( "class" ); ?> <?php scaleup_the_form_field_attr( "value" ); ?>>
    <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>
  </div>
</div>