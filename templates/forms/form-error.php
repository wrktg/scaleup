<?php if ( is_wp_error( $error = get_form_attr( 'error' ) ) ) : ?>
  <div class="alert alert-error">
    <?php echo $error->get_error_message(); ?>
  </div>
<?php endif; ?>