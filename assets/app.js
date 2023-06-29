
$(document).ready(function () {
    $('#shorten').submit(function (e) {
        e.preventDefault();
        $.post('/shorten', $(this).serialize(), function (res) {
            document.location.href = '/success?link=' + res.shortUrl;
        });
    });
});

