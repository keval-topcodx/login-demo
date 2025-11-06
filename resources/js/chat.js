import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    $("#chatBox").hide();

    $("#chatButton").on("click", function () {
        $("#chatBox").toggle();
    });

    $("#closeChat").on("click", function () {
        $("#chatBox").hide();
    });


        // ----------------------
        // Toggle New Chat Card
        // ----------------------
        $('#newChatBtn').on('click', function() {
            $('#newChatCard').removeClass('d-none');
        });

        $('#closeNewChat').on('click', function() {
            $('#newChatCard').addClass('d-none');
        });

    $('#activeBtn').on('click', function() {
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('#archiveBtn').removeClass('btn-primary').addClass('btn-outline-primary');

        $('#activeChats').show();
        $('#archivedChats').hide();
    });

    $('#archiveBtn').on('click', function() {
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('#activeBtn').removeClass('btn-primary').addClass('btn-outline-primary');

        $('#activeChats').hide();
        $('#archivedChats').show();
    });

    $('#activeChats').show();
    $('#archivedChats').hide();
$(document).on("input", "#searchUser", function() {
    let searchValue = $(this).val().trim();
    $.ajax({
        url: '/search-user',
        type: 'POST',
        data: {search: searchValue},
        success: function(response) {
            console.log(response);
        }

    })
})

});
