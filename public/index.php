<?php // @codingStandardsIgnoreFile
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
\chdir(\dirname(__DIR__));

// Redirect legacy requests to enable/disable development mode to new tool
if (\php_sapi_name() === 'cli'
    && $argc > 2
    && 'development' == $argv[1]
    && \in_array($argv[2], ['disable', 'enable'])
) {
    // Windows needs to execute the batch scripts that Composer generates,
    // and not the Unix shell version.
    $script = \defined('PHP_WINDOWS_VERSION_BUILD') && \constant('PHP_WINDOWS_VERSION_BUILD')
        ? '.\\vendor\\bin\\zf-development-mode.bat'
        : './vendor/bin/zf-development-mode';
    \system(\sprintf('%s %s', $script, $argv[2]), $return);
    exit($return);
}

// Decline static file requests back to the PHP built-in webserver
if (\php_sapi_name() === 'cli-server'
    && \is_file(__DIR__ . \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

if (\extension_loaded('newrelic')) {
    \newrelic_disable_autorum();
}

if (!\file_exists('vendor/autoload.php')) {
    throw new RuntimeException('Unable to load application.');
}

if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
    \define('REMOTE_ADDR', $_SERVER['HTTP_X_FORWARDED_FOR']);
} else {
    \define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
}

if (isset($_SERVER['APPLICATION_ENV'])) {
    \define('APPLICATION_ENV', $_SERVER['APPLICATION_ENV']);

} elseif (PHP_SAPI === 'cli') {
    throw new \RuntimeException('Cli connections are not enabled');
} else {
    // Linux Server
    if ($_SERVER['SERVER_ADDR'] === '10.0.3.6') {
        \define('APPLICATION_ENV', 'production');
        // Others
    } else {
        \define('APPLICATION_ENV', 'local');
    }
}

// Composer autoloading
// Setup autoloading
include 'vendor/autoload.php';

if (!\defined('APIGILITY_PATH')) {
    \define('APIGILITY_PATH', \realpath(__DIR__ . '/../'));
}

// Path para a pasta de dados do site
\defined('APPLICATION_DATA')
|| \define('APPLICATION_DATA', APIGILITY_PATH . '/data');

$appConfig = include APIGILITY_PATH . '/config/application.config.php';

if (\file_exists(APIGILITY_PATH . '/config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig,
        include APIGILITY_PATH . '/config/development.config.php');
}

// are fully qualified (e.g., IBM i). The following prefixes the default glob
// path with the value of the current working directory to ensure configuration
// globbing will work cross-platform.
if (isset($appConfig['module_listener_options']['config_glob_paths'])) {
    foreach ($appConfig['module_listener_options']['config_glob_paths'] as $index => $path) {
        if ($path !== 'config/autoload/{,*.}{global,local}.php') {
            continue;
        }
        $appConfig['module_listener_options']['config_glob_paths'][$index] = getcwd() . '/' . $path;
    }
}

// Run the application!
Zend\Mvc\Application::init($appConfig)->run();
