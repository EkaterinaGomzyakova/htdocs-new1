<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if (isset($request['action'])) {
    $result = ['success' => true];
    switch ($request->get('action')) {
        case 'check_email':
            if ($USER->IsAuthorized()) {
                if (empty($USER->GetEmail())) {
                    $result['success'] = false;
                }
            }
            break;
        case 'update_email':
            if ($USER->IsAuthorized()) {
                $arUser = CUser::GetByID($USER->GetID())->Fetch();
                $user = new CUser;
                $fields = array(
                    "LOGIN" => $arUser['LOGIN'],
                    "EMAIL" => $request->get('email'),
                );
                $user->Update($USER->GetID(), $fields);
            }
            break;
    }
    echo \Bitrix\Main\Web\Json::encode($result);
}



