<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender\Dataset;

class Rating
{
    /**
     * ID (main value) of item
     * @var
     */
    protected $_item;

    /**
     * ID (main value) of category
     * @var
     */
    protected $_category;

    /**
     * Rating
     * @var double
     */
    protected $_rating;

    /**
     * Rating constructor.
     * @param $item
     * @param $category
     * @param $rating
     */
    public function __construct($item, $category, $rating)
    {
        $this->_item = $item;
        $this->_category = $category;
        $this->_rating = $rating;
    }

    /**
     * Gets Item main value.
     * @return mixed
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Gets Category main value.
     * @return mixed
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Gets rating.
     * @return float
     */
    public function getRating()
    {
        return $this->_rating;
    }

    /**
     * Factory for Rating
     * @param $item
     * @param $category
     * @param $rating
     * @return Rating
     */
    public static function create($item, $category, $rating)
    {
        return new Rating($item, $category, $rating);
    }
}