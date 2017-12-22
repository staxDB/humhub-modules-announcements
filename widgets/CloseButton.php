<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 * PollWallEntryWidget is used to display a poll inside the stream.
 *
 * This Widget will used by the Poll Model in Method getWallOut().
 *
 * @author davidborn
 */
class CloseButton extends Widget
{
    public $announcement;
    
    public function run()
    {
        return $this->render('closeButton', ['announcement' => $this->announcement]);
    }

}

?>