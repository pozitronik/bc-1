<?php
declare(strict_types = 1);

namespace app\models\dealers\active_record\references;

use pozitronik\references\models\CustomisableReference;

/**
 * Class RefBranches
 */
class RefBranches extends CustomisableReference {

	public $menuCaption = "Справочник филиалов";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'ref_branches';
	}
}