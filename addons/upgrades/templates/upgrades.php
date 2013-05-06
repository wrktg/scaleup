<?php get_header(); ?>

  <div id="wrapper">
    <div class="container">
      <div class="row span4">

        <h3>Upgrades</h3>
        <?php the_form( 'upgrades' ); ?>

        <h3>Applied Upgrades</h3>
        <ul>
          <?php foreach ( $applied_upgrades as $applied ): ?>
            <li><?php echo $applied ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="row span4">

        <h2>Alerts</h2>
        <div id="alerts">

        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    jQuery(document).ready( function($){

      function scaleup_upgrades_alerts( data ) {
        data.alerts.forEach(function(alert) {
          $("#alerts").prepend( '<div class="alert '+alert.type+'">' + alert.msg + '</div>');
        });

      }

      $('#field_button').click(function(e){
        $( "input[type=checkbox]:checked" ).each(function(){
          var name = $(this).attr('name')
          $.ajax({
            url: '<?php echo $this->view->get_url() ?>',
            data: $("#upgrades").serialize(),
            dataType: 'json',
            type: 'POST'
          }).done(scaleup_upgrades_alerts);
        });
        e.preventDefault();
      });
    });
  </script>

<?php get_footer(); ?>