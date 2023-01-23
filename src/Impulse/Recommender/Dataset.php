<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender;

require_once __DIR__ . '/Dataset/Item.php';
require_once __DIR__ . '/Dataset/Category.php';
require_once __DIR__ . '/Dataset/Rating.php';
require_once __DIR__ . '/../Exception.php';

use \Impulse\Exception as ImpulseException;
use \Impulse\Recommender\Dataset\Item as Item;
use \Impulse\Recommender\Dataset\Category as Category;
use \Impulse\Recommender\Dataset\Rating as Rating;

class Dataset
{
    /**
     * Stores Items.
     * @var array
     */
    protected $_items = [];

    /**
     * Stores Categories.
     * @var array
     */
    protected $_categories = [];

    /**
     * Stores Ratings in structure.
     * @var array
     */
    protected $_ratings = [];

    /**
     * Stores Ratings in flat vector.
     * @var array
     */
    protected $_ratings_1d = [];

    /**
     * Adds Item. It must not exists previously.
     * @param Dataset\Item $item
     * @return $this
     * @throws ImpulseException
     */
    public function addItem(Item $item)
    {
        if (array_key_exists($item->getId(), $this->_items)) {
            throw ImpulseException::create(ImpulseException::ITEM_EXISTS, [
                'id' => $item->getId()
            ]);
        }

        $this->_items[$item->getId()] = $item;

        return $this;
    }

    /**
     * Adds Category. It must not exists previously.
     * @param Dataset\Category $category
     * @return $this
     * @throws ImpulseException
     */
    public function addCategory(Category $category)
    {
        if (array_key_exists($category->getId(), $this->_items)) {
            throw ImpulseException::create(ImpulseException::CATEGORY_EXISTS, [
                'id' => $category->getId()
            ]);
        }

        $this->_categories[$category->getId()] = $category;

        return $this;
    }

    /**
     * Adds rating. Item and Categories must be already added.
     * @param Dataset\Rating $rating
     * @return $this
     * @throws ImpulseException
     */
    public function addRating(Rating $rating)
    {
        if (!array_key_exists($rating->getItem(), $this->_items)) {
            throw ImpulseException::create(ImpulseException::ITEM_NOT_EXISTS, [
                'item' => $rating->getItem()
            ]);
        }
        if (!array_key_exists($rating->getCategory(), $this->_categories)) {
            throw ImpulseException::create(ImpulseException::CATEGORY_NOT_EXISTS, [
                'category' => $rating->getCategory()
            ]);
        }
        $this->_ratings[$rating->getItem()][$rating->getCategory()] = $rating;
        $this->_ratings_1d[] = $rating;

        return $this;
    }

    /**
     * Gets number of items.
     * @return int
     */
    public function getNumOfItems()
    {
        return count($this->_items);
    }

    /**
     * Gets number of categories.
     * @return int
     */
    public function getNumOfCategories()
    {
        return count($this->_categories);
    }

    /**
     * Gets reference to Items.
     * @return array
     */
    public function & getItems()
    {
        return $this->_items;
    }

    /**
     * Gets reference to Categories.
     * @return array
     */
    public function & getCategories()
    {
        return $this->_categories;
    }

    /**
     * Gets reference to Ratings.
     * @return array
     */
    public function & getRatings()
    {
        return $this->_ratings;
    }

    /**
     * Exports dataset to array.
     * @return array
     */
    public function export()
    {
        $result = [];

        foreach ($this->_items as $item) {
            $result['items'][] = [
                'i' => $item->getId(),
                'd' => $item->getData()
            ];
        }

        foreach ($this->_categories as $category) {
            $result['categories'][] = [
                'i' => $category->getId(),
                'd' => $category->getData()
            ];
        }

        foreach ($this->_ratings_1d as $rating) {
            $result['ratings'][] = [
                'i' => $rating->getItem(),
                'c' => $rating->getCategory(),
                'r' => $rating->getRating()
            ];
        }

        return $result;
    }

    /**
     * Imports Dataset from \stdClass
     * @param $data \stdClass
     * @return Dataset
     */
    public static function import($data)
    {
        $dataset = new Dataset();

        foreach ($data->items as $items) {
            $dataset->addItem(Item::create($items->i, $items->d));
        }

        foreach ($data->categories as $category) {
            $dataset->addCategory(Category::create($category->i, $category->d));
        }

        foreach ($data->ratings as $rating) {
            $dataset->addRating(Rating::create($rating->i, $rating->c, $rating->r));
        }

        return $dataset;
    }
}
