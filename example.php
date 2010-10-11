<?php
require(dirname(__FILE__) . '/taskman.inc.php');

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
function task_say($args)
{
  if(isset($args[0]))
    echo $args[0] . "\n";
}                       
