# Fern Framework

PHP Framework designed in mind for write less code.

### Features
- Simpe and powerful ORM
- CRUD with a couple of lines.
- Fully customizable Router
- Template engine
- Form creation and validation with database models



### Simple hello word app

app/index.php

	require_once '../framework/Loader.php';
	$app = new fw\App();
	$app->getRouter()->get('/', function(){
	   return '<h1>Hello word</h1>'; 
	});
	$app->execute();


### Advanced Hello word
This example counts and store origin of visitors using single MCV Controller, templates and database models.

**app/index.php**

	require_once '../framework/Loader.php';
	$app = new fw\App();
	$app->getRouter()->singleController('hello.php', '/');
	$app->setDefaultDatabase(new fw\DBContext('yourdatabase', 'root', 'yourpassword'));
	$app->execute();

**app/models/Visit.php**

The first time of instantiation this model auto create table if does not exist.

	class Visit extends \fw\Data\Record{
		/**
		*@type String
		*@required
		*/
		public $Origin;
	}

**app/hello.php**

	class Hello extends \fw\Controller{
		public function defaultGetAction(){
			$visit = new Visit();
			$visit->Origin = $_SERVER["HTTP_ORIGIN"];
			$visit->save();
			$this->count = $visit->getRepository()->count();
			return self::View();
		}
	}

**app/default.tpl**

	<h1>Hello word! Visits: <?=$self->count?></h1>



### TODOS
- Documentation
- Optimizations
- ...
- ...
- ...


