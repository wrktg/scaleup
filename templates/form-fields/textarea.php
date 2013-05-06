<div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php ScaleUp::get_template_part( "label" ); ?>
  <div class="controls">
    <textarea <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "class" ); ?> <?php the_form_field_attr( "rows" ); ?>><?php echo get_form_field_attr( "value" ) ?></textarea>
  </div>
  <?php ScaleUp::get_template_part( "help" ); ?>
</div>
