<?php
require(dirname(__FILE__) . '/taskman.inc.php');

$version = trim(file_get_contents(dirname(__FILE__) . '/VERSION'));
$build_name = "taskman-$version"; 
$build_dir = dirname(__FILE__) . "/build";

taskman_run($argv);

/**
 * @deps clean,prepare,zip,tar
 */
function task_release()
{}

function task_clean()
{
  global $build_dir;
  global $build_name;

  system("rm -rf $build_dir/$build_name");
}

function task_prepare()
{
  global $build_dir;
  global $build_name;

  mkdir("$build_dir/$build_name", 0777, true);
  system("cp README $build_dir/$build_name");
  system("cp VERSION $build_dir/$build_name");
  system("cp taskman.inc.php $build_dir/$build_name");
  system("cp example.php $build_dir/$build_name");
}

/**
 * @deps prepare
 */
function task_tar()
{
  global $build_dir;
  global $build_name;

  system("cd $build_dir && tar czf $build_name.tgz $build_name");
}

/**
 * @deps prepare
 */
function task_zip()
{
  global $build_dir;
  global $build_name;

  system("cd $build_dir && zip -r -9 $build_name.zip $build_name");
}
