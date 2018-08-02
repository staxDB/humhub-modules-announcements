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
//        // We don't want an edit menu when the announcement is closed
//        if (version_compare(Yii::$app->version, '1.0.0-beta.4', 'lt') || $this->contentObject->closed) {
//            $this->editRoute = '';
//        }

        return $this->render('entry', [
            'announcement' => $this->contentObject,
            'contentContainer' => $this->contentObject->content->container
        ]);
    }

}
