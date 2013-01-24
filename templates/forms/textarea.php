<?php get_template_part( "/forms/label.php" ) ?>

<?php echo get_form_field_attr( "before_field" ); ?>
  <textarea <?php the_form_field_attr( "name" ); ?> <?php the_form_field_attr( "id" ); ?> <?php the_form_field_attr( "class" ); ?> <?php the_form_field_attr( "rows" ); ?>>
    <?php echo get_form_field_attr( "value" ) ?>
  </textarea>
<?php echo get_form_field_attr( "after_field" ); ?>

<?php get_template_part( "/forms/help.php" ) ?>