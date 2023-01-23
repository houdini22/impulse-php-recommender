<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender;

require_once __DIR__ . '/../Exception.php';

use \Impulse\Exception as ImpulseException;

class Trainer
{
    /**
     * Stores LearningModel
     * @var LearningModel|null
     */
    protected $_model = null;

    /**
     * Stores learning rate.
     * @var float
     */
    protected $_learningRate = 0.01;
    /**
     * @var int
     */

    /**
     * Stores number of steps.
     * @var int
     */
    protected $_iterations = 500;

    /**
     * Stores verbose flag.
     * @var bool
     */
    protected $_verbose = true;

    /**
     * Stores verbose steps interval.
     * @var int
     */
    protected $_verboseStep = 20;

    /**
     * Trainer constructor.
     * @param LearningModel $model
     * @param array $params
     * @throws ImpulseException
     */
    public function __construct(LearningModel & $model, array $params = [])
    {
        $this->_model = $model;

        if (!empty($params['learningRate'])) {
            if (is_numeric($params['learningRate'])) {
                $this->_learningRate = doubleval($params['learningRate']);
            } else {
                throw ImpulseException::create(ImpulseException::PARAMETER_NOT_DOUBLE, [
                    'param' => 'learningRate'
                ]);
            }
        }

        if (!empty($params['iterations'])) {
            if (is_int($params['iterations'])) {
                $this->_iterations = intval($params['iterations']);
            } else {
                throw ImpulseException::create(ImpulseException::PARAMETER_NOT_INTEGER, [
                    'param' => 'iterations'
                ]);
            }
        }

        if (isset($params['verbose'])) {
            if (is_bool($params['verbose'])) {
                $this->_verbose = boolval($params['verbose']);
            } else {
                throw ImpulseException::create(ImpulseException::PARAMETER_NOT_BOOL, [
                    'param' => 'verbose'
                ]);
            }
        }

        if (isset($params['verboseStep'])) {
            if (is_int($params['verboseStep'])) {
                $this->_verboseStep = intval($params['verboseStep']);
            } else {
                throw ImpulseException::create(ImpulseException::PARAMETER_NOT_INTEGER, [
                    'param' => 'verboseStep'
                ]);
            }
        }
    }

    /**
     * Trains a model.
     * @return float
     */
    public function train()
    {
        if ($this->_verbose) {
            echo "Starting train with {$this->_iterations} steps.\n";
        }

        $categories = $this->_model->getDataset()->getCategories();
        $categoriesKeys = array_keys($categories);
        $items = $this->_model->getDataset()->getItems();
        $itemKeys = array_keys($items);
        $y = $this->_model->getY();

        $step = 0;
        $iterations = $this->_iterations;
        $error = INF;

        while ($step < $iterations) {
            $predictions = $this->_model->calculatePredictions();
            $theta = $this->_model->getTheta();
            $x = $this->_model->getX();

            $newX = [];
            foreach ($x as $itemKey => $itemX) {
                $newItemX = [];
                for ($i = 0; $i < count($itemX); $i++) {
                    $gradientSum = 0.0;
                    $j = 0;
                    foreach ($categories as $categoryId => $category) {
                        if ($this->_model->isItemRatedByCategory($itemKeys[$itemKey], $categoryId)) {
                            $gradientSum += ($predictions[$j][$itemKey] - $y[$j][$itemKey]) * $theta[$j][$i];
                        }
                        $j++;
                    }

                    $newItemX[$i] = $itemX[$i] - ($this->_learningRate * $gradientSum);
                }
                $newX[] = $newItemX;
            }

            $newTheta = [];
            foreach ($theta as $categoryKey => $categoryTheta) {
                $newCategoryTheta = [];
                for ($i = 0; $i < count($categoryTheta); $i++) {
                    $gradientSum = 0.0;
                    $j = 0;
                    foreach ($items as $itemId => $item) {
                        if ($this->_model->isItemRatedByCategory($itemId, $categoriesKeys[$categoryKey])) {
                            $gradientSum += ($predictions[$categoryKey][$j] - $y[$categoryKey][$j]) * $newX[$j][$i];
                        }
                        $j++;
                    }

                    $newCategoryTheta[$i] = $categoryTheta[$i] - ($this->_learningRate * $gradientSum);
                }

                $newTheta[] = $newCategoryTheta;
            }

            $this->_model->setX($newX);
            $this->_model->setTheta($newTheta);

            $error = $this->_model->getError();

            if ($this->_verbose AND $step % $this->_verboseStep === 0) {
                echo "Step {$step} with error {$error}\n";
            }

            $step++;
        }

        if ($this->_verbose) {
            echo "Training ended with error {$error} after {$step} steps.\n";
        }

        return $error;
    }
}