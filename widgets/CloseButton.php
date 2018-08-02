<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 *
 * CloseButton for close an announcement and mark it as old
 *
 * This Widget will used by the Announcement Module in Events.php
 *
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class CloseButton extends Widget
{
    public $announcement;
    
    public function run()
    {
        return $this->render('closeButton', ['announcement' => $this->announcement]);
    }

}
