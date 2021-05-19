<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\product\SimCard;
use app\models\product\SimCardSearch;
use pozitronik\core\traits\ControllerTrait;

/**
 * Class SimCardController
 */
class SimCardController extends DefaultController {
	use ControllerTrait;

	public string $modelClass = SimCard::class;
	public string $modelSearchClass = SimCardSearch::class;

}