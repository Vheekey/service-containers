<?php
namespace App\Database;

use App\Config\Config;

class Database
{
    protected Config $config;

    public function __construct(Config $config){

        $this->config = $config;
    }

    public function connect(){
        return $this->config->get('db.host');
    }
}
