# ScaleUp Framework

ScaleUp is a WordPress-ish developer centric framework for building things with WordPress. Its designed to allow developers to create reusable functionality that is portable and easily customisable. These features can be packaged as plugins or included in theme's functions.php file.

## Features

### Forms API

ScaleUp Forms API makes it easy to programatically create, display and process custom forms.

#### Code Example

##### Create a form in theme's functions.php or plugin
```php
# create a new empty form with handle 'contact'
$form = add_form( array( 
	'name' 	=> 'contact',
	'title' => 'Send me a message',
));

# add form fields
add_form_field( $form, array(
	'name' 		=> 'name',
	'label'		=> 'Name',
	'validation'=> array( 'required' ),
));
add_form_field( $form, array(
	'name' 		=> 'email',
	'label'		=> 'Email',
	'validation'=> array( 'required' ),
));
add_form_field( $form, array(
	'name'		=> 'message',
	'label'		=> 'Message',
));
add_form_field( $form, array(
	'name'		=> 'submit',
	'type'		=> 'button',
	'text'		=> 'Submit'
));

# add a notification to the form
add_form_notification( $form, array(
	'method'		=> 'email',
	'to'			=> get_bloginfo( 'admin_email' ),
	'from'			=> '{name} <{email}>',
	'subject'		=> '{name} sent you a message via your site',
	'message'		=> '{message}'
));

# write code to handle form processing
if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$form->process( $_POST );
}
```

##### Display form using a template tag
```php
# in a template in your theme
the_form( 'contact' );
```

### Items API

ScaleUp Items API is a unified API for manipulate WordPress content. Using the ScaleUp Items API you can programatically CRUD(create, read, update & delete) content and its associated metadata, taxonomies and relationships. 

Checkout an example of Items API in action
```php
$item = create_item( array(
	'
));
```
