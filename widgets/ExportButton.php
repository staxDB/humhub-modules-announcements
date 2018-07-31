<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;
use Yii;
use yii\helpers\Url;

/**
 *
 * @author davidborn
 */
class ExportButton extends Widget
{
    public $announcement;

    public function run()
    {
        return $this->render('exportButton', ['announcement' => $this->announcement]);
    }
}
