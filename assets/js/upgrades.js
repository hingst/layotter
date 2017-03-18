jQuery(function($){
    function upgrade() {
        $('#layotter-upgrade-button-wrapper').hide();
        $('#layotter-upgrade-loading-wrapper').show();
        var status = 0;
        updateStatus(status);
        var interval = setInterval(function(){
            status += 10;
            if (status > 100) {
                status = 100;
                clearInterval(interval);
            }
            updateStatus(status);
        }, 1000);
    }

    function updateStatus(status) {
        $('#layotter-upgrade-loading-bar').css({ right: (100 - status) + '%' });
        $('#layotter-upgrade-loading-percent').html(status + '%');
    }

    function confirmUpgrade() {
        if (confirm('Please confirm that you have a created a database backup and want to run the upgrade now.')) {
            upgrade();
        }
    }

    $('#layotter-upgrade-button').click(confirmUpgrade);
});