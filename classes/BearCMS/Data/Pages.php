<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Data;

use BearCMS\Internal;
use BearCMS\Internal\Config;

/**
 * 
 */
class Pages
{
    //    use \BearFramework\Models\ModelsRepositoryTrait;
    //    use \BearFramework\Models\ModelsRepositoryRequestTrait;
    //    use \BearFramework\Models\ModelsRepositoryToArrayTrait;
    //    use \BearFramework\Models\ModelsRepositoryToJSONTrait;
    //
    //    function __construct()
    //    {
    //        $this->setModel(\BearCMS\Data\Pages\Page::class, 'id');
    //        $this->useAppDataDriver('bearcms/pages/page/');
    //    }

    /**
     * 
     * @param string $id
     * @return \BearCMS\Data\Pages\Page|null
     */
    public function get(string $id): ?\BearCMS\Data\Pages\Page
    {
        $data = Internal\Data::getValue('bearcms/pages/page/' . md5($id) . '.json');
        if ($data !== null) {
            return \BearCMS\Data\Pages\Page::fromJSON($data);
        }
        if ($id === 'home' && Config::$autoCreateHomePage) {
            return \BearCMS\Internal\Data\Pages::getDefaultHomePage();
        }
        return null;
    }

    /**
     * 
     * @return \BearFramework\Models\ModelsList
     */
    public function getList(): \BearFramework\Models\ModelsList
    {
        return Internal\Data\Pages::getPagesList();
    }
}
