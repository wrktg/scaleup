<form id="<?php echo get_form_attr( "name" ) ?>" <?php the_form_attr( "action" ) ?> <?php the_form_attr( "enctype" ) ?> <?php the_form_attr( "method" ) ?> <?php the_form_attr( "class" ) ?>>
  <?php if ( get_form_attr( 'title' ) ) : ?>
    <?php echo get_form_attr( 'before_title' ) . get_form_attr( 'title' ) . get_form_attr( 'after_title' ); ?>
  <?php endif ?>

  <?php if ( get_form_attr( 'description' ) ) : ?>
    <p><?php echo get_form_attr( 'description' ); ?></p>
  <?php endif; ?>

  <?php while ( form_has_fields() ) : ?>
    <?php the_form_field() ?>
  <?php endwhile; ?>
</form>