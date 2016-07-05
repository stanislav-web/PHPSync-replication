<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

class Hotline extends EntityAbstract
{
	/**
	 * Entity mapper
	 *
	 * @const MAPPER
	 */
	const MAPPER = 'HotlineMapper';

	/**
	 * Load transfer properties
	 *
	 * @return mixed
	 */
	public function load()
	{
        $frontendMapper = $this->getFrontendMapper();
        $shopIdsFront = current($frontendMapper->loadShopIds())['shop_id'];

		$backendMapper = $this->getBackendMapper();
		$backendMapper->setQueryParams([
				'dateStart' => $backendMapper->getSyncronizeDate(),
	            'shopIds'   => $shopIdsFront,
			]
		);

        $hotlineData = $backendMapper->loadHotlineData();


        if(ENV != 'production') {
            echo Message::success('hotlineLoad', [
	            count($hotlineData['hotline']),
	            count($hotlineData['hotlineCount']),
	            count($hotlineData['hotlineItems'])
            ]);
        }

		$this->prepare($hotlineData);
	}

	/**
	 * Prepare entity data
	 *
	 * @return mixed
	 */
	public function prepare(array $data)
	{
        $prepareData = [];

        if(empty($data['hotline']) === false || empty($data['hotlineCount']) === false || empty($data['hotlineItems']) === false) {

            $prepareData['add']['hotline'] = $data['hotline'];
            $prepareData['add']['hotlineCount'] = $data['hotlineCount'];
            $prepareData['add']['hotlineItems'] = $data['hotlineItems'];
        }

        $this->unload($prepareData);
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
                    'addedHotline' => $frontendMapper->unloadHotline($data['add']['hotline']),
                    'addedHotlineCount' => $frontendMapper->unloadHotlineCount($data['add']['hotlineCount']),
                    'addedHotlineItems' => $frontendMapper->unloadHotlineItems($data['add']['hotlineItems']),
				];
			});

			$message = Message::success('hotlineUnload', [
				$data['addedHotline'],
				$frontendMapper::TABLE,
				$data['addedHotlineCount'],
				$frontendMapper::TABLE_COUNT,
				$data['addedHotlineItems'],
				$frontendMapper::TABLE_ITEMS,
			]);

			$this->getLogger()->debug($message);
			echo (ENV != 'production') ? $message : null;
		}

        $this->getBackendMapper()->setSyncronizeDate();
    }

}

