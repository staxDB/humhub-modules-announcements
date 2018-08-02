<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 * ResetButton for reopen (closed) announcement
 *
 * This Widget will used by the Announcement Module in Events.php
 */
class ResetButton extends Widget
{
    public $announcement;

    public function run()
    {
        return $this->render('resetButton', ['announcement' => $this->announcement]);
    }

}
