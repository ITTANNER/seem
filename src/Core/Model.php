<?php

/*
 * Copyright 2017 jvillalv.
 *
 * you may not edit, copy or distribute this file except for use by an AutoZone employee or affiliate.
 */

namespace Core;

use Core\ConnectionManager;

/**
 * Description of Model
 *
 * @author jvillalv
 */
class Model {

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The model's database name
     *
     * @var string
     */
    protected $connection;
    
    /**
     * The model's table name.
     *
     * @var string
     */
    protected $table = "";

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /*     * ***********************************************************WHERE THE MAGIC HAPPENS*********************************************************************** */

    /**
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key) {
        return (isset($this->attributes[$key]));
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        return $this->getAttribute($key);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }

    public function __call($method, $parameters) {

        return $this->$method($parameters);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     */
    public function getConnection() {
        return ConnectionManager::getConnection($this->connection);
    }

    public function fill(array $attributes) {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    public static function allWith(array $keys, array $values) {
        $model = new static();
        $things = [];
        $query = "Select * from " . $model->table . " where ";
        $params = [];
        for ($i = 0; $i < count($keys); $i++) {
            $params[$keys[$i]] = $values[$i];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $keys[$i] . " = :" . $keys[$i];
        }
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {

            array_push($things, (new static)->fill($record));
        }
        return $things;
    }

    public static function all($columns = ['*']) {
        $cols = implode(',', $columns);
        $model = new static();
        $things = [];
        $data = $model->getConnection()->query("Select " . $cols . " from " . $model->table);
        foreach ($data as $recordkey => $record) {

            array_push($things, (new static)->fill($record));
        }
        return $things;
    }

    public static function findFirstBy($field, $value) {
        $model = new static();
        $values = $model->getConnection()->row("Select * from " . $model->table . " where " . $field . " = :field", array('field' => $value));
        if ($values) {
            $model = $model->fill($values);
            return $model;
        }
        return null;
    }

    public static function findWith(array $keys, array $values) {
        $model = new static();
        $query = "Select * from " . $model->table . " where ";
        $params = [];
        for ($i = 0; $i < count($keys); $i++) {
            $params[$keys[$i]] = $values[$i];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $keys[$i] . " = :" . $keys[$i];
        }
        $values = $model->getConnection()->row($query, $params);
        if ($values) {
            $model = $model->fill($values);
            return $model;
        }
        return null;
    }

    public static function find($id) {
        $model = new static();
        $values = $model->getConnection()->row("Select * from " . $model->table . " where " . $model->primaryKey . " = :id", array('id' => $id));
        if ($values) {
            $model = $model->fill($values);
            return $model;
        } else {
            throw new \exception('find() is Unable to find such record');
        }
    }

    public static function truncate() {
        $model = new static();
        if ($model->getConnection()->query("TRUNCATE TABLE " . $model->table)) {
            return true;
        } else {
            return false;
        }
    }

    // non static find function
    public function get($id) {
        return $this->fill($this->getConnection()->row("Select * from " . $this->table . " where " . $this->primaryKey . " = :id", array('id' => $id)));
    }

    public function delete() {
        $key = $this->attributes[$this->primaryKey];
        $query = "DELETE FROM " . $this->table . " where " . $this->primaryKey . " = :id";
        return $this->getConnection()->query($query, array('id' => $key));
    }

    public function update() {
        $query = "UPDATE " . $this->table . " SET ";
        $keys = array_keys($this->attributes);
        $values = $this->attributes;
        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $this->primaryKey ) {
                $query = $query . '`' . $keys[$i] . '`' . " = :" . $keys[$i] . " ";
                if ($i >= 1 && $i < count($keys) - 1 /* && $this->$keys[$i+1] != '' */) {
                    $query = $query . ', ';
                }               
            }
        }
        $query = $query . " where " . $this->primaryKey . " = " . $this->attributes[$this->primaryKey];        
        unset($values[$this->primaryKey]);
        $this->getConnection()->query($query, $values);
        return $query;
    }

    public function save() {
        $conn = $this->getConnection();
        if ($conn->query("INSERT INTO " . $this->table . " ( " . $this->getKeys() . ") VALUES (" . $this->getPreparedKeys() . ");", $this->getAttributeArray())) {
            $pkname = (string) $this->primaryKey;
            $this->$pkname = $conn->lastInsertId();
            return $this->getAttributeArray();
        } else {
            throw new \Exception('Unable to Write');
        }
        return false;
    }

    public function getAttribute($key) {
        if (!$key) {
            return;
        }
        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
        if (array_key_exists($key, $this->attributes)) {
            return $this->getAttributeValue($key);
        }/* else{
          throw new \Exception('Attribute has not been defined or set');
          } */
        // Here we will determine if the model base class itself contains this given key
        // since we do not want to treat any of those methods are relationships since
        // they are all intended as helper methods and none of these are relations.
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key) {
        $value = $this->getAttributeFromArray($key);
        //CASTING SUPPORT TO DO
        /* If the attribute has a get mutator, we will call that then return what
          // it returns as the value, which is useful for transforming values on
          // retrieval from the model to a form that is more useful for usage.
          if ($this->hasGetMutator($key)) {
          return $this->mutateAttribute($key, $value);
          }
          // If the attribute exists within the cast array, we will convert it to
          // an appropriate native PHP type dependant upon the associated value
          // given with the key in the pair. Dayle made this comment line up.
          if ($this->hasCast($key)) {
          return $this->castAttribute($key, $value);
          }
          // If the attribute is listed as a date, we will convert it to a DateTime
          // instance on retrieval, which makes it quite convenient to work with
          // date fields without having to create a mutator for each property.
          if (in_array($key, $this->getDates()) &&
          ! is_null($value)) {
          return $this->asDateTime($value);
          }- */
        return $value;
    }

    protected function setAttribute($key, $value) {
        //if (!isset($this->attributes[$key])) {
        $this->attributes[$key] = $value;
        //}
    }

    protected function getAttributeFromArray($key) {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public function getPreparedKeys() {
        $res = [];
        $keys = array_keys($this->attributes);
        foreach ($keys as $key) {
            array_push($res, ':' . $key);
        }
        return implode(", ", $res);
    }

    public function getKeys() {
        $keys = array_keys($this->attributes);
        $escapedkeys = [];
        foreach ($keys as $key) {
            array_push($escapedkeys, '`' . $key . '`');
        }

        return implode(", ", $escapedkeys);
    }

    public function getSetAttributes() {
        $values = $this->attributes;
        foreach ($values as $attribute => $value) {
            if ($value) {
                unset($values[$attribute]);
            }
        }
        return $values;
    }

    public function getAttributeArray() {
        return $this->attributes;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->attributes, $options);
    }

    
    public static function findAllWith(array $conditions) {
        $model = new static();
        $things = [];
        $query = "Select * from " . $model->table . " where ";
        $params = [];
        for ($i = 0; $i < count($conditions); $i++) {
            $paramName= $conditions[$i][0];
            if(array_key_exists($paramName, $params)){
                $paramName = $paramName. rand(0, 500);
            }   
            $params[$paramName] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $conditions[$i][0] . " " . $conditions[$i][1] . " :" . $paramName;
        }        
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {
            array_push($things, (new static)->fill($record));
        }
        return $things;
    }

    public static function findAllWithIn(array $conditions, array $in) {
        $model = new static();
        $things = [];
        $params = [];
        $tables = [];
        
        $query = "Select * from " . $model->table  . " where ";
        
        for ($i = 0; $i < count($conditions); $i++) {
            $paramName= $conditions[$i][0];
            if(array_key_exists($paramName, $params)){
                $paramName = $paramName. rand(0, 500);
            }   
            $params[$paramName] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $conditions[$i][0] . " " . $conditions[$i][1] . " :" . $paramName;
        }
        $query = $query . " AND " .$in[0]. " IN ( ".$in[1]." )";
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {
            array_push($things, (new static)->fill($record));
        }
        return $things;
    }  
    
        public static function findAllJoinWithIn(array $join, array $on, array $conditions, array $in) {
        $model = new static();
        $things = [];
        $params = [];
        $tables = [];
        $query_joins = "";
        for ($i = 0; $i < count($join); $i++) {
            $joinName= $join[$i][0];
            $query_joins = $query_joins . " JOIN " .$joinName. " ON " .$joinName.'.'.$on[$i][0].$on[$i][1].$model->table.'.'.$on[$i][2] ;
        }        
        $query = "Select * from " . $model->table . $query_joins ." where ";
        
        for ($i = 0; $i < count($conditions); $i++) {
            $paramName= $conditions[$i][0];
            if(array_key_exists($paramName, $params)){
                $paramName = $paramName. rand(0, 500);
            }   
            $params[$paramName] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $conditions[$i][0] . " " . $conditions[$i][1] . " :" . $paramName;
        }
        $query = $query . " AND " .$in[0]. " IN ( ".$in[1]." )";
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {
            array_push($things, (new static)->fill($record));
        }
        return $things;
    } 
    
    public static function findFirstWith(array $conditions) {
        $model = new static();
        $things = [];
        $query = "Select * from " . $model->table . " where ";
        $params = [];
        for ($i = 0; $i < count($conditions); $i++) {
            $params[$conditions[$i][0]] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " `" . $conditions[$i][0] . "` " . $conditions[$i][1] . " :" . $conditions[$i][0];
        }
        //Uncomment to debugg params and query
//        var_dump($params);
//        var_dump($query);
        $data = $model->getConnection()->row($query, $params);   
        if($data != false){
            return (new static)->fill($data);
        }
        return ;
    }
    
    public static function findAllWithOrderASCBy(array $conditions,$field) {
        $model = new static();
        $things = [];
        $query = "Select * from " . $model->table . " where ";
        $params = [];
        for ($i = 0; $i < count($conditions); $i++) {
            $paramName= $conditions[$i][0];
            if(array_key_exists($paramName, $params)){
                $paramName = $paramName. rand(0, 500);
            }       
            $params[$paramName] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $conditions[$i][0] . " " . $conditions[$i][1] . " :" . $paramName;
        }        
        $query = $query . " order by $field ASC";
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {
            array_push($things, (new static)->fill($record));
        }
        return $things;
    }

    public static function findAllJoinWith(array $join, array $on, array $conditions) {
        $model = new static();
        $things = [];
        $params = [];
        $tables = [];
        $query_joins = "";
        for ($i = 0; $i < count($join); $i++) {
            $joinName= $join[$i][0];
            $query_joins = $query_joins . " JOIN " .$joinName. " ON " .$joinName.'.'.$on[$i][0].$on[$i][1].$model->table.'.'.$on[$i][2] ;
        }        
        $query = "Select * from " . $model->table . $query_joins ." where ";
        
        for ($i = 0; $i < count($conditions); $i++) {
            $paramName= $conditions[$i][0];
            if(array_key_exists($paramName, $params)){
                $paramName = $paramName. rand(0, 500);
            }   
            $params[$paramName] = $conditions[$i][2];
            if ($i > 0) {
                $query = $query . " AND ";
            }
            $query = $query . " " . $conditions[$i][0] . " " . $conditions[$i][1] . " :" . $paramName;
        }
        $data = $model->getConnection()->query($query, $params);
        foreach ($data as $recordkey => $record) {
            array_push($things, (new static)->fill($record));
        }
        return $things;
    }    
    
    public static function query($query, $params) {
        $model = new static();        
        return $model->getConnection()->query($query, $params);
    }

}
