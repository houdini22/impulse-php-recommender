<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender\Dataset;

include_once __DIR__ . '/BaseModel.php';

class Item extends BaseModel
{
    /**
     * Factory for Item
     * @param $id
     * @param null $data
     * @return Item
     */
    public static function create($id, $data = null)
    {
        $instance = new Item();
        $instance->setId($id);
        $instance->setData($data);

        return $instance;
    }

}
