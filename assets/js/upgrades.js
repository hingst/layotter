jQuery(function($){
    function upgrade() {
        $('#layotter-upgrade-button-wrapper').hide();
        $('#layotter-upgrade-loading-wrapper').show();
        var status = 0;
        updateStatus(status);
        setCurrentTask('Updating post layouts'); // TODO: layotterData.i18n.upgrades.confirm

        var interval = setInterval(function(){
            status += 10;
            if (status >= 100) {
                status = 100;
                clearInterval(interval);
                setTaskComplete();
                $('#layotter-upgrade-complete-wrapper').show();
            }

            updateStatus(status);

            if (status == 20) {
                setTaskComplete();
                setCurrentTask('Updating element templates'); // TODO: layotterData.i18n.upgrades.templates
            } else if (status == 40) {
                setTaskComplete();
                setCurrentTask('Updating posts'); // TODO: layotterData.i18n.upgrades.posts
            }
        }, 1000);
    }

    function updateStatus(status) {
        $('#layotter-upgrade-loading-bar').css({ right: (100 - status) + '%' });
        $('#layotter-upgrade-loading-percent').html(status + '%');
    }

    function confirmUpgrade() {
        if (confirm('Please confirm that you have a created a database backup and want to run the upgrade now.')) { // TODO: layotterData.i18n.upgrades.confirm
            upgrade();
        }
    }

    function setTaskComplete() {
        $('#layotter-upgrade-tasks li:last').append('done.');
    }

    function setCurrentTask(title) {
        $('#layotter-upgrade-tasks').append('<li>' + title + ' &hellip; </li>');
    }

    $('#layotter-upgrade-button').click(confirmUpgrade);
});