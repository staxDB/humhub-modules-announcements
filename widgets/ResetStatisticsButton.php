<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 * ResetButton for closing polls per poll dropdown menu
 *
 * This Widget will used by the Poll Modul in Events.php
 *
 * @package humhub.modules.polls.widgets
 * @since 0.5
 * @author Luke
 */
class ResetStatisticsButton extends Widget
{
    public $announcement;

    public function run()
    {
        return $this->render('resetStatisticsButton', ['announcement' => $this->announcement]);
    }

}
