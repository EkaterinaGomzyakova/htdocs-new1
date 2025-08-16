<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\Component;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

global $APPLICATION, $USER;

class AuthConsole extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onBeforeAction(Event $event)
    {
        global $USER;
        $hasAccess = $USER->isAdmin();
        if (!$hasAccess) {
            return new EventResult(EventResult::ERROR, null, null, $this);
        }
        return null;
    }
}


class PhpconsoleComponent extends CBitrixComponent implements Controllerable, Errorable
{
    private static string $dataPath;
    protected ErrorCollection $errorCollection;
    protected $request;
    private static array $exceptionCodes = [
        'FILE_ID_NOT_FOUND' => 1,
        'FILE_CONTENTS_NOT_FOUND' => 2,
        'REQUIRED_REQUEST_ARGUMENTS_MISSING' => 3,
    ];

    public function __construct($component = null)
    {
        self::$dataPath = $_SERVER["DOCUMENT_ROOT"] . "/local/php_console";
        if (!is_dir(self::$dataPath)) {
            mkdir(self::$dataPath, BX_DIR_PERMISSIONS, true);
        }
        parent::__construct($component);
    }

    public function configureActions(): array
    {
        return [
            'loadIndex' => ['prefilters' => [new Csrf(), new AuthConsole()]],
            'loadFile' => ['prefilters' => [new Csrf(), new AuthConsole()]],
            'saveFile' => ['prefilters' => [new Csrf(), new AuthConsole()]],
            'loadMeta' => ['prefilters' => [new Csrf(), new AuthConsole()]],
            'saveMeta' => ['prefilters' => [new Csrf(), new AuthConsole()]],
            'unlink' => ['prefilters' => [new Csrf(), new AuthConsole()]],
        ];
    }

    public function onPrepareComponentParams($arParams): array
    {
        $this->errorCollection = new ErrorCollection();
        $this->request = Application::getInstance()->getContext()->getRequest();
        return $arParams;
    }

    /**
     * Load index action
     * @return Component
     * @throws Exception
     */
    public function loadIndexAction(): Component
    {
        return new Component(
            $this->getName(),
            '',
            [
                'PAGE' => 'index',
            ]
        );
    }

    /**
     * Load metadata action
     * @return string
     * @throws Exception
     * @global $_REQUEST =
     * [
     *   'FILE_ID' => file ID
     * ]
     */
    public function loadMetaAction(): string
    {
        if (!isset($this->request['FILE_ID'])) {
            throw new Exception(Loc::getMessage('REQUIRED_REQUEST_ARGUMENTS_MISSING'),
                self::$exceptionCodes['REQUIRED_REQUEST_ARGUMENTS_MISSING']);
        }

        $index = self::getIndex();
        if ($res = $index[$this->request['FILE_ID']]) {
            return Json::encode($res);
        }
        throw new Exception(Loc::getMessage('FILE_ID_NOT_FOUND'),
            self::$exceptionCodes['FILE_ID_NOT_FOUND']);
    }

    /**
     * Get index (load file contents)
     * @return array
     */
    private static function getIndex(): array
    {
        if (!self::checkIndex($indexFile)) {
            return [];
        }
        $content = file_get_contents($indexFile);
        return $content ? Json::decode($content) : [];
    }

    /**
     * Check index for existence
     * @param string &$indexFile - return file path
     * @return bool
     */
    private static function checkIndex(&$indexFile = ''): bool
    {
        $indexFile = self::$dataPath . '/index.json';
        return file_exists($indexFile);
    }

    /**
     * Save metadata action
     * @return bool
     * @throws Exception
     * @global $_REQUEST =
     * [
     *   'FILE_ID' => file ID,
     *   'TITLE' => file title,
     *   'DESCRIPTION' => file description
     * ]
     */
    public function saveMetaAction(): bool
    {
        if (!isset($this->request['FILE_ID']) || !(isset($this->request['TITLE']) || isset($this->request['DESCRIPTION']))) {
            throw new Exception(Loc::getMessage('REQUIRED_REQUEST_ARGUMENTS_MISSING'),
                self::$exceptionCodes['REQUIRED_REQUEST_ARGUMENTS_MISSING']);
        }

        $index = self::getIndex();
        if ($index[$this->request['FILE_ID']]) {
            if (isset($this->request['TITLE']) && ($title = trim($this->request['TITLE']))) {
                $index[$this->request['FILE_ID']]['TITLE'] = $title;
            }
            if (isset($this->request['DESCRIPTION']) && ($description = trim($this->request['DESCRIPTION']))) {
                $index[$this->request['FILE_ID']]['DESCRIPTION'] = $description;
            }
            self::setIndex($index);
            return true;
        }
        throw new Exception(Loc::getMessage('FILE_ID_NOT_FOUND'),
            self::$exceptionCodes['FILE_ID_NOT_FOUND']);
    }

    /**
     * Unlink file action
     * @return bool
     * @throws Exception
     * @global $_REQUEST =
     * [
     *   'FILE_ID' => file ID,
     * ]
     */
    public function unlinkAction(): bool
    {
        if (!isset($this->request['FILE_ID'])) {
            throw new Exception(Loc::getMessage('REQUIRED_REQUEST_ARGUMENTS_MISSING'),
                self::$exceptionCodes['REQUIRED_REQUEST_ARGUMENTS_MISSING']);
        }

        $index = self::getIndex();
        if ($index[$this->request['FILE_ID']]) {
            unset($index[$this->request['FILE_ID']]);
            self::setIndex($index);
            if (self::checkFileById($this->request['FILE_ID'], $fileName)) {
                unlink($fileName);
            }
            return true;
        }

        throw new Exception(Loc::getMessage('FILE_ID_NOT_FOUND'),
            self::$exceptionCodes['FILE_ID_NOT_FOUND']);
    }

    /**
     * Create file ID
     * @return string
     */
    private static function createFileId(): string
    {
        return uniqid('php_');
    }

    /**
     * Set index (save file)
     * @param array $data
     * @return void
     */
    private static function setIndex(array $data): void
    {
        self::checkIndex($indexFile);
        file_put_contents($indexFile, Json::encode($data));
    }

    /**
     * Load file action
     * @return string
     * @throws Exception
     * @global $_REQUEST =
     * [
     *   'FILE_ID' => file ID
     * ]
     */
    public function loadFileAction(): string
    {
        if (!isset($this->request['FILE_ID'])) {
            throw new Exception(Loc::getMessage('REQUIRED_REQUEST_ARGUMENTS_MISSING'), 
                self::$exceptionCodes['REQUIRED_REQUEST_ARGUMENTS_MISSING']);
        }

        $file = self::getFileDataById($this->request['FILE_ID']);
        if ($file !== false) {
            return $file['CONTENT'];
        }
        throw new Exception(Loc::getMessage('FILE_ID_NOT_FOUND'),
            self::$exceptionCodes['FILE_ID_NOT_FOUND']);
    }

    /**
     * Get file with metadata by ID
     * @param $fileId
     * @return array|false
     * @throws Exception
     */
    private static function getFileDataById($fileId): array|false
    {
        $index = self::getIndex();
        if ($res = $index[$fileId]) {
            $contents = self::getFileById($fileId);
            if ($contents === false) {
                throw new Exception(Loc::getMessage('FILE_CONTENTS_NOT_FOUND'),
                    self::$exceptionCodes['FILE_CONTENTS_NOT_FOUND']);
            }

            $file = [];
            if (!empty($res['TITLE'])) {
                $file[] = "//title: " . $res['TITLE'];
            }
            $file[] = "//fileId: " . $fileId;
            $file[] = $contents;

            $res['CONTENT'] = implode("\n", $file);
            return $res;
        }
        return false;
    }

    /**
     * Get file content by ID
     * @param string $fileId - file ID
     * @return string
     */
    private static function getFileById(string $fileId): string|false
    {
        if (!self::checkFileById($fileId, $fileName)) {
            return '';
        }
        return file_get_contents($fileName);
    }

    /**
     * Check file ID for existence
     * @param string $fileId - file ID
     * @param string|null &$fileName - file path
     * @return bool
     */
    private static function checkFileById(string $fileId, string|null &$fileName = ''): bool
    {
        $fileName = self::$dataPath . '/' . $fileId . '.phpcons';
        return file_exists($fileName);
    }

    /**
     * Save file
     * @return string
     * @throws Exception
     * @global $_REQUEST =
     * [
     *   'FILE_DATA' => file data
     * ]
     */
    public function saveFileAction(): string
    {
        if (!isset($this->request['FILE_DATA'])) {
            throw new Exception(Loc::getMessage('REQUIRED_REQUEST_ARGUMENTS_MISSING'), 
                self::$exceptionCodes['REQUIRED_REQUEST_ARGUMENTS_MISSING']);
        }

        $arContentVars = self::getContentVars($this->request['FILE_DATA']);
        $title = $arContentVars['TITLE'] ?: Loc::getMessage('NEW_FILE_TITLE');
        $fileId = $arContentVars['FILE_ID'];

        $index = self::getIndex();
        if ($fileId && !isset($index[$fileId])) {
            throw new Exception(Loc::getMessage('FILE_ID_NOT_FOUND'), 
                self::$exceptionCodes['FILE_ID_NOT_FOUND']);
        }
        if ($fileId && isset($index[$fileId])) {
            $index[$fileId]['TITLE'] = $title;
        } else {
            $fileId = self::createFileId();
            $index[$fileId] = [
                'TITLE' => $title,
                'DESCRIPTION' => '',
            ];
        }
        self::setIndex($index);

        self::checkFileById($fileId, $fileName);
        file_put_contents($fileName, $arContentVars['CONTENT']);

        $file = self::getFileDataById($fileId);
        return $file['CONTENT'];
    }

    private static function getContentVars($content): array
    {
        $title = '';
        $fileId = '';
        $arContent = explode("\n", $content);
        $strTitle = current($arContent);
        if (preg_match('~^\s*//title:\s*(.*)\s*$~', $strTitle, $matches)) {
            $title = trim($matches[1]);
            array_shift($arContent);
        }
        $strFileId = current($arContent);
        if (preg_match('~^\s*//fileId:\s*(.*)\s*$~', $strFileId, $matches)) {
            $fileId = trim($matches[1]);
            array_shift($arContent);
        }
        return [
            'TITLE' => $title,
            'FILE_ID' => $fileId,
            'CONTENT' => implode("\n", $arContent),
        ];
    }

    /**
     * Component run
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     */
    public function executeComponent(): void
    {
        global $USER;
        if (!$USER->isAdmin()) {
            return;
        }
        if ($this->arParams['PAGE'] ===  'index') {
            $this->arResult['INDEX'] = self::getIndex();
            uasort($this->arResult['INDEX'], fn($a, $b) => $a['TITLE'] <=> $b['TITLE']);
        }
        $this->includeComponentTemplate($this->arParams['PAGE'] ?? '');
    }

    /**
     * Returns error array
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * Returns error by code
     * @param mixed $code - error code (string|int)
     * @return Error
     */
    public function getErrorByCode($code): Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}
