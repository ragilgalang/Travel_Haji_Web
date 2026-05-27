<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
echo asset('http://localhost:8000/uploads/images/test.jpg');
