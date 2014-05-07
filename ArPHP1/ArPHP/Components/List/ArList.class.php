<?php
class ArList extends ArComponent {

    public $c = array();

    public function contains($key)
    {
        return isset($this->c[$key]);

    }

    public function set($key, $value)
    {
        $this->c[$key] = $value;

    }



    public function get($key)
    {
        $r = null;
        if ($this->contains($key))
            $r = $this->c[$key];
        return $r;

    }

    public function flush()
    {
        $this->c = array();

    }

}