<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;

class Statistics extends EntityAbstract
{
    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'StatisticsMapper';

    const ALLOW_DIFF_ITEMS = 50;

    const ALLOW_DIFF_COUNT = 500;

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
        $backendMapper->setQueryParams($shopIdsFront);

        $publishedItemsBack = current($backendMapper->loadPublishedItems())['count'];
        $countItemsBack = current($backendMapper->loadCountItems())['count'];

        $publishedItemsFront = current($frontendMapper->loadPublishedItems())['count'];
        $countItemsFront = current($frontendMapper->loadCountItems())['count'];

        $publishedWithoutSizesItems = $frontendMapper->loadPublishedWithoutSizesInStockItems();

        if(ENV != 'production') {
            echo Message::success('publishedItems', [
                $publishedItemsBack,
                $countItemsBack,
                $publishedItemsFront,
                $countItemsFront,
                count($publishedWithoutSizesItems)
            ]);
        }

        $this->prepare([
            'publishedItemsBack' => $publishedItemsBack,
            'countItemsBack' => $countItemsBack,
            'publishedItemsFront' => $publishedItemsFront,
            'countItemsFront' => $countItemsFront,
            'publishedWithoutSizesItems' => $publishedWithoutSizesItems
        ]);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        $prepareData = ['diffPublishedItems' => 0, 'diffCountItems' => 0];

        if(empty($data['publishedItemsBack']) === false && empty($data['publishedItemsFront']) === false) {
            $prepareData['diffPublishedItems'] = abs($data['publishedItemsBack'] - $data['publishedItemsFront']);
        }

        if(empty($data['countItemsBack']) === false && empty($data['countItemsFront']) === false) {
            $prepareData['diffCountItems'] = abs($data['countItemsBack'] - $data['countItemsFront']);
        }

        if(empty($data['publishedWithoutSizesItems']) === false) {
            $prepareData['publishedWithoutSizesItems'] = $data['publishedWithoutSizesItems'];
        }

        $this->unload(array_merge($prepareData, $data));
    }

    /**
     * Unload data to frontend
     *
     * @return mixed
     */
    public function unload(array $data = [])
    {
        if(empty($data) === false) {
            $warning = false;

            if($data['diffPublishedItems'] >= self::ALLOW_DIFF_ITEMS) $warning = true;

            if($data['diffCountItems'] >= self::ALLOW_DIFF_COUNT) $warning = true;

            $message = Message::success('diffItems', [
                $data['diffPublishedItems'],
                $data['diffCountItems'],
                count($data['publishedWithoutSizesItems'])
            ]);

            $this->getLogger()->debug($message);

            if($warning === true) {
                $result = $this->getNotification()->sendMail(
                    'Warning: a big difference of published items between Back & Front',
                    Message::get('diffItems', [
                        $data['diffPublishedItems'],
                        $data['diffCountItems']
                    ])
                );
                Message::error($result['message']);
            }


            if(date('i') > 0 && date('i') < 5) {
                $this->getNotification()->sendMail(
                    'Real quantity of published items between Back & Front',
                    Message::get('quantityItems', [
                        $data['publishedItemsBack'],
                        $data['countItemsBack'],
                        $data['publishedItemsFront'],
                        $data['countItemsFront']
                    ])
                );
            }

            if(count($data['publishedWithoutSizesItems']) > 0){

                $messages = [];
                foreach($data['publishedWithoutSizesItems'] as $item) {
                    $messages[] = Message::get('publishedWithoutSizesItem', [
                        $item['articul'],
                        $item['published'],
                        $item['filter_size'],
                        $item['date_update'],
                    ]);
                }

                $result = $this->getNotification()->sendMail(
                    'Warning: there are published products without sizes in stock on Back & Front',
                    Message::get('publishedWithoutSizesItems', [
                        count($data['publishedWithoutSizesItems']),
                        implode("", $messages)
                    ])
                );
                Message::error($result['message']);
            }

            echo (ENV != 'production') ? $message : null;
        }

    }

}

