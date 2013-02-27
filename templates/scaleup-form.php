<form <?php scaleup_the_form_attr( "id" ) ?> <?php scaleup_the_form_attr( "action" ) ?> <?php scaleup_the_form_attr( "enctype" ) ?> <?php scaleup_the_form_attr( "method" ) ?> <?php scaleup_the_form_attr( "class" ) ?>>
  <?php if ( scaleup_get_form_attr( 'title' ) ) : ?>
    <?php echo scaleup_get_form_attr( 'before_title' ) . scaleup_get_form_attr( 'title' ) . scaleup_get_form_attr( 'after_title' ); ?>
  <?php endif ?>

  <?php if ( scaleup_get_form_attr( 'description' ) ) : ?>
    <p><?php echo scaleup_get_form_attr( 'description' ); ?></p>
  <?php endif; ?>

  <?php while ( scaleup_form_has_fields() ) : ?>
    <?php scaleup_the_form_field() ?>
  <?php endwhile; ?>
</form>