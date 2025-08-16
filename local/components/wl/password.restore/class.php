<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ProfilePasswordRestoreComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;
        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'send':
                    $APPLICATION->RestartBuffer();
                    $this->arResult['VALUES'] = $_REQUEST;
                    $user = $this->findUserByPhone($_REQUEST['PHONE']);
                    if (empty($user)) {
                        $this->arResult['ERROR'] = 'Пользователь с таким телефоном не зарегистрирован';
                        $this->includeComponentTemplate('form');
                    } else {
                        try {
                            $this->SendConfirmCode($user['ID'], $_REQUEST['PHONE']);
                        } catch (Exception $exception) {
                            $this->arResult['ERROR'] = $exception->getMessage();
                        }
                        $this->includeComponentTemplate('confirm_form');
                    }
                    die();
                    break;
                case 'confirm':
                    try{
                        $this->ChangePassword($_REQUEST['CODE'], $_REQUEST['NEW_PASSWORD'], $_REQUEST['PASSWORD_CONFIRM']);
                        $this->includeComponentTemplate('result');
                    }catch (Exception $exception){
                        $this->arResult['ERROR'] = $exception->getMessage();
                        $this->includeComponentTemplate('confirm_form');
                    }
                    break;
            }
        } else {
            $this->includeComponentTemplate();
        }
    }

    function ChangePassword($code, $password, $confirmPassword)
    {
        global $USER;
        if ($_SESSION['SMS_CODE'] != $code) {
            throw new Exception('Неверный код из смс');
        }
        if ($password != $confirmPassword) {
            throw new Exception('Пароль и подтверждение пароля не совпадают');
        }

        $securityPolicy = \CUser::GetGroupPolicy([$_SESSION['SMS_CODE_USER_ID']]);
        $errors = (new \CUser)->CheckPasswordAgainstPolicy($password, $securityPolicy);
        if(!empty($errors)){
            throw new Exception(implode('<br>', $errors));
        }

        $user = new CUser;
        $fields = Array(
            "PASSWORD" => $password,
            "CONFIRM_PASSWORD" => $confirmPassword,
        );
        $user->Update($_SESSION['SMS_CODE_USER_ID'], $fields);
        if ($user->LAST_ERROR) {
            throw new Exception($user->LAST_ERROR);
        }
        $USER->Authorize($_SESSION['SMS_CODE_USER_ID']);
    }

    //Отправка кода подверждения
    function SendConfirmCode($userID, $phone)
    {
        $_SESSION['SMS_CODE_USER_ID'] = $userID;
        $_SESSION['SMS_CODE'] = rand(1000, 9999);
        $sms4b = new Csms4b();
        $message = 'Ваш код подтверждения: ' . $_SESSION['SMS_CODE'];
        $to = $phone;
        $sender = 'clanbeauty';
        $sms4b->sendSingleSms($message, $to, $sender);
    }

    function findUserByPhone($phone)
    {
        global $DB;
        $phone = '+' . preg_replace("/[^0-9]/", '', $phone);
        $select = ['FIELDS' => ['ID', 'LOGIN', 'CONFIRM_CODE'], 'SELECT' => ['UF_NEED_CHANGE_PAS']];
        $phoneAuth = $DB->Query("SELECT * from b_user_phone_auth where PHONE_NUMBER='$phone'")->fetch();
        if (empty($phoneAuth)) {
            $filter = ['LOGIN' => $phone];
        } else {
            $filter = ['ID' => $phoneAuth['USER_ID']];
        }
        $arUser = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $select)->fetch();
        if (empty($arUser)) {
            $filter = ['PERSONAL_PHONE' => $phone];
            $arUser = CUser::GetList(($by = "id"), ($order = "desc"), $filter)->fetch();
        }
        return $arUser;
    }
}
