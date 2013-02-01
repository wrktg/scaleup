<?php get_header(); ?>

  <div id="primary" class="span12">
    <div id="content" role="main">

      <h2>Schemas</h2>
      <?php global $scaleup_view; $schema_types = $scaleup_view->get( 'schema_types' ); ?>

      <?php if ( empty( $schema_types ) ) : ?>
        <p class="error"><?php _e( 'This site does not have any active schemas.' ) ?></p>
      <?php else: ?>
        <table class="table-bordered">
          <caption>This site uses the following schemas.</caption>
          <thead>
            <tr>
              <td><?php _e( 'Schema Type' ) ?></td><td><?php _e( 'Description' ) ?></td><td><?php _e( 'Link' ) ?></td>
            </tr>
          </thead>
          <tbody>
          <?php foreach ( $schema_types as $schema_type => $schema_type_object ) : ?>
            <tr>
              <td><?php echo $schema_type_object->get( 'label' ) ?></td>
              <td><?php echo $schema_type_object->get( 'comment') ?></td>
              <td><a href="<?php echo get_view_permalink( 'schema', array( 'schema_type' => $schema_type_object->get( 'label' ) ) ) ?>">View Properties</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div><!-- #content -->
  </div><!-- #primary -->

<?php get_footer(); ?>