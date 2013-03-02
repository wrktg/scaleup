<div class="control-group <?php echo ( scaleup_has_form_field_attr( 'errors' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">
    <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>
    <?php echo scaleup_get_form_field_attr( 'before' ) ?>
    <select <?php scaleup_the_form_field_attr( 'id' ) ?> <?php scaleup_the_form_field_attr( 'name' ) ?> <?php scaleup_the_form_field_attr( "class" ); ?>>
      <?php if ( scaleup_has_form_field_attr( 'options' ) ) : $options = scaleup_get_form_field_attr( 'options' ); ?>
        <?php if ( is_array( $options ) ) : ?>
          <?php foreach ( $options as $value => $text ) : ?>
            <option value="<?php echo $value ?>"><?php echo $text ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </select>
    <?php echo scaleup_get_form_field_attr( 'after' ) ?>
  </div>
</div>