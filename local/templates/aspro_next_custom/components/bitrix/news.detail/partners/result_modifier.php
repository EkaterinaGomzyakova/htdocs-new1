<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die;?>
<?
CModule::IncludeModule("iblock");
$arResult['GALLERY'] = $arResult['DOCUMENTS'] = array();

if($arResult['DISPLAY_PROPERTIES'])
{

	if($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] && is_array($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE']) && $arParams['SHOW_GALLERY'] == 'Y'){
		foreach($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] as $img){
			$arResult['GALLERY'][] = array(
				'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
				'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1500, 'height' => 1500), BX_RESIZE_PROPORTIONAL_ALT, true),
				'THUMB' => CFile::ResizeImageGet($img, array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_EXACT, true),
				'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
				'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
			);
		}
	}
	$arResult['DOCUMENTS'] = $arResult['DISPLAY_PROPERTIES']['DOCUMENTS'];
	$arResult['DISPLAY_PROPERTIES'] = CNext::PrepareItemProps($arResult['DISPLAY_PROPERTIES']);
}

$arFilter = array(
	"IBLOCK_ID" => "2",
	"PROPERTY_BRAND" => $arResult['ID'],

);
$dbLinkedProducts = CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter,false, false, array("ID", "IBLOCK_ID", "PROPERTY_BRAND") ); //
$arLinkedProductsIDs = array();

while($arSingleProduct = $dbLinkedProducts->Fetch())
{
	$arLinkedProductsIDs[] = $arSingleProduct['ID'];
}
$arResult['LINKED_PRODUCTS'] = $arLinkedProductsIDs;

?>