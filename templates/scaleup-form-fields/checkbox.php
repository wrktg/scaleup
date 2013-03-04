<div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php get_template_part( "/scaleup-form-fields/label.php" ); ?>
  <div class="controls">
    <?php if ( has_form_field_attr( 'options' ) ) : $options = get_form_field_attr( 'options' ); ?>
      <?php if ( is_array( $options ) ) : ?>
        <?php foreach ( $options as $value => $text ) : $id = get_form_field_attr( "id" ) . '_' . sanitize_title( $value ) ?>
          <label for="<?php echo $id ?>" class="checkbox">
            <input id="<?php echo $id ?>"
                   name="<?php echo get_form_field_attr( 'name' ) ?>[]" <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "class" ); ?>
                   value="<?php echo $value ?>">
            <?php echo $text ?></label>
        <?php endforeach; ?>
      <?php endif ?>
    <?php else: ?>
      <label for="<?php echo get_form_field_attr( "id" ); ?>">
        <input <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "type" ); ?> <?php the_form_field_attr( "class" ); ?>
          value="<?php echo $value ?>">
        <?php echo get_form_field_attr( "text" ); ?>
      </label>
    <?php endif; ?>
  </div>
  <?php get_template_part( "/scaleup-form-fields/help.php" ); ?>
</div>