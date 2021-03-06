<?php
#phpinfo() and exit;

const DS = DIRECTORY_SEPARATOR; // All folder constants end in a trailing slash /

define('APP_ROOT', __DIR__ . DS);  // Set our root folder for the application

const SERVER_ROOT = APP_ROOT;        // I would like to change to only using app_root soon

if (false === (include __DIR__ .'/vendor/autoload.php'))
{
    // Load the autoload() for composer dependencies located in the Services folder
    print '<h1>Loading Composer Failed. See CarbonPHP.com for documentation.</h1>' and die;
    // Composer autoload
}

$app = new CarbonPHP\CarbonPHP(APP_ROOT . 'config/config.php');

/** At one point I returned the invocation of $app to show that
 * the application will not exit on completion, but rather return
 * back to this index file. This means you can still execute code
 * after $app(); I stopped returning the __invoke() because if false
 * is returned here, the index will re-execute.
 * This turns very bad quickly.
 */

$app(\App\RootPrerogative::class);

return true;

