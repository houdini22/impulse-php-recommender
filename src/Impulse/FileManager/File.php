<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\FileManager;

require_once __DIR__ . '/Directory.php';
require_once __DIR__ . '/Resource.php';
require_once __DIR__ . '/../FileManager.php';
require_once __DIR__ . '/../Exception.php';

use \Impulse\Exception as ImpulseException;
use \Impulse\FileManager as FileManager;

class File extends Resource
{
    /**
     * File constructor.
     * @param $path
     * @throws ImpulseException
     */
    public function __construct($path)
    {
        if (!is_file($path)) {
            throw ImpulseException::create(ImpulseException::PATH_NOT_FILE, [
                'path' => $path
            ]);
        }

        parent::__construct($path);
    }

    /**
     * Creates file
     * @param $dir
     * @param $name
     * @return File
     */
    public static function create($dir, $name)
    {
        FileManager::checkNotDirectory($dir);
        FileManager::checkNotWritable($dir);

        $path = $dir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, '');

        return new File($path);
    }

    /**
     * Sets file content.
     * @param $str
     * @return $this
     */
    public function content($str)
    {
        file_put_contents($this->_path, $str);
        return $this;
    }

    /**
     * Returns decoded json from file content.
     * @return mixed
     */
    public function toJSON()
    {
        return json_decode(file_get_contents($this->_path));
    }
}