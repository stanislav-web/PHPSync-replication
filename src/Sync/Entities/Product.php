<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Resolvers\Format;
use Sync\Development\Message;
/**
 * Class Product. Product entity
 *
 * @package Sync\Entities
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Product.php
 */
class Product extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'ProductMapper';

    /**
     * Remove product chunk size
     *
     * @const CHUNK_SIZE
     */
    const CHUNK_SIZE = 300;

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

        // load products
        $products = $backendMapper->setQueryParams(
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $shopIdsFront
                    )->loadProducts();

        // load product category relations
        $productCategories = $backendMapper->setQueryParams(
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $shopIdsFront
                    )->loadProductCategories();

        // load product tags relations
        $productTags = $backendMapper->setQueryParams(
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $shopIdsFront
                    )->loadProductTags();

        // load product sizes relations
        $productSizes = $backendMapper->setQueryParams(
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $shopIdsFront
                    )->loadProductSizes();

        // load product types relations
        $productTypes = $backendMapper->setQueryParams(
                        $backendMapper->getSyncronizeDate(),
                        $backendMapper->getSyncronizeDate(),
                        $shopIdsFront
                    )->loadProductTypes();


	    if(ENV != 'production') {
		    echo Message::success('loadProducts', [
			    count($products),
			    count($productCategories),
			    count($productTags),
			    count($productSizes),
			    count($productTypes)
		    ]);
	    }

        $this->prepare([
            'products'          => $products,
            'productCategories' => $productCategories,
            'productTags'       => $productTags,
            'productTypes'      => $productTypes,
            'productSizes'       => $productSizes,
        ]);
    }

    /**
     * Prepare entity data
     *
     * @return mixed
     */
    public function prepare(array $data)
    {
        // get product ids column
        $products = array_chunk($data['products'], self::CHUNK_SIZE);

        $splProduct = new \SplFixedArray(count($products));

        $prepareData = [
            'remove' => [
                'productsIds' => []
            ]
        ];
        for($i = 0; $i < $splProduct->count(); $i++) {
            // part array as chunk for remove
            $prepareData['remove']['productsIds'][$i] = Format::getColumn($products[$i], 'id');
        }

        $prepareData['add']['products']          = $data['products'];
        $prepareData['add']['productTags']       = $data['productTags'];
        $prepareData['add']['productTypes']      = $data['productTypes'];
        $prepareData['add']['productSizes']      = $data['productSizes'];
        $prepareData['add']['productCategories'] = $data['productCategories'];

        $this->unload($prepareData);
    }

    /**
     * Make entity report
     *
     * @return mixed
     */
    public function unload(array $data)
    {

        $frontendMapper = $this->getFrontendMapper();

        $data = $frontendMapper->callTransaction(function() use ($frontendMapper, $data) {

            $removedProductTags = 0;

            if(empty($data['remove']['productsIds']) === false) {
                foreach($data['remove']['productsIds'] as $productIds) {
                    $removedProductTags += $frontendMapper->removeProductTags(['product_id' => $productIds]);
                }
            }

            return [
                // delete all tags from relations
                'removedProductTags'     => $removedProductTags,

                // insert products
                'addedProducts'         => $frontendMapper->unloadProducts($data['add']['products']),

                // insert categories into rel
                'addedProductCategories'       => $frontendMapper->unloadProductCategories($data['add']['productCategories']),

                // insert tags_tags into rel
                'addedProductTags'       => $frontendMapper->unloadProductTags($data['add']['productTags']),

                // insert product types into rel
                'addedProductTypes'       => $frontendMapper->unloadProductTags($data['add']['productTypes']),

                // insert product sizes into rel
                'addedProductSizes'       => $frontendMapper->unloadProductTags($data['add']['productSizes']),
            ];
        });

        $message = Message::success('unloadProducts', [
            $data['removedProductTags'],
            $frontendMapper::TABLE_REL,
            $data['addedProducts'],
            $frontendMapper::TABLE,
            $data['addedProductCategories'],
            $data['addedProductTags'],
            $data['addedProductTypes'],
            $data['addedProductSizes'],
            $frontendMapper::TABLE_REL,
        ]);

        $this->getLogger()->debug($message);
        echo (ENV != 'production') ? $message : null;

        $this->getBackendMapper()->setSyncronizeDate();
    }
}