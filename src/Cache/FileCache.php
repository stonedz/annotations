<?php

namespace Minime\Annotations\Cache;

use Minime\Annotations\Interfaces\CacheInterface;

class FileCache implements CacheInterface
{
    protected $path;

    public function __construct($path = null)
    {

        $this->path = $path;

        if (! $this->path) {
            $this->path = sys_get_temp_dir() . '/minime-annotations/';
            if (! is_dir($this->path) ) {
                mkdir($this->path);
            }
        }

        if (! is_dir($this->path) || ! is_writable($this->path) || ! is_readable($this->path)) {
            throw new \InvalidArgumentException("Cache path is not a writable/readable directory: {$this->path}.");
        }

    }

    public function getKey($docblock)
    {
        return md5($docblock);
    }

    public function set($key, array $annotations)
    {
        $file = $this->getFileName($key);
        if (! file_exists($file)) {
            file_put_contents($file, serialize($annotations));
        }
    }

    public function get($key)
    {
        $file = $this->getFileName($key);
        if (file_exists($file)) {
            return unserialize(file_get_contents($file));
        }

        return false;
    }

    public function clear()
    {
        foreach (glob($this->path . '*{.annotations}', GLOB_BRACE | GLOB_NOSORT) as $file) {
            unlink($file);
        }
    }

    protected function getFileName($key)
    {
        return $this->path . $key . '.annotations';
    }

}
