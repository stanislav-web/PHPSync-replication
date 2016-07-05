<?php
namespace Sync\Entities;

use Sync\Aware\EntityAbstract;
use Sync\Development\Message;
use Sync\Resolvers\Format;

/**
 * Class Banner. Banner entity
 *
 * @package Sync\Entities\Frontend
 * @subpackage Sync
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | <stanisov@gmail.com>
 * @filesource /Sync/Entities/Banner.php
 */
class Banner extends EntityAbstract {

    /**
     * Entity mapper
     *
     * @const MAPPER
     */
    const MAPPER = 'BannerMapper';

    /**
     * Load transfer properties
     */
    public function load()
    {
        $frontendMapper = $this->getFrontendMapper();
        $siteIdsFront = Format::getColumn($frontendMapper->loadSiteIds(), 'site_id');

        $backendMapper = $this->getBackendMapper();
        $banners = $backendMapper->loadBanners();

        if(ENV != 'production') {
            echo Message::success('load', [count($banners)]);
        }

        $this->prepare([
            'siteIdsFront' => $siteIdsFront,
            'banners' => $banners
        ]);
    }

    /**
     * Prepare entity data
     *
     * @param array $data
     * @return null|void
     */
    public function prepare(array $data)
    {
        $banners = [];
        if(empty($data['banners']) === false) {

            foreach ($data['banners'] as $banner) {

                $sites = json_decode($banner['site_id']);
	            $sites = (!is_array($sites)) ? (array) $sites : $sites;
                foreach ($sites as $site_id) {
                    if(in_array($site_id, $data['siteIdsFront'])) {
                        $banner['site_id'] = $site_id;
                        $banners[] = $banner;
                    }
                }
            }
        }

        $this->unload($banners);
    }

    /**
     * Unload data to frontend
     *
     * @param array $data
     * @return null
     */
    public function unload(array $data = [])
    {
        if(empty($data) === false) {

            $frontendMapper = $this->getFrontendMapper();

            $data = $frontendMapper->callTransaction(function() use ($frontendMapper, $data) {

                return [
                    'addedBanners'     => $frontendMapper->unload($data),
                    'removedBanners'   => $frontendMapper->removeBanners(),
                ];
            });

            $message = Message::success('banner', [
                $data['removedBanners'],
                $data['addedBanners'],
                $frontendMapper::TABLE,
            ]);

            $this->getLogger()->debug($message);
            echo (ENV != 'production') ? $message : null;
        }

        $this->getBackendMapper()->setSyncronizeDate();
    }
}