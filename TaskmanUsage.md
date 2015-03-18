# Introduction #

**taskman** is a simple [PHP](http://php.net) library for writing automation tasks in a similar with [Ant](http://ant.apache.org) and [rake](http://rake.rubyforge.org) fashion.

# Usage #

## Bootstrap ##

In order to run the taskman powered script(say, **task.php**) you need to include the library and call the **taskman\_run(...)** function in your script passing it the command line arguments. For example:

```
<?php
require('taskman.inc.php');
taskman_run($argv);
```

Running this script should yield something as follows:

```
> php task.php 
************************ Running task 'help' ************************

Usage:
 php <taskman-script> [OPTIONS] <task-name1>[,<task-name2>,..] [-D PROP1=value [-D PROP2]]

Available options:
 -c    specify PHP script to be included(handy for setting props,config options,etc)
 -v    be verbose(default)
 -q    be quite
 -b    batch mode: be super quite, don't even output any system messages

Available tasks:
---------------------------------
 help  @desc Shows this help

************************* 'help' done (0 sec.)*************************
```

## Tasks ##
A task is a PHP function which starts with **task`_`** prefix. For example:

```
function task_foo(){}
```

### Task invocation ###

You can invoke the task from the command line as follows:

```
> php task.php foo
```

It's also possible to invoke several tasks using comma separated list of task names in the command line.

You should not directly call task functions in a PHP code since you won't get any benefits taskman provides you with(e.g dependency resolution). To invoke the task in the code you should rather use a **taskman\_runtask($name, $args = array())** API function. For example:

```
function task_foo()
{
  taskman_runtask("bar");
}
```

**Note:** tasks are executed _only once_. It's not a bug, it's a feature. If you really need to repeatedly call some piece of logic you can simply place this logic into a regular PHP function and use it.

### Task arguments ###

You can pass arguments to the task in the command line as follows:

```
> php task.php foo hello world
```


These arguments will be available as an array argument for the task function:

```
function task_foo($args)
{
  var_dump($args);
}
```

### Show tasks ###

You can view all tasks available using the built-in **help** task as follows:

```
> php task.php help
```

**Tip:** if you can't recall the full name of the task it's possible to make a partial matching search for all similar tasks - just provide a part of the task name as an argument for help as follows:

```
> php tasks.php help what_
```

### Task dependencies ###

Tasks can depend on execution of other tasks. Dependencies are specified using **@deps** tag as follows.

```
/**
 * @deps bar,zoo
 */
function task_foo(){}
```

In this example tasks **bar** and **zoo** will be executed(and their dependencies as well) before execution of the **foo** task.

See below all tags available.

### Task aliases ###

Sometimes it's convenient to provide an alias for the task with a long name. For example:

```
/**
 * @alias b
 */
function task_bar(){}
```

An alias acts absolutely as a regular task name, you can use them interchangeably without any restrictions.

See below for all tags available.

## Tags ##
Each task can have misc meta tags attached to it using Javadoc-alike description. For example:

```
<?php
/**
 * @alias b
 * @descr This is a really helpful task!
 */
function task_bar(){}
/**
 * @deps bar
 * @alias f
 */
function task_foo(){}
```

The following tags are currently supported:

  * **@after task [,task2,... ]** - comma separated list of task names _after_ which the current task must be executed
  * **@alias name [,name2,... ]** - task name alias(several aliases can be specified using comma)
  * **@always** - mark the task which must be executed _always_
  * **@before task [,task2,... ]** - comma separated list of task names _before_ which the current task must be executed
  * **@default** - mark the task to be executed by default
  * **@deps task [,task2,... ]** - comma separated list of task names which must be executed _before_ the current one
  * **@desc txt** - detailed description of a task which can be seen in a help screen

## Properties ##

Properties are similar to global variables. You can access/modify properties in tasks.

### Setting properties ###

Properties can be passed in the command line using -D option. For example:

```
> php task.php -D FOO=1 -D BAR=2
```

Properties can be set as well using taskman internal API. For that purpose you can use **taskman\_propset($name, $value)** function, for example:

```
function task_foo()
{
  taskman_propset("FOO", 42);
}
```

Sometimes it's convenient to assign a value to the property only if it is not set already. This way it's possible to define default values for properties in one place and make it possible to override some of them from the command line. In order to achieve this you should use **taskman\_propsetor($name, $value)** function as follows:

```
/**
 * @always
 */
function task_defaults()
{
  taskman_propsetor("FOO", 42);
}
```

### Accessing properties ###

To access the property from the task you can use **taskman\_prop($name)** or **`__`(...)** functions. For example:

```
function task_foo()
{
  echo taskman_prop("FOO") . " " . taskman_prop("BAR");
}
```

The code above can be re-written using somewhat a simpler notation:

```
function task_foo()
{
  echo __("%FOO% %BAR%");
}
```

You can check if the prop exists using the **taskman\_isprop($name)** function. For example:

```
function task_foo()
{
  if(taskman_isprop("BAR")
  {
    //do something useful
  }
}
```

Alternatively, you can use the **taskman\_propor($name, $default)** function if you want to get some default value in case the property is not set. For example:

```
function task_foo()
{
  $value = taskman_propor("FOO", 42);
}
```

### External properties ###

It's convenient to provide an optional possibility to store properties in an external file.  This can be achieved as follows, put the following lines into the top of your tasks script:

```
if(file_exists(dirname(__FILE__) . '/task.props.php')
  include(dirname(__FILE__) . '/tasks.props.php');
```

**task.props.php** is just a plain PHP file which may store properties as follows:

```
taskman_propsetor("FOO", 42);
taskman_propsetor("BAR", "Whatever");
```

Usually this file is not under version control, so that members of your team may customize it to their taste.

**Tip:** As you may note, we are using **taskman\_propsetor** here so it's still possible to override these properties over the command line.