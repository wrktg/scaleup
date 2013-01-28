<div class="control-group <?php echo ( has_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/forms/label.php" ); ?>
  <div class="controls">
      <input <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "placeholder" ); ?> <?php the_form_field_attr( "class" ); ?> <?php the_form_field_attr( "value" ); ?>>
    <?php get_template_part( "/forms/help.php" ); ?>
  </div>
</div>