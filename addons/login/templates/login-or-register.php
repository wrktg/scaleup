<?php get_header(); ?>

  <div id="primary" class="span12">
    <div id="content" role="main">

      <div class="row">
        <div class="offset1 span4">
          <?php the_form( 'register' ); ?>
        </div>
        <div class="offset1 span4">
          <?php the_form( 'login' ); ?>
        </div>
      </div>

    </div>
  </div>

<?php get_footer(); ?>