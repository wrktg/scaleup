<?php
/** @var $scaleup_form_field ScaleUp_Form_Field */
global $scaleup_form_field;
$alerts = $scaleup_form_field->get_features( 'alerts' );
/**
 * @todo: this might need to be redone. This might be very fragile and error prone.
 */
if ( $alerts ) : ?>
  <span class="help-block">
  <?php foreach ( $alerts as $alert ) : ?>
    <span class="alert alert-<?php echo $alert[ 'type' ]; ?>"><?php echo $alert[ 'msg' ]; ?></span>
  <?php endforeach; ?>
  </span>
<?php endif;