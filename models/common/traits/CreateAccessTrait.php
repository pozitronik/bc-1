<?php
declare(strict_types = 1);

namespace app\models\common\traits;

use app\models\site\RestorePasswordForm;
use app\models\sys\users\Users;
use Yii;

/**
 * Trait CreateAccessTrait
 *
 * @property array $registrationErrors массив с ошибками во время регистрации
 * @property bool $isFioChanged менялись ли значения полей name, surname, patronymic
 *
 * @property string $email
 * @property string $login
 *
 * @property Users $relatedUser Пользователь связанный с продавцом/менеджером
 *
 * @property string $fio ФИО
 * @property string $urlToEntity
 */
trait CreateAccessTrait {
	public array $registrationErrors = [];
	public bool $isFioChanged = false;

	/**
	 * Создает учетную запись для продавца/менеджера и привязывает ее к продавцу/менеджеру. Если не удается,
	 * то отправляется письмо с ошибками администратору.
	 * @return void
	 */
	public function createAccess():void {
		if (!$this->createUser()) {
			$this->registrationErrors[] = 'Не удалось создать системного пользователя';
		}

		if ($this->registrationErrors) {
			$this->sendErrors();
		} else {
			$this->confirmRegistrationRequest();
		}
	}

	/**
	 * Обновляет поле username для системного пользователя на основе данных для продавца/менеджера.
	 * @return void
	 */
	public function modifyName():void {
		if ($this->isFioChanged & null !== $this->relatedUser) {
			$user = $this->relatedUser;
			$user->username = $this->fio;
			if (!$user->save()) {
				$this->registrationErrors[] = 'Не удалось модифицировать ФИО системного пользователя';
			}

			if ($this->registrationErrors) {
				$this->sendErrors();
			}
		}
	}

	/**
	 * @return bool
	 */
	public function saveRestoreCode():bool {
		$restoreCode = Users::generateSalt();
		$this->relatedUser->restore_code = $restoreCode;
		return $this->relatedUser->save();
	}

	/**
	 * Создает учетную запись пользователя на основе созданного продавца/менеджера
	 * @return bool
	 */
	public function createUser():bool {
		$user = new Users([
			'login' => $this->login,
			'username' => $this->fio,
			'password' => Users::DEFAULT_PASSWORD,
			'comment' => 'Пользователь автоматический создан. '.self::RUS_CLASS_NAME.' связан с этой УЗ.',
			'email' => $this->email,
			'phones' => $this->login
		]);
		if (!$user->save()) return false;
		$this->relatedUser = $user;
		return true;
	}

	/**
	 * @return string
	 */
	public function getFio():string {
		return trim("{$this->surname} {$this->name} {$this->patronymic}");
	}

	/**
	 * Пользователь создается с паролем по умолчанию. Для входа в систему нужно поменять этот пароль. Мы отправляем
	 * письмо с ссылкой для изменения пароля
	 * @return void
	 */
	public function confirmRegistrationRequest():void {
		if ($this->saveRestoreCode()) {
			RestorePasswordForm::sendRestoreMail(
				'registration/confirm-registration',
				$this->relatedUser,
				'Подтверждение регистрации на '.Yii::$app->name
			);
		}
	}

	/**
	 * Отправка ошибок регистрации администратору
	 * @return void
	 */
	public function sendErrors():void {
		Yii::$app->mailer->compose('registration/registration-errors', [
			'entity' => $this,
			'entityName' => self::RUS_CLASS_NAME,
			'entityUrl' => $this->urlToEntity,
			'errors' => $this->registrationErrors
		])
			->setFrom('todo@config.param')/*todo*/
			->setTo('todo@config.param')/*todo*/
			->setSubject("Ошибки при регистрации {$this->fio} (".self::RUS_CLASS_NAME.')')
			->send();
	}

	/**
	 * URL для нахождения сущности по ID
	 * @return string
	 */
	abstract public function getUrlToEntity():string;

	/**
	 * @inheritDoc
	 */
	public function beforeSave($insert):bool {
		if (count(array_intersect(['name', 'surname', 'patronymic'], array_keys($this->dirtyAttributes))) > 0) {
			$this->isFioChanged = true;
		}
		return static::beforeValidate();
	}

}