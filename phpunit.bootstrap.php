<?php
// Não é a melhor forma de lidar com isso.
// Mas é a mais segura sem haver disciplina na hora de fazer os deploys
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'testing');

if (!defined('APIGILITY_PATH')) {
    define('APIGILITY_PATH', realpath(__DIR__));
}

// Isso ainda é usado no cache da Relejo no ZF2 e no ZF3. Tem que colocar no autoload!
defined('APPLICATION_DATA') || define('APPLICATION_DATA', realpath(__DIR__ . '/data'));

// Isso ainda é usado no cache da Relejo no ZF2 e no ZF3. Tem que colocar no autoload!
defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(__DIR__ . '/'));

// Required for password hashs
define('HASHTAG', 'super secret hashtag');

require 'vendor/autoload.php';


