<form id="<?php the_form_attr( "id" ) ?>"
      action="<?php the_form_attr( "action" ) ?>"
      enctype="<?php the_form_attr( "encoding" ) ?>"
      class="<?php the_form_attr( "class") ?>">
  <?php while ( form_has_fields() ) : the_form_field(); ?>

    <?php get_template_part( "/bootstrap-form/" . get_form_field_type(), get_form_field_attr( 'template' ) ); ?>

  <?php endwhile; ?>
</form>
