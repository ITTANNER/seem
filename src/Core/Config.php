<?php

namespace Core;
use Exception;

final class Config {

    /** @var array config data */
    private static $DATA = null;


    public static function getConfig($section = null) {
        if ($section === null) {
            return self::getData();
        }
        $data = self::getData();
        if (!array_key_exists($section, $data)) {   
            throw new Exception('Unknown config section: ' . $section);
        }
        return $data[$section];
    }


    private static function getData() {
        if (self::$DATA !== null) {
            return self::$DATA;
        }
        self::$DATA = parse_ini_file(__DIR__ . '/../../config/config.ini', true);
        return self::$DATA;
    }

}
