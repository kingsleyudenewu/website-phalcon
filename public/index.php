<?php

require __DIR__ . '/../apps/bootstrap.php';

use Phalcon\Mvc\Application;

try
{
    echo $application->main();
} catch (\Exception $e)
{
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
