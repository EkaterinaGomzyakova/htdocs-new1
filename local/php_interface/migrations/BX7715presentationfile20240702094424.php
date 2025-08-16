<?php

namespace Sprint\Migration;


class BX7715presentationfile20240702094424 extends Version
{
    protected $author = "d.shram@weblipka.ru";

    protected $description = "Свойство бренда - фильтр";

    protected $moduleVersion = "4.9.8";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('aspro_next_brands', 'catalog_dictionaries');
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Фильтр',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'FILTER',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'Y',
            'XML_ID' => NULL,
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
            'VALUES' =>
                array(
                    0 =>
                        array(
                            'VALUE' => 'Популярные',
                            'DEF' => 'N',
                            'SORT' => '100',
                            'XML_ID' => 'HOT',
                        ),
                    1 =>
                        array(
                            'VALUE' => 'Новинки',
                            'DEF' => 'N',
                            'SORT' => '200',
                            'XML_ID' => 'NEW',
                        ),
                ),
            'FEATURES' =>
                array(
                    0 =>
                        array(
                            'MODULE_ID' => 'iblock',
                            'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
                            'IS_ENABLED' => 'Y',
                        ),
                    1 =>
                        array(
                            'MODULE_ID' => 'iblock',
                            'FEATURE_ID' => 'LIST_PAGE_SHOW',
                            'IS_ENABLED' => 'Y',
                        ),
                    2 =>
                        array(
                            'MODULE_ID' => 'yandex.market',
                            'FEATURE_ID' => 'YAMARKET_COMMON',
                            'IS_ENABLED' => 'N',
                        ),
                    3 =>
                        array(
                            'MODULE_ID' => 'yandex.market',
                            'FEATURE_ID' => 'YAMARKET_TURBO',
                            'IS_ENABLED' => 'N',
                        ),
                ),
        ));

    }
}
