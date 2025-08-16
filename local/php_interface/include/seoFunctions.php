<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/lib/template/functions/fabric.php');

use Bitrix\Main;

/**
 * Вывести свойство если заполнено
 */
$eventManager = Main\EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnTemplateGetFunctionClass", "ifFilledSeoClass");

function ifFilledSeoClass(Bitrix\Main\Event $event) {
   $arParam = $event->getParameters();
   $functionClass = $arParam[0];
   if (is_string($functionClass) && class_exists($functionClass) && $functionClass == 'iffilled'){
      $result = new Bitrix\Main\EventResult(1, $functionClass);
      return $result;
   }
}

class iffilled extends Bitrix\Iblock\Template\Functions\FunctionBase
{
    public function calculate(array $parameters)
    {
        if (count($parameters) < 2) {
            return '';
        }
        $format = array_pop($parameters);
        while (count($parameters) && empty($value = array_shift($parameters)));
        return empty($value) ? '' : sprintf($format, $value);
    }
}

/**
 * Обрезать теги и переносы строк
 */

$eventManager = Main\EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnTemplateGetFunctionClass","onStripTagsEventHandler"); 

function onStripTagsEventHandler(Bitrix\Main\Event $event) {
     $arParam = $event->getParameters();
     $functionClass = $arParam[0];
     if (is_string($functionClass) && class_exists($functionClass) && $functionClass=='wlstriptags'){
        $result = new Bitrix\Main\EventResult(1, $functionClass);
        return $result;
     }
}

class wlstriptags extends Bitrix\Iblock\Template\Functions\FunctionBase
{
    public function onPrepareParameters(\Bitrix\Iblock\Template\Entity\Base $entity, $parameters)
     {
        $arguments = array();

        foreach ($parameters as $parameter)
        {
             $arguments[] = $parameter->process($entity);
        }
        return $arguments;
     }
    

    public function calculate(array $parameters)
    {
        if(isset($parameters[0]) && $parameters[0])
        {
            $without_tags = str_replace(array("</li>", "</ul>"), ". ", $parameters[0]);
            $without_tags = str_replace(". . ", ". ", $without_tags);
            $without_tags = strip_tags($without_tags);;
            $without_tags = str_replace(["\n", "\r", "\t"], '', $without_tags);
            $without_tags = str_replace("  ", ' ', $without_tags);
            $without_tags = str_replace('"', "'", $without_tags);
            $without_tags = str_replace('&nbsp;', " ", $without_tags);
            $without_tags = trim($without_tags);
            return $without_tags;
        }
        return "";
    }
}