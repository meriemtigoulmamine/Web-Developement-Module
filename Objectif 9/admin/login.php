<?php
require_once __DIR__ . "/../controllers/AuthController.php";
(new AuthController("../"))->admin_login();