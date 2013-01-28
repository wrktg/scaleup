<?php if ( get_form_field_attr( "help" ) ):  ?>
  <span class="help-block"><?php echo get_form_field_attr( "help" ); ?></span>
<?php endif ?>

<?php if ( has_form_field_attr( 'error' ) ): ?>
  <?php $error = get_form_field_attr( 'error' ); ?>
  <?php $messages = $error->get_error_messages(); ?>
  <?php foreach ( $messages as $message ): ?>
    <span class="help-block error"><?php echo $message; ?></span>
  <?php endforeach; ?>
<?php endif; ?>