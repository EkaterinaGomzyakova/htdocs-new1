<?php

/* ORMENTITYANNOTATION:WL\OnecLoyalty\Tables\BonusSyncTable */
namespace WL\OnecLoyalty\Tables {
	/**
	 * EO_BonusSync
	 * @see \WL\OnecLoyalty\Tables\BonusSyncTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setUserId(\int|\Bitrix\Main\DB\SqlExpression $userId)
	 * @method bool hasUserId()
	 * @method bool isUserIdFilled()
	 * @method bool isUserIdChanged()
	 * @method \int remindActualUserId()
	 * @method \int requireUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetUserId()
	 * @method \int fillUserId()
	 * @method \string getMethod()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setMethod(\string|\Bitrix\Main\DB\SqlExpression $method)
	 * @method bool hasMethod()
	 * @method bool isMethodFilled()
	 * @method bool isMethodChanged()
	 * @method \string remindActualMethod()
	 * @method \string requireMethod()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetMethod()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetMethod()
	 * @method \string fillMethod()
	 * @method array getParams()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setParams(array|\Bitrix\Main\DB\SqlExpression $params)
	 * @method bool hasParams()
	 * @method bool isParamsFilled()
	 * @method bool isParamsChanged()
	 * @method array remindActualParams()
	 * @method array requireParams()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetParams()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetParams()
	 * @method array fillParams()
	 * @method \Bitrix\Main\Type\DateTime getTimestamp()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setTimestamp(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $timestamp)
	 * @method bool hasTimestamp()
	 * @method bool isTimestampFilled()
	 * @method bool isTimestampChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualTimestamp()
	 * @method \Bitrix\Main\Type\DateTime requireTimestamp()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetTimestamp()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetTimestamp()
	 * @method \Bitrix\Main\Type\DateTime fillTimestamp()
	 * @method \boolean getIsCompleted()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setIsCompleted(\boolean|\Bitrix\Main\DB\SqlExpression $isCompleted)
	 * @method bool hasIsCompleted()
	 * @method bool isIsCompletedFilled()
	 * @method bool isIsCompletedChanged()
	 * @method \boolean remindActualIsCompleted()
	 * @method \boolean requireIsCompleted()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetIsCompleted()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetIsCompleted()
	 * @method \boolean fillIsCompleted()
	 * @method \Bitrix\Main\Type\DateTime getDateExec()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setDateExec(\Bitrix\Main\Type\DateTime|\Bitrix\Main\DB\SqlExpression $dateExec)
	 * @method bool hasDateExec()
	 * @method bool isDateExecFilled()
	 * @method bool isDateExecChanged()
	 * @method \Bitrix\Main\Type\DateTime remindActualDateExec()
	 * @method \Bitrix\Main\Type\DateTime requireDateExec()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetDateExec()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetDateExec()
	 * @method \Bitrix\Main\Type\DateTime fillDateExec()
	 * @method array getResult()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setResult(array|\Bitrix\Main\DB\SqlExpression $result)
	 * @method bool hasResult()
	 * @method bool isResultFilled()
	 * @method bool isResultChanged()
	 * @method array remindActualResult()
	 * @method array requireResult()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetResult()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetResult()
	 * @method array fillResult()
	 * @method \int getAttempt()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setAttempt(\int|\Bitrix\Main\DB\SqlExpression $attempt)
	 * @method bool hasAttempt()
	 * @method bool isAttemptFilled()
	 * @method bool isAttemptChanged()
	 * @method \int remindActualAttempt()
	 * @method \int requireAttempt()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetAttempt()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetAttempt()
	 * @method \int fillAttempt()
	 * @method \string getError()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setError(\string|\Bitrix\Main\DB\SqlExpression $error)
	 * @method bool hasError()
	 * @method bool isErrorFilled()
	 * @method bool isErrorChanged()
	 * @method \string remindActualError()
	 * @method \string requireError()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetError()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetError()
	 * @method \string fillError()
	 * @method \Bitrix\Main\EO_User getUser()
	 * @method \Bitrix\Main\EO_User remindActualUser()
	 * @method \Bitrix\Main\EO_User requireUser()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setUser(\Bitrix\Main\EO_User $object)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetUser()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetUser()
	 * @method bool hasUser()
	 * @method bool isUserFilled()
	 * @method bool isUserChanged()
	 * @method \Bitrix\Main\EO_User fillUser()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount getAccount()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount remindActualAccount()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount requireAccount()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync setAccount(\WL\OnecLoyalty\Tables\EO_UserAccount $object)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync resetAccount()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unsetAccount()
	 * @method bool hasAccount()
	 * @method bool isAccountFilled()
	 * @method bool isAccountChanged()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount fillAccount()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync set($fieldName, $value)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync reset($fieldName)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method mixed fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync wakeUp($data)
	 */
	class EO_BonusSync {
		/* @var \WL\OnecLoyalty\Tables\BonusSyncTable */
		static public $dataClass = '\WL\OnecLoyalty\Tables\BonusSyncTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace WL\OnecLoyalty\Tables {
	/**
	 * EO_BonusSync_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getUserIdList()
	 * @method \int[] fillUserId()
	 * @method \string[] getMethodList()
	 * @method \string[] fillMethod()
	 * @method array[] getParamsList()
	 * @method array[] fillParams()
	 * @method \Bitrix\Main\Type\DateTime[] getTimestampList()
	 * @method \Bitrix\Main\Type\DateTime[] fillTimestamp()
	 * @method \boolean[] getIsCompletedList()
	 * @method \boolean[] fillIsCompleted()
	 * @method \Bitrix\Main\Type\DateTime[] getDateExecList()
	 * @method \Bitrix\Main\Type\DateTime[] fillDateExec()
	 * @method array[] getResultList()
	 * @method array[] fillResult()
	 * @method \int[] getAttemptList()
	 * @method \int[] fillAttempt()
	 * @method \string[] getErrorList()
	 * @method \string[] fillError()
	 * @method \Bitrix\Main\EO_User[] getUserList()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection getUserCollection()
	 * @method \Bitrix\Main\EO_User_Collection fillUser()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount[] getAccountList()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection getAccountCollection()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection fillAccount()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\WL\OnecLoyalty\Tables\EO_BonusSync $object)
	 * @method bool has(\WL\OnecLoyalty\Tables\EO_BonusSync $object)
	 * @method bool hasByPrimary($primary)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync getByPrimary($primary)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync[] getAll()
	 * @method bool remove(\WL\OnecLoyalty\Tables\EO_BonusSync $object)
	 * @method void removeByPrimary($primary)
	 * @method array|\Bitrix\Main\ORM\Objectify\Collection|null fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection merge(?\WL\OnecLoyalty\Tables\EO_BonusSync_Collection $collection)
	 * @method bool isEmpty()
	 * @method array collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
	 */
	class EO_BonusSync_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \WL\OnecLoyalty\Tables\BonusSyncTable */
		static public $dataClass = '\WL\OnecLoyalty\Tables\BonusSyncTable';
	}
}
namespace WL\OnecLoyalty\Tables {
	/**
	 * @method static EO_BonusSync_Query query()
	 * @method static EO_BonusSync_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_BonusSync_Result getById($id)
	 * @method static EO_BonusSync_Result getList(array $parameters = [])
	 * @method static EO_BonusSync_Entity getEntity()
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync createObject($setDefaultValues = true)
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync_Collection createCollection()
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync wakeUpObject($row)
	 * @method static \WL\OnecLoyalty\Tables\EO_BonusSync_Collection wakeUpCollection($rows)
	 */
	class BonusSyncTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_BonusSync_Result exec()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync fetchObject()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection fetchCollection()
	 */
	class EO_BonusSync_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync fetchObject()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection fetchCollection()
	 */
	class EO_BonusSync_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync createObject($setDefaultValues = true)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection createCollection()
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync wakeUpObject($row)
	 * @method \WL\OnecLoyalty\Tables\EO_BonusSync_Collection wakeUpCollection($rows)
	 */
	class EO_BonusSync_Entity extends \Bitrix\Main\ORM\Entity {}
}
/* ORMENTITYANNOTATION:WL\OnecLoyalty\Tables\UserAccountTable */
namespace WL\OnecLoyalty\Tables {
	/**
	 * EO_UserAccount
	 * @see \WL\OnecLoyalty\Tables\UserAccountTable
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int getId()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount setId(\int|\Bitrix\Main\DB\SqlExpression $id)
	 * @method bool hasId()
	 * @method bool isIdFilled()
	 * @method bool isIdChanged()
	 * @method \int getUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount setUserId(\int|\Bitrix\Main\DB\SqlExpression $userId)
	 * @method bool hasUserId()
	 * @method bool isUserIdFilled()
	 * @method bool isUserIdChanged()
	 * @method \int remindActualUserId()
	 * @method \int requireUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount resetUserId()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount unsetUserId()
	 * @method \int fillUserId()
	 * @method \float getCurrentBudget()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount setCurrentBudget(\float|\Bitrix\Main\DB\SqlExpression $currentBudget)
	 * @method bool hasCurrentBudget()
	 * @method bool isCurrentBudgetFilled()
	 * @method bool isCurrentBudgetChanged()
	 * @method \float remindActualCurrentBudget()
	 * @method \float requireCurrentBudget()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount resetCurrentBudget()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount unsetCurrentBudget()
	 * @method \float fillCurrentBudget()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @property-read array $primary
	 * @property-read int $state @see \Bitrix\Main\ORM\Objectify\State
	 * @property-read \Bitrix\Main\Type\Dictionary $customData
	 * @property \Bitrix\Main\Authentication\Context $authContext
	 * @method mixed get($fieldName)
	 * @method mixed remindActual($fieldName)
	 * @method mixed require($fieldName)
	 * @method bool has($fieldName)
	 * @method bool isFilled($fieldName)
	 * @method bool isChanged($fieldName)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount set($fieldName, $value)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount reset($fieldName)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount unset($fieldName)
	 * @method void addTo($fieldName, $value)
	 * @method void removeFrom($fieldName, $value)
	 * @method void removeAll($fieldName)
	 * @method \Bitrix\Main\ORM\Data\Result delete()
	 * @method mixed fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method mixed[] collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
	 * @method \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\UpdateResult|\Bitrix\Main\ORM\Data\Result save()
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount wakeUp($data)
	 */
	class EO_UserAccount {
		/* @var \WL\OnecLoyalty\Tables\UserAccountTable */
		static public $dataClass = '\WL\OnecLoyalty\Tables\UserAccountTable';
		/**
		 * @param bool|array $setDefaultValues
		 */
		public function __construct($setDefaultValues = true) {}
	}
}
namespace WL\OnecLoyalty\Tables {
	/**
	 * EO_UserAccount_Collection
	 *
	 * Custom methods:
	 * ---------------
	 *
	 * @method \int[] getIdList()
	 * @method \int[] getUserIdList()
	 * @method \int[] fillUserId()
	 * @method \float[] getCurrentBudgetList()
	 * @method \float[] fillCurrentBudget()
	 *
	 * Common methods:
	 * ---------------
	 *
	 * @property-read \Bitrix\Main\ORM\Entity $entity
	 * @method void add(\WL\OnecLoyalty\Tables\EO_UserAccount $object)
	 * @method bool has(\WL\OnecLoyalty\Tables\EO_UserAccount $object)
	 * @method bool hasByPrimary($primary)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount getByPrimary($primary)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount[] getAll()
	 * @method bool remove(\WL\OnecLoyalty\Tables\EO_UserAccount $object)
	 * @method void removeByPrimary($primary)
	 * @method array|\Bitrix\Main\ORM\Objectify\Collection|null fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL) flag or array of field names
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount_Collection wakeUp($data)
	 * @method \Bitrix\Main\ORM\Data\Result save($ignoreEvents = false)
	 * @method void offsetSet() ArrayAccess
	 * @method void offsetExists() ArrayAccess
	 * @method void offsetUnset() ArrayAccess
	 * @method void offsetGet() ArrayAccess
	 * @method void rewind() Iterator
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount current() Iterator
	 * @method mixed key() Iterator
	 * @method void next() Iterator
	 * @method bool valid() Iterator
	 * @method int count() Countable
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection merge(?\WL\OnecLoyalty\Tables\EO_UserAccount_Collection $collection)
	 * @method bool isEmpty()
	 * @method array collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
	 */
	class EO_UserAccount_Collection implements \ArrayAccess, \Iterator, \Countable {
		/* @var \WL\OnecLoyalty\Tables\UserAccountTable */
		static public $dataClass = '\WL\OnecLoyalty\Tables\UserAccountTable';
	}
}
namespace WL\OnecLoyalty\Tables {
	/**
	 * @method static EO_UserAccount_Query query()
	 * @method static EO_UserAccount_Result getByPrimary($primary, array $parameters = [])
	 * @method static EO_UserAccount_Result getById($id)
	 * @method static EO_UserAccount_Result getList(array $parameters = [])
	 * @method static EO_UserAccount_Entity getEntity()
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount createObject($setDefaultValues = true)
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount_Collection createCollection()
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount wakeUpObject($row)
	 * @method static \WL\OnecLoyalty\Tables\EO_UserAccount_Collection wakeUpCollection($rows)
	 */
	class UserAccountTable extends \Bitrix\Main\ORM\Data\DataManager {}
	/**
	 * Common methods:
	 * ---------------
	 *
	 * @method EO_UserAccount_Result exec()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount fetchObject()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection fetchCollection()
	 */
	class EO_UserAccount_Query extends \Bitrix\Main\ORM\Query\Query {}
	/**
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount fetchObject()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection fetchCollection()
	 */
	class EO_UserAccount_Result extends \Bitrix\Main\ORM\Query\Result {}
	/**
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount createObject($setDefaultValues = true)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection createCollection()
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount wakeUpObject($row)
	 * @method \WL\OnecLoyalty\Tables\EO_UserAccount_Collection wakeUpCollection($rows)
	 */
	class EO_UserAccount_Entity extends \Bitrix\Main\ORM\Entity {}
}