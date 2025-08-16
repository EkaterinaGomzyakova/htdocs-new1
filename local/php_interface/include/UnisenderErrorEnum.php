<?php

namespace WL;

enum UnisenderErrorEnum: int
{
    case MODULE_NOT_FOUND = 1;
    case API_NOT_FOUND = 2;
    case INVALID_API_KEY = 3;
    case INVALID_RESPONSE = 4;
    case WRONG_LIST_ID = 5;
    case UNSPECIFIED = 6;
    case UNAUTHORIZED = 7;
    case ACCESS_DENIED = 8;
    case NOT_ENOUGH_MONEY = 9;
    case RETRY = 10;
    case API_CALL_LIMIT = 11;
    case USER_MANDATORY_FIELDS_NOT_SET = 12;
}