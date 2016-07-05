<?php
namespace Sync\Resolvers;
use Sync\Exceptions\InputException;

/**
 * Class Input. Input params resolver
 *
 * @package Sync\Resolvers
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Resolvers/Input.php
 */
class Input {

    /**
     * Required cli arguments
     *
     * @var array $arguments
     */
    private $arguments = [
        't:'     => 'target:',
    ];

    /**
     * Get CLI request options
     *
     * @throws \Sync\Exceptions\InputException
     * @return array $options
     */
    public function getOptions() {

        $options = getopt(implode('', array_keys($this->arguments)), array_values($this->arguments));

        if(empty($options) === true) {
            throw new InputException('Passed invalid arguments');
        }

        return $options;
    }

}