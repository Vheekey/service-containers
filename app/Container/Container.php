<?php

namespace App\Container;

use App\Container\Exceptions\NotFoundException;
use ReflectionClass;
use ReflectionException;

class Container
{

    protected array $items = [];

    public function set($name, callable $closure){
        $this->items[$name] = $closure;
    }

    public function has($name): bool
    {
        return isset($this->items[$name]);
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function get($name){
        if($this->has($name)){
            return $this->items[$name]($this);
        }

       return $this->autoWire($name);
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function autoWire($name): mixed
    {
        if(!class_exists($name)){
            throw new NotFoundException;
        }

        $reflector = $this->getReflector($name);

        if(!$reflector->isInstantiable()){
            throw new NotFoundException;
        }


        if($constructor = $reflector->getConstructor()){
            return $reflector->newInstanceArgs(
                $this->getConstructorDependencies($constructor)
            );

        }

        return new $name();
    }


    public function getConstructorDependencies(\ReflectionMethod $constructor): array
    {
        return array_map(/**
         * @throws ReflectionException
         * @throws NotFoundException
         */ function (\ReflectionParameter $dependency){
            return $this->resolveDependency($dependency);
        }, $constructor->getParameters());
    }

    /**
     * @throws ReflectionException
     * @throws NotFoundException
     */
    public function resolveDependency(\ReflectionParameter $dependency){
         $name = $dependency->getType() && !$dependency->getType()->isBuiltin()
           ? new ReflectionClass($dependency->getType()->getName())
               : null;


        if(is_null($name)){
            throw new NotFoundException;
        }


        return $this->get($name);
    }


    /**
     * @throws ReflectionException
     */
    public function getReflector($class): ReflectionClass
    {
        return new ReflectionClass($class);
    }


    public function share($name, callable $closure){
        $this->items[$name] = function () use ($closure){
            static $resolved;

            if(!$resolved){
                $resolved = $closure($this);
            }

            return $resolved;
        };

    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function __get($name){
        return $this->get($name);
    }
}
