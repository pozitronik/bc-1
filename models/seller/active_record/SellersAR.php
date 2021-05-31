<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\models\core\prototypes\ActiveRecordTrait;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\store\Stores;
use app\modules\status\models\traits\StatusesTrait;
use pozitronik\helpers\DateHelper;
use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sellers".
 *
 * @property int $id
 * @property string $name Имя
 * @property string $surname Фамилия
 * @property string $patronymic Отчество
 * @property string $birthday Дата рождения
 * @property string $login Логин
 * @property string $email Email
 * @property string $create_date Дата регистрации
 * @property string $update_date Дата обновления
 * @property int $is_resident Резидент
 * @property string $passport_series Серия паспорта
 * @property string $passport_number Номер паспорта
 * @property string $passport_whom Кем выдан паспорт
 * @property string $passport_when Когда выдан паспорт
 * @property int $gender Пол
 * @property string $reg_address Адрес регистрации
 * @property string $entry_date Дата въезда в страну
 * @property string $inn ИНН
 * @property string $snils СНИЛС
 * @property string $keyword Ключевое слово для  «Горячей линии»
 * @property int $is_wireman_shpd Монтажник ШПД
 * @property int $dealer Дилер
 * @property int $sale_point Торговая точка
 * @property int $contract_signing_address Адрес подписания договора
 * @property int $deleted
 *
 * @property RelStoresToSellers[] $relatedStoresToSellers Связь к промежуточной таблице к продавцам
 * @property Stores[] $stores Магазины продавца
 */
class SellersAR extends ActiveRecord {
	use ActiveRecordTrait;
	use StatusesTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sellers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors():array {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'create_date',
				'updatedAtAttribute' => 'update_date',
				'value' => DateHelper::lcDate(),
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[
				[
					'name', 'surname', 'login', 'passport_series', 'passport_number', 'passport_whom',
					'passport_when', 'birthday', 'reg_address', 'keyword', 'email', 'is_wireman_shpd', 'is_resident'
				],
				'required'
			],
			[['create_date', 'update_date'], 'safe'],
			[['passport_when', 'birthday', 'entry_date'], 'date', 'format' => 'php:Y-m-d'],
			['patronymic', 'default', 'value' => null],
			['email', 'email'],
			[['gender', 'is_resident', 'is_wireman_shpd', 'sale_point', 'dealer', 'deleted'], 'integer'],
			[['name', 'surname', 'patronymic', 'email'], 'string', 'max' => 128],
			[['login', 'passport_series', 'passport_number', 'keyword'], 'string', 'max' => 64],
			[['passport_whom', 'reg_address', 'contract_signing_address'], 'string', 'max' => 255],
			['inn', 'string', 'max' => 12],
			['snils', 'string', 'max' => 14],
			[['inn', 'snils', 'email'], 'unique'],
			[
				['passport_series', 'passport_number'],
				'unique',
				'targetAttribute' => ['passport_series', 'passport_number']
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'patronymic' => 'Отчество',
			'birthday' => 'Дата рождения',
			'login' => 'Логин',
			'email' => 'Email',
			'create_date' => 'Дата регистрации',
			'update_date' => 'Дата обновления',
			'is_resident' => 'Резидент',
			'passport_series' => 'Серия паспорта',
			'passport_number' => 'Номер паспорта',
			'passport_whom' => 'Кем выдан паспорт',
			'passport_when' => 'Когда выдан паспорт',
			'gender' => 'Пол',
			'reg_address' => 'Адрес регистрации',
			'entry_date' => 'Дата въезда в страну',
			'inn' => 'ИНН',
			'snils' => 'СНИЛС',
			'keyword' => 'Ключевое слово для  «Горячей линии»',
			'is_wireman_shpd' => 'Монтажник ШПД',
			'dealer' => 'Дилер',
			'sale_point' => 'Торговая точка',
			'contract_signing_address' => 'Адрес подписания договора',
			'deleted' => 'Deleted'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedStoresToSellers():ActiveQuery {
		return $this->hasMany(RelStoresToSellers::class, ['seller_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStores():ActiveQuery {
		return $this->hasMany(Stores::class, ['id' => 'store_id'])->via('relatedStoresToSellers');
	}

	/**
	 * @param mixed $stores
	 * @throws Throwable
	 */
	public function setStores($stores):void {
		RelStoresToSellers::linkModels($stores, $this, true);/* Соединение идёт "наоборот", добавляем ключ backLink */
	}
}
