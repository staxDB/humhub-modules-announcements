<?php

namespace humhub\modules\announcements\components;

use Yii;
use humhub\modules\stream\actions\ContentContainerStream;
use humhub\modules\announcements\models\Announcement;

class StreamAction extends ContentContainerStream
{

    public function setupFilters()
    {
        if (in_array('messages_mine', $this->filters)) {

            $this->activeQuery->leftJoin('announcement', 'content.object_id=announcement.id AND content.object_model=:modelClass', [':modelClass' => Announcement::className()]);
        }
    }

}

?>
