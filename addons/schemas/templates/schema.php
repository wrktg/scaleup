<?php get_header(); ?>

  <div id="primary" class="span12">
    <div id="content" role="main">

      <?php
      global $scaleup_view;
      $schema = $scaleup_view->get( 'schema' );
      $schema_type = $schema->get( 'schema_type' );
      ?>
      <a class="btn btn-primary" href="<?php echo get_view_permalink( 'schemas' ) ?>"><i class="icon-white icon-arrow-left"></i> Back</a>
      <h2><?php echo $schema_type ?></h2>

      <?php if ( empty( $schema ) ) : ?>
        <p class="error"><?php _e( 'This schema is not available on this site.' ) ?></p>
      <?php else: ?>
        <table class="table-bordered">
          <thead>
          <tr>
            <td><?php _e( 'Property' ) ?></td><td><?php _e( 'Property name' ) ?></td><td><?php _e( 'Description' ) ?></td><td><?php _e( 'Data Types' ) ?></td>
          </tr>
          </thead>
          <tbody>
          <?php foreach ( $schema as $property_name => $property ) : ?>
            <tr>
              <td><strong><?php echo $property->get( 'label' ); ?></strong></td>
              <td><?php echo $property->get( 'id' ) ?></td>
              <td><?php echo $property->get( 'comment'); ?></td>
              <td><?php echo implode( ', ', $property->get( 'ranges' ) ); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div><!-- #content -->
  </div><!-- #primary -->

<?php get_footer(); ?>