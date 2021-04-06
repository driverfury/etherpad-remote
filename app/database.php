<?php

$db_config = (require('config.php'))['database'];
return mysqli_connect(
    $db_config['host'],
    $db_config['user'],
    $db_config['password'],
    $db_config['name']
);
