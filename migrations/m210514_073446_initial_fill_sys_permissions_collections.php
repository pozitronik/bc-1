<?php
declare(strict_types = 1);
use yii\db\Migration;
use app\models\sys\permissions\active_record\PermissionsCollections;

/**
 * Class m210514_073446_initial_fill_sys_permissions_collections
 */
class m210514_073446_initial_fill_sys_permissions_collections extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->execute("INSERT INTO sys_permissions_collections (name, comment) VALUES
		('Продавец', '1. Участник мотивационной программы, может обладать ролями в DOL продавец, продавец макси, менеджер торговой точки (определяется в дол), данные роли должны поддерживать онлайн регистрацию.2. Участник мотивационной программы, может обладать ролью продавец мини, для такой роли необходимо предусмотреть возможность регистрации СНМП через территориала в ручном режиме, подтверждение наличие продавца должно проводиться через код в смс сообщении.'),
		('Партнер', ''),
		('Территориальный специалист', ''),
		('Специалист поддержки', ''),
		('Сотрудник ГПОД', ''),
		('Региональный менеджер', ''),
		('Менеджер ШК', ''),
		('Администратор СНМП', '');
    ");
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		PermissionsCollections::deleteAll();
	}

}
