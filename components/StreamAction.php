<?php

namespace humhub\modules\announcements\components;

use Yii;
use humhub\modules\stream\actions\ContentContainerStream;
use humhub\modules\announcements\models\Announcement;

class StreamAction extends ContentContainerStream
{

    public function setupFilters()
    {
        if (in_array('announcement_notAnswered', $this->filters) || in_array('announcement_mine', $this->filters)) {
            $this->activeQuery->leftJoin('announcement', 'content.object_id=announcement.id AND content.object_model=:modelClass', [':modelClass' => Announcement::class]);

            if (in_array('announcement_notAnswered', $this->filters)) {
                $this->activeQuery->leftJoin('announcement_user', 'announcement.id=announcement_user.announcement_id AND announcement_user.user_id=:userId', [':userId' => Yii::$app->user->id]);
                $this->activeQuery->andWhere(['announcement_user.confirmed' => false]);
            }
        }
    }

}
