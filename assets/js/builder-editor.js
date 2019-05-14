"use strict";

(function () {
    let editor = document.querySelector('[data-type="post_content"] > .element-wrap'),
        buidArea = document.querySelector('.pagex-builder-area');

    if (!_.isNull(editor) && _.isNull(buidArea)) {
        if (!document.body.className.match('wp-admin')) {
            editor.classList.add('pagex-builder-area');
        }
    }

    if (!document.querySelector('.pagex-builder-area')) {
        alert('Pagex Error! Content area is missing. Please make sure that your template has content element.');
    }
})();

// prevent link actions
jQuery(document).on('click', '.pagex-builder-area a, .pagex-builder-area button, .pagex-builder-area [type="submit"], .pagex-builder-frame-active a, .pagex-builder-frame-active button, .pagex-builder-frame-active [type="submit"]', function (e) {
    e.preventDefault();
});

var pagexDelay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

var pagex = {
    currentElement: null,
    currentElementTemplate: null,
    currentElementParams: null,
    currentElementFormParams: null,
    currentParam: null,
    currentDevice: null,

    // should new element be appended or prepended
    prependElement: false,

    option_start: _.template(document.getElementById('pagex-control-option-start-template').innerHTML),
    element_start: _.template(document.getElementById('pagex-control-element-start-template').innerHTML),
    option_end: _.template(document.getElementById('pagex-control-option-end-template').innerHTML),
    element_end: _.template(document.getElementById('pagex-control-element-end-template').innerHTML),

    paramsForm: window.parent.document.getElementById("pagex-params-form"),
    pagexModal: window.parent.document.getElementById("pagex-params-modal"),
    allElementsModal: window.parent.document.getElementById("pagex-all-elements-modal"),
    elementInfoLink: window.parent.document.getElementById("pagex-element-info-link"),

    // icon picker
    iconsModal: window.parent.document.getElementById("pagex-icons-modal"),

    // add layout
    layoutsModal: window.parent.document.getElementById("pagex-layouts-modal"),
    currentSection: null,

    // save layout
    saveLayoutsModal: window.parent.document.getElementById("pagex-save-layouts-modal"),

    searchResults: window.parent.document.getElementById("pagex-search-elements-result"),
    searchTitle: window.parent.document.getElementById("pagex-search-elements-title"),
    modalTitle: window.parent.document.getElementById("pagex-params-modal-title"),

    // responsive switcher
    switchersControl: window.parent.document.getElementById('pagex-control-responsive-switcher'),
    switchersOptions: window.parent.document.querySelector('.pagex-responsive-switchers-options'),
    currentSwitcher: null,

    // backend
    postContentArea: document.getElementById("pagex_post_content"),
    postElementsParamsArea: document.getElementById("pagex_elements_params"),
    excerptPreviewArea: document.getElementById("pagex-excerpt-preview-frame"),


    genID: function () {
        return 'p' + Math.random().toString(36).substr(2, 5);
    },

    genShortcode: function (data) {
        let that = this;

        _.forEach(data, function (v, k) {
            let option = that.findById(that.currentElementParams, k),
                action = !_.isNull(option) && !_.isUndefined(option.action) ? true : false;

            // remove all empty values, custom actions and default parameters
            if (k.indexOf('pagex') !== -1 || !v.length || action) {
                delete data[k];
            }
        });


        if (!_.isEmpty(data)) {
            let shortcode = encodeURIComponent(JSON.stringify(data));
            return '[' + this.currentElementParams.callback + ' data="' + shortcode + '"]';
        }

        return '[' + this.currentElementParams.callback + ']';

    },

    genDataForDynamicEl: function (data) {
        let that = this;

        _.forEach(data, function (v, k) {
            let option = that.findById(that.currentElementParams, k),
                action = !_.isNull(option) && !_.isUndefined(option.action) ? true : false;

            // remove all empty values, custom actions and default parameters
            if (k.indexOf('pagex') !== -1 || !v || action) {
                delete data[k];
            }
        });

        return data;
    },

    genSliderData: function (data) {
        let obj = {
            mode: 'horizontal',
            loop: !!data.pagex_slider_loop,
            effect: data.pagex_slider_effect,
            autoHeight: !!data.pagex_slider_autoHeight,
            centeredSlides: !!data.pagex_slider_centeredSlides,
            speed: data.pagex_slider_speed ? Number(data.pagex_slider_speed) : 500,
            spaceBetween: data.pagex_slider_spaceBetween.xs ? Number(data.pagex_slider_spaceBetween.xs) : 0,
            slidesPerView: data.pagex_slider_slidesPerView.xs ? Number(data.pagex_slider_slidesPerView.xs) : 1,
            slidesPerGroup: data.pagex_slider_slidesPerGroup.xs ? Number(data.pagex_slider_slidesPerGroup.xs) : 1,
            breakpoints: {
                576: {},
                768: {},
                992: {},
                1200: {}
            }
        };

        if (data.pagex_slider_autoplay) obj.autoplay = {delay: data.pagex_slider_autoplay_delay ? Number(data.pagex_slider_autoplay_delay) : 2000};

        if (data.pagex_slider_pa_type) obj.paginationtype = 'fraction';
        if (data.pagex_slider_pel) obj.pagination_el = data.pagex_slider_pel;

        if (data.pagex_slider_spaceBetween.sm) obj.breakpoints[576].spaceBetween = Number(data.pagex_slider_spaceBetween.sm);
        if (data.pagex_slider_slidesPerView.sm) obj.breakpoints[576].slidesPerView = Number(data.pagex_slider_slidesPerView.sm);
        if (data.pagex_slider_slidesPerGroup.sm) obj.breakpoints[576].slidesPerGroup = Number(data.pagex_slider_slidesPerGroup.sm);

        if (data.pagex_slider_spaceBetween.md) obj.breakpoints[768].spaceBetween = Number(data.pagex_slider_spaceBetween.md);
        if (data.pagex_slider_slidesPerView.md) obj.breakpoints[768].slidesPerView = Number(data.pagex_slider_slidesPerView.md);
        if (data.pagex_slider_slidesPerGroup.md) obj.breakpoints[768].slidesPerGroup = Number(data.pagex_slider_slidesPerGroup.md);

        if (data.pagex_slider_spaceBetween.lg) obj.breakpoints[992].spaceBetween = Number(data.pagex_slider_spaceBetween.lg);
        if (data.pagex_slider_slidesPerView.lg) obj.breakpoints[992].slidesPerView = Number(data.pagex_slider_slidesPerView.lg);
        if (data.pagex_slider_slidesPerGroup.lg) obj.breakpoints[992].slidesPerGroup = Number(data.pagex_slider_slidesPerGroup.lg);

        if (data.pagex_slider_spaceBetween.xl) obj.breakpoints[1200].spaceBetween = Number(data.pagex_slider_spaceBetween.xl);
        if (data.pagex_slider_slidesPerView.xl) obj.breakpoints[1200].slidesPerView = Number(data.pagex_slider_slidesPerView.xl);
        if (data.pagex_slider_slidesPerGroup.xl) obj.breakpoints[1200].slidesPerGroup = Number(data.pagex_slider_slidesPerGroup.xl);

        if (_.isEmpty(obj.breakpoints[576])) delete obj.breakpoints[576];
        if (_.isEmpty(obj.breakpoints[768])) delete obj.breakpoints[768];
        if (_.isEmpty(obj.breakpoints[992])) delete obj.breakpoints[992];
        if (_.isEmpty(obj.breakpoints[1200])) delete obj.breakpoints[1200];


        return JSON.stringify(obj);
    },

    // return option of element by id
    findById: function (obj, item) {
        let par = null;

        _.forEach(obj.options, function (array) {
            _.forEach(array.params, function (option) {
                if (option.id === item) {
                    par = option;
                }
            });
        });

        return par;
    },

    genIcon: function (id, data) {
        if (data[id] === 'font-awesome') return '<i class="' + data[id + "_fa"] + ' pagex-icon"></i>';
        if (data[id] === 'svg') return this.validateSVG(data[id + "_svg"]);
        if (data[id] === 'image' && data[id + "_image"].length) return '<img class="pagex-icon" src="' + data[id + "_image"] + '" alt="icon">';
    },

    // add pagex-icon class to svg so it could react to style changes
    validateSVG: function (svg) {
        if (!svg.trim().length)
            return svg;

        let doc = new DOMParser().parseFromString(svg, 'text/html'),
            svgBody = doc.body.querySelector('svg'),
            imgBody = doc.body.querySelector('img');

        if (!_.isNull(svgBody)) {
            svgBody.classList.add('pagex-icon');
            return doc.body.innerHTML;
        }

        if (!_.isNull(imgBody)) {
            imgBody.classList.add('pagex-icon');
            return doc.body.innerHTML;
        }

        return svg;

    },

    parseCssNumber: function (value) {
        value = value.trim();

        if (!value.length) return 0;

        if (!isNaN(value)) {
            return value + 'px';
        } else {
            return value;
        }
    },

    // render main form each time when edit element options are displayed
    renderForm: function () {
        let htmlParams = '',
            that = this;

        this.currentElementFormParams = []; // clear current element param

        this.currentElementParams = _.find(pagexElements, {id: this.currentElement.getAttribute('data-type')});

        this.modalTitle.innerHTML = this.currentElementParams.title;
        this.elementInfoLink.href = !_.isUndefined(this.currentElementParams.info) ? this.currentElementParams.info : 'https://github.com/yumecommerce/pagex/wiki';

        htmlParams += '<div class="pagex-params-tabs d-flex">';
        _.forEach(that.currentElementParams.options, function (options) {
            if (!options.params.length) return;
            htmlParams += '<div class="pagex-params-tab-title">' + options.title + '</div>';
        });
        htmlParams += '</div>';

        htmlParams += '<div class="pagex-params-tabs-wrapper">';

        _.forEach(that.currentElementParams.options, function (options) {
            if (!options.params.length) return;

            htmlParams += '<div class="pagex-params-tab-content pagex-scroll pagex-hide"><div class="form-row align-items-start">';
            _.forEach(options.params, function (value, key) {
                // push all params in one obj
                that.currentElementFormParams.push(value);
                let control = _.template(document.getElementById('pagex-control-' + value.type + '-template').innerHTML);
                // is it form param or layout
                if (_.isUndefined(value.id)) {
                    htmlParams += control({data: value});
                } else {
                    if (!_.isUndefined(value.type) && value.type === 'repeater') {
                        let repStart = _.template(document.getElementById('pagex-control-repeater-start-template').innerHTML);
                        htmlParams += repStart({data: value});
                        htmlParams += document.getElementById('pagex-control-repeater-item-start-template').innerHTML;
                        _.forEach(value.params, function (repValue) {
                            let repControl = _.template(document.getElementById('pagex-control-' + repValue.type + '-template').innerHTML);
                            if (_.isUndefined(repValue.id)) {
                                htmlParams += repControl({data: repValue});
                            } else {
                                // add additional class for easy conditional logic
                                if (_.isUndefined(repValue.class)) {
                                    repValue.class = 'col-12 pagex-control-repeater-wrapper';
                                } else {
                                    repValue.class = repValue.class + ' pagex-control-repeater-wrapper';
                                }
                                htmlParams += that.option_start({data: repValue}) + repControl({data: repValue}) + that.option_end({data: repValue});
                            }
                        });
                        htmlParams += document.getElementById('pagex-control-repeater-item-end-template').innerHTML;
                        htmlParams += document.getElementById('pagex-control-repeater-end-template').innerHTML;
                    } else {
                        htmlParams += that.option_start({data: value}) + control({data: value}) + that.option_end({data: value});
                    }
                }

            });
            htmlParams += '</div></div>';
        });
        htmlParams += '</div>';

        this.paramsForm.innerHTML = htmlParams;

        this.paramsForm.querySelector('.pagex-params-tab-title').classList.add('active');
        this.paramsForm.querySelector('.pagex-params-tab-content').classList.remove('pagex-hide');

        // fill the form
        let formOptions = pagexLocalize.all_params[this.currentElement.getAttribute('data-id')];
        if (!_.isUndefined(formOptions)) {
            _.forEach(formOptions, function (param, key) {
                let paramOptions = _.find(that.currentElementFormParams, {id: key}),
                    type = _.isUndefined(paramOptions) || _.isUndefined(paramOptions.type) ? '' : paramOptions.type;

                if (_.isObject(param)) {
                    if (type === 'dimension') {
                        // if responsive css dimension inputs
                        _.forEach(param, function (respValueObj, k) {
                            _.forEach(respValueObj, function (respValue, respKey) {
                                that.paramsForm.querySelector('[name="' + key + '[' + k + '][' + respKey + ']"]').value = respValue;
                            });
                        });
                    } else if (type === 'repeater') {
                        // create numbers of repeater items based on saved setting
                        let repContainer = that.paramsForm.querySelector('.pagex-repeater-items'),
                            repItem = repContainer.innerHTML;

                        for (let i = 0; i < (param.length - 1); i++) {
                            repContainer.insertAdjacentHTML('beforeend', repItem);
                        }

                        let repeaterParams = paramOptions.params;

                        _.forEach(param, function (repeaterVal, repeaterKey) {
                            _.forEach(repeaterVal, function (v, k) {

                                let repIt = _.find(repeaterParams, {id: key + '[][]' + k}),
                                    repType = _.isUndefined(repIt) || _.isUndefined(repIt.type) ? null : repIt.type;

                                if (repType === 'checkbox' || v === 'true' || v === 'pagex-checkbox-true') {
                                    that.paramsForm.querySelectorAll('[name="' + key + '[][]' + k + '"]')[repeaterKey].checked = true;
                                } else if (_.isObject(v)) {
                                    // if responsive inputs
                                    _.forEach(v, function (value, keyed) {
                                        let paramInput = that.paramsForm.querySelectorAll('[name="' + key + '[][]' + k + '[' + keyed + ']"]')[repeaterKey];
                                        if (paramInput) {
                                            paramInput.value = value;
                                        }
                                    });
                                } else {
                                    let repKeyParam = that.paramsForm.querySelectorAll('[name="' + key + '[][]' + k + '"]')[repeaterKey];
                                    if (repKeyParam) {
                                        if (k.substr(-4) === '_svg') {
                                            repKeyParam.value = decodeURIComponent(v);
                                        } else {
                                            repKeyParam.value = v;
                                        }
                                    }
                                }
                            });
                        });
                    } else {
                        if (_.isArray(param)) {
                            // multiselect
                            _.forEach(param, function (multiselectObj, k) {
                                _.forEach(multiselectObj, function (selectValue, selectKey) {
                                    let paramSelect = that.paramsForm.querySelector('[name="' + key + '[][' + selectKey + '][]"]');
                                    if (!_.isNull(paramSelect)) {
                                        for (var i = 0; i < paramSelect.options.length; i++) {
                                            paramSelect.options[i].selected = selectValue.indexOf(paramSelect.options[i].value) >= 0;
                                        }
                                    }
                                });
                            });
                        } else {
                            // if responsive css inputs
                            _.forEach(param, function (v, k) {
                                let paramInput = that.paramsForm.querySelector('[name="' + key + '[' + k + ']"]');
                                if (!_.isNull(paramInput)) {
                                    if (v === 'true' || v === 'pagex-checkbox-true') {
                                        paramInput.checked = true;
                                    } else {
                                        paramInput.value = v;
                                    }
                                }
                            });
                        }
                    }
                } else if (type === 'checkbox' || param === 'pagex-checkbox-true') {
                    // if checkbox or custom option checkbox from link type
                    let paramInput = that.paramsForm.querySelector('[name=' + key + ']');
                    if (!_.isNull(paramInput)) {
                        paramInput.checked = true;
                    }
                } else {
                    let paramInput = that.paramsForm.querySelector('[name=' + key + ']');
                    if (!_.isNull(paramInput)) {
                        if (key.substr(-4) === '_svg') {
                            paramInput.value = decodeURIComponent(param);
                        } else {
                            paramInput.value = param;
                        }
                    }
                }
            });
        }

        // set background images for image controls
        for (var item of this.paramsForm.querySelectorAll('.pagex-image-control')) {
            item.querySelector('.pagex-image-placeholder').style.backgroundImage = "url('" + item.querySelector('.pagex-image-url').value + "')";
        }

        // fill content options
        _.forEach(that.currentElementFormParams, function (value) {
            if (!_.isUndefined(value.action) && value.action === 'content') {
                let selector = value.selector,
                    elSelectorContent = that.currentElement.querySelector(selector);

                if (!_.isNull(elSelectorContent)) {
                    that.paramsForm.querySelector('[name="' + value.id + ':skip"]').value = elSelectorContent.innerHTML.replace(/<br>/gi, "");
                }
            }

            // fill content for repeater
            if (!_.isUndefined(value.type) && value.type === 'repeater') {
                _.forEach(value.params, function (repValue) {
                    if (!_.isUndefined(repValue.action) && repValue.action === 'content') {
                        let repSelector = repValue.selector,
                            elRepContents = that.currentElement.querySelectorAll(repSelector);
                        _.forEach(elRepContents, function (elRepCon, index) {
                            let repContEl = that.paramsForm.querySelectorAll('[name="' + repValue.id + ':skip"]')[index];
                            if (!_.isUndefined(repContEl)) {
                                repContEl.value = elRepCon.innerHTML;
                            }
                        });
                    }
                });
            }
        });

        // add repeater item label
        for (let item of that.paramsForm.querySelectorAll('.pagex-repeater-value')) {
            let val = item.querySelector('.pagex-control-field').value;

            if (val.length) {
                item.closest('.pagex-repeater-item').querySelector('.pagex-repeater-title').innerHTML = val;
            }
        }

        // make repeater sortable
        this.repeaterSortable();

        // create color inputs
        window.parent.colorPicker.initAll();

        // set initial conditionals
        _.forEach(this.paramsForm.querySelectorAll('[data-condition]'), function (v) {
            let repeater = v.closest('.pagex-repeater-item'),
                condData = v.getAttribute('data-condition'),
                cond = JSON.parse(condData);

            var hide = false;

            _.forEach(cond, function (condVal, condKey) {
                let isReversed = condKey.charAt(0) === '!';
                condKey = isReversed ? condKey.substr(1) : condKey;
                let pVal = _.isNull(repeater) ? that.paramsForm.querySelector('[name="' + condKey + '"]') : repeater.querySelector('[name="' + condKey + '"]');

                if (pVal.type && pVal.type === 'checkbox') {
                    pVal = pVal.checked ? 'true' : 'false';
                } else {
                    pVal = pVal.value;
                }

                if (hide) return;

                if (isReversed) {
                    if (!condVal.includes(pVal)) {
                        hide = false;
                    } else {
                        hide = true;
                    }
                } else {
                    if (condVal.includes(pVal)) {
                        hide = false;
                    } else {
                        hide = true;
                    }
                }

            });

            if (hide) {
                v.style.display = 'none';
            } else {
                v.style = '';
            }
        });

        // set responsive dropdown
        if (!_.isNull(this.currentDevice) && this.currentDevice !== 'default') {
            jQuery(this.switchersControl.querySelector('[data-device-switcher="' + this.currentDevice + '"]')).trigger('click');
        }

        // show modal form
        this.pagexModal.classList.remove('pagex-hide');
    },


    renderElement: function (forced = false) {
        let form = jQuery(this.paramsForm).serializeJSON(),
            elId = this.currentElement.getAttribute('data-id'),
            currentParamName = this.currentParam !== null ? this.currentParam.getAttribute('name') : null,
            oldParams = pagexLocalize.all_params[elId],
            dynamicElementUpdate = false,
            that = this;

        // do not update element content if default param or slider was changed
        let renderEl = !_.isNull(currentParamName) && currentParamName.indexOf('pagex') !== -1 ? false : true;

        // check if currentParam is action type
        if (renderEl && this.currentParam !== null) {
            let paramWrap = this.currentParam.closest('.pagex-control-wrapper');

            if (paramWrap.matches('.pagex-control-action-type-css') || paramWrap.matches('.pagex-control-action-type-class')) {
                renderEl = false;
            }
        }

        // do not render if it's one of basic elements
        if (renderEl && _.includes(['section', 'container', 'row', 'inner-row', 'column'], this.currentElementParams.id)) {
            renderEl = false;
        }

        if (renderEl || forced) {
            // pass json form to element template ignoring "skip" attribute of content actions
            let formObj = jQuery(this.paramsForm).serializeJSON({}, true);

            if (this.currentElementParams.type === 'static') {
                // update static elements
                this.currentElementTemplate = _.template(document.getElementById('pagex-element-' + this.currentElementParams.id + '-template').innerHTML);

                // update current element
                this.currentElement.querySelector('.element-wrap').innerHTML = this.currentElementTemplate({
                    data: formObj,
                    firstInit: _.isUndefined(oldParams),
                    currentParamName: currentParamName
                });
            } else {
                // update dynamic element only for frontend builder
                if (this.postContentArea === null) {
                    this.currentElement.classList.add('pagex-element-updating');
                    dynamicElementUpdate = true;

                    jQuery.post(pagexLocalize.ajaxUrl, {
                        action: that.currentElementParams.callback,
                        query_string: pagexLocalize.query_string,
                        post_id: pagexLocalize.post_id,
                        atts: that.genDataForDynamicEl(formObj),
                        "pagex-frame": true, // skip pagex content filter
                    }, function (data) {
                        // use jQuery html() to run inline script if presented
                        jQuery(that.currentElement.querySelector('.element-wrap')).html(data);

                        window.dispatchEvent(new CustomEvent('pagexElementUpdated', {
                            detail: {
                                el: that.currentElement,
                                formData: form
                            }
                        }));
                        that.currentElement.classList.remove('pagex-element-updating');
                    }).fail(function () {
                        console.error('Fail to update the element');
                    });
                }
            }
        }

        // add custom class
        let cClass = form.pagex_custom_class.trim(),
            oldClass = _.isUndefined(oldParams) ? '' : _.filter(_.map(oldParams.pagex_custom_class.split(" "), function (v) {
                return _.escape(v.trim());
            }), function (v) {
                return v.length
            });

        if (cClass.length) {
            if (oldClass.length) {
                this.currentElement.classList.remove(...oldClass);
            }
            cClass = _.filter(_.map(cClass.split(" "), function (v) {
                return _.escape(v.trim())
            }), function (v) {
                return v.length
            });
            this.currentElement.classList.add(...cClass);
        }

        if (oldClass.length && !cClass.length) {
            this.currentElement.classList.remove(...oldClass);
        }

        // add custom id
        let customId = _.escape(form.pagex_custom_id.replace(new RegExp(' ', 'g'), ''));

        if (customId.length) {
            this.currentElement.id = customId;
        } else {
            this.currentElement.removeAttribute('id');
        }

        // section shape divider changed
        let isShapeChanged = !_.isNull(currentParamName) && ['pagex_shape_top'].includes(currentParamName);
        if (isShapeChanged) {
            let topShape = this.currentElement.querySelector(':scope > .pagex-shape-top'),
                topType = form.pagex_shape_top;
            if (topType === '' && topShape !== null) {
                topShape.remove();
            } else if (topType && topShape) {
                topShape.innerHTML = '<svg><use xlink:href="#' + topType + '"></use></svg>';
            } else if (topType && topShape === null) {
                this.currentElement.insertAdjacentHTML('afterbegin', '<div class="pagex-shape pagex-shape-top"><svg><use xlink:href="#' + topType + '"></use></svg></div>');
            }
        }

        let isShapeBottomChanged = !_.isNull(currentParamName) && ['pagex_shape_bottom'].includes(currentParamName);
        if (isShapeBottomChanged) {
            let topShape = this.currentElement.querySelector(':scope > .pagex-shape-bottom'),
                topType = form.pagex_shape_bottom;
            if (topType === '' && topShape !== null) {
                topShape.remove();
            } else if (topType && topShape) {
                topShape.innerHTML = '<svg><use xlink:href="#' + topType + '"></use></svg>';
            } else if (topType && topShape === null) {
                this.currentElement.insertAdjacentHTML('afterbegin', '<div class="pagex-shape pagex-shape-bottom"><svg><use xlink:href="#' + topType + '"></use></svg></div>');
            }
        }


        // change element bg only when any related bg params changed
        let isBgParamChanged = !_.isNull(currentParamName) && ['pagex_background', 'pagex_background_ov', 'pagex_video_url', 'pagex_video_start', 'pagex_video_end', 'pagex_background_svg:skip', 'pagex_dynamic_background'].includes(currentParamName);

        if (isBgParamChanged) {
            // design background image, video
            let currentBg = this.currentElement.querySelector(':scope > .pagex-bc'),
                bgType = form.pagex_background,
                bgOverlay = form.pagex_background_ov,
                bgVideo = form.pagex_video_url.trim(),
                bgContent = '';

            if (currentBg !== null) {
                currentBg.remove();
            }

            switch (bgType) {
                case 'image':
                    bgContent += '<div class="pagex-image-bg"></div>';
                    break;
                case 'svg':
                    // since it is content action get val manually
                    let bgSVG = this.paramsForm.querySelector('[name="pagex_background_svg:skip"]');
                    if (bgSVG) {
                        bgContent += '<div class="pagex-svg-bg">' + bgSVG.value + '</div>';
                    }
                    break;
                case 'dynamic':
                    if (form.pagex_dynamic_background.length) {
                        bgContent += '<div class="pagex-image-bg" data-dynamic-background="' + form.pagex_dynamic_background + '"><div class="pagex-dynamic-image-bg"></div></div>';

                        setTimeout(function () {
                            jQuery.post(pagexLocalize.ajaxUrl, {
                                action: 'pagex_dynamic_background',
                                query_string: pagexLocalize.query_string,
                                post_id: pagexLocalize.post_id,
                                atts: {key: form.pagex_dynamic_background},
                            }, function (data) {
                                that.currentElement.querySelector('.pagex-image-bg').innerHTML = data;
                            });
                        }, 500);
                    }
                    break;
                case 'video':
                    if (bgVideo.length) {
                        if (bgVideo.includes('youtube')) {
                            let nID = this.genID(),
                                timeLine = encodeURIComponent(JSON.stringify({
                                    start: form.pagex_video_start,
                                    end: form.pagex_video_end,
                                }));
                            bgContent += '<div class="pagex-video-bg pagex-video-bg-youtube" data-video-bg="' + bgVideo + '" data-video-timeline="' + timeLine + '"><div class="pagex-video-youtube" id="' + nID + '"></div></div>';

                            // render youtube background
                            setTimeout(function () {
                                renderYouTubeIframe(bgVideo, nID, timeLine);
                            }, 1500);
                        } else {
                            bgContent += '<div class="pagex-video-bg"><video class="pagex-video-hosted" autoplay loop muted src="' + bgVideo + '"></video></div>';
                        }
                    }
                    break;
            }

            if (bgContent.length || bgOverlay) {
                this.currentElement.insertAdjacentHTML('afterbegin', '<div class="pagex-bc"><div class="pagex-bc-wrapper"><div class="pagex-image-bg-ov"></div>' + bgContent + '</div></div>');
            }
        }

        // create slider data attribute based on form value layout == pagex_slider
        if (!_.isUndefined(form.layout)) {
            if (form.layout === 'pagex_slider') {
                let sliderData = this.genSliderData(form);
                this.currentElement.querySelector('.element-wrap').setAttribute('data-slider', sliderData);
            } else {
                this.currentElement.querySelector('.element-wrap').removeAttribute('data-slider');
            }
        }


        if (!_.isNull(currentParamName)) {
            // remove all attributes when section position is changed
            if (currentParamName === 'pagex_section_pos') {
                if (form.pagex_section_pos === 'pagex-section-position-fixed' || (!_.isUndefined(oldParams) && !_.isUndefined(oldParams.pagex_section_pos) && oldParams.pagex_section_pos === 'pagex-section-position-fixed')) {
                    if (typeof pagexUtils !== "undefined") {
                        setTimeout(function () {
                            pagexUtils.refreshWaypoint();
                        }, 1500);
                    }
                }
            }

            // change element entrance animation
            if (currentParamName === 'pagex_ea' || currentParamName === 'pagex_ea_delay' || currentParamName === 'pagex_ea_duration') {
                this.currentElement.classList.remove('pagex-animated');
                if (typeof pagexUtils !== "undefined") {
                    pagexUtils.refreshWaypoint();
                }
            }

            // remove inline style if parallax for background was turned off
            if (currentParamName === 'pagex_background_parallax' && !form.pagex_background_parallax.length) {
                this.currentElement.querySelector('.pagex-bc-wrapper').removeAttribute('style');
            }

            // remove inline style if parallax for element was turned off
            if (currentParamName === 'pagex_parallax' && !form.pagex_parallax.length) {
                this.currentElement.removeAttribute('style');
            }

            // add custom the whole link data to el
            if (currentParamName.includes('pagex_link')) {
                if (form['pagex_link'] || form['pagex_link-dynamic']) {
                    this.currentElement.setAttribute('data-custom-link', encodeURIComponent(form['pagex_link']));
                } else {
                    this.currentElement.removeAttribute('data-custom-link');
                }
            }
        }


        // create style and append to element
        let extraSmall = '',
            small = '',
            medium = '',
            large = '',
            extraLarge = '',
            allStyleRules = '';

        // find all custom actions like "class", "css" in params and add them to a element
        _.forEach(this.currentElementFormParams, function (param) {
            if (!param.action) return;

            let optionValue = form[param.id],
                selector = param.selector,
                cssRule = '';

            if (_.isUndefined(optionValue)) {
                optionValue = '';
            }

            if (param.action === 'class') {
                // remove class from a selector if it is not checked
                if (that.currentParam !== null) {
                    // if option is checkbox
                    if (that.currentParam.getAttribute('type') === 'checkbox') {
                        if (that.currentParam.checked !== true) {
                            if (selector === '[el]') {
                                that.currentElement.classList.remove(that.currentParam.value);
                            } else {
                                let item = that.currentElement.querySelector(selector);
                                if (!_.isNull(item)) {
                                    item.classList.remove(that.currentParam.value);
                                }
                            }
                        }
                    } else {
                        _.forEach(that.currentParam.options, function (v) {
                            //v.value = v.value.trim(); // comment due to issue with space before select option value (ex. font weight)
                            // do not check empty values
                            if (!v.value.trim().length) return;
                            // check only values of currently changed param
                            if (that.currentParam.getAttribute('name').indexOf(param.id) === -1) return;
                            // if we apply class to parent element we need to remove all classes from select values
                            if (selector === '[el]') {
                                that.currentElement.classList.remove(v.value);
                            } else {
                                if (_.isUndefined(param.scope)) {
                                    for (let item of that.currentElement.querySelectorAll(selector)) {
                                        item.classList.remove(v.value);
                                    }
                                } else {
                                    let item = that.currentElement.querySelector(selector);
                                    if (!_.isNull(item)) {
                                        item.classList.remove(v.value);
                                    }
                                }
                            }
                        });
                    }
                }

                // apply selected class to a selector
                if (selector === '[el]') {
                    // if select control is responsive
                    if (_.isObject(optionValue)) {
                        _.forEach(_.compact(Object.values(optionValue)), function (v) {
                            that.currentElement.classList.add(v);
                        });
                    } else {
                        if (optionValue.length) {
                            that.currentElement.classList.add(optionValue);
                        }
                    }
                } else if (optionValue !== undefined) {
                    if (_.isObject(optionValue)) {
                        let arrClassName = _.compact(optionValue);
                        if (arrClassName.length) {
                            arrClassName.forEach(function (classValue) {
                                // class can be added to all inner selectors if scope is undefined
                                if (_.isUndefined(param.scope)) {
                                    for (let item of that.currentElement.querySelectorAll(selector)) {
                                        item.classList.add(classValue);
                                    }
                                } else {
                                    let item = that.currentElement.querySelector(selector);
                                    if (!_.isNull(item)) {
                                        item.classList.add(classValue);
                                    }
                                }
                            });
                        }
                    } else if (optionValue.length) {
                        if (_.isUndefined(param.scope)) {
                            for (let item of that.currentElement.querySelectorAll(selector)) {
                                item.classList.add(optionValue);
                            }
                        } else {
                            let item = that.currentElement.querySelector(selector);
                            if (!_.isNull(item)) {
                                item.classList.add(optionValue);
                            }
                        }
                    }
                }
            }

            if (param.action === 'css') {
                // check if it is a responsive option
                if (_.isObject(optionValue)) {
                    _.forEach(optionValue, function (responsiveValue, pref) {
                        cssRule = '';
                        if (_.isObject(responsiveValue)) {
                            // if it is a dimension
                            let dimOp = [];

                            ['top', 'right', 'bottom', 'left'].forEach((v) => {
                                if (responsiveValue[v].length) dimOp.push(selector + '{' + param.property + '-' + v + ': ' + that.parseCssNumber(responsiveValue[v]) + '}');
                            });

                            cssRule = dimOp.join('');
                        } else if (responsiveValue.length) {
                            // do not parse css value if it is a select or number input type
                            if (param.type === 'select' || param.type === 'number') {
                                cssRule = selector.replace(new RegExp('\\[val\\]', 'g'), responsiveValue.trim());
                            } else {
                                cssRule = selector.replace(new RegExp('\\[val\\]', 'g'), that.parseCssNumber(responsiveValue));
                            }
                        }

                        switch (pref) {
                            case "xs":
                                extraSmall += cssRule;
                                break;
                            case "sm":
                                small += cssRule;
                                break;
                            case "md":
                                medium += cssRule;
                                break;
                            case "lg":
                                large += cssRule;
                                break;
                            case "xl":
                                extraLarge += cssRule;
                                break;
                            default:
                                console.error('Responsive value is not defined');
                        }
                    });
                } else if (optionValue.length) {
                    // do not parse css value if it is a select
                    if (param.type === 'select' || param.type === 'number') {
                        cssRule = selector.replace(new RegExp('\\[val\\]', 'g'), optionValue.trim());
                    } else {
                        cssRule = selector.replace(new RegExp('\\[val\\]', 'g'), that.parseCssNumber(optionValue));
                    }

                    allStyleRules += cssRule;
                }
            }

            if (param.action === 'data') {
                if (selector === '[el]') {
                    if (optionValue.length) {
                        that.currentElement.setAttribute('data-' + param.attribute, optionValue);
                    } else {
                        that.currentElement.removeAttribute('data-' + param.attribute);
                    }
                } else {
                    let toAttr = that.currentElement.querySelector(selector);

                    if (!_.isNull(toAttr)) {
                        if (optionValue.length) {
                            toAttr.setAttribute('data-' + param.attribute, optionValue);
                        } else {
                            toAttr.removeAttribute('data-' + param.attribute);
                        }
                    }
                }
            }

            // add Google font link if font family is selected
            if (param.id.indexOf('font_family') !== -1) {
                let paramEl = that.paramsForm.querySelector('[name="' + param.id + '"]'),
                    prefixName = param.id.slice(0, -12),
                    weightParam = that.paramsForm.querySelector('[name="' + prefixName + '_font_weight"]').value.trim(),
                    fontType = paramEl.options[paramEl.selectedIndex].parentNode.label,
                    oldSelector = that.currentElement.querySelector('.pagex-google-font-' + param.id);

                if (!_.isNull(oldSelector)) {
                    oldSelector.remove();
                }

                if (!_.isUndefined(fontType) && fontType === "Google") {
                    let googleFont = encodeURIComponent(optionValue),
                        fontWeight = weightParam ? ':' + weightParam : '',
                        fontSubset = '&subset=' + pagexLocalize.settings.subsets.join(','),
                        googleHref = 'https://fonts.googleapis.com/css?family=' + googleFont + fontWeight + fontSubset;

                    that.currentElement.insertAdjacentHTML('afterbegin', '<link class="pagex-google-font pagex-google-font-' + param.id + '" href="' + googleHref + '" rel="stylesheet">');
                }
            }
        });

        let oldStyle = document.getElementById(elId);
        if (!_.isNull(oldStyle)) {
            oldStyle.remove();
        }

        if (extraSmall.length) allStyleRules += extraSmall;
        if (small.length) allStyleRules += '@media (min-width: 576px) {' + small + '}';
        if (medium.length) allStyleRules += '@media (min-width: 768px) {' + medium + '}';
        if (large.length) allStyleRules += '@media (min-width: 992px) {' + large + '}';
        if (extraLarge.length) allStyleRules += '@media (min-width: 1200px) {' + extraLarge + '}';

        allStyleRules = allStyleRules.replace(new RegExp('\\[el\\]', 'g'), '[data-id="' + elId + '"]');

        if (allStyleRules.length) {
            // backend with pagexstyle to avoid issues with default style
            if (this.postContentArea !== null) {
                allStyleRules = '/*pagexstyle ' + allStyleRules + ' pagexstyle*/';
            }

            this.currentElement.insertAdjacentHTML('afterbegin', '<style id="' + elId + '">' + allStyleRules + '</style>');
        }

        // do not store params with empty values
        Object.keys(form).forEach(function (prop) {
            // do no remove class param since we need to keep previous value
            if (prop === 'pagex_custom_class') return;

            // encode svg field for dynamic elements
            if (prop.substr(-4) === '_svg') {
                form[prop] = encodeURIComponent(form[prop]);
            }

            if (_.isObject(form[prop])) {
                let notEmpty = false;
                Object.keys(form[prop]).forEach((propVal) => {
                    // for responsive params
                    if (_.isObject(form[prop][propVal])) {
                        // for responsive group params like margin or padding
                        Object.keys(form[prop][propVal]).forEach((propValRes) => {
                            if (form[prop][propVal][propValRes].length) {
                                notEmpty = true;

                                // encode svg field for dynamic elements inside repeater
                                if (propValRes.substr(-4) === '_svg') {
                                    form[prop][propVal][propValRes] = encodeURIComponent(form[prop][propVal][propValRes]);
                                }
                            }
                        });
                    } else if (form[prop][propVal].length) {
                        notEmpty = true;
                    }
                });

                if (!notEmpty) {
                    delete form[prop];
                }
                return;
            }

            if (!form[prop].length) {
                delete form[prop];
            }
        });

        // store all form values with element id
        pagexLocalize.all_params[elId] = form;

        // update post content for backend
        this.updatePostContent();

        // create window event to init/trigger frontend scripts
        // only for static elements
        // event will trigger by dynamic elements when content is updated
        if (!dynamicElementUpdate) {
            window.dispatchEvent(new CustomEvent('pagexElementUpdated', {
                detail: {
                    el: this.currentElement,
                    formData: form
                }
            }));
        }

        // make sure to hide undo button
        window.parent.document.body.classList.remove('pagex-element-removed');
    },

    updatePostContent: function () {
        let that = this;

        if (this.postContentArea !== null) {
            this.postContentArea.value = document.getElementById('pagex-backend-content').innerHTML;
            this.postElementsParamsArea.value = JSON.stringify(pagexLocalize.all_params);

            // update for excerpt preview
            if (this.excerptPreviewArea !== null) {
                that.excerptPreviewArea.classList.add('pagex-preview-loading');
                pagexDelay(function () {
                    jQuery.post(pagexLocalize.ajaxUrl, {
                        action: 'pagex_excerpt_preview',
                        content: that.postContentArea.value,
                        post_type: document.getElementById('pagex-excerpt-preview-post-type').value,
                        post_id: document.getElementById('pagex-excerpt-preview-post-id').value,
                        pagex_elements_params: JSON.stringify(pagexLocalize.all_params),
                        "pagex-excerpt-preview": true // skip pagex filter content function
                    }, function (data) {
                        let innerDoc = that.excerptPreviewArea.contentDocument || that.excerptPreviewArea.contentWindow.document,
                            page = innerDoc.querySelector('#page');

                        that.excerptPreviewArea.contentWindow.pagexUtils.refreshWaypoint();

                        page.innerHTML = data;

                        setTimeout(function () {
                            that.excerptPreviewArea.height = innerDoc.body.scrollHeight;
                        }, 1000);

                        that.excerptPreviewArea.classList.remove('pagex-preview-loading');
                    }).fail(function () {
                        console.error("Fail to update excerpt preview");
                    });
                }, 1000);
            }
        } else {
            window.parent.document.body.classList.add('pagex-layout-changed');
        }
    },

    setFormConditions: function (e) {
        let repeater = e.closest('.pagex-repeater-item'),
            elements = repeater === null ? this.paramsForm.querySelectorAll('[data-condition]:not(.pagex-control-repeater-wrapper)') : repeater.querySelectorAll('[data-condition]'),
            that = this;

        _.forEach(elements, function (v) {
            var condData = v.getAttribute('data-condition'),
                cond = JSON.parse(condData),
                hide = false;

            _.forEach(cond, function (condVal, condKey) {
                let isReversed = condKey.charAt(0) === '!';
                condKey = isReversed ? condKey.substr(1) : condKey;

                let pVal = _.isNull(repeater) ? that.paramsForm.querySelector('[name="' + condKey + '"]') : repeater.querySelector('[name="' + condKey + '"]');

                if (pVal.type && pVal.type === 'checkbox') {
                    pVal = pVal.checked ? 'true' : 'false';
                } else {
                    pVal = pVal.value;
                }

                if (hide) return;

                if (isReversed) {
                    if (!condVal.includes(pVal)) {
                        hide = false;
                    } else {
                        hide = true;
                    }
                } else {
                    if (condVal.includes(pVal)) {
                        hide = false;
                    } else {
                        hide = true;
                    }
                }
            });

            if (hide) {
                v.style.display = 'none';
            } else {
                v.style = '';
            }

        });
    },

    fillLinkUrl: function (e) {
        let link = e.target.closest('.form-group'),
            inputs = link.querySelectorAll('.pagex-link-control-field'),
            urlStr = {};

        inputs.forEach(function (input) {
            if (input.value === '') return;

            if (input.matches('.pagex-link-control-href'))
                urlStr.href = _.escape(input.value);

            if (input.matches('.pagex-link-control-onclick')) {
                urlStr.onclick = _.escape(input.value);
                if (urlStr.href === undefined) {
                    urlStr.href = 'javascript:void(0);';
                }
            }

            if (input.matches('.pagex-link-control-title'))
                urlStr.title = _.escape(input.value);

            if (input.matches('.pagex-link-control-dynamic')) {
                urlStr['data-dynamic-link'] = input.value;
                urlStr.href = input.value;
            }

            if (input.matches('.pagex-link-control-rel') && input.checked === true)
                urlStr.rel = 'nofollow';

            if (input.matches('.pagex-link-control-target') && input.checked === true)
                urlStr.target = '_blank';

        });

        if (urlStr.href === undefined) {
            link.querySelector('.pagex-link-control').value = '';
        } else {
            link.querySelector('.pagex-link-control').value = Object.keys(urlStr).map(function (key) {
                return key + '="' + urlStr[key] + '"';
            }).join(' ');
        }
    },

    searchForLink: function (e) {
        let link = e.target.closest('.form-group'),
            input = link.querySelector('.pagex-link-control-href').value;

        if (!input.length) return;

        jQuery.post(pagexLocalize.ajaxUrl, {
            action: 'pagex_link_search_callback',
            search: input,
        }, function (data) {
            link.querySelector('.pagex-link-search-results').innerHTML = data;
        }).fail(function () {
            alert("error");
        });
    },

    insertLink: function (e) {
        let url = e.target.getAttribute('data-link-url'),
            link = e.target.closest('.form-group'),
            input = link.querySelector('.pagex-link-control-href');

        input.value = url;
        this.fillLinkUrl(e);
        this.renderElement();

        link.querySelector('.pagex-link-search-results').innerHTML = '';
    },

    showLinkAttrs: function (e) {
        let linkId = e.target.closest('.form-group'),
            div = linkId.querySelector('.pagex-link-attrs');

        div.classList.toggle('pagex-hide');
    },

    switchDevices: function (el, _device = null) {
        let device = _device ? _device : el.getAttribute('data-device-switcher');

        if (device === 'all') {
            let formGroup = _device ? el.closest('.form-group') : this.currentSwitcher.closest('.form-group');
            formGroup.classList.toggle('pagex-responsive-switcher-all');
            this.switchersControl.classList.add('pagex-hide');
            return;
        }

        let buttons = this.paramsForm.querySelectorAll('.pagex-responsive-switcher-button'),
            pref = ['default', 'xs', 'sm', 'md', 'lg', 'xl'];

        this.currentDevice = device;

        for (let item of buttons) {
            item.innerHTML = el.innerHTML
        }

        for (let item of this.paramsForm.querySelectorAll('.pagex-responsive-params')) {
            item.classList.remove('active');
        }

        for (let item of this.paramsForm.querySelectorAll('.pagex-device-' + device)) {
            item.classList.add('active');
        }

        // remove classes
        pref.forEach(function (v) {
            window.parent.document.body.classList.remove('pagex-device-preview-' + v);
        });

        window.parent.document.body.classList.add('pagex-device-preview-' + device);

        for (let item of window.parent.document.querySelectorAll('#pagex-settings [data-device-switcher]')) {
            item.classList.remove('active');
        }
        window.parent.document.querySelector('#pagex-settings [data-device-switcher="' + device + '"]').classList.add('active');

        for (let item of this.switchersControl.querySelectorAll('.pagex-device-switcher.active')) {
            item.classList.remove('active');
        }

        this.switchersControl.querySelector('[data-device-switcher="' + device + '"]').classList.add('active');

        this.switchersControl.classList.add('pagex-hide');

        // make sure slider init right positioning
        setTimeout(function () {
            window.dispatchEvent(new Event("resize"));
        }, 400);
    },

    showSwitcher: function (e) {
        this.currentSwitcher = e.target;
        this.switchersOptions.style.top = e.clientY + 'px';
        this.switchersOptions.style.left = e.clientX + 'px';
        this.switchersControl.classList.remove('pagex-hide');
    },

    hideSwitcher: function (e) {
        this.switchersControl.classList.add('pagex-hide');
    },

    switchTabsForm: function (e) {
        let index = Array.from(e.target.parentNode.children).indexOf(e.target),
            tabs = e.target.closest('.pagex-params-modal');

        for (var item of tabs.querySelectorAll('.pagex-params-tab-title')) {
            item.classList.remove('active');
        }

        for (var item of tabs.querySelectorAll('.pagex-params-tab-content')) {
            item.classList.add('pagex-hide');
        }

        e.target.classList.add('active');
        tabs.querySelectorAll('.pagex-params-tab-content')[index].classList.remove('pagex-hide');

        // load library layouts
        if (e.target.matches('.pagex-layouts-modal-layouts') && !e.target.matches('.loaded')) {
            e.target.classList.add('loaded');
            this.loadLibraryLayouts('layouts');
        }

        if (e.target.matches('.pagex-layouts-modal-templates') && !e.target.matches('.loaded')) {
            e.target.classList.add('loaded');
            this.loadLibraryLayouts('templates');
        }

        if (e.target.matches('.pagex-layouts-modal-excerpts') && !e.target.matches('.loaded')) {
            e.target.classList.add('loaded');
            this.loadLibraryLayouts('excerpts');
        }
    },

    loadLibraryLayouts: function (type) {
        jQuery.post(pagexLocalize.ajaxUrl, {
            action: 'pagex_get_layouts_from_library',
            type: type,
        }, function (response) {
            window.parent.document.querySelector('.pagex-layouts-modal-content-' + type).innerHTML = response.data.content;
        }).fail(function () {
            alert('error');
        });
    },

    filterLibraryLayout: function (value) {
        let area = window.parent.document.querySelectorAll('.pagex-params-tab-content:not(.pagex-hide) [data-library-cat]');

        for (let item of area) {
            item.classList.remove('pagex-hide');
        }

        if (value !== '') {
            for (let item of area) {
                if (item.getAttribute('data-library-cat') !== value) {
                    item.classList.add('pagex-hide');
                }
            }
        }
    },

    insertImage: function (e) {
        let imageForm = e.target.closest('.pagex-image-control'),
            inputUrl = imageForm.querySelector('.pagex-image-url'),
            prevImg = e.target,
            inputData = imageForm.querySelector('.pagex-image-data'),
            that = this;

        let file_frame_img = window.parent.wp.media.frames.downloadable_file = window.parent.wp.media({
            multiple: false
        });

        file_frame_img.open();

        file_frame_img.on('select', function () {
            let attachment = file_frame_img.state().get('selection').first().toJSON(),
                data = JSON.stringify({
                    id: attachment.id,
                    alt: attachment.alt,
                    caption: attachment.caption,
                    sizes: attachment.sizes,
                });

            inputData.value = data;
            inputUrl.value = attachment.url;
            prevImg.style.backgroundImage = "url('" + attachment.url + "')";
            that.currentParam = inputUrl;
            that.renderElement();
        });
    },

    deleteImage: function (e) {
        let control = e.target.closest('.pagex-image-control');

        control.querySelector('.pagex-image-url').value = '';
        control.querySelector('.pagex-image-data').value = '';
        control.querySelector('.pagex-image-placeholder').removeAttribute('style');

        this.renderElement();
    },

    insertURL: function (e) {
        let urlForm = e.target.closest('.input-group'),
            urlInput = urlForm.querySelector('.pagex-control-url'),
            that = this;

        let file_frame_img = window.parent.wp.media.frames.downloadable_file = window.parent.wp.media({
            multiple: false,
            library: {
                type: ['video']
            },
        });

        file_frame_img.open();

        file_frame_img.on('select', function () {
            let attachment = file_frame_img.state().get('selection').first().toJSON();
            urlInput.value = attachment.url;
            that.renderElement();
        });
    },

    changeImageUrlSize: function (e) {
        let imageForm = e.target.closest('.pagex-image-control'),
            inputSizes = imageForm.querySelector('.pagex-image-data').value,
            inputUrl = imageForm.querySelector('.pagex-image-url'),
            valSize = e.target.value;

        if (!inputSizes.length) return;

        inputSizes = JSON.parse(inputSizes);
        let newUrl = inputSizes.sizes[valSize];

        if (_.isUndefined(newUrl)) return;

        inputUrl.value = newUrl.url;
    },

    renderSearchElement: function (e) {
        if (e.keyCode === 13) {
            this.searchResults.querySelector('#pagex-search-elements-result .pagex-elements-item').click();
            return;
        }

        let term = e.target.value.toLowerCase().trim();
        if (!term.length) {
            this.searchTitle.classList.remove('active');
            this.allElementsModal.querySelectorAll('.pagex-params-tab-title')[1].classList.add('active');
            for (let item of this.allElementsModal.querySelectorAll('.pagex-params-tab-content')) {
                item.classList.add('pagex-hide');
            }
            this.allElementsModal.querySelectorAll('.pagex-params-tab-content')[1].classList.remove('pagex-hide');
            return;
        }

        this.allElementsModal.querySelector('.pagex-params-tab-title.active').classList.remove('active');

        for (let item of this.allElementsModal.querySelectorAll('.pagex-params-tab-content')) {
            item.classList.add('pagex-hide');
        }

        this.searchResults.classList.remove('pagex-hide');
        this.searchTitle.classList.add('active');

        let results = _.filter(pagexElements, function (v) {
            return (v.title.toLowerCase().includes(term) || (!_.isUndefined(v.description) && v.description.toLowerCase().includes(term))) && !_.isUndefined(v.category)
        });

        this.searchResults.innerHTML = '';

        if (results.length) {
            var that = this;
            results.forEach(function (element) {
                that.searchResults.innerHTML += '<div class="pagex-elements-item trn-300 mr-2 mb-2 p-3" data-element-item-id="' + element.id + '"><h5>' + element.title + '</h5><small>' + element.description + '</small></div>';
            });
        }
    },

    switchSectionOptions: function (e) {
        for (var item of document.querySelectorAll('.pagex-section-set')) {
            item.classList.remove('active');
        }

        e.target.classList.add('active');
    },

    appendNewElement: function (e) {
        let element = e.target.getAttribute('data-add');

        // open all elements modal if we add new custom element
        if (element === 'element') {
            this.prependElement = e.target.matches('.prepend');
            let searchInput = this.allElementsModal.querySelector('#pagex-search-elements');
            this.currentElement = e.target.closest('[data-type="column"]');
            this.pagexModal.classList.add('pagex-hide');
            this.allElementsModal.classList.remove('pagex-hide');

            // reset search
            searchInput.value = '';
            searchInput.focus();

            // reset tab select
            for (let item of this.allElementsModal.querySelectorAll('.pagex-params-tab-title')) {
                item.classList.remove('active');
            }

            this.allElementsModal.querySelectorAll('.pagex-params-tab-title')[1].classList.add('active');

            for (let item of this.allElementsModal.querySelectorAll('.pagex-params-tab-content')) {
                item.classList.add('pagex-hide');
            }

            this.allElementsModal.querySelectorAll('.pagex-params-tab-content')[1].classList.remove('pagex-hide');

            return;
        }

        // open all layouts modal if we add new layout
        if (element === 'layout') {
            this.pagexModal.classList.add('pagex-hide');
            this.layoutsModal.classList.remove('pagex-hide');
            this.currentSection = e.target.closest('[data-type="section"]');
            return;
        }

        let template = _.template(document.getElementById('pagex-element-' + element + '-template').innerHTML),
            divDom = document.createElement('div');

        divDom.innerHTML = template();

        if (element === 'column') {
            // including standard row and inner row
            e.target.closest('.row').appendChild(divDom.firstChild);
        } else if (element === 'row') {
            e.target.closest('[data-type="container"]').appendChild(divDom.firstChild);
        } else if (element === 'container') {
            e.target.closest('[data-type="section"]').appendChild(divDom.firstChild);
        } else if (element === 'section') {
            e.target.closest('[data-type="section"]').parentElement.insertBefore(divDom.firstChild, e.target.closest('[data-type="section"]').nextSibling);
        }

        this.elementSortable();
    },

    appendItemElement: function (e) {
        this.currentParam = null;

        let elSettings = _.find(pagexElements, {id: e.target.getAttribute('data-element-item-id')}),
            divDom = document.createElement('div');

        if (elSettings.id === 'inner-row') {
            this.currentElementTemplate = _.template(document.getElementById('pagex-element-inner-row-template').innerHTML);
            divDom.innerHTML = this.currentElementTemplate();
        } else {
            divDom.innerHTML = this.element_start({data: elSettings}) + this.element_end();
        }

        // currentElement is a column
        if (this.prependElement) {
            this.currentElement.insertAdjacentElement('afterbegin', divDom.firstChild);
        } else {
            this.currentElement.appendChild(divDom.firstChild);
        }

        if (elSettings.id !== 'inner-row') {
            // change current element to added one
            if (this.prependElement) {
                this.currentElement = this.currentElement.querySelector(':scope > .element:first-child');
            } else {
                this.currentElement = this.currentElement.querySelector(':scope > .element:last-child');
            }

            this.renderForm();
            this.renderElement();
        }

        this.allElementsModal.classList.add('pagex-hide');
    },

    toggleSectionOptions: function (el) {
        el.closest('.pagex-options').classList.toggle('pagex-hide-options-set');
    },

    cloneElement: function (e) {
        let original = e.target.getAttribute('data-clone') === 'element' ? e.target.closest('.element') : e.target.closest('[data-type="' + e.target.getAttribute('data-clone') + '"]'),
            originalId = original.getAttribute('data-id'),
            cloned = original.cloneNode(true),
            newId = this.genID(),
            style = cloned.querySelector('#' + originalId),
            inParam = pagexLocalize.all_params[originalId];

        // remove builder options from cloned item
        for (let item of cloned.querySelectorAll('.pagex-options')) {
            item.remove();
        }

        cloned.setAttribute('data-id', newId);

        if (!_.isNull(style)) {
            style.id = newId;
            style.innerHTML = style.innerHTML.replace(new RegExp(originalId, 'g'), newId);
        }

        if (!_.isUndefined(inParam)) {
            pagexLocalize.all_params[newId] = inParam;
        }

        // replace data-id by new one for all children
        cloned = this.cloneNewIds(cloned);

        original.after(cloned);

        this.updatePostContent();
    },

    cloneNewIds: function (cloned) {
        for (let item of cloned.querySelectorAll('[data-type]')) {
            let oldId = item.getAttribute('data-id'),
                newId = this.genID(),
                inParam = pagexLocalize.all_params[oldId],
                style = item.querySelector('#' + oldId),
                modal = item.querySelector('[data-id="' + oldId + '"]');

            if (!_.isNull(style)) {
                style.id = newId;
                style.innerHTML = style.innerHTML.replace(new RegExp(oldId, 'g'), newId);
            }

            if (!_.isNull(modal)) {
                modal.setAttribute('data-id', newId);
            }

            item.setAttribute('data-id', newId);

            if (!_.isUndefined(inParam)) {
                pagexLocalize.all_params[newId] = inParam;
            }
        }

        return cloned;
    },


    removeElement: function (e) {
        // close settings modal
        this.pagexModal.classList.add('pagex-hide');

        let modal = e.target.closest('.pagex-modal'),
            html = modal ? modal.innerHTML : document.querySelector('.pagex-builder-area').innerHTML;

        // save current data before deleting
        localStorage.setItem('pagexHistory', JSON.stringify({
            html: html,
            params: pagexLocalize.all_params,
            isModal: !_.isNull(modal)
        }));

        let type = e.target.getAttribute('data-remove'),
            el = type === 'element' ? e.target.closest('.element') : e.target.closest('[data-type="' + type + '"]'),
            elId = el.getAttribute('data-id'),
            inParam = pagexLocalize.all_params[elId];

        if (!_.isUndefined(inParam)) {
            delete pagexLocalize.all_params[elId];
        }

        for (let item of el.querySelectorAll('[data-id]')) {
            let elId = item.getAttribute('data-id'),
                inParam = pagexLocalize.all_params[elId];

            if (!_.isUndefined(inParam)) {
                delete pagexLocalize.all_params[elId];
            }
        }

        el.remove();

        // make sure that we still have at least one row
        this.validateEmptyElements();

        // make sure we have empty col with no builder option
        for (var item of document.querySelectorAll('.pagex-options')) {
            item.remove();
        }

        window.parent.document.body.classList.add('pagex-element-removed');

        // make sure we restore option buttons
        document.body.classList.remove('pagex-hide-not-element-options');
    },

    undoRemove: function () {
        window.parent.document.body.classList.remove('pagex-element-removed');
        let history = localStorage.getItem('pagexHistory');

        if (history !== null) {
            let data = JSON.parse(history);

            if (data.isModal) {
                document.querySelector('.pagex-modal-show:last-child').innerHTML = data.html;
            } else {
                document.querySelector('.pagex-builder-area').innerHTML = data.html;
            }

            pagexLocalize.all_params = data.params;

            this.elementSortable();
        }
    },

    validateEmptyElements: function () {
        let section = _.template(document.getElementById('pagex-element-section-template').innerHTML),
            container = _.template(document.getElementById('pagex-element-container-template').innerHTML),
            row = _.template(document.getElementById('pagex-element-row-template').innerHTML),
            innerRow = _.template(document.getElementById('pagex-element-inner-row-template').innerHTML),
            column = _.template(document.getElementById('pagex-element-column-template').innerHTML),
            area = document.querySelector('.pagex-builder-area');

        if (!area.querySelectorAll('[data-type="section"]').length) {
            area.insertAdjacentHTML('beforeend', section());
        }

        for (var item of document.querySelectorAll('[data-type="section"]')) {
            if (!item.querySelectorAll('[data-type="container"]').length) {
                item.insertAdjacentHTML('beforeend', container());
            }
        }

        for (var item of document.querySelectorAll('[data-type="container"]')) {
            if (!item.querySelectorAll('[data-type="row"]').length) {
                item.insertAdjacentHTML('beforeend', row());
            }
        }

        for (var item of document.querySelectorAll('[data-type="row"]')) {
            if (!item.querySelectorAll('[data-type="column"]').length) {
                item.insertAdjacentHTML('beforeend', column());
            }
        }

        for (var item of document.querySelectorAll('[data-type="inner-row"]')) {
            if (!item.querySelectorAll('[data-type="column"]').length) {
                item.insertAdjacentHTML('beforeend', column());
            }
        }

        for (var item of document.querySelectorAll('.pagex-inner-row-holder')) {
            if (!item.querySelectorAll('[data-type="inner-row"]').length) {
                item.insertAdjacentHTML('beforeend', innerRow());
            }
        }

        this.elementSortable();
    },

    openSaveAsLayoutModal: function (el) {
        this.currentElement = el.closest('[data-type="section"]');
        this.saveLayoutsModal.classList.remove('pagex-hide');
    },

    saveAsLayout: function (el) {
        let section = this.currentElement.outerHTML,
            title = window.parent.document.getElementById('pagex-custom-layout-title').value,
            sectionID = this.currentElement.getAttribute(['data-id']),
            button_text = el.querySelector('span'),
            button_icon = el.querySelector('i'),
            section_params = {};

        button_text.innerHTML = pagexLocalize.string.saving;
        button_icon.className = 'fas fa-spinner fa-spin';

        if (!_.isUndefined(pagexLocalize.all_params[sectionID])) {
            section_params[sectionID] = pagexLocalize.all_params[sectionID];
        }

        for (let item of this.currentElement.querySelectorAll('[data-id]')) {
            let id = item.getAttribute(['data-id']);
            if (!_.isUndefined(pagexLocalize.all_params[id])) {
                section_params[id] = pagexLocalize.all_params[id];
            }
        }

        jQuery.post(pagexLocalize.ajaxUrl, {
            action: 'pagex_save_as_layout',
            pagex_post_content: section,
            title: title,
            pagex_elements_params: JSON.stringify(section_params),
            pagex_page_status: true // for save_post_data filter
        }, function (data) {
            if (data.error === true) {
                button_text.innerHTML = data.message;
            } else {
                button_icon.className = 'fas fa-check';
                button_text.innerHTML = data.message;
            }
        }).fail(function () {
            button_text.innerHTML = 'Error during saving the layout';
        });
    },

    elementSortable: function () {
        let that = this;

        let sortableObj = {
            placeholder: 'pagex-sortable-placeholder',
            connectWith: jQuery('[data-type="column"]'),
            handle: '.pagex-options',
            cancel: '',
            revert: true,
            appendTo: document.body,
            tolerance: 'pointer',
            cursorAt: {left: 20, top: 20},
            zIndex: 9999,
            start: function (event, ui) {
                ui.placeholder.addClass(ui.item.attr('class'));
                jQuery('body').addClass('pagex-sortable-start');
                ui.item.addClass('pagex-sortable-element-start');
            },
            helper: function () {
                return jQuery('<div class="pagex-sortable-helper"><i class="fas fa-arrows-alt"></i></div>');
            },
            stop: function (event, ui) {
                ui.item.removeClass('pagex-sortable-element-start');
                jQuery('body').removeClass('pagex-sortable-start');
                that.validateEmptyElements();
            },
        };

        // sortable elements
        jQuery('.pagex-builder-area > [data-type="section"] > [data-type="container"] > [data-type="row"] > [data-type="column"]').sortable(sortableObj);
        jQuery('.pagex-builder-area [data-type="inner-row"] [data-type="column"]').sortable(sortableObj);
        jQuery('.pagex-modal-builder-area [data-type="column"]').sortable(sortableObj);

        // sortable columns of the inner row
        let innercolumns = jQuery('.pagex-builder-area .row');
        sortableObj.handle = '.pagex-column-options';
        sortableObj.connectWith = innercolumns;
        innercolumns.sortable(sortableObj);

        // sortable columns of the inner row of modal windows
        let modalinnercolumns = jQuery('.pagex-modal-builder-area [data-type="inner-row"]');
        sortableObj.handle = '.pagex-column-options';
        sortableObj.connectWith = modalinnercolumns;
        modalinnercolumns.sortable(sortableObj);

        // sortable columns
        let columns = jQuery('.pagex-builder-area > [data-type="section"] > [data-type="container"] > [data-type="row"]');
        sortableObj.connectWith = columns;
        columns.sortable(sortableObj);

        // sortable rows
        let rows = jQuery('.pagex-builder-area > [data-type="section"] > [data-type="container"]');
        sortableObj.handle = '.pagex-row-options';
        sortableObj.connectWith = rows;
        rows.sortable(sortableObj);

        // sortable containers
        let containers = jQuery('.pagex-builder-area > [data-type="section"]');
        sortableObj.handle = '.pagex-container-options';
        sortableObj.connectWith = containers;
        containers.sortable(sortableObj);

        // sortable sections
        let sections = jQuery('.pagex-builder-area');
        sortableObj.handle = '.pagex-section-options';
        sortableObj.connectWith = sections;
        sections.sortable(sortableObj);

        // after new element added or content area validated
        this.updatePostContent();
    },

    repeaterSortable: function () {
        let that = this;

        jQuery('.pagex-repeater-items', window.parent.document).sortable({
            placeholder: 'pagex-sortable-placeholder',
            handle: '.pagex-repeater-tools',
            scroll: false,
            axis: 'y',
            cancel: '',
            stop: function (event, ui) {
                that.renderElement(true);
            }
        });
    },

    addRepeaterParams: function () {
        let that = this,
            con = this.paramsForm.querySelector('.pagex-repeater-items'),
            elementParams = _.find(pagexElements, {id: this.currentElement.getAttribute('data-type')}),
            repeaterParams = _.find(elementParams.options[0].params, {type: 'repeater'}),
            html = '';

        html += document.getElementById('pagex-control-repeater-item-start-template').innerHTML;
        _.forEach(repeaterParams.params, function (repValue) {
            let repControl = _.template(document.getElementById('pagex-control-' + repValue.type + '-template').innerHTML);

            if (_.isUndefined(repValue.id)) {
                html += repControl({data: repValue});
            } else {
                html += that.option_start({data: repValue}) + repControl({data: repValue}) + that.option_end({data: repValue});
            }
        });
        html += document.getElementById('pagex-control-repeater-item-end-template').innerHTML;

        con.insertAdjacentHTML('beforeend', html);

        this.setFormConditions(this.paramsForm.querySelector('.pagex-repeater-item:last-child'));

        this.renderElement(true);
    },

    cloneRepeaterItem: function (e) {

        let item = e.target.closest('.pagex-repeater-item'),
            itemSelects = item.querySelectorAll('select'),
            clone = item.cloneNode(true);

        // copy select values to a copy
        _.forEach(itemSelects, function (v) {
            clone.querySelector('[name="' + v.getAttribute('name') + '"]').value = v.value;
        });

        for (let item of clone.querySelectorAll('.pagex-control-action-type-content .form-control')) {
            let val = item.value;

            if (val.length) {
                let doc = new DOMParser().parseFromString(val, 'text/html'),
                    cloned = this.cloneNewIds(doc.body);

                item.value = cloned.innerHTML;
            }
        }

        item.after(clone);

        this.renderElement(true);
    },

    removeRepeaterItem: function (e) {
        let repeater = e.target.closest('.pagex-repeater-item');

        for (let item of repeater.querySelectorAll('.pagex-control-action-type-content .form-control')) {
            let val = item.value;

            if (val.length) {
                let doc = new DOMParser().parseFromString(val, 'text/html');

                // remove all saved params
                for (let itemID of doc.body.querySelectorAll('[data-id]')) {
                    let elId = itemID.getAttribute('data-id'),
                        inParam = pagexLocalize.all_params[elId];

                    if (!_.isUndefined(inParam)) {
                        delete pagexLocalize.all_params[elId];
                    }
                }
            }
        }

        repeater.remove();

        this.renderElement(true);
    },

    toggleRepeaterItem: function (el) {
        el.closest('.pagex-repeater-tools').classList.toggle('active')
    },

    iconpickerModalShow: function () {
        let searchInput = this.iconsModal.querySelector('#pagex-search-icons');

        searchInput.value = '';

        for (let item of this.iconsModal.querySelectorAll('.pagex-iconpicker-icon')) {
            item.classList.remove('pagex-hide');
        }

        this.iconsModal.classList.remove('pagex-hide');

        searchInput.focus();
    },

    renderSearchIcons: function (el) {
        let query = el.value.toLowerCase().trim();

        for (let item of this.iconsModal.querySelectorAll('.pagex-iconpicker-icon')) {
            let iconName = item.getAttribute('data-iconpicker');
            if (!iconName.includes(query)) {
                item.classList.add('pagex-hide');
            } else {
                item.classList.remove('pagex-hide');
            }
        }
    },

    setIconpickerIcon: function (el) {
        let icon = el.getAttribute('data-iconpicker');

        this.currentParam.value = icon;
        this.iconsModal.classList.add('pagex-hide');

        this.renderElement();
    },

    closeModalWindow: function (el) {
        el.closest('.pagex-params-modal').classList.add('pagex-hide');
    },

    saveLayout: function (e) {
        if (!_.isUndefined(pagexModal)) {
            pagexModal.closeAll();
        }

        let button_text = e.target.querySelector('span'),
            button_icon = e.target.querySelector('i');

        button_text.innerHTML = pagexLocalize.string.saving;
        button_icon.className = 'fas fa-spinner fa-spin mr-2';

        // set timeout so actions like modal have time to be closed
        setTimeout(function () {
            jQuery.post(pagexLocalize.ajaxUrl, {
                action: 'pagex_save_layout',
                pagex_post_content: document.querySelector('.pagex-builder-area').innerHTML,
                pagex_elements_params: JSON.stringify(pagexLocalize.all_params),
                pagex_page_status: true, // for save_post_data filter
                post_id: pagexLocalize.post_id,
            }, function (data) {
                if (data.error === undefined) {
                    console.error('Something went wrong during saving the layout.');
                    return;
                }

                button_icon.className = 'fas fa-check mr-2';
                button_text.innerHTML = data.message;
                window.parent.document.body.classList.remove('pagex-layout-changed');

                setTimeout(function () {
                    button_text.innerHTML = pagexLocalize.string.save;
                    button_icon.className = 'far fa-save mr-2';
                }, 10000);

            }).fail(function () {
                alert("error");
            });
        }, 700);
    },

    removeBuilderOptions: function () {
        for (let item of document.querySelectorAll('.pagex-options')) {
            item.remove();
        }
    },

    importPostLayout: function (el) {
        let layout = el.getAttribute('data-import-post-layout'),
            button_text = el.querySelector('span'),
            button_icon = el.querySelector('i'),
            _this = this;

        button_text.innerHTML = pagexLocalize.string.importing;
        button_icon.className = 'fas fa-spinner fa-spin';

        jQuery.post(pagexLocalize.ajaxUrl, {
            action: 'pagex_import_post_layout',
            query_string: pagexLocalize.query_string,
            post_layout: layout,
            "pagex-frame": true, // skip pagex content filter
        }, function (response) {
            if (!response.success) {
                alert(response.data.content);
                return;
            }

            _this.appendNewImportedLayout(response.data);

            button_text.innerHTML = pagexLocalize.string.import;
            button_icon.className = 'fas fa-file-import';

        }).fail(function () {
            console.error('Fail to import layout');
        });
    },

    appendNewImportedLayout: function (data) {
        let content = jQuery('<div>' + data.content + '</div>'),
            params = JSON.parse(data.params),
            newParams = {};

        content = content[0];

        // check only elements only with [data-type] since modal windows also have [data-id]
        for (let item of content.querySelectorAll('[data-type]')) {
            let oldId = item.getAttribute('data-id'),
                newId = this.genID(),
                inParam = params[oldId],
                style = item.querySelector('#' + oldId),
                modal = item.querySelector('[data-id="' + oldId + '"]');

            if (!_.isNull(style)) {
                style.id = newId;
                style.innerHTML = style.innerHTML.replace(new RegExp(oldId, 'g'), newId);
            }

            if (!_.isNull(modal)) {
                modal.setAttribute('data-id', newId);
            }

            item.setAttribute('data-id', newId);

            if (!_.isUndefined(inParam)) {
                newParams[newId] = inParam;
            }
        }

        let newData = {content: content.innerHTML, params: newParams};

        // use jQuery to run inline script if presented
        jQuery(newData.content).insertAfter(jQuery(this.currentSection));

        if (newData.params) {
            pagexLocalize.all_params = Object.assign(pagexLocalize.all_params, newData.params);
        }

        this.layoutsModal.classList.add('pagex-hide');

        this.updatePostContent();

        if (typeof pagexUtils !== "undefined") {
            setTimeout(function () {
                pagexUtils.setupRefresh();
            }, 1500);
        }

        this.elementSortable();
    },

    pagexSetting: function (el) {
        if (!el.matches('.pagex-debug-mode-clear-layout')) {
            el.classList.toggle('active');
        }
    },

    previewMode: function () {
        document.body.classList.toggle('pagex-preview-mode');
    },

    previewModeHideControls: function () {
        document.body.classList.toggle('pagex-preview-mode-hide-controls');
    },

    debugHideHeader: function () {
        document.body.classList.toggle('pagex-debug-mode-hide-header');
    },

    debugClearLayout: function () {
        if (confirm(pagexLocalize.string.clear_layout)) {
            document.querySelector('.pagex-builder-area').innerHTML = '';
            pagexLocalize.all_params = {};

            this.validateEmptyElements();
        }
    },

    excerptPreviewIframeLoaded: function () {
        this.updatePostContent();
    },

    excerptPreviewWidth: function (e) {
        let that = this;
        this.excerptPreviewArea.className = 'pagex-excerpt-preview-window-width-' + e.value;

        let innerDoc = this.excerptPreviewArea.contentDocument || this.excerptPreviewArea.contentWindow.document;

        setTimeout(function () {
            that.excerptPreviewArea.height = innerDoc.body.scrollHeight;
        }, 300);
    },

    openModalButton: function () {
        this.currentElement.querySelector('.pagex-modal-trigger').click();
    },

    uploadLayout: function (input) {
        let file_data = input.files[0],
            form_data = new FormData(),
            icon = input.nextSibling.querySelector('i'),
            iconClass = icon.className,
            button = input.nextSibling.querySelector('span'),
            buttonText = button.innerHTML,
            _this = this;

        button.innerHTML = pagexLocalize.string.importing;
        icon.className = 'fas fa-spinner fa-spin';

        form_data.append('file', file_data);
        form_data.append('action', 'pagex_upload_layout');
        form_data.append('pagex-frame', true);
        form_data.append('query_string', pagexLocalize.query_string);

        jQuery.ajax({
            url: pagexLocalize.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: form_data,
            contentType: false,
            processData: false,
        }).done(function (response) {
            if (!response.success) {
                alert(response.data.content);
                return;
            }

            _this.appendNewImportedLayout(response.data);
        }).fail(function () {
            alert('Error uploading layout');
        }).always(function () {
            button.innerHTML = buttonText;
            icon.className = iconClass;
            // clear file input so we could import same layout again
            input.closest('form').reset();
        });
    },

    exportCurrentSection: function () {
        let content = this.currentElement.outerHTML,
            sectionID = this.currentElement.getAttribute(['data-id']),
            params = {};

        if (!_.isUndefined(pagexLocalize.all_params[sectionID])) {
            params[sectionID] = pagexLocalize.all_params[sectionID];
        }
        for (let item of this.currentElement.querySelectorAll('[data-id]')) {
            let id = item.getAttribute(['data-id']);
            if (!_.isUndefined(pagexLocalize.all_params[id])) {
                params[id] = pagexLocalize.all_params[id];
            }
        }

        this.export(params, content);

        return false;
    },

    exportBuilderArea: function () {
        this.export(pagexLocalize.all_params, document.querySelector('.pagex-builder-area').innerHTML);

        return false;
    },

    export: function (params, content) {
        jQuery.post(pagexLocalize.ajaxUrl, {
            action: 'pagex_export_layout',
            content: content,
            pagex_elements_params: JSON.stringify(params),
            pagex_page_status: true
        }, function (data) {
            let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify({
                    params: params,
                    layout: data.data
                })),
                downloadAnchorNode = document.createElement('a');

            downloadAnchorNode.setAttribute('href', dataStr);
            downloadAnchorNode.setAttribute('download', 'pagex-layout.json');
            window.parent.document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        }).fail(function () {
            alert('Error exporting layout');
        });
    },
};

pagex.validateEmptyElements();
pagex.elementSortable();

document.addEventListener('click', function (e) {
    if (!e.target) return;
    let el = e.target;

    if (el.matches('[data-edit]')) {
        let elEditType = el.getAttribute('data-edit');

        if (elEditType === 'element') {
            pagex.currentElement = el.closest('.element');
        } else {
            pagex.currentElement = el.closest('[data-type="' + elEditType + '"]');
        }

        pagex.renderForm();
    }

    // saving section as layout
    if (el.matches('.pagex-save-custom-layout-modal')) pagex.openSaveAsLayoutModal(el);

    if (el.matches('[data-add]')) pagex.appendNewElement(e);
    if (el.matches('[data-clone]')) pagex.cloneElement(e);
    if (el.matches('[data-remove]')) pagex.removeElement(e);

    // section options control
    if (el.matches('.pagex-section-set')) pagex.switchSectionOptions(e);
    if (el.matches('.pagex-options-toggle')) pagex.toggleSectionOptions(el);
});

window.parent.document.addEventListener('click', function (e) {

    if (!e.target) return;

    let el = e.target;

    // append new element
    if (el.matches('[data-element-item-id]')) pagex.appendItemElement(e);

    // undo remove
    if (el.matches('.pagex-undo-remove')) pagex.undoRemove();

    // option controls
    if (el.matches('.pagex-link-control-search')) pagex.searchForLink(e);
    if (el.matches('.pagex-link-control-insert')) pagex.insertLink(e);
    if (el.matches('.pagex-link-show-attrs')) pagex.showLinkAttrs(e);

    // responsive switcher
    if (el.matches('.pagex-responsive-label')) pagex.switchDevices(el, 'all');
    if (el.matches('.pagex-device-switcher')) pagex.switchDevices(el);
    if (el.matches('.pagex-responsive-params')) {
        // get text (sm,md) from :before style
        let deviceCodeText = window.getComputedStyle(el, ':before').content.replace(/'|"/gi, "");
        window.parent.document.querySelector(`[data-device-switcher="${deviceCodeText}"]`).click();
    }

    if (el.matches('.pagex-responsive-switcher-button')) pagex.showSwitcher(e);
    if (el.matches('#pagex-control-responsive-switcher')) pagex.hideSwitcher(e);


    if (el.matches('.pagex-params-tab-title')) pagex.switchTabsForm(e);
    if (el.matches('.pagex-add-repeater-item')) pagex.addRepeaterParams(e);
    if (el.matches('.pagex-repeater-clone')) pagex.cloneRepeaterItem(e);
    if (el.matches('.pagex-repeater-remove')) pagex.removeRepeaterItem(e);
    if (el.matches('.pagex-repeater-title')) pagex.toggleRepeaterItem(el);

    // open button modal
    if (el.matches('.pagex-open-modal-trigger')) pagex.openModalButton(e);

    // image control
    if (el.matches('.pagex-image-placeholder')) pagex.insertImage(e);
    if (el.matches('.pagex-image-delete')) pagex.deleteImage(e);

    // url control
    if (el.matches('.pagex-url-control-insert')) pagex.insertURL(e);

    // saving section as layout
    if (el.matches('.pagex-save-custom-layout')) pagex.saveAsLayout(el);

    // layout import
    if (el.matches('.pagex-library-post-layout-import')) pagex.importPostLayout(el);

    // set icon
    if (el.matches('.pagex-iconpicker-icon')) pagex.setIconpickerIcon(el);

    // settings
    if (el.matches('.pagex-params-modal-close')) pagex.closeModalWindow(el);
    if (el.matches('.pagex-save')) pagex.saveLayout(e);
    if (el.matches('.pagex-setting')) pagex.pagexSetting(el);
    if (el.matches('.pagex-preview-mode')) pagex.previewMode();
    if (el.matches('.pagex-preview-mode-hide-controls')) pagex.previewModeHideControls();
    if (el.matches('.pagex-debug-mode-hide-header')) pagex.debugHideHeader();
    if (el.matches('.pagex-debug-mode-clear-layout')) pagex.debugClearLayout();

    // export/import layouts
    if (el.matches('#pagex-export-current-section')) pagex.exportCurrentSection();
    if (el.matches('#pagex-export-builder-area')) pagex.exportBuilderArea();

});

window.parent.document.addEventListener('mousedown', function (e) {
    if (!e.target) return;
    let el = e.target;

    // iconpicker
    if (el.matches('.pagex-control-iconpicker')) {
        e.preventDefault();
        pagex.currentParam = el;
        pagex.iconpickerModalShow();
    }
});


window.parent.document.addEventListener('keyup', function (e) {
    if (!e.target) return;
    let el = e.target;

    if (el.matches('#pagex-search-elements')) pagex.renderSearchElement(e);
    if (el.matches('#pagex-search-icons')) pagex.renderSearchIcons(el);
    if (el.matches('.pagex-link-control-field')) pagex.fillLinkUrl(e);

    // backend
    if (el.matches('#pagex-excerpt-preview-post-id')) pagex.updatePostContent();

    if (el.matches('.pagex-control-field')) {
        pagex.currentParam = el;

        pagexDelay(function () {
            pagex.renderElement();
        }, 300);

        // repeater item label
        if (el.closest('.pagex-control-wrapper').matches('.pagex-repeater-value')) {
            el.closest('.pagex-repeater-item').querySelector('.pagex-repeater-title').innerHTML = el.value.length ? el.value : pagexLocalize.string.item;
        }
    }
});

window.parent.document.addEventListener('change', function (e) {
    if (!e.target) return;
    let el = e.target;

    switch (true) {
        case el.matches('.pagex-image-sizes'):
            // no break since it matches pagex-control-option
            pagex.changeImageUrlSize(e);
        case el.matches('.pagex-link-control-field'):
            // no break since it matches pagex-control-option
            pagex.fillLinkUrl(e);
        case el.matches('.pagex-control-option'):
            pagex.currentParam = el;
            setTimeout(pagex.setFormConditions(el), 0);
            setTimeout(pagex.renderElement(), 100);
            break;
        case el.matches('.pagex-layouts-modal-filter-cat'):
            // layout library filter
            pagex.filterLibraryLayout(el.value);
            break;
        case el.matches('#pagex-excerpt-preview-post-type'):
            // backend
            pagex.updatePostContent();
            break;
        case el.matches('#pagex-excerpt-preview-width'):
            // backend
            pagex.excerptPreviewWidth(el);
            break;
        case el.matches('#pagex-upload-layout-file'):
            // upload layout
            pagex.uploadLayout(el);
            break;
    }
});

window.parent.addEventListener('colorPickerChange', function (data) {
    pagex.currentParam = data.detail.el;
    setTimeout(pagex.renderElement(), 100);
});

(function ($, window, document) {
    var elementOptions = document.getElementById("pagex-control-element-options-template").innerHTML,
        columnOptions = document.getElementById("pagex-control-column-options-template").innerHTML,
        addElementOptions = document.getElementById("pagex-control-add-element-options-template").innerHTML,
        sectionOptions = document.getElementById("pagex-control-section-options-template").innerHTML,
        sectionAddNewOptions = document.getElementById("pagex-control-section-add-new-template").innerHTML,
        innerRowOptions = document.getElementById("pagex-control-inner-row-template").innerHTML;

    $(document).on({
        mouseenter: function () {
            $(this).prepend(elementOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area .element, .pagex-modal-builder-area .element');
    $(document).on({
        mouseenter: function () {
            if ($(this).children('.pagex-options').length) return;
            $(this).prepend(columnOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area [data-type="column"], .pagex-modal-builder-area [data-type="column"]');
    $(document).on({
        mouseenter: function () {
            $(this).prepend(addElementOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area [data-type="column"]:empty, .pagex-modal-builder-area [data-type="column"]:empty');
    $(document).on({
        mouseenter: function () {
            let cont = $(this).closest('[data-type="container"]').prev('[data-type="container"]'),
                rowt = $(this).prev('[data-type="row"]');

            // if we have more than one container in a section hide section options
            if (cont.length) {
                $(this).closest('[data-type="section"]').addClass('pagex-hide-section-options');
            } else {
                $(this).closest('[data-type="section"]').removeClass('pagex-hide-section-options');
            }

            // if we have more than one row in container hide container options
            if (rowt.length) {
                $(this).closest('[data-type="section"]').addClass('pagex-hide-container-options');
            } else {
                $(this).closest('[data-type="section"]').removeClass('pagex-hide-container-options');
            }

            if ($(this).children('.pagex-options').length) return;

            $(this).prepend(sectionOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area [data-type="row"]');
    $(document).on({
        mouseenter: function () {
            if ($(this).children('.pagex-options').length) return;
            $(this).prepend(innerRowOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area [data-type="inner-row"], .pagex-modal-builder-area [data-type="inner-row"]');
    $(document).on({
        mouseenter: function () {
            if ($(this).children('.pagex-options').length) return;
            $(this).prepend(sectionAddNewOptions);
        },
        mouseleave: function () {
            $(this).find('.pagex-options').remove();
        }
    }, '.pagex-builder-area [data-type="section"]');
    $(document).on({
        mouseenter: function () {
            document.body.classList.add('pagex-hide-not-element-options');
        },
        mouseleave: function () {
            document.body.classList.remove('pagex-hide-not-element-options');
        }
    }, '.pagex-element-options');
    $(document).on({
        mouseenter: function () {
            tippy('.pagex-tooltip', {
                delay: 0,
                duration: [200, 200],
                size: 'small',
                arrow: true,
                arrowType: 'round',
                animation: 'fade'
            });
        },
        mouseleave: function () {
            for (const popper of document.querySelectorAll('.tippy-popper')) {
                const instance = popper._tippy;

                if (instance.state.visible) {
                    instance.popperInstance.disableEventListeners();
                    instance.hide();
                }
            }
        }
    }, '.pagex-tooltip');
}(window.jQuery, window, document));
