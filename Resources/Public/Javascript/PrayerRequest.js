/**
 * Update the hidden publicAuthor field according to settings
 * @return void
 */
function prUpdatePublicName() {
    if ($('#publicAuthorSelect').val() == 4) {
        $('#alertGenderSelect').show();
    } else {
        $('#alertGenderSelect').hide();
    }
    var n = $('#authorRealName').val();
    switch ($('#publicAuthorSelect').val()) {
        case '1':
            $('#publicAuthor').val(n);
            break;
        case '2':
            var r = n.split(' ');
            $('#publicAuthor').val(r[0]);
            break;
        case '3':
            var r = n.split(' ');
            $('#publicAuthor').val(r[0].substr(0, 1)+'.');
            break;
        case '4':
            var x=['', 'Eine Person', 'Ein Bruder', 'Eine Schwester'];
            $('#publicAuthor').val(x[$('input[name=inlineRadioOptions]:checked').val()]);
            break;
    }
    $('#showPublicAuthor').html($('#publicAuthor').val());
}


/**
 * Restore the mode of publicAuthor calculation from data
 * @return void
 */
function checkNameFunction() {
    var n = $('#publicAuthor').val();
    if (typeof n != 'undefined') {
        var func;
        func = 1;
        if ((func == 1) && (n.length == 2) && (n.substr(1,1)=='.')) func = 3;
        if ((func == 1) && (n.indexOf(' ') == -1)) func = 2;
        if (func == 1) {
            if (n.indexOf('Eine Person') > -1) {
                func = 4;
                $('#anonymousPerson').prop('checked', true);
            }
            if (n.indexOf('Ein Bruder') > -1) {
                func = 4;
                $('#anonymousBrother').prop('checked', true);
            }
            if (n.indexOf('Eine Schwester') > -1) {
                func = 4;
                $('#anonymousSister').prop('checked', true);
            }
        }
        $('#publicAuthorSelect').val(func);
    }
}

var originalStatus = 0; // track original record status

/**
 * Take action upon completed document loading
 */
$(document).ready(function () {
    // hide the genderSelect box
    $('#alertGenderSelect').hide();

    // restore the mode of publicAuthor calculation from data
    checkNameFunction();

    /**
     * set default Audience when nothing is selected
     */
    if (!$("input[name='tx_vmfdsprayerrequests_requests[prayerRequest][audience]']:checked").val()) {
        $('#defaultAudience').prop('checked', true);
    }

    /**
     * Update publicAuthor according to input
     */
    $('#authorRealName').on('input', function () {
        prUpdatePublicName();
    });
    $('#publicAuthorSelect').change(function () {
        prUpdatePublicName();
    });
    $('input[name=inlineRadioOptions]').change(function () {
        prUpdatePublicName();
    });
    prUpdatePublicName();

    /**
     * Forward to single view when clicking of row
     */
    $('.prayerRow').click(function(){
        window.location.href= $(this).data('url');
    });

    /**
     * Track status changes
     */
    originalStatus = $('#selectStatus').val();
    $('#selectStatus').change(function(){
        $('#statusChange').val($('#selectStatus').val() == originalStatus ? 1 : 0);
    });


});