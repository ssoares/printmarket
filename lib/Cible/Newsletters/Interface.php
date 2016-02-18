<?php

interface Cible_Newsletters_Interface
{
    public function setApiKey($apiKey);
    public function setConfig($config);
    public function setId($id);
    public function getModel($modelId);
    public function setParameters();

}