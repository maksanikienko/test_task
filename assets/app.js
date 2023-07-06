$(document).ready(function () {
    $('#shorten').submit(function (e) {
        const form = e.target;
        const field = form.querySelector('[name="longUrl"]');
        e.preventDefault();
        $.post(form.action, $(this).serialize())
            .done(function (res) {
                field.value = '';
                loadLinks();
            })
            .fail(function (err) {
                const errors = err.responseJSON.errors;
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
    const $html = $('#link-list');
    if ($html.length === 0) {
        return; // because there is no container to which to load links
    }
    $html.find('tbody')?.remove();
    const $tbody = $(document.createElement('tbody'));

    const xhr = new XMLHttpRequest();
    $.get("/api/links")
        .done((res) => {
            const data = res.responseJSON?.links;

            if(!res.responseJSON || data.length === 0){
                $tbody.append(`<tr class="text-center">
                    <td colspan="3">Cannot load your links. Please contact support.</td>
                </tr>`)
                $html.append($tbody);
                return;
            }

            for (let i = 0; i < data.length; i++) {
                let link = data[i];
                $tbody.append(`<tr>
                    <td><a target="_blank" href="/go-${link.shortCode}" class="text-decoration-none link--secondary">${link.longUrl}</a></td>
                    <td>${link.owner}</td>
                    <td>${link.clickCount}</td>
                </tr>`)
            }
            $html.append($tbody);

        })
        .fail(() => {
            $tbody.append(`<tr class="text-center"> <td colspan="3">Cannot load your links. Please contact support.</td></tr>`)
            $html.append($tbody);
        })
    ;
}