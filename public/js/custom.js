$(function () {
    let csrfToken = $('[name="csrf-token"]').attr('content');

    setInterval(refreshToken, 600000); // 10 min

    function refreshToken(){
        $.get($('meta[name="base-path"]').attr('content')+'/refresh-csrf').done(function(data){
            csrfToken = data; // the new token
        });
    }

    //setInterval(refreshToken, 600000); // 1 min
});


function checkSession(e){401==e.status&&location.reload()}

$(".alert-auto-dismiss").fadeTo(5000,500).slideUp(500,function(){$(".alert-auto-dismiss").slideUp(500)});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function () {
        $('#modal-loader').css('display', 'block');
    },
    complete: function () {
        $('#modal-loader').css('display', 'none');
    }
});
$(function () {
    $('.double-click').click(function() {
        return false;
    }).dblclick(function() {
        window.location = this.href;
        return false;
    });

    $('.card-toggle-custom').click(function (){
        if($(this).children('i')[0].className.split(' ').indexOf('icon-minus') !== -1) {
            $(this).children('i').removeClass('icon-minus').addClass('icon-plus');
        } else {
            $(this).children('i').removeClass('icon-plus').addClass('icon-minus');
        }
    });

});

    //Idle Time Functionality
    // Set timeout variables.
    let timoutWarning = 840000; // Display warning in 14 Mins.
    let timoutNow = 840000; // Timeout in 15 mins.
    let logoutUrl = $('meta[name="base-path"]').attr('content') + '/logout'; // URL to logout page.
    let lockscreen = $('meta[name="base-path"]').attr('content') + '/lockscreen'; // URL to logout page.

    let warningTimer;
    let timeoutTimer;

    // Start timers.
    function StartTimers() {
        warningTimer = setTimeout("IdleWarning()", timoutWarning);
        timeoutTimer = setTimeout("IdleTimeout()", timoutNow);
    }

    // Reset timers.
    function ResetTimers() {
        clearTimeout(warningTimer);
        clearTimeout(timeoutTimer);
        StartTimers();
        //$("#timeout").dialog('close');
    }

    // Show idle timeout warning dialog.
    function IdleWarning() {
        window.location.reload();
        //alert('Warning: No activity detected, session will be end soon.');
    }

    // Logout the user.
    function IdleTimeout() {
        window.location = logoutUrl;
        //window.location = lockscreen;
    }

    $(function () {
        //StartTimers();
        $('body').mousemove(function() {
            ResetTimers();
        });

        $('body').keypress(function() {
            ResetTimers();
        });
    });
//Idle Time Functionality

/* --- Pnotify Custom Js --- */
function trigger_pnofify(type = 'default', title = '', message = '') {
    new PNotify({
        title: (title === '') ? false : title,
        text: (message === '') ? false : message,
        type: type,
        icon: (title === '') ? 'none' : true,
        buttons: {
            sticker: false
        },
        delay: 5000
    });

}
/* --- END - Pnotify Custom Js --- */

function downloadSampleFile(file_name) {
    return window.location.href = $('meta[name="base-path"]').attr('content') + '/public/storage/sample/' + file_name;
}

function notificationMarkAllAsRead()
{
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') + '/notification/mark-all-as-read',
        success: function (response) {
            if(response.status === true) {
                $('.new-notification').css('display', 'none');
                $('.no-new-notification').css('display', 'block');
                $('#new-notification-count').css('display', 'none');
                $('#notification-mark-all-as-read-button').css('display', 'none');
                trigger_pnofify('success', 'Successful', 'Notification marked as read.');
            } else {
                trigger_pnofify('error', 'Something went wrong', response.message);
            }
        }
    });
}

jQuery(document).bind("keyup keydown", function(e){
    if(e.ctrlKey && e.keyCode === 76){
        window.location = lockscreen;
    }
});








