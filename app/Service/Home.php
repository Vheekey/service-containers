<?php
namespace App\Service;

use App\Config\Config;
use App\Database\Database;

class Home
{
    protected Config $config;
    protected Database $database;

    public function __construct(Config $config, Database $database){

        $this->config = $config;
        $this->database = $database;
    }

    public function index(): array
    {
        return [
            $this->config->get('app.name'),
            $this->database->connect()
        ];
    }
}
