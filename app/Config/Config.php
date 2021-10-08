<?php
namespace App\Config;


class Config
{
    /**
     * Loaded Config
     * @var array|string[][]
     */
    protected array $config = [

        'app'=> [
            'name'=> 'vheekey'
        ],
        'db' => [
            'host' => '127.0.0.1',
            'database' => 'containers',
            'username' => 'root',
            'password' => 'password'
        ]
    ];

    /**
     * The Cache
     * @var array
     */
    protected array $cache = [];


    /**
     * Get A Config File
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get ($key, $default = null): mixed
    {
        if ($this->existsInCache($key)){
            return $this->fromCache($key);
        }

        return $this->addToCache(
            $key, $this->extractFromConfig($key)?? $default
        );
    }

    protected function extractFromConfig($key){

        $filtered = $this->config;

        foreach (explode('.', $key) as $segment) {
            if ( $this->exists($filtered, $segment) ) {
                $filtered = $filtered[$segment];
                continue;
            }

            return;
        }

        return $filtered;
    }

    protected function addToCache($key,$value){
        $this->cache[$key] = $value;

        return $value;
    }

    protected function fromCache($key){
        return $this->cache[$key];
    }

    protected function exists(array $config, $key): bool
    {
        return array_key_exists($key,$config);
    }

    protected function existsInCache($key): bool
    {
        return isset($this->cache[$key]);
    }
}
