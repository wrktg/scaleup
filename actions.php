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
      <div class="help-block">
      <?php foreach ( $alerts as $alert ) : ?>
          <div class="alert alert-<?php echo $alert[ 'type' ]; ?>"><?php echo $alert[ 'msg' ]; ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif;
  }
}