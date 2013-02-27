<div class="control-group <?php echo ( scaleup_has_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">

    <select id="<?php echo $id ?>" <?php echo scaleup_the_form_field_attr( 'name' ) ?> <?php scaleup_the_form_field_attr( "class" ); ?>>

      <?php if ( scaleup_has_form_field_attr( 'options' ) ) : $options = scaleup_get_form_field_attr( 'options' ); ?>

        <?php if ( is_array( $options ) ) : ?>

          <?php foreach ( $options as $value => $text ) : ?>

          <option value="<?php echo $value ?>"><?php echo $text ?></option>

          <?php endforeach; ?>

        <?php endif; ?>

      <?php endif; ?>

    </select>

    <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>

  </div>
</div>