<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

$files = Storage::allFiles('logfiles');

echo "dummy";