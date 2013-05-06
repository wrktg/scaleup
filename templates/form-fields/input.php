<?php if ( "hidden" == get_form_field_attr( 'type' ) ) : ?>
  <input <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "placeholder" ); ?> <?php the_form_field_attr( "class" ); ?> <?php the_form_field_attr( "value" ); ?>>
<?php else : ?>
  <div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
    <?php ScaleUp::get_template_part( "label" ); ?>
    <div class="controls">
      <?php echo get_form_field_attr( 'before' ) ?>
      <input <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "placeholder" ); ?> <?php the_form_field_attr( "class" ); ?> <?php the_form_field_attr( "value" ); ?>>
      <?php echo get_form_field_attr( 'after' ) ?>
    </div>
    <?php ScaleUp::get_template_part( "help" ); ?>
  </div>
<?php endif ?>