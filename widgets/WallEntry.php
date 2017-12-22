<?php

namespace humhub\modules\announcements\widgets;

use Yii;

/**
 * ConfirmMessageWallEntryWidget is used to display a confirm message inside the stream.
 *
 * This Widget will used by the ConfirmMessage Model in Method getWallOut().
 *
 * @author davidborn
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    public $editRoute = "/announcements/message/edit";

    public function run()
    {
        //We don't want an edit menu when the poll is closed
        if(version_compare(Yii::$app->version, '1.0.0-beta.4', 'lt') || $this->contentObject->closed) {
            $this->editRoute = '';
        }

        return $this->render('entry', ['announcement' => $this->contentObject,
            'user' => $this->contentObject->content->user,
            'contentContainer' => $this->contentObject->content->container
        ]);
    }

}