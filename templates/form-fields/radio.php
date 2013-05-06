<div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php ScaleUp::get_template_part( "label" ); ?>
  <div class="controls">
    <?php if ( has_form_field_attr( 'options' ) ) : $options = get_form_field_attr( 'options' ); ?>
      <?php if ( is_array( $options ) ) : ?>
        <?php foreach ( $options as $value => $text ) : $id = get_form_field_attr( "id" ) . '_' . sanitize_title( $value ); ?>
          <label for="<?php echo $id ?>">
            <input id="<?php echo $id ?>" value="<?php echo $value ?>" type="radio" <?php the_form_field_attr( 'name' ) ?> <?php echo ( !is_null( get_form_field_attr( 'value' ) ) && $value == get_form_field_attr( 'value' ) ) ? 'checked' : ''; ?>>
            <?php echo $text ?>
          </label>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php ScaleUp::get_template_part( "help" ); ?>
</div>