<?php

class ArSession extends ArList {

    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);

        $obj->setContainer($_SESSION);

        return $obj;

    }

    public function setContainer(&$value)
    {
        $this->c = &$value;

    }

}