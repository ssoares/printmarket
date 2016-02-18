<?php
interface Cible_Log_Interface
{
    public function __construct($args = null);

    public function getGlobalLog($data);
}