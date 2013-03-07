<?php
if ( !function_exists( 'scaleup_form_header_alerts' ) ) {
  /**
   * Show form errors above the form
   *
   * @param       $form ScaleUp_Form
   * @param array $args
   */
  function scaleup_form_header_alerts( $form, $args = array() ) {
    $alerts = $form->get_features( 'alerts' );
    if ( $alerts ) : ?>
      <span class="help-block">
      <?php foreach ( $alerts as $alert ) : ?>
          <span class="alert alert-<?php echo $alert[ 'type' ]; ?>"><?php echo $alert[ 'msg' ]; ?></span>
        <?php endforeach; ?>
      </span>
    <?php endif;
  }
}