<?php

namespace PHPSTORM_META {

    registerArgumentsSet(
        'clan_module_list',
        'wl.onec_loyalty',
    );
    expectedArguments(\CModule::IncludeModule(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\Loader::includeModule(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\Loader::requireModule(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\ModuleManager::isModuleInstalled(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\Config\Option::get(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\Config\Option::set(), 0, argumentsSet('clan_module_list'));
    expectedArguments(\Bitrix\Main\Config\Configuration::getInstance(), 0, argumentsSet('clan_module_list'));
    expectedArguments(
        \Bitrix\Main\DI\ServiceLocator::registerByModuleSettings(),
        0,
        argumentsSet('clan_module_list')
    );
}