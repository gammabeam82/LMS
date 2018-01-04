$(document).ready(() => {

    const subscribe = $('.messages-subscribe').text();
    const unsubscribe = $('.messages-unsubscribe').text();
    const subLink = $('.subscribe');

    subLink.on('click', function (event) {
        event.preventDefault();

        $.get($(this).attr('href')).then(
            (data) => {
                if (typeof data['subscribed'] !== 'undefined') {
                   let subMsg = data['subscribed'] ? unsubscribe : subscribe;
                   subLink.text(subMsg);
                }
            }
        );
    });

});
