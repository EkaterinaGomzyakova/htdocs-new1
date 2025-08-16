<?php

namespace SnailShop\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use setasign\Fpdi\Tfpdf;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use WL\HL;

class GenerateCertificate extends Controller
{
    //    const PATH_TO_TEMPLATE = ;

    public function configureActions()
    {
        return [
            'certificateInfo' => [],
            'generate' => [],
        ];
    }

    /**
     * Получить сертификат
     * @param string $text
     * @return array
     * @throws Exception
     */
    public function certificateInfoAction(string $text): array
    {
        Loader::includeModule('catalog');
        Loader::IncludeModule('sale');
        $isCertificate = false;

        $arResult['COUPON'] = \CCatalogDiscountCoupon::GetList([], ['=COUPON' => $text])->fetch();
        if (!empty($arResult['COUPON'])) {
            $arResult['COUPON']['SUM'] = ' ';
        }

        if (empty($arResult['COUPON'])) {
            Loader::includeModule('sale');
            $arResult['COUPON'] = \Bitrix\Sale\Internals\DiscountCouponTable::getList(['filter' => ['COUPON' => $text]])->fetch();

            if (!empty($arResult['COUPON'])) {
                $arResult['COUPON']['SUM'] = 0;
                $dateCreate = new \DateTime($arResult['COUPON']['DATE_CREATE']);
                $arResult['COUPON']['DATE_CREATE'] = $dateCreate->format('d.m.Y H:i:s');

                $parentDiscount = \Bitrix\Sale\Internals\DiscountTable::getList(['filter' => ['ID' => $arResult['COUPON']['DISCOUNT_ID']]])->fetch();
                $arResult['COUPON']['DISCOUNT_NAME'] = $parentDiscount['NAME'];
                $arResult['COUPON']['SUM'] = $parentDiscount['SHORT_DESCRIPTION_STRUCTURE']['VALUE'];
                $isCertificate = self::discountIsCertificate($parentDiscount);

                $arBasketItemProp = \Bitrix\Sale\Internals\BasketPropertyTable::getList(
                    [
                        'filter' => ['CODE' => 'COUPON', 'VALUE' => $text],
                        'select' => ['BASKET_ID'],
                    ]
                )->fetch();

                if ($arBasketItemProp['BASKET_ID'] > 0) {
                    $arBasket = \Bitrix\Sale\Basket::getList(
                        [
                            'filter' => ['ID' => $arBasketItemProp['BASKET_ID']],
                            'select' => ['ORDER_ID']
                        ]
                    )->fetch();

                    if ($arBasket['ORDER_ID'] > 0) {
                        $arResult['COUPON']['ORDER_ID'] = $arBasket['ORDER_ID'];
                    }
                }
            } else {
                throw new Exception('Купон не найден');
            }
        }

        return [
            'ID' => $arResult['COUPON']['ID'],
            'DISCOUNT_NAME' => $arResult['COUPON']['DISCOUNT_NAME'],
            'DATE_CREATE' => $arResult['COUPON']['DATE_CREATE'],
            'ACTIVE' => $arResult['COUPON']['ACTIVE'],
            'ACTIVE_DISPLAY' => ($arResult['COUPON']['ACTIVE'] == 'Y') ? 'Да' : 'Нет',
            'DESCRIPTION' => $arResult['COUPON']['DESCRIPTION'],
            'SUM' => $arResult['COUPON']['SUM'],
            'SUM_DISPLAY' => CurrencyFormat($arResult['COUPON']['SUM'], "RUB"),
            'IS_CERTIFICATE' => $isCertificate ? 'Y' : 'N',
            'ORDER_ID' => $arResult['COUPON']['ORDER_ID'] ?: "Отсутствует",
        ];
    }

    /**
     * Получение метаданных для шаблона сертификата
     *
     * @param string|null $designXmlId
     * @param int|null $defaultPictureId
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getCertificateTemplateData(string $designXmlId = null, int $defaultPictureId = null): array
    {
        $result = [
            'PDF_TEMPLATE' => Application::getDocumentRoot() . '/local/gadgets/wl/generate_certificate/template.pdf',
            'FONTS' => [
                [
                    'NAME' => 'DejaVuSans',
                    'STYLE' => '',
                    'FILE' => 'DejaVuSans.ttf'
                ],
                [
                    'NAME' => 'DejaVuSans',
                    'STYLE' => 'B',
                    'FILE' => 'DejaVuSans-Bold.ttf'
                ],
                [
                    'NAME' => 'DejaVuSerif',
                    'STYLE' => '',
                    'FILE' => 'DejaVuSerif.ttf'
                ],
                [
                    'NAME' => 'DejaVuSerifCondensed',
                    'STYLE' => '',
                    'FILE' => 'DejaVuSerifCondensed.ttf'
                ],
            ],
        ];
        $defaultMockData = [
            'TEXT' => [
                'SUM' => [
                    'VALUE' => '',
                    'FONT' => 'DejaVuSerifCondensed',
                    'STYLE' => '', // BIU - case insensitive, any (or none)
                    'SIZE' => 66,
                    'COLOR' => '#000000',
                    'X' => '50%',
                    'Y' => '52.5%',
                ],
                'RUB' => [
                    'VALUE' => 'руб.',
                    'FONT' => 'DejaVuSans',
                    'STYLE' => 'B', // BIU - case insensitive, any (or none)
                    'SIZE' => 14,
                    'COLOR' => '#000000',
                    'X' => '50%',
                    'Y' => '56.5%',
                ],
                'COUPON' => [
                    'VALUE' => '',
                    'FONT' => 'DejaVuSans',
                    'STYLE' => '', // BIU - case insensitive, any (or none)
                    'SIZE' => 16,
                    'COLOR' => '#000000',
                    'X' => '50%',
                    'Y' => '75.5%',
                ],
            ],
        ];
        $imageData = false;
        $mockData = $defaultMockData;
        if ($designXmlId) {
            $item = HL::table('Certificatedesigns')
                ->filter([
                    'UF_XML_ID' => $designXmlId
                ])->get();
            if ($item) {
                $imageData = \CFile::GetFileArray($item['UF_FILE']);
                try {
                    $newMockData = \Bitrix\Main\Web\Json::decode($item['UF_MOCK_DATA']);
                    foreach ($mockData as $groupKey => &$groupData) {
                        if (isset($newMockData[$groupKey]) && is_array($newMockData[$groupKey])) {
                            foreach ($groupData as $key => &$data) {
                                if (isset($newMockData[$groupKey][$key]) && is_array($newMockData[$groupKey][$key])) {
                                    $data = array_merge($data, $newMockData[$groupKey][$key]);
                                }
                            }
                            unset($data);
                        }
                    }
                    unset($groupData);
                } catch (exception $e) {
                }
            }
        } else {
            $imageData = \CFile::GetFileArray($defaultPictureId);
        }
        $result = array_merge($result, $mockData);
        $result['IMAGE'] = $imageData ? Application::getDocumentRoot() . $imageData['SRC'] : null;

        return $result;
    }

    /**
     * Генерация файла сертификата по шаблону, выбранному по дизайну
     *
     * @param string $coupon купон
     * @param string $sum сумма
     * @param string $design вариант дизайна
     * @return string[]
     * @throws ArgumentException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws ObjectPropertyException
     * @throws PdfParserException
     * @throws PdfReaderException
     * @throws PdfTypeException
     * @throws SystemException
     */
    public static function generateAction(string $coupon, string $sum, string $design): array
    {
        $pdfTemplate = GenerateCertificate::getCertificateTemplateData($design);
        $pdfTemplate['TEXT']['COUPON']['VALUE'] = $coupon;
        $pdfTemplate['TEXT']['SUM']['VALUE'] = $sum;

        return self::makePdf($pdfTemplate);
    }

    /**
     * Генерация файла сертификата по шаблону
     *
     * @param array $pdfTemplate
     * @return string[]
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    public static function makePdf(array $pdfTemplate): array
    {
        $pdf = new Tfpdf\Fpdi();
        $coupon = $pdfTemplate['TEXT']['COUPON']['VALUE'];
        $hashCoupon = md5($coupon);
        $dir = new Directory(Application::getDocumentRoot() . "/upload/coupons/");
        if (!$dir->isExists()) {
            $dir->create();
        }
        $path = "/upload/coupons/{$hashCoupon}.pdf";

        $pdf->setSourceFile($pdfTemplate['PDF_TEMPLATE']);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useImportedPage($templateId);

        foreach ($pdfTemplate['FONTS'] as $font) {
            $pdf->AddFont($font['NAME'], $font['STYLE'], $font['FILE'], true);
        }

        if ($pdfTemplate['IMAGE']) {
            $pdf->Image($pdfTemplate['IMAGE'], 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
        }

        foreach ($pdfTemplate['TEXT'] as $arTextFields) {
            self::placePdfText($pdf, $arTextFields);
        }

        $pdf->Output('F', Application::getDocumentRoot() . $path);
        return [
            'link' => $path,
            'file_name' => strtolower($coupon) . '.pdf'
        ];
    }

    /**
     * Размещение текста в документ PDF
     *
     * @param Fpdi $pdf
     * @param array $arFields
     * @return void
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfReaderException
     * @throws PdfTypeException
     */
    private static function placePdfText(Tfpdf\Fpdi &$pdf, array $arFields): void
    {
        $w = $pdf->GetPageWidth();
        $h = $pdf->GetPageHeight();

        if (preg_match("~^(?<value>\d*\.?\d*)(?<percent>%?)$~", (string) $arFields['X'], $matches)) {
            if ($matches['percent'] === '%') {
                $positionX = ((float) $matches['value']) * $w / 100;
            } else {
                $positionX =  $arFields['X'];
            }
        } else {
            $positionX = (float) $arFields['X'];
        }
        if (preg_match("~^(?<value>\d*\.?\d*)(?<percent>%?)$~", (string) $arFields['Y'], $matches)) {
            if ($matches['percent'] === '%') {
                $positionY = ((float) $matches['value']) * $h / 100;
            } else {
                $positionY =  $arFields['Y'];
            }
        } else {
            $positionY = (float) $arFields['Y'];
        }
        $pdf->SetFont($arFields['FONT'], $arFields['STYLE'], $arFields['SIZE']);
        if (preg_match("~^#(?<R>[0-9a-fA-F]{2})(?<G>[0-9a-fA-F]{2})(?<B>[0-9a-fA-F]{2})~", $arFields['COLOR'], $matches)) {
            $r = intval($matches['R'], 16);
            $g = intval($matches['G'], 16);
            $b = intval($matches['B'], 16);
            $pdf->SetTextColor($r, $g, $b);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }
        $text = $arFields['VALUE'];
        $textWidth = $pdf->GetStringWidth($text);
        $positionX = $positionX - round($textWidth / 2);
        $pdf->Text($positionX, $positionY, $text);
    }

    /**
     * Проверка, принадлежит ли набор полей скидки к установленным в системе сертификатам
     *
     * @param array $arFields
     * @return bool
     */
    private function discountIsCertificate(array $arFields)
    {
        if (serialize($arFields['CONDITIONS_LIST']) != 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:0:{}}') {
            return false;
        }
        $act = $arFields['ACTIONS_LIST'];
        if ($act['CLASS_ID'] != 'CondGroup' || !isset($act['CHILDREN']) || !is_array($act['CHILDREN']) || count($act['CHILDREN']) != 1)
            return false;
        $children = current($act['CHILDREN']);
        if ($children['CLASS_ID'] != 'ActSaleBsktGrp' || count($children['CHILDREN']) != 0)
            return false;
        $data = $children['DATA'];
        if ($data['Type'] != 'Discount' || $data['Unit'] != 'CurAll' || $data['Value'] < 1)
            return false;
        return true;
    }
}
