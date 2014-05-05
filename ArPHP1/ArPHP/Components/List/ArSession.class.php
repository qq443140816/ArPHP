<?php

class ArSession extends ArList {

    static public function init($cfg, $class)
    {
        $obj = parent::init($cfg, $class);

        $obj->setContainer($_SESSION);

        return $obj;

    }

    public function setContainer(&$value)
    {
        $this->c = &$value;

    }

}