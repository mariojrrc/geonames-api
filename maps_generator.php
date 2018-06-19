#!/usr/bin/env php
<?php
declare(strict_types=1);

define('APPLICATION_ENV', 'production');
$config = require "config/application.config.php";

chdir(__DIR__ );

foreach($config['modules'] as $module) {
    if (is_dir(__DIR__ . '/module/' . $module)) {
        echo "\nmodulo $module\n";

        $command = "vendor/zendframework/zendframework/bin/classmap_generator.php -l module/$module";
        $handle = popen($command . ' 2>&1', 'r');
        echo fread($handle, 2096);
        pclose($handle);

        $command = "vendor/zendframework/zendframework/bin/templatemap_generator.php -l module/$module -v module/$module/view";
        $handle = popen($command . ' 2>&1', 'r');
        echo fread($handle, 2096);
        pclose($handle);

    }
}

echo "\nclass maps and template maps generated\n\n";

exit(0);
