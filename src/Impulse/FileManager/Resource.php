<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\FileManager;

abstract class Resource
{
    /**
     * Path to the resource.
     * @var bool|string
     */
    protected $_path;

    /**
     * pathinfo() data
     * @see pathinfo()
     * @var mixed
     */
    protected $_path_info;

    /**
     * Resource constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $path = realpath($path);
        $this->_path = $path;
        $this->_path_info = pathinfo($path);
    }

    /**
     * Gets path.
     * @return bool|string
     */
    public function getPath()
    {
        return $this->_path;
    }
}