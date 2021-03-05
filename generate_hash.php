<?php
$time = time();
echo "Time: $time".PHP_EOL."Hash: ".sha1($argv[1].$time.'esta es la clave secreta').PHP_EOL;