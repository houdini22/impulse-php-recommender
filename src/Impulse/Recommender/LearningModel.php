<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender;

require_once __DIR__ . '/../Exception.php';
require_once __DIR__ . '/../Recommender/Dataset.php';

use \Impulse\Exception as ImpulseException;
use \Impulse\Recommender\Dataset as Dataset;

class LearningModel
{
    /**
     * Stores Dataset.
     * @var Dataset|null
     */
    protected $_dataset = null;

    /**
     * X vector. It stores Items features.
     * @var array
     */
    protected $_x = [];

    /**
     * Y vector. It stores Ratings.
     * @var array
     */
    protected $_y = [];

    /**
     * Theta vector. It stores Categories features.
     * @var array
     */
    protected $_theta = [];

    /**
     * Cached predictions for each Item in Category.
     * @var array
     */
    protected $_predictions = [];

    /**
     * Means for rated Items for Categories. Required for mean normalization.
     * @var array
     */
    protected $_means = [];

    /**
     * LearningModel constructor.
     * @param Dataset $dataset
     * @param array $params
     * @throws ImpulseException
     */
    public function __construct(Dataset & $dataset, array $params)
    {
        if (empty($params['numFeatures'])) {
            throw ImpulseException::create(ImpulseException::PARAMETER_NOT_EXISTS, [
                'param' => 'numFeatures'
            ]);
        }
        if (!is_int($params['numFeatures'])) {
            throw  ImpulseException::create(ImpulseException::PARAMETER_NOT_INTEGER, [
                'param' => 'numFeatures'
            ]);
        }
        if ($params['numFeatures'] <= 1) {
            throw ImpulseException::create(ImpulseException::PARAMETER_SHOULD_BE_GREATER, [
                'param' => 'numFeatures',
                'value' => 1
            ]);
        }

        $this->_dataset = $dataset;

        $items = $dataset->getItems();
        $categories = $dataset->getCategories();
        $ratings = $dataset->getRatings();

        $i = 0;
        foreach ($items as $item) {
            $j = 0;
            foreach ($categories as $category) {
                $rating = null;
                if (isset($ratings[$item->getId()][$category->getId()])) {
                    $rating = $ratings[$item->getId()][$category->getId()]->getRating();
                }
                $this->_y[$j][$i] = $rating;

                $j++;
            }

            for ($k = 0; $k < $params['numFeatures']; $k++) {
                $this->_x[$i][$k] = mt_rand(1, 100) / 399;
            }

            $i++;
        }

        for ($i = 0; $i < $dataset->getNumOfCategories(); $i++) {
            for ($k = 0; $k < $params['numFeatures']; $k++) {
                $this->_theta[$i][$k] = mt_rand(1, 100) / 399;
            }
        }

        for ($i = 0; $i < $this->_dataset->getNumOfItems(); $i++) {
            $this->_means[$i] = 0;
            $count = 0;
            for ($j = 0; $j < $this->_dataset->getNumOfCategories(); $j++) {
                $value = $this->_y[$j][$i];
                if (!is_null($value)) {
                    $this->_means[$i] += $value;
                    $count++;
                }
            }
            $this->_means[$i] /= $count;
            for ($j = 0; $j < $this->_dataset->getNumOfCategories(); $j++) {
                if (!is_null($this->_y[$j][$i])) {
                    $this->_y[$j][$i] -= $this->_means[$i];
                }
            }
        }
    }

    /**
     * Calculates all predictions.
     * @return array
     */
    public function calculatePredictions()
    {
        $this->_predictions = [];

        for ($i = 0; $i < $this->_dataset->getNumOfItems(); $i++) {
            for ($j = 0; $j < $this->_dataset->getNumOfCategories(); $j++) {
                $this->_predictions[$j][$i] = 0;
                $vecX = $this->_x[$i];
                $vecTheta = $this->_theta[$j];
                for ($k = 0; $k < count($vecX); $k++) {
                    $this->_predictions[$j][$i] += $vecX[$k] * $vecTheta[$k];
                }
            }
        }

        return $this->_predictions;
    }

    /**
     * Finds related Items for given $item ID (main value).
     * @param $item
     * @param array $params
     * @return array
     * @throws ImpulseException
     */
    public function findRelated($item, array $params)
    {
        $key = -1;
        $vecX = null;
        $i = 0;
        foreach ($this->_dataset->getItems() as $m) {
            if ($m->getId() === $item) {
                $vecX = $this->_x[$i];
                $key = $i;
                break;
            }
            $i++;
        }

        if ($key === -1) {
            throw ImpulseException::create(ImpulseException::ITEM_NOT_EXISTS, [
                'item' => $item
            ]);
        }

        $results = [];

        for ($i = 0; $i < $this->_dataset->getNumOfItems(); $i++) {
            if ($i === $key) {
                continue;
            }
            $vecX2 = $this->_x[$i];
            $result = [];
            for ($j = 0; $j < count($vecX); $j++) {
                $result[$j] = $vecX[$j] - $vecX2[$j];
            }
            $norm = 0;
            for ($j = 0; $j < count($result); $j++) {
                $norm += pow($result[$j], 2);
            }
            $results[$i] = [
                'key' => $i,
                'similarity' => sqrt($norm)
            ];
        }

        usort($results, function ($a, $b) {
            if ($a['similarity'] > $b['similarity']) {
                return 1;
            }
            return -1;
        });

        $results = array_slice($results, 0, $params['limit']);

        $items = $this->_dataset->getItems();
        $keys = array_keys($items);
        foreach ($results as $key => & $res) {
            $res['model'] = [
                '_id' => $items[$keys[$res['key']]]->getId(),
                'data' => $items[$keys[$res['key']]]->getData()
            ];
            unset($res['key']);
        }

        return $results;
    }

    /**
     * Predicts Item Rating for given Category or not.
     * @param $item
     * @param null $category
     * @return mixed
     * @throws ImpulseException
     */
    public function predict($item, $category = null)
    {
        if (empty($this->_predictions)) {
            $this->calculatePredictions();
        }

        $itemKeys = array_keys($this->_dataset->getItems());
        $categoryKeys = array_keys($this->_dataset->getCategories());

        $itemKey = array_search($item, $itemKeys);
        $categoryKey = array_search($category, $categoryKeys);


        if (is_null($category)) {
            return $this->_means[$itemKey];
        }

        if (isset($this->_predictions[$categoryKey][$itemKey])) {
            return $this->_predictions[$categoryKey][$itemKey] + $this->_means[$itemKey];
        }

        throw ImpulseException::create(ImpulseException::ITEM_OR_CATEGORY_NOT_FOUND);
    }

    /**
     * Sets theta.
     * @param array $theta
     * @return $this
     */
    public function setTheta(array $theta)
    {
        $this->_theta = $theta;
        return $this;
    }

    /**
     * Gets theta.
     * @return array
     */
    public function getTheta()
    {
        return $this->_theta;
    }

    /**
     * Sets x.
     * @param array $x
     * @return $this
     */
    public function setX(array $x)
    {
        $this->_x = $x;
        return $this;
    }

    /**
     * Gets x.
     * @return array
     */
    public function getX()
    {
        return $this->_x;
    }

    /**
     * Gets Dataset.
     * @return Dataset|null
     */
    public function & getDataset()
    {
        return $this->_dataset;
    }

    /**
     * Check if Item is rated by Category.
     * @param $item
     * @param $category
     * @return bool
     */
    public function isItemRatedByCategory($item, $category)
    {
        $itemKeys = array_keys($this->_dataset->getItems());
        $categoryKeys = array_keys($this->_dataset->getCategories());

        $itemKey = array_search($item, $itemKeys);
        $categoryKey = array_search($category, $categoryKeys);

        return !is_null($this->_y[$categoryKey][$itemKey]);
    }

    /**
     * Gets all predictions.
     * @return array
     */
    public function getPredictions()
    {
        return $this->_predictions;
    }

    /**
     * Gets y.
     * @return array
     */
    public function getY()
    {
        return $this->_y;
    }

    /**
     * Gets overall error for given Dataset.
     * @return float
     */
    public function getError()
    {
        $this->calculatePredictions();

        $items = $this->_dataset->getItems();
        $categories = $this->_dataset->getCategories();

        $sum = 0.0;
        $i = 0;
        foreach ($categories as $categoryId => $category) {
            $j = 0;
            foreach ($items as $itemId => $item) {
                if ($this->isItemRatedByCategory($itemId, $categoryId)) {
                    $sum += pow($this->_predictions[$i][$j] - $this->_y[$i][$j], 2);
                }
                $j++;
            }
            $i++;
        }

        $error = 0.5 * $sum;

        return $error;
    }

    /**
     * Exports data to array.
     * @return array
     */
    public function export()
    {
        $this->calculatePredictions();

        return [
            'x' => $this->_x,
            'y' => $this->_y,
            'theta' => $this->_theta,
            'means' => $this->_means,
            'predictions' => $this->_predictions
        ];
    }

    /**
     * Imports data from \stdClass
     * @param $data \stdClass
     * @param $dataset
     * @return LearningModel
     */
    public static function import($data, $dataset)
    {
        $model = new LearningModel($dataset, [
            'numFeatures' => count($data->x[0])
        ]);

        $model->_x = $data->x;
        $model->_y = $data->y;
        $model->_theta = $data->theta;
        $model->_means = $data->means;
        $model->_predictions = $data->predictions;

        return $model;
    }
}