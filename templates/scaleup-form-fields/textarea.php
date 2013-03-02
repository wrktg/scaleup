<div class="control-group <?php echo ( scaleup_has_form_field_attr( 'errors' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">
    <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>
    <textarea <?php scaleup_the_form_field_attr( "name" ); ?> <?php scaleup_the_form_field_attr( "id" ); ?> <?php scaleup_the_form_field_attr( "class" ); ?> <?php scaleup_the_form_field_attr( "rows" ); ?>><?php echo scaleup_get_form_field_attr( "value" ) ?></textarea>
  </div>
</div>
