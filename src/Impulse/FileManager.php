<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse;

require_once __DIR__ . '/Exception.php';

use \Impulse\Exception as ImpulseException;

class FileManager
{
    /**
     * Checks if $name directory can be created in $dir directory.
     * @param $dir
     * @param $name
     * @throws ImpulseException
     */
    public function checkCreateDirectory($dir, $name)
    {
        FileManager::checkNotDirectory($dir);
        FileManager::checkNotWritable($dir);

        $path = $dir . DIRECTORY_SEPARATOR . $name;

        FileManager::checkPathExists($path);
    }

    /**
     * Checks if $path is existing directory.
     * @param $path
     * @throws ImpulseException
     */
    public static function checkNotDirectory($path)
    {
        if (!is_dir($path)) {
            throw ImpulseException::create(ImpulseException::PATH_NOT_DIR, [
                'path' => $path
            ]);
        }
    }

    /**
     * Checks if directory is writable.
     * @param $path
     * @throws ImpulseException
     */
    public static function checkNotWritable($path)
    {
        if (!is_writable($path)) {
            throw ImpulseException::create(ImpulseException::PATH_NOT_WRITABLE, [
                'path' => $path
            ]);
        }
    }

    public static function checkPathExists($path)
    {
        if (is_file($path) OR is_dir($path)) {
            throw ImpulseException::create(ImpulseException::PATH_EXISTS, [
                'path' => $path
            ]);
        }
    }
}