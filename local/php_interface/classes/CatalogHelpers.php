<?php
namespace Clanbeauty;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use CIBlockElement;
use CUtil;
use RuntimeException;
use WL\IblockUtils;
use Bitrix\Main\LoaderException;
use WL\Log;

class CatalogHelpers
{
    public static function onSave($id)
    {
        if ($id > 0) {
            Loader::includeModule('iblock');

            $items = [];
            $filter = ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ID' => $id, 'ACTIVE' => 'Y'];
            $row = CIBlockElement::GetList(
                arFilter      : $filter,
                arSelectFields: [
                    'ID',
                    'IBLOCK_ID',
                    'ACTIVE',
                ]
            )->Fetch();
            $row['PROPERTIES'] = [];
            $items[$row['ID']] = $row;

            CIBlockElement::GetPropertyValuesArray(
                result        : $items,
                iblockID      : $filter['IBLOCK_ID'],
                filter        : $filter,
                propertyFilter: ['CODE' => ['BRAND', 'BRAND_TEXT', 'CML2_TRAITS']]
            );

            $item = array_pop($items);
            //Автопривязка бренда
            try {
                $brandId = null;
                $brandName = trim($item['PROPERTIES']['BRAND_TEXT']['VALUE']);
                if ($brandName) {
                    $iblockId = IblockUtils::getIdByCode('aspro_next_brands');
                    if (!$iblockId) {
                        throw new RuntimeException('Инфоблок Бренды не найден');
                    }

                    $filter = [
                        '=IBLOCK_ID' => IblockUtils::getIdByCode('aspro_next_brands'),
                        'NAME' => $brandName,
                    ];
                    $brand = CIBlockElement::GetList(arFilter: $filter, arNavStartParams: ['nTopCount' => 1])->fetch();
                    if($brand){
                        $brandId = $brand['ID'];
                    } else {
                        $element = new CIBlockElement();
                        $brandId = $element->Add(
                            arFields: [
                                'IBLOCK_ID' => $iblockId,
                                'NAME'      => $brandName,
                                'CODE'      => CUtil::translit(str: $brandName, lang: 'ru'),
                            ]
                        );

                        if (!$brandId) {
                            throw new RuntimeException($element->LAST_ERROR);
                        }
                    }
                }

                //Заполняем Код из 1С
                $onecCode = null;
                $index = array_search(
                    needle  : 'Код',
                    haystack: $item['PROPERTIES']['CML2_TRAITS']['DESCRIPTION'],
                    strict  : true
                );
                if ($index >= 0) {
                    $onecCode = $item['PROPERTIES']['CML2_TRAITS']['VALUE'][$index];
                }

                CIBlockElement::SetPropertyValuesEx(
                    ELEMENT_ID: $id,
                    IBLOCK_ID: GOODS_IBLOCK_ID,
                    PROPERTY_VALUES: [
                        'BRAND'   => $brandId,
                        'CODE_1C' => $onecCode,
                    ]
                );
            }catch (\Throwable $exception){
                Debug::writeToFile([
                    'message' => $exception->getMessage()
                ]);
            }

            $brandId = $item['PROPERTIES']['BRAND']['VALUE'];
            //Активация/деактивация брендов
            if ($item['ACTIVE'] === 'Y') {
                $el = new CIBlockElement();
                $el->Update($brandId, ['ACTIVE' => 'Y']);
            } else {
                $filter = [
                    'IBLOCK_ID' => GOODS_IBLOCK_ID,
                    'PROPERTY_BRAND' => $brandId,
                    'ACTIVE' => 'Y'
                ];

                $cnt = CIBlockElement::GetList([], $filter, [], false, ['ID']);
                if ($cnt == 0) {
                    $el = new CIBlockElement();
                    $el->Update($brandId, ['ACTIVE' => 'N']);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * На сохранение SKU
     *
     * @param int $id
     *
     * @return bool
     * @throws LoaderException
     */
    public static function onSaveSku(int $id): bool
    {
        if ($id > 0) {
            Loader::requireModule('iblock');

            $items = [];
            $filter = ['IBLOCK_ID' => SKU_IBLOCK_ID, 'ID' => $id, 'ACTIVE' => 'Y'];
            $row = CIBlockElement::GetList(
                arFilter      : $filter,
                arSelectFields: [
                    'ID',
                    'IBLOCK_ID',
                    'ACTIVE',
                ]
            )->Fetch();
            $row['PROPERTIES'] = [];
            $items[$row['ID']] = $row;

            CIBlockElement::GetPropertyValuesArray(
                result        : $items,
                iblockID      : $filter['IBLOCK_ID'],
                filter        : $filter,
                propertyFilter: ['CODE' => ['VARIANT_1', 'VARIANT']]
            );

            $item = array_pop($items);
            try {
                $variant1CValue = trim($item['PROPERTIES']['VARIANT_1']['VALUE']);
                if(empty($variant1CValue)) {
                    return false;
                }
                else if (is_numeric($variant1CValue)) {
                    $variantValue = $variant1CValue;
                } else {
                    $iblockId = IblockUtils::getIdByCode('product_variant');
                    $code = CUtil::translit(str: $variant1CValue, lang: 'ru');
                    $filter = [
                        '=IBLOCK_ID' => $iblockId,
                        [
                            'LOGIC' => 'OR',
                            'NAME'  => $variant1CValue,
                            'CODE'  => $code
                        ],
                    ];
                    $variant = CIBlockElement::GetList(arFilter: $filter, arNavStartParams: ['nTopCount' => 1])->fetch(
                    );
                    if ($variant) {
                        $variantValue = $variant['ID'];
                    } else {
                        $element = new CIBlockElement();
                        $variantValue = $element->Add(
                            arFields: [
                                'IBLOCK_ID' => $iblockId,
                                'NAME'      => $variant1CValue,
                                'CODE'      => $code,
                            ]
                        );

                        if (!$variantValue) {
                            throw new RuntimeException($element->LAST_ERROR);
                        }
                    }
                }

                CIBlockElement::SetPropertyValuesEx(
                    ELEMENT_ID     : $id,
                    IBLOCK_ID      : SKU_IBLOCK_ID,
                    PROPERTY_VALUES: [
                        'VARIANT' => $variantValue,
                    ]
                );
            } catch (\Throwable $exception) {
                Log::getInstance()->exception($exception);
            }
        }
        return true;
    }
}