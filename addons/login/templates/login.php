<?php get_header(); ?>

  <div id="primary" class="span8">
    <?php tha_content_before(); ?>
    <div id="content" role="main">

      <div class="row">
        <div class="offset2 span4">
          <?php if ( the_form( 'login' ) ) : ?>
            <?php get_template_part( '/forms/form.php' ); ?>
          <?php endif; ?>
        </div>
        <div class="span4">
          <?php if ( the_form( 'register' ) ) : ?>
            <?php get_template_part( '/forms/form.php' ); ?>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- #content -->
    <?php tha_content_after(); ?>
  </div><!-- #primary -->

<?php
get_sidebar();
get_footer();


/* End of file page.php */
/* Location: ./wp-content/themes/the-bootstrap/page.php */