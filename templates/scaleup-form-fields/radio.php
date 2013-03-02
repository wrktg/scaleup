<div class="control-group <?php echo ( scaleup_get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">
    <?php if ( scaleup_has_form_field_attr( 'options' ) ) : $options = scaleup_get_form_field_attr( 'options' ); ?>
      <?php if ( is_array( $options ) ) : ?>
        <?php foreach ( $options as $value => $text ) : $id = scaleup_get_form_field_attr( "id" ) . '_' . sanitize_title( $value ); ?>
          <label for="<?php echo $id ?>">
            <input id="<?php echo $id ?>" value="<?php echo $value ?>" type="radio" <?php scaleup_the_form_field_attr( 'name' ) ?> <?php echo ( $value == scaleup_get_form_field_attr( 'value' ) ) ? 'checked' : ''; ?>>
            <?php echo $text ?>
          </label>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>
</div>