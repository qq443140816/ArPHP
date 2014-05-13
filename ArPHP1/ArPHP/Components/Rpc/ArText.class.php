<?php
abstract class ArText extends ArComponent {

    abstract public function callApi($api);

    abstract protected function parse($parseStr);

}
