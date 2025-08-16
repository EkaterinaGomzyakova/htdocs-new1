<?

use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use Bitrix\Main\UserPhoneAuthTable;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


try {
    $result['success'] = true;
    $request = Context::getCurrent()->getRequest();
    $phone = $request->get('phone');
    if(empty($phone)){
        throw new Exception('Не указан номер телефона');
    }
    $phone =  '+' . preg_replace("/[^0-9]/", '', $phone);
    $row = UserPhoneAuthTable::getList(['filter' => ['PHONE_NUMBER' => $phone]])->fetch();
    $result['user_login'] = $phone;
    if(empty($row)){
        $result['is_new_user'] = true;
    }else{
        $result['is_new_user'] = false;
        $result['user_id'] = $row['USER_ID'];
        if($row['CONFIRMED'] == 'N'){
            $result['need_change_password'] = true;
        }else{
            $result['need_change_password'] = false;
        }
    }
} catch (Exception $exception) {
    $result['success'] = false;
    $result['error'] = $exception->getMessage();
}

print_r(Json::encode($result));