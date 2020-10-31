<?php
namespace config\IStorage;
/**
 * Interface IStorage
 */
interface IStorage
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);
}