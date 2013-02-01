<?php get_header(); ?>

  <div id="primary" class="span12">
    <div id="content" role="main">

      <div class="row">
        <div class="offset2 span3">
          <?php if ( the_form( 'register' ) ) : ?>
            <?php get_template_part( '/forms/form.php' ); ?>
          <?php endif; ?>
        </div>
        <div class="offset1 span3">
          <?php if ( the_form( 'login' ) ) : ?>
            <?php get_template_part( '/forms/form.php' ); ?>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- #content -->
  </div><!-- #primary -->

<?php get_footer(); ?>