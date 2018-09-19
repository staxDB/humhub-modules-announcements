<?php

namespace humhub\modules\announcements\models\forms;

use Yii;
use \yii\base\Model;

class EditForm extends Model
{
    /**
     * notification settings
     */
    public $notifyCreated = true;
    public $notifyUpdated = false;
    public $notifyClosed = false;
    public $notifyResetStatistics = true;

    /**
     * filter settings
     */
    public $showFilters = true;

    /**
     * move content settings
     */
    public $setClosed = true;

    /**
     * skip creator in read by - list
     */
    public $skipCreator = true;



    /**
     * @inheritdocs
     */
    public function init()
    {
        $settings = Yii::$app->getModule('announcements')->settings;
        $this->notifyCreated = $settings->get('notify_created', $this->notifyCreated);
        $this->notifyUpdated = $settings->get('notify_updated', $this->notifyUpdated);
        $this->notifyClosed = $settings->get('notify_closed', $this->notifyClosed);
        $this->notifyResetStatistics = $settings->get('notify_resetStatistics', $this->notifyResetStatistics);
        $this->showFilters = $settings->get('show_filters', $this->showFilters);
        $this->setClosed = $settings->get('set_closed', $this->setClosed);
        $this->skipCreator = $settings->get('skip_creator', $this->skipCreator);
    }
    
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['notifyCreated', 'notifyUpdated', 'notifyClosed', 'notifyResetStatistics', 'showFilters', 'setClosed', 'skipCreator'],  'boolean'],
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'notifyCreated' => Yii::t('AnnouncementsModule.forms', 'Notify all Space Members if an announcement has been created.'),
            'notifyUpdated' => Yii::t('AnnouncementsModule.forms', 'Notify all Space Members if an announcement has been updated.'),
            'notifyClosed' => Yii::t('AnnouncementsModule.forms', 'Notify all Space Members if an announcement has been closed or reopened.'),
            'notifyResetStatistics' => Yii::t('AnnouncementsModule.forms', 'Notify all Space Members if an announcement statistics has been reset.'),
            'showFilters' => Yii::t('AnnouncementsModule.forms', 'Show additional announcement filters on stream.'),
            'setClosed' => Yii::t('AnnouncementsModule.forms', 'Set Announcement as old after moving to another space.'),
            'skipCreator' => Yii::t('AnnouncementsModule.forms', 'Skip Creator of announcement in \'read by\'-list.'),
        );
    }
    
    /**
     * Saves the given form settings.
     */
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $settings = Yii::$app->getModule('announcements')->settings;
        $this->notifyCreated = $settings->set('notify_created', $this->notifyCreated);
        $this->notifyUpdated = $settings->set('notify_updated', $this->notifyUpdated);
        $this->notifyClosed = $settings->set('notify_closed', $this->notifyClosed);
        $this->notifyResetStatistics = $settings->set('notify_resetStatistics', $this->notifyResetStatistics);
        $this->showFilters = $settings->set('show_filters', $this->showFilters);
        $this->setClosed = $settings->set('set_closed', $this->setClosed);
        $this->skipCreator = $settings->set('skip_creator', $this->skipCreator);
        return true;
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self;
    }

}
