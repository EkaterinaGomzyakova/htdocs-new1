<?

use Bitrix\Main\Web\Json;
use Bitrix\Main\UserPhoneAuthTable;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;
switch ($_REQUEST['type']) {
    case 'send':
        try {
            $phone = '+' . preg_replace("/[^0-9]/", '', $_REQUEST['PHONE']);
            $arUser = ValidateData($_REQUEST['NEW_USER_PASSWORD'], $_REQUEST['NEW_USER_PASSWORD_CONFIRM'], $_REQUEST['USER_ID']);
            $_SESSION['SMS_CODE_USER_ID'] = $arUser['ID'];
            $_SESSION['SMS_CODE'] = rand(1000, 9999);
            $sms4b = new Csms4b();
            $message = 'Ваш код подтверждения: ' . $_SESSION['SMS_CODE'];
            $to = $phone;
            $sender = 'clanbeauty';
            $sms4b->sendSingleSms($message, $to, $sender);
            $result['success'] = true;

        } catch (Exception $exception) {
            $result['success'] = false;
            $result['error'] = $exception->getMessage();
        }
        print_r(Json::encode($result));
        break;
    case 'confirm':
        try {
            $phone = '+' . preg_replace("/[^0-9]/", '', $_REQUEST['PHONE']);
            $arUser = ValidateData($_REQUEST['NEW_USER_PASSWORD'], $_REQUEST['NEW_USER_PASSWORD_CONFIRM'], $_REQUEST['USER_ID']);
            if($_SESSION['SMS_CODE'] != $_REQUEST['CODE']){
                throw new Exception('Неверный код подтверждения');
            }

            $user = new CUser;
            $fields = array(
                "PASSWORD" => $_REQUEST['NEW_USER_PASSWORD'],
                "CONFIRM_PASSWORD" => $_REQUEST['NEW_USER_PASSWORD_CONFIRM'],
                "LOGIN" => $phone
            );
            $user->Update($_REQUEST['USER_ID'], $fields);

            $row = UserPhoneAuthTable::getList(['filter' => ['USER_ID' => $_REQUEST['USER_ID']]])->fetchObject();
            $row->set('CONFIRMED', "Y");
            $row->set('DATE_SENT', date('d.m.Y H:i:s'));
            $row->save();
            $USER->Authorize($_REQUEST['USER_ID']);
            $result['success'] = true;

        } catch (Exception $exception) {
            $result['success'] = false;
            $result['error'] = $exception->getMessage();
        }
        print_r(Json::encode($result));
        break;
}

function ValidateData($password, $confirmPassword, $userID)
{
    if ($password !== $confirmPassword) {
        throw new Exception('Пароль и подтверждение пароля не совпадают');
    }
    if (empty($userID)) {
        throw new Exception('Не указан ID пользователя');
    }

    $filter = ['ID' => $_REQUEST['USER_ID']];
    $select = ['FIELDS' => ['ID', 'LOGIN']];
    $arUser = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $select)->fetch();
    if (empty($arUser)) {
        throw new Exception('Пользователь не найден');
    }

    $securityPolicy = \CUser::GetGroupPolicy([$_REQUEST['USER_ID']]);
    $password = $_REQUEST['NEW_USER_PASSWORD'];
    $errors = (new \CUser)->CheckPasswordAgainstPolicy($password, $securityPolicy);
    if (!empty($errors)) {
        throw new Exception(implode('<br>', $errors));
    }

    return $arUser;
}

