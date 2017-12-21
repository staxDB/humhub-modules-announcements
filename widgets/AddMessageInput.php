<?php

namespace humhub\modules\announcements\widgets;

use humhub\components\Widget;

/**
 * PollWallEntryWidget is used to display a poll inside the stream.
 *
 * This Widget will used by the Poll Model in Method getWallOut().
 *
 * @package humhub.modules.polls.widgets
 * @since 0.5
 * @author Luke
 */
class AddMessageInput extends Widget
{
    
    public $name;
    public $showTitle;
    
    public function run()
    {
        return $this->render('addMessageInput', ['name' => $this->name, 'showTitle' => $this->showTitle]);
    }

}

?>