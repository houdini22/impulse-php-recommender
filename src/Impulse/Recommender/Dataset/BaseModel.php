<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender\Dataset;

require_once __DIR__ . '/../../Exception.php';

use \Impulse\Exception as ImpulseException;

abstract class BaseModel
{
    /**
     * Main value. Used to identify Item or Category
     * @var null
     */
    protected $_id = null;

    /**
     * Your values.
     * @var null
     */
    protected $_data = null;

    /**
     * Sets main value.
     * @param $id
     * @return $this
     * @throws ImpulseException
     */
    public function setId($id)
    {
        if (empty($id)) {
            throw ImpulseException::create(ImpulseException::EMPTY_MODEL_ID);
        }
        if (is_array($id) OR is_object($id) OR is_callable($id) OR is_bool($id) OR is_resource($id)) {
            throw ImpulseException::create(ImpulseException::INCORRECT_MODEL_TYPE);
        }
        if (!empty($this->_id)) {
            throw ImpulseException::create(ImpulseException::MODEL_ID_ALREADY_SET);
        }

        $this->_id = $id;

        return $this;
    }

    /**
     * Gets id.
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets your data.
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;

        return $this;
    }

    /**
     * Returns your values.
     * @return null | mixed
     */
    public function getData()
    {
        return $this->_data;
    }
}