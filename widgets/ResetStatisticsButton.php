<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 * ResetButton for reset an announcement statistics per dropdown menu
 *
 * This Widget will used by the Announcement Module in Events.php
 */
class ResetStatisticsButton extends Widget
{
    public $announcement;

    public function run()
    {
        return $this->render('resetStatisticsButton', ['announcement' => $this->announcement]);
    }

}
