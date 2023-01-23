<?php

namespace Impulse;

class Exception extends \Exception
{

    // codes
    const PATH_NOT_DIR = 1;
    const PATH_EXISTS = 2;
    const EMPTY_MODEL_ID = 3;
    const INCORRECT_MODEL_TYPE = 4;
    const MODEL_ID_ALREADY_SET = 5;
    const ITEM_EXISTS = 6;
    const CATEGORY_EXISTS = 7;
    const ITEM_NOT_EXISTS = 8;
    const CATEGORY_NOT_EXISTS = 9;
    const PARAMETER_NOT_EXISTS = 10;
    const PARAMETER_NOT_INTEGER = 11;
    const PARAMETER_SHOULD_BE_GREATER = 12;
    const ITEM_OR_CATEGORY_NOT_FOUND = 13;
    const PARAMETER_NOT_DOUBLE = 14;
    const PARAMETER_NOT_BOOL = 15;
    const PATH_NOT_WRITABLE = 16;
    const PATH_NOT_FILE = 17;

    protected static $messages = [
        self::PATH_NOT_DIR => 'Path :path is not a directory.',
        self::PATH_EXISTS => 'Path :path exists.',
        self::EMPTY_MODEL_ID => 'Dataset model ID should be not empty.',
        self::INCORRECT_MODEL_TYPE => 'Dataset model ID should be string or integer.',
        self::MODEL_ID_ALREADY_SET => 'Dataset model ID is already set.',
        self::ITEM_EXISTS => 'Item with ID = :id already exists in dataset.',
        self::CATEGORY_EXISTS => 'Category with ID = :id already exists in dataset.',
        self::ITEM_NOT_EXISTS => 'Item with ID = :item not exists in dataset',
        self::CATEGORY_NOT_EXISTS => 'Category with ID = :category not exists in dataset',
        self::PARAMETER_NOT_EXISTS => 'Parameter :param is required.',
        self::PARAMETER_NOT_INTEGER => 'Parameter :param should be an integer.',
        self::PARAMETER_SHOULD_BE_GREATER => 'Parameter :param should be greater than :value.',
        self::ITEM_OR_CATEGORY_NOT_FOUND => 'Item or category not found',
        self::PARAMETER_NOT_DOUBLE => 'Parameter :param should be type of float.',
        self::PARAMETER_NOT_BOOL => 'Parameter :param should be type of boolean.',
        self::PATH_NOT_WRITABLE => 'Path :path is not writable.',
        self::PATH_NOT_FILE => 'Path :path is not file.'
    ];

    public static function create($code, array $messageParams = [])
    {
        $message = self::$messages[$code];
        $message = self::prepareMessage($message, $messageParams);

        return new Exception($message, $code);
    }

    public static function prepareMessage($message, $messageParams)
    {
        foreach ($messageParams as $name => $value) {
            $message = str_replace(':' . $name, '"' . $value . '"', $message);
        }
        return $message;
    }

}