<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\announcements\models\filters;

use humhub\modules\announcements\models\Announcement;
use humhub\modules\stream\models\filters\StreamQueryFilter;

class AnnouncementStreamFilter extends StreamQueryFilter
{
    /**
     * Default filters
     */
    const FILTER_NOT_READ = "not_read";
    const FILTER_CLOSED = "closed";

    /**
     * Array of stream filters to apply to the query.
     * There are the following filter available:
     *
     *  - 'not_read': Filters content which is not read by user
     *
     * @var array
     */
    public $filters = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filters'], 'safe']
        ];
    }

    public function init()
    {
        $this->filters = $this->streamQuery->filters;
        parent::init();
        $this->filters = (is_string($this->filters)) ? [$this->filters] : $this->filters;
    }

    public function apply()
    {
        // Show only my items
        if ($this->isFilterActive(self::FILTER_NOT_READ)) {
            $this->filterNotRead();
        }
        // Show only closed items
        if ($this->isFilterActive(self::FILTER_CLOSED)) {
            $this->filterClosed();
        }
    }

    public function isFilterActive($filter)
    {
        return in_array($filter, $this->filters);
    }

    protected function filterNotRead()
    {
        if ($this->streamQuery->user) {
            $this->query->leftJoin('announcement', 'content.object_model=:announcementClass AND content.object_id=announcement.id', ['announcementClass' => Announcement::class]);
            $this->query->leftJoin('announcement_user', 'announcement_user.announcement_id=announcement.id');
            $this->query->andWhere('announcement_user.user_id=:userId AND announcement_user.confirmed=0', ['userId' => $this->streamQuery->user->id]);
        }

        return $this;
    }

    protected function filterClosed()
    {
        if ($this->isFilterActive(self::FILTER_NOT_READ)) {
            $this->query->andWhere('announcement.closed=1');
        } else {
            $this->query->leftJoin('announcement', 'content.object_model=:announcementClass AND content.object_id=announcement.id', ['announcementClass' => Announcement::class]);
            $this->query->andWhere('announcement.closed=1');
        }

        return $this;
    }

}
