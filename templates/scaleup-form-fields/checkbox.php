<div class="control-group <?php echo ( scaleup_has_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">

    <?php if ( scaleup_has_form_field_attr( 'options' ) ) : $options = scaleup_get_form_field_attr( 'options' ); ?>

      <?php if ( is_array( $options ) ) : ?>

        <?php foreach ( $options as $value => $text ) : $id = scaleup_get_form_field_attr( "id" ) . '_' . sanitize_title( $value ) ?>

          <input id="<?php echo $id ?>" name="<?php echo scaleup_get_form_field_attr( 'name' ) ?>[]" <?php scaleup_the_form_field_attr( "type" ); ?> <?php scaleup_the_form_field_attr( "class" ); ?> value="<?php echo $value ?>">
          <label for="<?php echo $id ?>"><?php echo $text ?></label><br>

        <?php endforeach; ?>

      <?php endif ?>

    <?php else: ?>

      <input <?php scaleup_the_form_field_attr( "id" ); ?> <?php scaleup_the_form_field_attr( "name" ); ?> <?php scaleup_the_form_field_attr( "type" ); ?> <?php scaleup_the_form_field_attr( "class" ); ?> value="<?php echo $value ?>">
      <label for="<?php echo scaleup_get_form_field_attr( "id" ); ?>"><?php echo scaleup_get_form_field_attr( "text" ); ?></label>

    <?php endif; ?>

    <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>

  </div>
</div>