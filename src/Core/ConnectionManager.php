<?php

/*
 * Copyright 2018 AutoZone, Inc.
 * Content is confidential to and proprietary information of AutoZone, Inc., its
 * subsidiaries and affiliates.
 */

namespace Core;

use Core\Database;
/**
 * Description of ConnectionManager
 *
 * @author jvillalv
 */
class ConnectionManager {    
    private static $connections; 
    public function __construct() {
        if(!isset($this->connections)){
            $this->connections = [];
        }          
    }
    
    public static function getConnection( $key){
        if(!isset(ConnectionManager::$connections[$key])){
            ConnectionManager::$connections[$key] =  new Database($key);
        }
        return ConnectionManager::$connections[$key];
    }
}
