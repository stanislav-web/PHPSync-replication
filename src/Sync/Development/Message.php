<?php
namespace Sync\Development;

/**
 * Class Message. Message interface
 *
 * @package Sync\Development
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Development/Message.php
 */
class Message {

	/**
	 * Reserved messages template
	 *
	 * @var array $messages
	 */
    private static $messages = [
        'startLoad'         => 'Started loading of "%s".',
        'connected'         => 'Connected to: %s.',
	    'elapsed'           => 'Transfer processed in: %s sec.',
        'load'              => "Loaded %d rows from Backend.",
        'unload'            => "Unloaded %d rows into frontend `%s` table.",
        'loadProducts'      => "Loaded %d rows from Backend products and relations: %d categories, %d tags, %d sizes, %d types.",
        'unloadProducts'    => "Removed %d rows from frontend `%s` table.\nAdd %d rows to frontend `%s` table.\nAdded %d categories, %d tags, %d types, %d sizes to frontend `%s` table.",
        'loadCategories'    => "Loaded %d rows from Backend categories and %d relations.\nLoaded %d categories for Frontend.",
        'unloadCategories'  => "Removed %d rows from frontend `%s` table.\nAdded %d rows to frontend `%s` table.\nAdded %d rows to frontend `%s` table.",
        'tags'              => "Added %d tags.\nAdd %d sizes.\nAdd %d types to `%s` table.",
        'price'             => "Added %d rows and removed %d rows to frontend `%s` table",
        'banner'            => "Removed %d rows and added %d rows to frontend `%s` table",
        'buy'               => "Added %d rows and removed %d rows to frontend `%s` table",
        'buyTogether'      => "Added %d rows and removed %d rows to frontend `%s` table",
        'delivery'          => "Removed %d rows and added %d rows to frontend `%s` table",
        'document'          => "Removed %d rows and added %d rows to frontend `%s` table",
        'hotlineLoad'       => "Loaded from Backend: %d orders, %d total count orders, %d items from last orders.",
        'hotlineUnload'     => "Added %d rows to frontend `%s` table.\nAdded %d rows to frontend `%s` table.\nAdded %d rows to frontend `%s` table.",
        'publishedItems'    => "Loaded from Backend: %d published items, %d total count of published items in storage. \nLoaded from Frontend: %d published items, %d total count of published items in storage, %d published items without sizes in stock.",
        'diffItems'         => "Difference between published items of Back & Front: %d. \nDifference between total count of published items of Back & Front: %d. \nPublished items without sizes in stock: %d",
        'quantityItems'     => "Quantity of published items on Back: %d. \nQuantity of total count of published items on Back: %d. \nQuantity of published items on Front: %d. \nQuantity of total count of published items on Front: %d.",
        'publishedWithoutSizesItems' => "There are %d published products without sizes in stock on Back & Front. \n%s",
        'publishedWithoutSizesItem' => "Articul: %s, Status: %d, Sizes: %s, Date update: %s.",
    ];

    /**
     * Error message
     *
     * @param $message
     * @return string
     */
    public static function error($message) {
        return (new Color())->getColoredString($message, 'red').PHP_EOL;
    }

    /**
     * Success message
     *
     * @param $message
     * @return string
     */
    public static function success($message, array $params = []) {

	    $color = new Color();

	    if(empty($params)) {
		   return $color->getColoredString($message, 'green').PHP_EOL;
	    }

	    return $color->getColoredString(vsprintf(self::$messages[$message], $params), 'green').PHP_EOL;
    }

    /**
     * Debug message
     *
     * @param string $message
     * @param string $param
     * @return string
     */
    public static function debug($message, $param) {
        return (new Color())->getColoredString(sprintf(self::$messages[$message], $param), 'brown').PHP_EOL;
    }

    /**
     * Notice message
     *
     * @param string $message
     * @param string $param
     * @return string
     */
    public static function notice($message, $param) {
        return PHP_EOL.(new Color())->getColoredString(sprintf(self::$messages[$message], $param), 'purple').PHP_EOL;
    }

	/**
	 * Get message
	 *
	 * @param $message
	 * @param $params
	 *
	 * @return string
	 */
	public static function get($message, $params) {
		return PHP_EOL.(vsprintf(self::$messages[$message], $params)).PHP_EOL;
	}

}