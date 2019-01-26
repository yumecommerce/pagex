var pagexBackend = {
    accordion: {
        item: null,
        wrapper: null,
        content: null,
        container: null,

        init: function (el) {
            this.container = el.closest('.pagex-accordion');
            this.item = el.closest('.pagex-accordion-item');
            this.wrapper = this.item.querySelector('.pagex-accordion-item-content-wrapper');
            this.content = this.item.querySelector('.pagex-accordion-item-content');

            if (!this.container.matches('.pagex-accordion-toggle-separately') && !this.item.matches('.pagex-item-active')) {
                let activeItem = this.container.querySelector('.pagex-item-active');

                if (activeItem != null) {
                    let activeWrapper = activeItem.querySelector('.pagex-accordion-item-content-wrapper');
                    activeWrapper.classList.remove('pagex-accordion-active');
                    activeItem.classList.remove('pagex-item-active');
                }
            }

            if (this.item.matches('.pagex-item-active')) {
                this.wrapper.classList.remove('pagex-accordion-active');
                this.item.classList.remove('pagex-item-active');
            } else {
                this.item.classList.add('pagex-item-active');
                this.wrapper.classList.add('pagex-accordion-active');
            }

            // update post content for backend
            pagex.updatePostContent();
        },
    },

    tabs: {
        init: function (el) {
            if (el.matches('.pagex-item-active')) return;

            let element = el.closest('.pagex-tabs'),
                index = Array.from(el.parentNode.children).indexOf(el);

            element.querySelector('.pagex-tabs-nav-items .pagex-item-active').classList.remove('pagex-item-active');
            element.querySelector('.pagex-tabs-panes .pagex-item-active').classList.remove('pagex-item-active');

            element.querySelectorAll('.pagex-tabs-nav-items .pagex-tabs-nav-item')[index].classList.add('pagex-item-active');
            element.querySelectorAll('.pagex-tabs-panes .pagex-tabs-pane')[index].classList.add('pagex-item-active');

            pagex.updatePostContent();
        }
    },

    switchToBuilder: function () {
        document.body.classList.add('pagex-builder-editor-active');
        document.querySelector('[name="pagex_page_status"]').value = 'true';
    },

    switchToDefault: function () {
        document.body.classList.remove('pagex-builder-editor-active');
        document.querySelector('[name="pagex_page_status"]').value = 'false';
    },

    switchToFrontend: function () {
        if (pagexLocalize.front_link.length) {
            window.location.replace(pagexLocalize.front_link);
        }
    }
};

document.addEventListener('click', function (e) {
    if (!e.target) return;
    let el = e.target;
    // accordion
    if (el.matches('.pagex-accordion-item-header')) pagexBackend.accordion.init(el);
    // tabs
    if (el.matches('.pagex-tabs-nav-item')) pagexBackend.tabs.init(el);
    // switch editors
    if (el.matches('.pagex-switch-editor-builder')) pagexBackend.switchToBuilder();
    if (el.matches('.pagex-switch-editor-wordpress')) pagexBackend.switchToDefault();

    // redirect to frontend builder
    if (el.matches('.pagex-switch-editor-frontend')) pagexBackend.switchToFrontend();
});


document.addEventListener("DOMContentLoaded", function () {

    let advancedMetaSection = document.querySelector('#advanced-sortables'),
        builderArea = document.querySelector('#pagex-backend-editor'),
        pagexStatus = document.querySelector('[name="pagex_page_status"]'),
        currentStatus = pagexStatus == null ? false : pagexStatus.value;

    do {
        advancedMetaSection.parentNode.insertBefore(advancedMetaSection, advancedMetaSection.previousElementSibling);
    } while (advancedMetaSection && advancedMetaSection.previousElementSibling);

    if (builderArea && builderArea.previousElementSibling) {
        builderArea.parentNode.insertBefore(builderArea, builderArea.previousElementSibling);
    }

    if (currentStatus && currentStatus === 'true') {
        document.body.classList.add('pagex-builder-editor-active');
    }

    // add switch editor buttons
    setTimeout(function () {
        let gutenberg_editor = document.querySelector('.edit-post-header-toolbar'),
            standard_editor = document.querySelector('#wp-content-media-buttons'),
            editorBtns = '<div id="pagex-switch-editors"><button class="pagex-switch-editor-wordpress button" type="button">' + pagexLocalize.string.edit_with_wordpress + '</button><button class="pagex-switch-editor-frontend button button-primary" type="button">' + pagexLocalize.string.edit_with_frontend + '</button><button class="pagex-switch-editor-builder button button-primary" type="button">' + pagexLocalize.string.edit_with_pagex + '</button></div>';

        if (gutenberg_editor) {
            gutenberg_editor.insertAdjacentHTML('beforeend', editorBtns);
        }

        if (standard_editor) {
            standard_editor.insertAdjacentHTML('beforeend', editorBtns);
        }
    }, 800);

});