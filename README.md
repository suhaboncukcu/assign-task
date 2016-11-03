# AssignTask plugin for CakePHP

## Installation


Require the plugin;
```
composer require suhaboncukcu/assign-task:dev-master

```

Load the plugin;
```php

Plugin::load('AssignTask', ['bootstrap' => true, 'routes' => true]);

```

Create missions.php file in your config folder;
```php
//you can find an example in vendor/AssignTask/config/missions.sample.config


//in your bootstrap.php:
Configure::write('Missions.config', ['missions']);
```

**ATTENTION** Check the migration files and see if from_id and to_id types work for you.
You should check their types if your user table uses something other than integer for ids. 
```php

bin/cake migrations migrate -p AssignTask

```

## Examples

```php

	$this->loadModel('AssignTask.Missions');

    //create new mission
    $mission = $this->Missions->newEntity();
    $data = [
        'to_id' => 1,
        'from_id'=> 2,
        'mission' => 'please send mail to customers',
        'schedule' => '2016-12-12 10:00'
    ];
    $this->Missions->patchEntity($mission, $data);
    $this->Missions->save($mission);


    //assign existing mission to someone else
    $mission = $this->Missions->get(1);
    $mission->to_id = 3;
    //can change from id too. If somebody else this that assignment. 
    //for example, in this assignment user with id 3 assigns this mission to
    //himself/herself
    $mission->from_id = 3;
    $this->Missions->assignTo($mission);


    //complete an existing issue
    $mission = $this->Missions->get(5);
    $this->Missions->complete($mission);

    
    // list all uncompleted tasks including reassigned ones
    $missions = $this->Missions->find('Uncompleted');

    
    // list all completed tasks including reassigned ones
    $missions = $this->Missions->find('Completed');


    
    // list all uncompleted tasks including reassigned ones 
    // passed the due date
    $missions = $this->Missions->find('UncompletedPassed');

    
    // list all tasks without reassigned ones
    // so this is what you need to show current uncompleted tasks
    $missions = $this->Missions->find('WOReassigned');

    // list all  tasks 
    // while getting their parent tasks. So you can check 
    // which task this was.
    $missions = $this->Missions
                        ->find('Parents');


    // list all  tasks 
    // while getting their child tasks. So you can check 
    // which task reassigned again.
    $missions = $this->Missions
                        ->find('Children');


    // of course, you can use different finders together
    $missions = $this->Missions
                        ->find('Uncompleted')
                        ->find('WOReassigned')
                        ->find('Parents')
                        ->find('Children'); 


```
