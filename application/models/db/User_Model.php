<?php
class User_Model
{
    public function __construct()
    {
        // parent::__construct();
        $this->table = 'user';
        $this->relation = array();
    }
}
