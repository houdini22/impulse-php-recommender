<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\FileManager;

require_once __DIR__ . '/File.php';
require_once __DIR__ . '/Resource.php';
require_once __DIR__ . '/../Exception.php';

use \Impulse\Exception as ImpulseException;
use \Impulse\FileManager as FileManager;
use \Impulse\FileManager\File as File;

class Directory extends Resource
{
    /**
     * Directory constructor.
     * @param $path
     * @throws ImpulseException
     */
    public function __construct($path)
    {
        FileManager::checkNotDirectory($path);

        parent::__construct($path);
    }

    /**
     * Creates directory.
     * @param $dir
     * @param $name
     * @return Directory
     * @throws ImpulseException
     */
    public static function create($dir, $name)
    {
        FileManager::checkNotDirectory($dir);
        FileManager::checkNotWritable($dir);

        $path = $dir . DIRECTORY_SEPARATOR . $name;

        FileManager::checkPathExists($path);

        mkdir($path, 0777, true);

        return new Directory($path);
    }

    /**
     * Creates file.
     * @param $name
     * @return File
     */
    public function createFile($name)
    {
        FileManager::checkNotWritable($this->_path);

        return File::create($this->_path, $name);
    }
}