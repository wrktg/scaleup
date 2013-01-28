<?php get_header(); ?>

<div id="primary" class="span12">
  <?php tha_content_before(); ?>
  <div id="content" role="main">

    <div class="row">
      <div class="offset2 span3">
        <div class="alert success">
          <?php echo get_form_attr( 'confirmation' ); ?>
        </div>
      </div>
    </div>

  </div><!-- #content -->
  <?php tha_content_after(); ?>
</div><!-- #primary -->

<?php get_footer(); ?>