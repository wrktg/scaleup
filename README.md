# ScaleUp API for WordPress Web Apps

ScaleUp is framework for building web apps on WordPress. The goal of this framework is to provide an easy to use and
remember API that allows developers to programatically build custom applications and large sites.

## Features

### Forms API
ScaleUp Forms API makes it easy to programatically create, display and process custom forms. 

#### Code Example
Create a form in theme's functions.php or plugin
```php
# create a new empty form with handle 'contact'
$form = add_form( array( 
	'name' 	=> 'contact',
	'title' => 'Send me a message',
) );

# add form fields
add_form_field( $form, array(
	'name' 		=> 'name',
	'label'		=> 'Name',
	'validation'=> array( 'required' ),
) );
add_form_field( $form, array(
	'name' 		=> 'email',
	'label'		=> 'Email',
	'validation'=> array( 'required' ),
) );
add_form_field( $form, array(
	'name'		=> 'message',
	'label'		=> 'Message',
) );
add_form_field( $form, array(
	'name'		=> 'submit',
	'type'		=> 'button',
	'text'		=> 'Submit'
) );

# add a notification to the form
add_form_notification( $form, array(
	'method'	=> 'email',
	'to'		=> get_bloginfo( 'admin_email' ),
	'from'		=> '{name} <{email}>',
	'subject'	=> '{name} sent you a message via your site',
	'message'	=> '{message}'
) );

# write code to handle form processing
if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$form->process( $_POST );
}
```
Display form using a template tag
```php
# in a template in your theme
the_form( 'contact' );
```

#### Forms Style
By default, ScaleUp Forms uses [Bootstrap](http://twitter.github.com/bootstrap/) styling and html structure. The templates that are included with ScaleUp Forms follow can be easily overwritten in the parent or child theme. You can see ScaleUp Forms default templates in the [templates directory](templates). 

### Items API
ScaleUp Items API is a unified API for manipulate WordPress content. Using the ScaleUp Items API you can programatically CRUD(create, read, update & delete) content and its associated metadata, taxonomies and relationships. 

#### Code Example
To create an item using Items API
```php
$item = create_item( array(
	'post_title' 		=> 'Introduction to ScaleUp',
	'post_content'		=> '…',
	'post_status'		=> 'Draft',
	'post_categories' 	=> array( 'Article', 'ScaleUp' ),
	'post_tags'			=> array(),
	'post_thumbnail'	=> 'http://example.com/example.jpg' # or file upload array
) );
```

To update an item using Items API
```php
$updated = update_item( array(
	'ID'				=> 322,
	'post_title' 		=> 'New Title',
	'post_thumbnail'	=> array(
		'name' 		=> 'foo.txt',
        'type' 		=> 'image/jpeg',
        'tmp_name' 	=> '/tmp/phpYzdqkD',
        'error' 	=> 0
        'size' 		=> 123
        ),
    'post_tags'			=> array( 'Excellent', 'Relevant' ),
);
```

To read an item from the database
```php
$item = read_item( 323 );
# item object will contain all values including taxonomies, metadata & relationships
```

To delete an item
```php
delete_item( 323 );
```

### Schemas API
Schemas API allows a developer to apply soft structure to WordPress content. A schema is a post type and a collection of its properties, taxonomies and relationships. In fact, WordPress's post is implemented as a *post* schema and is available by default.

An item can have multiple schemas. A schema can be created with a post type assignment. ScaleUp's item functionality piggybacks on WordPress's post functionality, therefore an item can not have more than 1 schema with a post type assignment.

ScaleUp Schemas API relies on *scribu*'s [Posts 2 Posts](http://wordpress.org/extend/plugins/posts-to-posts/) to provide relationship functionality.

#### Code Example
Register a schema to make it available within a site
```php
register_schema( 'person', array(
	'givenName'	=> array(
		'type' => 'property',
	),
	'lastName'	=> array(
		'type' => 'property',
	),
	'organization'	=> array(
		'type' => 'relationship',
		'to'   => 'organization',
	),
	'department'	=> array(
		'type' 		=> 'taxonomy',
		'taxonomy' 	=> 'departments'
	),
);
```

### Templates API
ScaleUp Template API allows a developer to provide custom templates with a plugin that can be overwritten in the parent theme or child theme. Once a template is registered, it is available to be included in a theme's template using ```get_template_part()```.

#### Code Example
```php
add_template( array(
	'template' => '/custom-template.php',
	'path' => dirname( __FILE__ ) . '/templates' ,
) );
```

*Note:* 'path' + 'template' should be real path to your template.

### Assets API
ScaleUp Assets API is a unified API for managing styles and scripts. Once a script or style is registered via ScaleUp Assets API, they can be added to templates to be enqueued automatically when a template is included using ```get_template_part()```.

#### Code Example
```php
// register a js to be used in your plugin
register_asset( array(
	'my_js_asset' => array(
		'type'	=> 'script',
		'src'	=> '/my-plugin/js/plugin.js',
		'deps'	=> array( 'jquery' ),
	),
) );

// register a css to be used in your plugin
register_asset( array(
	'my_css_asset' => array(
		'type'	=> 'style',
		'src'	=> '/my-plugin/css/plugin.css',
	),
) );
```

### App API

### add_app( $args ) or $site->add( 'app', $args )

Returns an app object that you can add views and add-ons to. This function takes an args array. Via the args array, you can specify the base url for this app using the **url** option. All views that are added to this app, will follow the url that you specify via this option.

#### Functional example
```php
$my_app = add_app( array(
	'url'	=> '/my_app',
));
```

#### Object Oriented example
```php
$site = ScaleUp::get_site();		// gives you $site object that you can add Apps to
$site->add( 'app', array(
	'url' => '/my_app',
));
```

### add_view( $app, $args ) or $app->add( 'view', $args )

Returns view object that you can use to add callback functions that will be called when a request is matched to this view's url.

#### Functional example
```php
$my_view = add_view( $app, array(
	'url' => '/my-view'
));
```

#### Object Oriented example
```php
$my_view = $app->add( 'view', array(
	'url' => '/my_app',
));
```

### add_addon( $app, $args ) or $app->add( 'addon', $args )

Returns addon object that contains all features that are included with this addon.

#### Functional example
```php
$profile_addon = add_addon( $app, array(
	'name' => 'profile',
));
```

#### Object Oriented example
```php
$profile_addon = $app->add( 'addon', array(
	'name' => 'profile',
));
```

## API Status

The architecture has gone through 2 development iterations and I feel fairly confident that it will not change much going forward. Its been tested under a bunch of different scenarios and it has proven to be flexible enough to have all of the situations in an elegant way. 

The functions that are available in [functions.php](functions.php), [template-tags.php](template-tags.php), [actions.php](actions.php) and [filters.php](filters.php) are very minimal therefore they have very little reason to change. New functions will be added over time.
