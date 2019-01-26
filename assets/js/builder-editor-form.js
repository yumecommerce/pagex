// prevent accident exit
window.onbeforeunload = function confirmExit() {
    if (window.document.body.matches('.pagex-layout-changed') && !document.body.className.match('wp-admin')) {
        return true;
    }
};

// close modal on ESC key
document.addEventListener('keydown', function (e) {
    if (e.keyCode === 27) {
        e.preventDefault();
        for (var item of document.querySelectorAll('.pagex-params-modal')) {
            item.classList.add('pagex-hide');
        }
    }
});

jQuery('#pagex-params-modal').draggable({
    handle: '.pagex-params-modal-params-move',
});

jQuery('#pagex-settings').draggable({
    axis: 'y'
});
