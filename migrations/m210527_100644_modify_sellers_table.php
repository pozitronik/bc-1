<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210527_100644_modify_sellers_table
 */
class m210527_100644_modify_sellers_table extends Migration {
	private const INDEXES = [
		'name',
		'surname',
		'patronymic',
		'birthday',
		'gender',
		'entry_date',
		'inn',
		'snils',
		'keyword',
		'email',
		'create_date',
		'update_date',
		'tt_code'
	];

	private const UNIQUE_INDEXES = [
		'inn',
		'snils',
		'email'
	];

	private const COMPLEX_INDEX = [
		['passport_series', 'passport_number']
	];

	private const TABLE = 'sellers';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropTable(self::TABLE);

		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'name' => $this->string(128)->notNull()->comment('Имя'),
			'surname' => $this->string(128)->notNull()->comment('Фамилия'),
			'patronymic' => $this->string(128)->comment('Отчество'),
			'gender' => $this->integer()->comment('Пол'),
			'birthday' => $this->date()->notNull()->comment('Дата рождения'),
			'login' => $this->string(64)->notNull()->comment('Логин'),
			'email' => $this->string(128)->notNull()->comment('Email'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'update_date' => $this->dateTime()->comment('Дата обновления'),
			'is_resident' => $this->boolean()->notNull()->comment('Резидент'),
			'passport_series' => $this->string(64)->notNull()->comment('Серия паспорта'),
			'passport_number' => $this->string(64)->notNull()->comment('Номер паспорта'),
			'passport_whom' => $this->string()->notNull()->comment('Кем выдан паспорт'),
			'passport_when' => $this->date()->notNull()->comment('Когда выдан паспорт'),
			'reg_address' => $this->string()->notNull()->comment('Адрес регистрации'),
			'entry_date' => $this->date()->comment('Дата въезда в страну'),
			'inn' => $this->string(12)->notNull()->comment('ИНН'),
			'snils' => $this->string(14)->comment('СНИЛС'),
			'keyword' => $this->string(64)->notNull()->comment('Ключевое слово для  «Горячей линии»'),
			'is_wireman_shpd' => $this->boolean()->notNull()->comment('Монтажник ШПД'),
			'tt_code' => $this->integer()->notNull()->comment('Код торговой точки в СКАД'),
			'contract_signing_address' => $this->string()->notNull()->comment('Адрес подписания договора'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		foreach (self::INDEXES as $index) {
			$this->createIndex($index, self::TABLE, $index, in_array($index, self::UNIQUE_INDEXES));
		}

		foreach (self::COMPLEX_INDEX as $index) {
			$this->createIndex(implode('_', $index), self::TABLE, $index, true);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TABLE);

		$this->createTable(self::TABLE, [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->comment('Имя продавца'),
			'create_date' => $this->dateTime()->notNull()->comment('Дата регистрации'),
			'deleted' => $this->boolean()->notNull()->defaultValue(0)
		]);

		$this->createIndex('deleted', self::TABLE, 'deleted');
	}

}