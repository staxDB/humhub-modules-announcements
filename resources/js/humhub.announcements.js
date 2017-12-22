humhub.module('announcements', function (module, require, $) {

    var object = require('util').object;
    var client = require('client');
    var Content = require('content').Content;

    var Message = function (id) {
        Content.call(this, id);
    };

    object.inherits(Message, Content);

    Message.prototype.confirm = function (submitEvent) {
        this.update(client.submit(submitEvent));
    };

    Message.prototype.close = function (event) {
        this.update(client.post(event));
    };

    Message.prototype.update = function (update) {
        this.loader();
        update.then($.proxy(this.handleUpdateSuccess, this))
            .catch(Message.handleUpdateError)
            .finally($.proxy(this.loader, this, false));
    };

    Message.prototype.handleUpdateSuccess = function (response) {
        var streamEntry = this.streamEntry();
        return streamEntry.replace(response.output).then(function () {
            module.log.success('success.saved');
        });
    };

    Message.prototype.editSubmit = function (evt) {
        var that = this;
        var $errorMessage = that.$.find('.errorMessage');
        this.loader();
        $errorMessage.parent().hide();
        client.submit(evt).then(function (response) {
            if (!response.errors) {
                that.handleUpdateSuccess(response);
            } else {
                var errors = '';
                $.each(response.errors, function (key, value) {
                    errors += value + '<br />';
                });
                $errorMessage.html(errors).parent().show();
            }
        }).catch(Message.handleUpdateError)
            .finally($.proxy(this.loader, this, false));
    };

    Message.prototype.reset = function (evt) {
        this.update(client.post(evt));
    };

    Message.prototype.editCancel = function (evt) {
        this.update(client.post(evt));
    };

    Message.prototype.loader = function ($loader) {
        this.streamEntry().loader($loader);
    };

    Message.prototype.streamEntry = function () {
        return this.parent();
    };

    Message.handleUpdateError = function (e) {
        module.log.error(e, true);
    };

    module.export({
        Message: Message
    });
});