<?php
    require '../classes/class.logger.php';
    require '../classes/class.tags_checker.php';
    require '../classes/class.curl_request.php';
    require '../apps/class.tags_checker_app.php';


    define('APP_ROOT', getcwd());

    $app = TagsCheckerApp::getInstance();
    $app->main();
?>
