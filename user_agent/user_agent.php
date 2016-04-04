<?php
    require '../classes/class.logger.php';
    require '../classes/class.curl_request.php';
    require '../apps/class.agent_app.php';

    define('APP_ROOT', getcwd());

    $app = AgentApp::getInstance();
    $app->main();
?>
