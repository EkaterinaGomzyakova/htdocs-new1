<?php

namespace Sprint\Migration;

class BX_5873_certificateDesignChoice20230420000000 extends Version
{
    protected $description = "Миграция создания свойств 'Дизайн сертификата', 'Номинал сертификата' и связанного Хайлоадблока дизайнов";

    protected $moduleVersion = "4.1.2";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        \CModule::IncludeModule('iblock');

        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->getIblockIdIfExists('cosmetics_sku', 'catalog');
        if (!empty($iblockId)) {
            $hlblockId = $helper->Hlblock()->saveHlblock([
                'NAME' => 'Certificatedesigns',
                'TABLE_NAME' => 'b_hlbd_certificatedesigns',
            ]);
            if (!empty($hlblockId)) {
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_NAME',
                    'USER_TYPE_ID' => 'string',
                    'XML_ID' => '',
                    'SORT' => '200',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'Y',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'ROWS' => 1,
                            'REGEXP' => '',
                            'MIN_LENGTH' => 0,
                            'MAX_LENGTH' => 0,
                            'DEFAULT_VALUE' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Название',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Название',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Название',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_SORT',
                    'USER_TYPE_ID' => 'integer',
                    'XML_ID' => '',
                    'SORT' => '300',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'MIN_VALUE' => 0,
                            'MAX_VALUE' => 0,
                            'DEFAULT_VALUE' => 0,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Сортировка',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Сортировка',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Сортировка',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_XML_ID',
                    'USER_TYPE_ID' => 'string',
                    'XML_ID' => '',
                    'SORT' => '400',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'Y',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'ROWS' => 1,
                            'REGEXP' => '',
                            'MIN_LENGTH' => 0,
                            'MAX_LENGTH' => 0,
                            'DEFAULT_VALUE' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Внешний код',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Внешний код',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Внешний код',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_LINK',
                    'USER_TYPE_ID' => 'string',
                    'XML_ID' => '',
                    'SORT' => '500',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'ROWS' => 1,
                            'REGEXP' => '',
                            'MIN_LENGTH' => 0,
                            'MAX_LENGTH' => 0,
                            'DEFAULT_VALUE' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Ссылка',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Ссылка',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Ссылка',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_DESCRIPTION',
                    'USER_TYPE_ID' => 'string',
                    'XML_ID' => '',
                    'SORT' => '600',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'ROWS' => 1,
                            'REGEXP' => '',
                            'MIN_LENGTH' => 0,
                            'MAX_LENGTH' => 0,
                            'DEFAULT_VALUE' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Описание',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Описание',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Описание',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_FULL_DESCRIPTION',
                    'USER_TYPE_ID' => 'string',
                    'XML_ID' => '',
                    'SORT' => '700',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'ROWS' => 1,
                            'REGEXP' => '',
                            'MIN_LENGTH' => 0,
                            'MAX_LENGTH' => 0,
                            'DEFAULT_VALUE' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Полное описание',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Полное описание',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Полное описание',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_DEF',
                    'USER_TYPE_ID' => 'boolean',
                    'XML_ID' => '',
                    'SORT' => '800',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'DEFAULT_VALUE' => 0,
                            'DISPLAY' => 'CHECKBOX',
                            'LABEL' =>
                                [
                                    0 => NULL,
                                    1 => NULL,
                                ],
                            'LABEL_CHECKBOX' => NULL,
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'По умолчанию',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'По умолчанию',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'По умолчанию',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);
                $helper->Hlblock()->saveField($hlblockId, [
                    'FIELD_NAME' => 'UF_FILE',
                    'USER_TYPE_ID' => 'file',
                    'XML_ID' => '',
                    'SORT' => '900',
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' =>
                        [
                            'SIZE' => 20,
                            'LIST_WIDTH' => 0,
                            'LIST_HEIGHT' => 0,
                            'MAX_SHOW_SIZE' => 0,
                            'MAX_ALLOWED_SIZE' => 0,
                            'EXTENSIONS' =>
                                [
                                ],
                            'TARGET_BLANK' => 'Y',
                        ],
                    'EDIT_FORM_LABEL' =>
                        [
                            'ru' => 'Изображение',
                        ],
                    'LIST_COLUMN_LABEL' =>
                        [
                            'ru' => 'Изображение',
                        ],
                    'LIST_FILTER_LABEL' =>
                        [
                            'ru' => 'Изображение',
                        ],
                    'ERROR_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                    'HELP_MESSAGE' =>
                        [
                            'ru' => NULL,
                        ],
                ]);

                $helper->Iblock()->saveProperty($iblockId, [
                    'NAME' => 'Дизайн сертификата',
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'DESIGN',
                    'DEFAULT_VALUE' => '',
                    'PROPERTY_TYPE' => 'S',
                    'ROW_COUNT' => '1',
                    'COL_COUNT' => '30',
                    'LIST_TYPE' => 'L',
                    'MULTIPLE' => 'N',
                    'XML_ID' => NULL,
                    'FILE_TYPE' => '',
                    'MULTIPLE_CNT' => '5',
                    'LINK_IBLOCK_ID' => '0',
                    'WITH_DESCRIPTION' => 'N',
                    'SEARCHABLE' => 'N',
                    'FILTRABLE' => 'N',
                    'IS_REQUIRED' => 'N',
                    'VERSION' => '1',
                    'USER_TYPE' => 'directory',
                    'USER_TYPE_SETTINGS' =>
                        [
                            'size' => 1,
                            'width' => 0,
                            'group' => 'N',
                            'multiple' => 'N',
                            'TABLE_NAME' => 'b_hlbd_certificatedesigns',
                        ],
                    'HINT' => '',
                    'FEATURES' =>
                        [
                            0 =>
                                [
                                    'MODULE_ID' => 'catalog',
                                    'FEATURE_ID' => 'IN_BASKET',
                                    'IS_ENABLED' => 'Y',
                                ],
                            1 =>
                                [
                                    'MODULE_ID' => 'catalog',
                                    'FEATURE_ID' => 'OFFER_TREE',
                                    'IS_ENABLED' => 'Y',
                                ],
                            2 =>
                                [
                                    'MODULE_ID' => 'iblock',
                                    'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
                                    'IS_ENABLED' => 'Y',
                                ],
                            3 =>
                                [
                                    'MODULE_ID' => 'iblock',
                                    'FEATURE_ID' => 'LIST_PAGE_SHOW',
                                    'IS_ENABLED' => 'N',
                                ],
                            4 =>
                                [
                                    'MODULE_ID' => 'yandex.market',
                                    'FEATURE_ID' => 'YAMARKET_COMMON',
                                    'IS_ENABLED' => 'N',
                                ],
                            5 =>
                                [
                                    'MODULE_ID' => 'yandex.market',
                                    'FEATURE_ID' => 'YAMARKET_TURBO',
                                    'IS_ENABLED' => 'N',
                                ],
                        ],
                ]);
                $helper->Iblock()->saveProperty($iblockId, [
                    'NAME' => 'Подарочный сертификат',
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'GIFT_CERTIFICATE',
                    'DEFAULT_VALUE' => NULL,
                    'PROPERTY_TYPE' => 'S',
                    'ROW_COUNT' => '1',
                    'COL_COUNT' => '30',
                    'LIST_TYPE' => 'L',
                    'MULTIPLE' => 'N',
                    'XML_ID' => NULL,
                    'FILE_TYPE' => '',
                    'MULTIPLE_CNT' => '5',
                    'LINK_IBLOCK_ID' => '0',
                    'WITH_DESCRIPTION' => 'N',
                    'SEARCHABLE' => 'N',
                    'FILTRABLE' => 'N',
                    'IS_REQUIRED' => 'N',
                    'VERSION' => '1',
                    'USER_TYPE' => 'basket_rule',
                    'USER_TYPE_SETTINGS' => NULL,
                    'HINT' => '',
                ]);
                $helper->Iblock()->saveProperty($iblockId, [
                    'NAME' => 'Номинал сертификата',
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'NOMINAL',
                    'DEFAULT_VALUE' => '',
                    'PROPERTY_TYPE' => 'L',
                    'ROW_COUNT' => '1',
                    'COL_COUNT' => '30',
                    'LIST_TYPE' => 'L',
                    'MULTIPLE' => 'N',
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
                        [
                            0 =>
                                [
                                    'VALUE' => '1000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'acb17196dce6c6abb23cd384abd95c0f',
                                ],
                            1 =>
                                [
                                    'VALUE' => '10000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '247bbf6f099553fc3201f4adfbd33cef',
                                ],
                            2 =>
                                [
                                    'VALUE' => '1500',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'e05c34a7c0e057a50f50b978d0bb67a6',
                                ],
                            3 =>
                                [
                                    'VALUE' => '2000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'aefe53685ead67c087f83b52742058bd',
                                ],
                            4 =>
                                [
                                    'VALUE' => '2500',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '11a3876343eaecc15c021ebc29e28292',
                                ],
                            5 =>
                                [
                                    'VALUE' => '3000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'd1e9ab109a5c3eea5f018f1f978b9dd7',
                                ],
                            6 =>
                                [
                                    'VALUE' => '3500',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '74773d85c5dc7b0f10b928a5f00d8bfc',
                                ],
                            7 =>
                                [
                                    'VALUE' => '4000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '56e69e2131c3ed143529f62d267e5585',
                                ],
                            8 =>
                                [
                                    'VALUE' => '4500',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '3dde4fde7238681bb1c69eb00e7f009d',
                                ],
                            9 =>
                                [
                                    'VALUE' => '500',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '5b64046be7bc5c71def2ba1e5387f75b',
                                ],
                            10 =>
                                [
                                    'VALUE' => '5000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'ba1f05e1ac64992f517b607118e08d8f',
                                ],
                            11 =>
                                [
                                    'VALUE' => '6000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'ef6fc2897e851dac9981fc14343b9ff9',
                                ],
                            12 =>
                                [
                                    'VALUE' => '7000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => '77c825296f5dde7fbaf307b1b27f17f2',
                                ],
                            13 =>
                                [
                                    'VALUE' => '8000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'd708b52233c2d76749c82a6675d0cc03',
                                ],
                            14 =>
                                [
                                    'VALUE' => '9000',
                                    'DEF' => 'N',
                                    'SORT' => '500',
                                    'XML_ID' => 'd1f54389becfdee67fb84d845043f0af',
                                ],
                        ],
                    'FEATURES' =>
                        [
                            0 =>
                                [
                                    'MODULE_ID' => 'catalog',
                                    'FEATURE_ID' => 'IN_BASKET',
                                    'IS_ENABLED' => 'Y',
                                ],
                            1 =>
                                [
                                    'MODULE_ID' => 'catalog',
                                    'FEATURE_ID' => 'OFFER_TREE',
                                    'IS_ENABLED' => 'Y',
                                ],
                            2 =>
                                [
                                    'MODULE_ID' => 'iblock',
                                    'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
                                    'IS_ENABLED' => 'Y',
                                ],
                            3 =>
                                [
                                    'MODULE_ID' => 'iblock',
                                    'FEATURE_ID' => 'LIST_PAGE_SHOW',
                                    'IS_ENABLED' => 'N',
                                ],
                            4 =>
                                [
                                    'MODULE_ID' => 'yandex.market',
                                    'FEATURE_ID' => 'YAMARKET_COMMON',
                                    'IS_ENABLED' => 'N',
                                ],
                            5 =>
                                [
                                    'MODULE_ID' => 'yandex.market',
                                    'FEATURE_ID' => 'YAMARKET_TURBO',
                                    'IS_ENABLED' => 'N',
                                ],
                        ],
                ]);
            }
        }
    }
}