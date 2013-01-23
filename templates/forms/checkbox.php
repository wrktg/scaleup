

<?php the_form_field_attr( "before_field" ); ?>

<textarea id="<?php the_form_field_attr( "id" ) ?>" class="<?php the_form_field_attr( "class" ) ?>" rows="<?php the_form_field_attr( "rows" ) ?>">
  <?php the_form_field_attr( "value" ) ?>
</textarea>

<?php the_form_field_attr( "after_field" ); ?>

<?php get_template_part( "/bootstrap-form/input-help" ) ?>


<label class="checkbox">
  <input type="checkbox" value="">
  Option one is this and that—be sure to include why it's great
</label>

<label class="radio">
  <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
  Option one is this and that—be sure to include why it's great
</label>
<label class="radio">
  <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
  Option two can be something else and selecting it will deselect option one
</label>