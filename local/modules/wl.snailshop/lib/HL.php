<?php


namespace WL;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

/**
 * Class DAO
 * Запросы к HL
 * @package WL
 */
class HL
{
    private $entity;
    private $arFilter = [];
    private $arSelect = [];
    private $arSort = [];
    private $result;
    private $loaded = false;

    public function __construct($hltable)
    {
        $entity = HighloadBlockTable::compileEntity($hltable);
        $this->entity = $entity->getDataClass();
    }

    /**
     * Установить HL
     * @param $hl
     * @return HL
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function table($hl): HL
    {
        Loader::includeModule("highloadblock");
        if(intval($hl) > 0){
            $hlblock = HighloadBlockTable::getById($hl)->fetch();
            return new HL($hlblock);
        }else{
            $hlblock = HighloadBlockTable::getList(['filter' => ['NAME' => $hl]])->fetch();
            return new HL($hlblock);
        }
    }

    /**
     * Ограничить количество элементов
     * @param $limit
     * @return $this
     */
    public function take($limit): HL
    {
        $this->arNavStartParams['nTopCount'] = $limit;
        return $this;
    }

    /**
     * Фильтр
     * @param array $params
     * @return $this
     */
    public function filter($params): HL
    {
        $this->arFilter = $params;
        return $this;
    }

    /**
     * Сортировка
     * @param array $params
     * @return $this
     */
    public function sort($params): HL
    {
        $this->arSort = $params;
        return $this;
    }

    /**
     * Поля
     * @param array $params
     * @return $this
     */
    public function select($params): HL
    {
        $this->arSelect = array_merge(['ID'], $params);
        return $this;
    }

    public function add(array $fields){
        return $this->entity::add($fields);
    }

    /**
     * Получить список элементов
     * @return array
     * @throws \Exception
     */
    public function all(): array
    {
        $result = [];
        if($this->loaded){
            return $this->result;
        }else{
            $filter = [];
            if(!empty($this->arSelect)){
                $filter['select'] = $this->arSelect;
            }

            if(!empty($this->arFilter)){
                $filter['filter'] = $this->arFilter;
            }

            if(!empty($this->arSort)){
                $filter['order'] = $this->arSort;
            }
            $rsData = $this->entity::getList($filter);
            while($arData = $rsData->Fetch()){
                $result[$arData['ID']] = $arData;
            }
            $this->result = $result;
            $this->loaded = true;
            return $result;
        }
    }

    /**
     * Получить элемент
     * @return array
     * @throws \Exception
     */
    public function get(): ?array
    {
        $this->take(1);
        $result = $this->all();
        if($result){
            return current($result);
        }else{
            return null;
        }
    }

    public function json(): array
    {
        if(!$this->loaded){
            $this->all();
        }
        $result = [];
        foreach ($this->result as $item) {
            $result[] = [
                'name' => $item['UF_NAME'],
                'value' => $item['UF_XML_ID'],
            ];
        }
        return $result;
    }

    public function getDefaultValue(){
        if(!$this->loaded){
            $this->all();
        }
        $result = '';
        foreach ($this->result as $item) {
            if ($item['UF_DEF'] == 1) {
                $result = $item['UF_XML_ID'];
            }
        }
        return $result;
    }
}