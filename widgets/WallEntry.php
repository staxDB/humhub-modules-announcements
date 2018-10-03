<?php

namespace humhub\modules\announcements\widgets;


/**
 * WallEntry is used to display an announcement message inside the stream.
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    public $editRoute = '/announcements/announcement/edit';

    public function run()
    {
        return $this->render('entry', [
            'announcement' => $this->contentObject,
            'contentContainer' => $this->contentObject->content->container
        ]);
    }

}
