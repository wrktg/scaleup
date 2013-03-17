# ScaleUp Framework

ScaleUp is a WordPress-ish developer centric framework for building things with WordPress. Its designed to allow developers to create reusable functionality that is portable and easily customisable. These features can be packaged as plugins or included in theme's functions.php file.

## Features

### Forms
ScaleUp Forms API makes it easy to programatically create and display custom forms.

Checkout an example of the API in action:

#### in your theme functions.php or plugin

```php
$form = add_form( 'contact' );
add_form_field( $form, array(
	'name' 		=> 'name',
	'label'		=> 'Name',
	'validation' 	=> array( 'required' ),
));
add_form_field( $form, array(
	'name' 		=> 'email',
	'label'		=> 'Email',
	'validation' 	=> array( 'required' ),
));
add_form_field( $form, array(
	'name'			=> 'message',
	'label'		=> 'Message',
));
add_form_field( $form, array(
	'name'			=> 'submit',
	'type'			=> 'button',
	'text'			=> 'Submit'
));
add_form_notification( $form, array(
	'method'		=> 'email',
	'to'			=> 'tarasm@gmail.com',
	'from'			=> '{name} <{email}>',
	'subject'		=> '{name} sent you a message via your site',
	'message'		=> '{message}'
));
```

#### in a template in your theme
```php
the_form( 'contact' );
```

### Templates
