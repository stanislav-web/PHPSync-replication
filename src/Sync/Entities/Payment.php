<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

/**
 * Class Payment. Payment entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Payment.php
 */
class Payment extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'PaymentMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $backendMapper = $this->getBackendMapper();
        $payments = $backendMapper->setQueryParams(
            $backendMapper->getSyncronizeDate()
        )->loadPayments();

        if(ENV != 'production') {
            echo Message::success('load', [count($payments)]);
        }

        $this->prepare($payments);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        $this->unload($data);
    }

    /**
     * Unload data to frontend
     *
     * @return mixed
     */
    public function unload(array $data = [])
    {
        if(empty($data) === false) {

            $frontendMapper = $this->getFrontendMapper();

            $data = $frontendMapper->callTransaction(function() use ($frontendMapper, $data) {

                return [
                    'addedPayments'      => $frontendMapper->unloadPayments($data),
                ];
            });

            $message = Message::success('unload', [
                $data['addedPayments'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}