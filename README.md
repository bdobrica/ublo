# this is the ublo framework #

## summary ##

this framework is intended to be used as mainly an easy to build CRUD interface to a relational database. i chose this approach as most of the business-oriented apps need only to manipulate data from a database. main directions that are followed during the building of this framework are:
* simplicity: this framework will do only the minimal things and will have an easy enough learning curve;
* object oriented: this framework will extensively use objects; heck, if the thing that it needs to do can't be an object, it won't be part of the framework;
* embedded html / buffered output: embedded html is the fastest so i'll use that; as operation order should not influence the render, the output is buffered;
* events: this framework will use events for all CRUD operations;

## file structure ##

the file structure will be kept clear and simple:
~~~~
web/----+
	+-config.php			: main config file
	+-index.php			: this is the main entry point for the web interface
	+-api/----------+		: this is where the async calls are stored
	|		+-ajax/-+	: here are all the ajax calls are made
	|		|	+-index.php : the ajax call entry point
	|		+-json/-+	: here are all the json calls made
	|		|	+-index.php : the json call entry point
	|		+-rpc/--+
	|			+-index.php : the rpc call entry point, requires token
	|			+-oauth/+
	|				+-index.php : the token generations
	|		
	+-assets/-------+		: this is the assets interface folder
	|		+-js/		: here are all the JS scripts stored
	|		+-css/		: here are all the CSS scripts stored
	|		+-img/		: here are all the images stored
	+-uploads/			: if any, here are the upload files stored
					: we choose a path like {ext}/{hash}.{ext} where {ext} is the 3 letter file extension
class/--+
	+-_init.php			: this is the initalization object (ublo/_init)
	+-api/----------+		: api objects are stored
	|		+-_ajax.php	: extends ublo/_api enabling ajax calls
	|		+-_json.php	: extends ublo/_api enable json calls
	|		+-_rpc.php	: extends ublo/_api enabling rpc calls
	|		+-ajax/-+
	|		|	+-_view.php	: extends ublo/api/_ajax enabling views for ajax
	|		|	+-_ctrl.php	: extends ublo/api/_ajax enabling ctrls for ajax
	|		|	+-view/
	|		|	+-ctrl/
	|		+-json/
	|		+-rpc/
	+-core/---------+
	|		+-_api.php
	|		+-_cron.php
	|		+-_db.php
	|		+-_event.php
	|		+-_file.php
	|		+-_model.php
	|		+-_oauth.php
	|		+-_theme.php
	|		+-_user.php
	|		+-_url.php
	+-cron/
	+-db/
	+-events/
	+-modules/
	+-vendor/
~~~~

## common objects ##

### ublo\_init ###
