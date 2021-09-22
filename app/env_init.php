<?php

//Load envs
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function env($key, $default_value = null) {
    if(isset($_ENV[$key])) {
        $value = $_ENV[$key];
    } else {
        $value = $default_value;
    }
    if(strpos($value, "\${") !== false) {
        foreach ($_ENV as $key => $val) {
            $value = str_replace("\$\{$key\}", $val, $value);
        }
    }
    if(!$value) return  false;
    if(in_array($value, ['yes', 'true']))  return true;
    if(in_array($value, ['no', 'false'])) return false;
    return $value;
}

