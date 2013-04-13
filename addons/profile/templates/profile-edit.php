<?php get_header(); ?>

<div id="primary" class="span12">
  <div id="content" role="main">
    <h2 class="title">Profile</h2>

    <div class="well span6">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
        <li><a href="#password" data-toggle="tab">Password</a></li>
      </ul>
      <div id="profileTabs" class="tab-content">
        <div class="tab-pane active in" id="profile">
          <?php the_form( 'profile' ); ?>
        </div>
        <div class="tab-pane fade" id="password">
          <?php the_form( 'password' ); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>