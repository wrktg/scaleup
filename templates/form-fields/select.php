<div class="control-group <?php echo ( get_form_field_attr( 'error' ) ) ? 'error' : ''; ?>">
  <?php ScaleUp::get_template_part( "label" ); ?>
  <div class="controls">
    <?php echo get_form_field_attr( 'before' ) ?>
    <select <?php the_form_field_attr( 'id' ) ?> <?php the_form_field_attr( 'name' ) ?> <?php the_form_field_attr( "class" ); ?>>
      <?php if ( has_form_field_attr( 'options' ) ) : $options = get_form_field_attr( 'options' ); ?>
        <?php if ( is_array( $options ) ) : ?>
          <?php foreach ( $options as $value => $text ) : ?>
            <option value="<?php echo $value ?>" <?php echo ( !is_null( get_form_field_attr( 'value' ) ) && $value == get_form_field_attr( 'value' ) ) ? 'selected' : ''; ?>><?php echo $text ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </select>
    <?php echo get_form_field_attr( 'after' ) ?>
  </div>
  <?php ScaleUp::get_template_part( "help" ); ?>
</div>