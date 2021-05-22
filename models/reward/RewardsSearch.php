<?php
declare(strict_types = 1);

namespace app\models\reward;

use app\models\reward\active_record\references\RefRewardOperations;
use app\models\reward\active_record\references\RefRewardRules;
use app\models\reward\active_record\references\RefRewardStatuses;
use app\models\reward\active_record\RewardsAR;
use pozitronik\core\models\LCQuery;
use yii\data\ActiveDataProvider;
use app\models\sys\users\Users;

/**
 * Class RewardsSearch
 * @property null|string $userName
 * @property null|string $ruleName
 */
final class RewardsSearch extends RewardsAR {

	public ?string $userName = null;
	public ?string $ruleName = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['id', 'value', 'deleted', 'status', 'operation'], 'integer'],
			['create_date', 'date', 'format' => 'php:Y-m-d H:i'],
			[['userName', 'ruleName'], 'string', 'max' => 255]
		];
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params):ActiveDataProvider {
		$query = $this->setQuery();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		$this->setSort($dataProvider);
		$this->load($params);
		$query->joinWith(['relatedUser', 'refRewardStatus', 'refRewardOperation', 'refRewardRule']);

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
			->andFilterWhere([self::tableName().'.value' => $this->value])
			->andFilterWhere(['>=', self::tableName().'.create_date', $this->create_date])
			->andFilterWhere([self::tableName().'.status' => $this->status])
			->andFilterWhere([self::tableName().'.operation' => $this->operation])
			->andFilterWhere(['like', RefRewardRules::tableName().'.name', $this->ruleName])
			->andFilterWhere(['like', Users::tableName().'.username', $this->userName])
			->andFilterWhere([self::tableName().'.deleted' => $this->deleted]);
	}

	/**
	 * @param $dataProvider
	 */
	private function setSort($dataProvider):void {
		$dataProvider->setSort([
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id',
				'value',
				'create_date',
				'deleted',
				'statusName' => [
					'asc' => [RefRewardStatuses::tableName().'.name' => SORT_ASC],
					'desc' => [RefRewardStatuses::tableName().'.name' => SORT_DESC]
				],
				'operationName' => [
					'asc' => [RefRewardOperations::tableName().'.name' => SORT_ASC],
					'desc' => [RefRewardOperations::tableName().'.name' => SORT_DESC]
				],
				'ruleName' => [
					'asc' => [RefRewardRules::tableName().'.name' => SORT_ASC],
					'desc' => [RefRewardRules::tableName().'.name' => SORT_DESC]
				],
				'userName' => [
					'asc' => [Users::tableName().'.username' => SORT_ASC],
					'desc' => [Users::tableName().'.username' => SORT_DESC]
				]
			],
		]);
	}

	/**
	 * @return LCQuery
	 */
	private function setQuery():LCQuery {
		return self::find()
			->select([
				self::tableName().'.*',
				RefRewardStatuses::tableName().'.name  AS statusName',
				RefRewardOperations::tableName().'.name  AS operationName',
				RefRewardRules::tableName().'.name  AS ruleName',
				Users::tableName().'.username  AS userName',
			])
			->distinct()
			->active();
	}

}