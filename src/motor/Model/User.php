<?php

namespace Motor\Model;

/*
 * Copyright 2019 Efrain Loya.
 *
 * you may not edit, copy or distribute this file except for use by an AutoZone employee or affiliate.
 */

/**
 * Description of Employee
 *
 * @author Efrain Loya
 */
use Core\Model;

class User extends Model {

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $connection = 'default';

}
