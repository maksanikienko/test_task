$(document).ready(function () {
    $('#shorten').submit(function (e) {
        let form = e.target;
        e.preventDefault();
        $.post(form.action, $(this).serialize())
            .done(function(res) {
                loadLinks();
            })
            .fail(function(err) {
                const errors = err.responseJSON.errors;
                const field = form.querySelector('[name="longUrl"]');
                field.setCustomValidity(errors.map((e) => e.message).join("\n"))
                field.reportValidity();
                setTimeout(() => {
                    field.setCustomValidity("")
                    field.reportValidity();
                }, 1500);
            });
    });
});

//{#если текущий пользователь admin#}
$(document).ready(loadLinks);

function loadLinks() {
    const xhr = new XMLHttpRequest();

    const url = "/api/links";

    xhr.open("GET", url, true);

    xhr.onload = function () {

        if (xhr.status === 200) {

            const data = JSON.parse(xhr.responseText).links;

            const html = $('#link-list');
            html.html('');
            const $tbody = $(document.createElement('tbody'));
            for (let i = 0; i < data.length; i++) {
                let link = data[i];
                $tbody.append(`<tr>
                    <td><a target="_blank" href="/go-${link.shortCode}" class="text-decoration-none link--secondary">${link.longUrl}</a></td>
                    <td>${link.owner}</td>
                    <td>${link.clickCount}</td>
                </tr>`)
            }
            html.append($tbody);
        }
    }
    xhr.send();
}