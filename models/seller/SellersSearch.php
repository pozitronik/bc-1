<?php
declare(strict_types = 1);

namespace app\models\seller;

use app\models\store\Stores;
use app\models\sys\users\Users;
use app\modules\status\models\Status;
use app\modules\status\models\StatusRulesModel;
use pozitronik\core\models\LCQuery;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use Throwable;

/**
 * Class StoresSearch
 * @property null|string $store
 *
 * @property null|string $passportExplodedSeries
 * @property null|string $passportExplodedNumber
 * @property null|string $userId
 * @property null|string $userLogin
 * @property null|string $userEmail
 * @property null|string $currentStatus
 */
final class SellersSearch extends Sellers {

	public ?string $userId = null;
	public ?string $userEmail = null;
	public ?string $userLogin = null;
	public ?string $store = null;
	public ?string $passport = null;
	public ?string $currentStatus = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[
				[
					'id', 'name', 'surname', 'patronymic', 'passport', 'keyword', 'birthday', 'entry_date',
					'create_date', 'update_date', 'store', 'userEmail', 'userLogin', 'userId'
				],
				'filter',
				'filter' => 'trim'
			],
			[['id', 'userId', 'gender', 'currentStatus'], 'integer'],
			[['deleted', 'is_wireman_shpd', 'is_resident'], 'boolean'],
			[['store', 'userEmail'], 'string', 'max' => 255],
			['userLogin', 'string', 'max' => 64],
			['userEmail', 'email'],
			[['birthday', 'entry_date'], 'date', 'format' => 'php:Y-m-d'],
			[['create_date', 'update_date'], 'date', 'format' => 'php:Y-m-d H:i'],
			[['keyword'], 'string', 'max' => 64],
			[['name', 'surname', 'patronymic', 'passport'], 'string', 'max' => 128]
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelStatus():ActiveQuery {
		return $this->hasOne(Status::class, [
			'model_key' => 'id'
		])->andOnCondition(['model_name' => Sellers::class]);
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = self::find()->distinct()->active();
		$query->joinWith(['relStatus', 'stores', 'relatedUser']);
		$this->initQuery($query);

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$this->setSort($dataProvider);
		$this->load($params);

		if (!$this->validate()) return $dataProvider;

		$this->filterData($query);

		return $dataProvider;
	}

	/**
	 * @param $query
	 * @return void
	 */
	private function filterData($query):void {
		$query->andFilterWhere([self::tableName().'.id' => $this->id])
			->andFilterWhere(['like', self::tableName().'.name', $this->name])
			->andFilterWhere(['like', self::tableName().'.surname', $this->surname])
			->andFilterWhere(['like', self::tableName().'.patronymic', $this->patronymic])
			->andFilterWhere([self::tableName().'.gender' => $this->gender])
			->andFilterWhere([self::tableName().'.birthday' => $this->birthday])
			->andFilterWhere(['>=', self::tableName().'.create_date', $this->create_date])
			->andFilterWhere(['>=', self::tableName().'.update_date', $this->update_date])
			->andFilterWhere([self::tableName().'.passport_series' => $this->passportExplodedSeries])
			->andFilterWhere([self::tableName().'.passport_number' => $this->passportExplodedNumber])
			->andFilterWhere([self::tableName().'.entry_date' => $this->entry_date])
			->andFilterWhere([self::tableName().'.keyword' => $this->keyword])
			->andFilterWhere([self::tableName().'.is_resident' => $this->is_resident])
			->andFilterWhere([self::tableName().'.is_wireman_shpd' => $this->is_wireman_shpd])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted])
			->andFilterWhere(['like', Stores::tableName().'.name', $this->store])
			->andFilterWhere([Users::tableName().'.id' => $this->userId])
			->andFilterWhere([Users::tableName().'.email' => $this->userEmail])
			->andFilterWhere([Users::tableName().'.login' => $this->userLogin])
			->andFilterWhere([Status::tableName().'.status' => $this->currentStatus]);
	}

	/**
	 * @param $dataProvider
	 */
	private function setSort($dataProvider):void {
		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'name',
				'surname',
				'patronymic',
				'gender',
				'birthday',
				'deleted',
				'create_date',
				'update_date',
				'entry_date',
				'create_date',
				'update_date',
				'keyword',
				'is_resident',
				'is_wireman_shpd',
				'userId' => [
					'asc' => [Users::tableName().'.id' => SORT_ASC],
					'desc' => [Users::tableName().'.id' => SORT_DESC]
				],
				'userLogin' => [
					'asc' => [Users::tableName().'.login' => SORT_ASC],
					'desc' => [Users::tableName().'.login' => SORT_DESC]
				],
				'userEmail' => [
					'asc' => [Users::tableName().'.email' => SORT_ASC],
					'desc' => [Users::tableName().'.email' => SORT_DESC]
				],
				'currentStatus' => [
					'asc' => ['currentStatus' => SORT_ASC],
					'desc' => ['currentStatus' => SORT_DESC]
				]
			]
		]);
	}

	/**
	 * Searching passport field consists of series and number. This getter returns us series.
	 * @return false|string[]
	 */
	public function getPassportExplodedSeries() {
		$passportArray = explode(' ', $this->passport);
		return array_shift($passportArray);
	}

	/**
	 * Searching passport field consists of series and number. This getter returns us number.
	 * @return mixed|string|null
	 */
	public function getPassportExplodedNumber() {
		$passportArray = explode(' ', $this->passport);
		return array_pop($passportArray);
	}

	/**
	 * @param LCQuery $query
	 * @throws Throwable
	 */
	private function initQuery(LCQuery $query):void {
		$query->select([
			self::tableName().'.*',
			Users::tableName().'.id AS userId',
			Users::tableName().'.login AS userLogin',
			Users::tableName().'.email AS userEmail',
			/*Так обеспечивается наполнение атрибута + алфавитная сортировка*/
			"ELT(".Status::tableName().'.status'.", '".implode("','", ArrayHelper::map(
				StatusRulesModel::getAllStatuses(Sellers::class),
				'id',
				'name'
			))."') AS currentStatus"
		])
			->distinct()
			->active();
	}
}