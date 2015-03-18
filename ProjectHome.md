**taskman** is a simple [PHP](http://php.net) library for writing automation tasks in a similar with [Ant](http://ant.apache.org) and [rake](http://rake.rubyforge.org) fashion.

It's probably not that elegant as rake but if you want to stick to PHP and have Ant-alike functionality _without any XML programming_ then taskman may turn out to be handy. taskman is very simple to use, it requires only one include, all its code resides in one PHP file, and it has no external dependencies.

You can read the complete library documentation on the TaskmanUsage page. You can also read comparisons with SimilarPhpSolutions.

Here is the simplest usage example. Download and unpack the archive. Put taskman.inc.php to some place where you can include it from(I personally tend to bundle this script with every project). Create a **task.php** script with the following contents:

```
<?php
require('taskman.inc.php');

taskman_run($argv);

function task_hello()
{
  echo "Hello\n";
}

function task_comma()
{
  echo ",\n";
}

/**
 * @deps comma
 */
function task_world()
{
  echo "World\n";
}

/**
 * @deps hello,world
 */
function task_say($args = array())
{
  if(isset($args[0]))
    echo $args[0] . "\n";
}                       
```

You can run this script now in the command line as follows:

```
>php task.php say Whatever
************************ Running task 'hello' ************************
Hello
************************* 'hello' done (0 sec.)*************************
************************ Running task 'comma' ************************
,
************************* 'comma' done (0 sec.)*************************
************************ Running task 'world' ************************
World
************************* 'world' done (0 sec.)*************************
************************ Running task 'say' ************************
Whatever
************************* 'say' done (0 sec.)*************************
************************ All done (0 sec.)************************
```

**taskman** was created as a part of the [Limb3](http://limb-project.com) but I really think it deserves to be a separate project.