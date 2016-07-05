<?php
/**
 * Startup app definitions
 *
 * @define Document root
 * @define Application path
 * @define Staging development environment
 */
defined('TMP_ROOT') || define('TMP_ROOT', './tmp');
defined('DOCUMENT_ROOT') || define('DOCUMENT_ROOT', dirname(dirname(__FILE__)));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', DOCUMENT_ROOT . '/../src/Sync');
define('ENV', (getenv('ENV') === 'production') ? 'production' :
    ((getenv('ENV') === 'testing') ? 'testing' : 'development'));
