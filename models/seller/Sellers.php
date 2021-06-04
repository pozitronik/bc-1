<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\controllers\SellersController;
use app\models\seller\active_record\SellersAR;
use app\models\site\RestorePasswordForm;
use app\models\sys\users\Users;
use pozitronik\filestorage\traits\FileStorageTrait;
use Yii;

/**
 * Class Sellers
 * Конкретный продавец
 * @property mixed $sellerDocs атрибут загрузки файла
 * @property Users|null $sysUser системный пользователь
 * @property array $registrationErrors массив с ошибками во время регистрации
 *
 * @property string $fio ФИО
 */
class Sellers extends SellersAR {
	use FileStorageTrait;

	public $sellerDocs;
	public ?Users $sysUser = null;
	public array $registrationErrors = [];

	/**
	 * @return string
	 */
	public function getFio():string {
		return trim("{$this->surname} {$this->name} {$this->patronymic}");
	}

	/**
	 * Создает учетную запись для продавца и привязывает ее к продавцу. Если не удается, то отправляется
	 * письмо с ошибками администратору.
	 * @return void
	 */
	public function createAccess():void {
		if ($this->createUser()) {
			if (!$this->linkToUser()) {
				$this->registrationErrors[] = 'Не удалось связать системного пользователя с продавцом';
			}
		} else {
			$this->registrationErrors[] = 'Не удалось создавать системного пользователя';
		}

		if ($this->registrationErrors) {
			$this->sendErrors();
		} else {
			$this->confirmRegistrationRequest();
		}
	}

	/**
	 * Создает учетную запись пользователя на основе созданного продавца
	 * @return bool
	 */
	public function createUser():bool {
		$this->sysUser = new Users([
			'login' => $this->login,
			'username' => $this->fio,
			'password' => Users::DEFAULT_PASSWORD,
			'comment' => "User automatically created from seller's registration",
			'email' => $this->email
		]);

		return $this->sysUser->save();
	}

	/**
	 * Связка продавец-пользователь
	 * @return bool
	 */
	public function linkToUser():bool {
		$this->user = $this->sysUser->id;
		return $this->save(true, ['user']);
	}

	/**
	 * Пользователь создается с паролем по умолчанию. Для входа в систему нужно поменять этот пароль. Мы отправляем
	 * письмо с ссылкой для изменения пароля
	 * @return void
	 */
	public function confirmRegistrationRequest():void {
		if ($this->saveRestoreCode()) {
			RestorePasswordForm::sendRestoreMail(
				'site/confirm-registration',
				$this->sysUser,
				'Подтверждение регистрации на '.Yii::$app->name
			);
		}
	}

	/**
	 * @return bool
	 */
	public function saveRestoreCode():bool {
		$restoreCode = Users::generateSalt();
		$this->sysUser->restore_code = $restoreCode;
		return $this->sysUser->save();
	}

	/**
	 * Отправка ошибок регистрации администратору
	 * @return void
	 */
	public function sendErrors():void {
		Yii::$app->mailer->compose('sellers/registration-errors', [
			'seller' => $this,
			'sellerUrl' => SellersController::to(
				'index',
				['SellersSearch[id]' => $this->id],
				true
			),
			'errors' => $this->registrationErrors
		])
			->setFrom('todo@config.param')/*todo*/
			->setTo('todo@config.param')/*todo*/
			->setSubject("Ошибки при регистрации продавца {$this->fio}")
			->send();
	}

}