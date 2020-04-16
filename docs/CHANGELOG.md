Changelog
=========
v0.5
----------------------
- Added: Support for humhub v1.4 
    - Removed canWrite()-Method. Rel.: https://github.com/staxDB/humhub-modules-announcements/issues/56
    - Removed canRead()-Method. Rel.: https://github.com/staxDB/humhub-modules-announcements/issues/55
    - Updated messages. Rel.: https://docs.humhub.org/docs/develop/modules-migrate#migrate-from-13-to-14
- Added: Additional checkboxes when existing announcement gets edited. Here you can decide whether you want to reset statistics and whether to re-notify users.

v0.4
----------------------
- Fixed: Fixed filter issue, if module is not enabled in space. Rel.: https://github.com/humhub/humhub/issues/3273.

v0.3.7
----------------------
- Fixed: Editing an announcement was not possible anymore.

v0.3.6
----------------------
- Added: Config for disabling auto follow each created announcement.
- Fixed: Disabled users still gets notifications and will be added to list of read/unread users (Thanks to funkycram).

v0.3.5
----------------------
- Added: Marked as read.

v0.3.4
----------------------
- Removed Deprecations:
    - Changed className()- to class-function
    - Changed arrays to short version []
- Added: Settings for skipping creator of announcement in read by-list.

v0.3.3
----------------------
- Updated to yii/base/BaseObject

v0.3.2
----------------------
- Added settings for moving content
- Moved announcement will be market as closed/old. Can be changed in settings
  
v0.3.1
----------------------
- Added Move-content functionality
- Added new stream filter and settings
  
v0.3
----------------------
- Added Topics-Feature for Humhub v1.3
- Changed to new Richtext-Editor for Humhub v1.3
- Added export to excel functionality
  
v0.2
----------------------
- Fixed: Module sends notifications two times
- Changed Behavior: Announcement statistics will now only be reset if manually reset them
- Added: Configuration for notification behavior

v0.1
----------------------
- Release: First release version 0.1

