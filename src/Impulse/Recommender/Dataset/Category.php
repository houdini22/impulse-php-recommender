<?php
/**
 * @package impulse-ml-recommender-php
 * @author <baniczek@gmail.com>
 */

namespace Impulse\Recommender\Dataset;

include_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{
    /**
     * Factory for Category
     * @param $id
     * @param null $data
     * @return Category
     */
    public static function create($id, $data = null)
    {
        $instance = new Category();
        $instance->setId($id);
        $instance->setData($data);

        return $instance;
    }

}
