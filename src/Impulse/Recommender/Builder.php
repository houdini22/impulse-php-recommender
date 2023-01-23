<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender;

require_once __DIR__ . '/../FileManager.php';
require_once __DIR__ . '/../FileManager/Directory.php';
require_once __DIR__ . '/../FileManager/File.php';
require_once __DIR__ . '/../Recommender/Dataset.php';
require_once __DIR__ . '/../Recommender/LearningModel.php';
require_once __DIR__ . '/../Exception.php';

use \Impulse\FileManager as FileManager;
use \Impulse\FileManager\Directory as Directory;
use \Impulse\FileManager\File as File;
use \Impulse\Recommender\Dataset as Dataset;
use \Impulse\Recommender\LearningModel as LearningModel;
use \Impulse\Exception as ImpulseException;

class Builder
{
    /**
     * Stores LearningModel
     * @var LearningModel|null
     */
    protected $_model = null;

    /**
     * Builder constructor.
     * @param LearningModel|NULL $model
     */
    public function __construct(LearningModel & $model)
    {
        $this->_model = $model;
        $this->_file_manager = new FileManager();
    }

    /**
     * Saves LearningModel to given directory $name in $dir directory.
     * @param $dir
     * @param $name
     * @return bool|string
     * @throws ImpulseException
     */
    public function save($dir, $name)
    {
        $this->_file_manager->checkCreateDirectory($dir, $name);

        $directory = Directory::create($dir, $name);

        $dataset = $directory->createFile('dataset.json');
        $model = $directory->createFile('model.json');

        $dataset->content(json_encode($this->_model->getDataset()->export()));
        $model->content(json_encode($this->_model->export()));

        return $directory->getPath();
    }

    /**
     * Restores model from give directory $name in $dir directory.
     * @param $dir
     * @param $name
     * @return LearningModel
     */
    public static function load($dir, $name)
    {
        $path = $dir . DIRECTORY_SEPARATOR . $name;
        $directory = new Directory($path);

        $dataset = new File($path . DIRECTORY_SEPARATOR . 'dataset.json');
        $model = new File($path . DIRECTORY_SEPARATOR . 'model.json');

        $datasetContent = $dataset->toJSON();
        $modelContent = $model->toJSON();

        $createdDataset = Dataset::import($datasetContent);
        $model = LearningModel::import($modelContent, $createdDataset);

        return $model;
    }
}