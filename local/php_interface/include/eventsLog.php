<?
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'catalog',
    'OnDocumentUpdate',
    ['CCustomEventsLog', 'logCatalogDocumentUpdate']
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'catalog',
    'OnDocumentDelete',
    ['CCustomEventsLog', 'logCatalogDocumentDelete']
);

class CCustomEventsLog
{
    public static function logCatalogDocumentUpdate($documentId, $arFields) {
        CEventLog::Add(array(
            "SEVERITY" => "INFO",
            "AUDIT_TYPE_ID" => "CAT_DOCUMENT_EDIT",
            "MODULE_ID" => "wl.snailshop",
            "ITEM_ID" => $documentId,
            "DESCRIPTION" => serialize($arFields),
        ));
    }

    public static function logCatalogDocumentDelete($documentId) {
        CEventLog::Add(array(
            "SEVERITY" => "INFO",
            "AUDIT_TYPE_ID" => "CAT_DOCUMENT_DELETE",
            "MODULE_ID" => "wl.snailshop",
            "ITEM_ID" => $documentId,
            "DESCRIPTION" => 'Deleted',
        ));
    }
}

