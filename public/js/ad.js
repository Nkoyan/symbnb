let widgetsCounter = $('#ad_images div.form-group').length;

$('#add-image').on('click', function () {

    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, widgetsCounter);

    $('#ad_images').append(tmpl);

    handleDeleteButtons();
    widgetsCounter++;
});

handleDeleteButtons();

function handleDeleteButtons() {
    $('button[data-action="delete"]').on('click', function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}