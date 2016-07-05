<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;
use Sync\Resolvers\Format;

/**
 * Class Category. Category entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Category.php
 */
class Category extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'CategoryMapper';

    /**
     * Load transfer properties
     *
     * @return mixed
     */
    public function load()
    {
        $frontendMapper = $this->getFrontendMapper();
        $categoryIdsFront = Format::getColumn($frontendMapper->loadCategoryIds(), 'id');
        $shopIdsFront = current($frontendMapper->loadShopIds())['shop_id'];

        $backendMapper = $this->getBackendMapper();
        $categories = $backendMapper->loadCategories();
        $categoryIdsBack = Format::getColumn($backendMapper->loadCategoriesIds(), 'id');

        $backendMapper->setQueryParams(
            Format::implodeType($categoryIdsBack),
            $shopIdsFront);

        $shopCategories = $backendMapper->loadShopCategories();

        if(ENV != 'production') {
            echo Message::success('loadCategories', [count($categoryIdsBack), count($shopCategories), count($categoryIdsFront)]);
        }

        $this->prepare([
            'categories'        => $categories,
            'shopCategories'    => $shopCategories,
            'categoryIdsBack'   => $categoryIdsBack,
            'categoryIdsFront'  => $categoryIdsFront,
            'frontendMapper'    => $frontendMapper
        ]);

    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
	public function prepare(array $data)
	{

        $prepareData = [];

        if(empty($data['categoryIdsBack']) === false) {
            // preparing an array of id to delete from database front `categories`

            $prepareData['remove']['categories']['id'] = (empty($data['categoryIdsFront']) === false)
                ? array_diff($data['categoryIdsFront'], $data['categoryIdsBack']) : [];

            // an array of categories to insert into frontend
            $prepareData['add']['categories'] = $data['categories'];
            $prepareData['add']['shopCategories'] = $data['shopCategories'];
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
                    'removedCategories'     => $frontendMapper->removeCategories($data['remove']['categories']),
                    'addedCategories'       => $frontendMapper->unloadCategories($data['add']['categories']),
                    'addedShopCategories'   => $frontendMapper->unloadShopCategories($data['add']['shopCategories']),
                ];
            });

            $message = Message::success('unloadCategories', [
                $data['removedCategories'],
                $frontendMapper::TABLE,
                $data['addedCategories'],
                $frontendMapper::TABLE,
                $data['addedShopCategories'],
                $frontendMapper::TABLE_REL,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}