<?php
global $scaleup_form_field;
if ( is_object( $scaleup_form_field ) ) {
  $template_name = $scaleup_form_field->get_default_template();

  $site = ScaleUp::get_site();
  $feature = $site->get_feature( 'template', $template_name );
  if ( is_object( $feature ) && $feature->has( 'template' )) {
    $template = $feature->get( 'template' );
    get_template_part( $template );
  }
}
?>

<script type="text/javascript">
  jQuery(document).ready(function ($) {
    $("#<?php echo scaleup_get_form_field_attr( 'id' ) ?>").select2(
      <?php echo json_encode( wp_parse_args( (array) scaleup_get_form_field_attr( 'params' ), array( 'width' => 'resolve' ) ) ); ?>
    );
  });
</script>