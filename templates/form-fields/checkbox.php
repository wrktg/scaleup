<div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <div class="controls">
    <?php if ( has_form_field_attr( 'options' ) ) : $options = get_form_field_attr( 'options' ); ?>
      <?php ScaleUp::get_template_part( "label" ); ?>
      <?php if ( is_array( $options ) ) : ?>
        <?php foreach ( $options as $value => $text ) : $id = get_form_field_attr( "id" ) . '_' . sanitize_title( $value ) ?>
          <label for="<?php echo $id ?>" class="checkbox">
            <input id="<?php echo $id ?>"
                   name="<?php echo get_form_field_attr( 'name' ) ?>[]" <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "class" ); ?>
                   value="<?php echo $value ?>" <?php echo ( in_array( $value, get_form_field_attr( "value" ) ) ) ? 'checked' : ''; ?> <?php echo ( true === get_form_field_attr( "disabled" ) ) ? 'disabled' : ''; ?>>
            <?php echo $text ?></label>
        <?php endforeach; ?>
      <?php endif ?>
    <?php else: ?>
      <label for="<?php echo get_form_field_attr( "id" ); ?>">
        <input <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "class" ); ?> <?php echo ( true === get_form_field_attr( "disabled" ) ) ? 'disabled' : ''; ?>>
        <?php echo get_form_field_attr( "label" ); ?>
      </label>
    <?php endif; ?>
  </div>
  <?php ScaleUp::get_template_part( "help" ); ?>
</div>