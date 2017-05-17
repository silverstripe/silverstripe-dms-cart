<?php

define('DMS_CART_DIR', basename(__DIR__));

if (!file_exists(BASE_PATH . DIRECTORY_SEPARATOR . DMS_DIR)) {
    user_error('DMS directory named incorrectly. Please install the DMS module into a folder named: ' . DMS_DIR);
}
