<?php
namespace Sync\Resolvers;

use Sync\Development\Message;
use Sync\Exceptions\RouteException;

/**
 * Class Router. Route params resolver
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/Router.php
 */
class Router {

    /**
     * Route params
     *
     * @var array $route
     */
    private $route = [];

	/**
	 * Config
	 *
	 * @var
	 */
    private $config;

    /**
     * Initialize route params
     *
     * @param Input $input
     */
    public function __construct(Input $input, \Sync\Resolvers\Config $config) {
        $this->route = $input->getOptions();
        $this->config = $config;
    }

    /**
     * Run target entity
     *
     * @throws \Sync\Exceptions\RouteException
     */
    public function run() {

        if((isset($this->route['target']) && $this->route['target'] == 'all')
            || (isset($this->route['t']) && $this->route['t'] == 'all')) {

            $this->load('shop');
            $this->load('brand');
            $this->load('category');
            $this->load('tag');
            $this->load('product');
            $this->load('price');
            $this->load('buy');
            $this->load('buyTogether');
            $this->load('banner');
            $this->load('delivery');
            $this->load('document');
            $this->load('payment');
            $this->load('region');
            $this->load('statistics');
        }
        else if((isset($this->route['t']) && $this->route['t'] !== 'all')
            || (isset($this->route['target']) && $this->route['target'] !== 'all')) {
            $object = (isset($this->route['target'])) ? $this->route['target']: $this->route['t'];
            $this->load($object);
        }
    }

    /**
     * Load entity
     *
     * @param string $object
     */
    private function load($object) {

	    echo (ENV === 'development') ? Message::notice('startLoad', $object) : null;

        $namespace = "Sync\\Entities\\".ucfirst($object);

        $object = new $namespace($this->config);
        if(method_exists(get_class($object), 'load')) {
            // load target entity
            $object->load();
        }
        else {
            throw new RouteException('Undefined method called');
        }
    }

}