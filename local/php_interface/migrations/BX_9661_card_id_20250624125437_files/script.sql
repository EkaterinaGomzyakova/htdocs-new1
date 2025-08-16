insert ignore into `b_sale_order_props` (`ID`, `PERSON_TYPE_ID`, `NAME`, `TYPE`, `REQUIRED`, `DEFAULT_VALUE`, `SORT`,
                                         `USER_PROPS`, `IS_LOCATION`, `PROPS_GROUP_ID`, `DESCRIPTION`, `IS_EMAIL`,
                                         `IS_PROFILE_NAME`, `IS_PAYER`, `IS_LOCATION4TAX`, `IS_FILTERED`, `CODE`,
                                         `IS_ZIP`, `IS_PHONE`, `ACTIVE`, `UTIL`, `INPUT_FIELD_LOCATION`, `MULTIPLE`,
                                         `IS_ADDRESS`, `SETTINGS`, `ENTITY_REGISTRY_TYPE`, `XML_ID`, `ENTITY_TYPE`,
                                         `IS_ADDRESS_FROM`, `IS_ADDRESS_TO`)
values (26, 1, 'Дисконтная карта', 'STRING', 'N', '', 0, 'N', 'N', 3, '', 'N', 'N', 'N', 'N', 'N', 'CARD_NUMBER', 'N',
        'N', 'Y', 'Y', 0, 'N', 'N',
        'a:5:{s:9:"MINLENGTH";s:0:"";s:9:"MAXLENGTH";s:0:"";s:7:"PATTERN";s:0:"";s:9:"MULTILINE";s:1:"N";s:4:"SIZE";s:0:"";}',
        'ORDER', 'CARD_NUMBER', 'ORDER', 'N', 'N');