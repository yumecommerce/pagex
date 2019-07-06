import { CountUp } from 'countup.js';
import 'waypoints/lib/noframework.waypoints.min.js';
import { Swiper, Navigation, Pagination, Autoplay, EffectFade } from '../../../node_modules/swiper/dist/js/swiper.esm.js';
var salvattore = require('salvattore');

"use strict";

// Install Swiper modules
Swiper.use([Navigation, Pagination, Autoplay, EffectFade]);

// WordPress comment-reply script
var addComment = {
    moveForm: function (a, b, c, d) {
        var e, f, g, h, i = this, j = i.I(a), k = i.I(c), l = i.I("cancel-comment-reply-link"),
            m = i.I("comment_parent"), n = i.I("comment_post_ID"), o = k.getElementsByTagName("form")[0];
        if (j && k && l && m && o) {
            i.respondId = c, d = d || !1, i.I("wp-temp-form-div") || (e = document.createElement("div"), e.id = "wp-temp-form-div", e.style.display = "none", k.parentNode.insertBefore(e, k)), j.parentNode.insertBefore(k, j.nextSibling), n && d && (n.value = d), m.value = b, l.style.display = "", l.onclick = function () {
                var a = addComment, b = a.I("wp-temp-form-div"), c = a.I(a.respondId);
                if (b && c) return a.I("comment_parent").value = "0", b.parentNode.insertBefore(c, b), b.parentNode.removeChild(b), this.style.display = "none", this.onclick = null, !1
            };
            try {
                for (var p = 0; p < o.elements.length; p++) if (f = o.elements[p], h = !1, "getComputedStyle" in window ? g = window.getComputedStyle(f) : document.documentElement.currentStyle && (g = f.currentStyle), (f.offsetWidth <= 0 && f.offsetHeight <= 0 || "hidden" === g.visibility) && (h = !0), "hidden" !== f.type && !f.disabled && !h) {
                    f.focus();
                    break
                }
            } catch (q) {
            }
            return !1
        }
    }, I: function (a) {
        return document.getElementById(a)
    }
};

function getYoutubeIDFromURL(url) {
    var videoIDParts = url.match(/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^?&"'>]+)/);
    return videoIDParts && videoIDParts[1];
}

function initYouTubePlayerAPI() {
    if (document.getElementById('youtube-player-api')) return;

    let tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    tag.async = 'true';
    tag.id = 'youtube-player-api';

    let firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
}

window.onYouTubePlayerAPIReady = function () {
    initBackgroundVideos();
};

function initBackgroundVideos() {
    let v = document.querySelectorAll('[data-video-bg]');
    for (let i = 0; i < v.length; i++) {
        let item = v[i],
            id = item.querySelector('.pagex-video-youtube').getAttribute('id'),
            url = item.getAttribute('data-video-bg'),
            timeline = item.getAttribute('data-video-timeline');

        if (item.querySelector('iframe') == null) {
            renderYouTubeIframe(url, id, timeline);
        }
    }
}

initBackgroundVideos();

function renderYouTubeIframe(url, id, timeline) {
    if (typeof(YT) == 'undefined' || typeof(YT.Player) == 'undefined') {
        initYouTubePlayerAPI();
        return;
    }
    let vid_id = getYoutubeIDFromURL(url),
        time = JSON.parse(decodeURIComponent(timeline)),
        player, iframe;

    player = new YT.Player(id, {
        videoId: vid_id,
        events: {
            onReady: function () {
                player.mute();
                iframe = player.getIframe();
                pagexVideo.fitVideo(iframe);
                player.seekTo(time.start || 0);
                player.playVideo();

                if (time.end && time.start) {
                    let timeLoop = Number(time.end) - Number(time.start);
                    if (timeLoop > 0) {
                        setInterval(function () {
                            if (!player.getIframe().contentWindow) {
                                return;
                            }
                            player.seekTo(time.start || 0);
                        }, timeLoop * 1000);
                    }
                }

            },
            onStateChange: function (event) {
                switch (event.data) {
                    case YT.PlayerState.PLAYING:
                        iframe.classList.add('pagex-video-embed-loaded');
                        break;
                    case YT.PlayerState.ENDED:
                        player.seekTo(time.start || 0);
                }
            }
        },
        playerVars: {
            controls: 0,
            showinfo: 0,
            rel: 0,
        }
    });
}

window.renderYouTubeIframe = renderYouTubeIframe;

var pagexVideo = {
    initByClick: function (el) {
        let wrapper = el.closest('.pagex-video'),
            video = wrapper.querySelector('video'),
            iframe = wrapper.querySelector('iframe');

        if (video) {
            video.play();
            wrapper.classList.add('pagex-video-overlay-hide');
            return;
        }

        if (iframe) {
            let lazy = iframe.getAttribute('data-lazy-load');
            lazy = lazy.replace('&autoplay=0', '&autoplay=1');
            iframe.src = lazy;

            setTimeout(function () {
                wrapper.classList.add('pagex-video-overlay-hide');
            }, 700);
        }
    },

    initElements: function () {
        let v = document.querySelectorAll('.pagex-video');
        for (let i = 0; i < v.length; i++) {
            let item = v[i];

            if (item.querySelector('.pagex-video-overlay')) {
                return;
            }

            let iframe = item.querySelector('iframe');

            if (iframe && !iframe.src.length) {
                iframe.setAttribute('src', iframe.getAttribute('data-lazy-load'));
            }
        }
    },

    initBackgrounds: function () {
        let v = document.querySelectorAll('.pagex-video-youtube');
        for (let i = 0; i < v.length; i++) {
            let item = v[i];

            if (!item.src.length) {
                let src = item.getAttribute('data-lazy-load');

                item.src = src;

                item.onload = function () {
                    item.classList.add('pagex-video-embed-loaded');
                };

                this.fitVideo(item);
            }
        }
    },

    fitVideo: function (item) {
        let containerWidth = item.clientWidth,
            containerHeight = item.clientHeight,
            aspectRatio = 1.77,
            ratioWidth = containerWidth / aspectRatio,
            ratioHeight = containerHeight * aspectRatio,
            isWidthFixed = containerWidth / containerHeight > aspectRatio,
            video_width = isWidthFixed ? containerWidth : ratioHeight,
            video_height = isWidthFixed ? ratioWidth : containerHeight,
            video_left = isWidthFixed ? 0 : (video_width - containerWidth) / 2 * -1;

        Object.assign(item.style, {width: video_width + 'px', height: video_height + 'px', left: video_left + 'px'});
    }
};

pagexVideo.initElements();

var pagexForm = {
    init: function () {
        let v = document.querySelectorAll('.pagex-form');
        for (let i = 0; i < v.length; i++) {
            let item = v[i];
            item.addEventListener("submit", function (e) {
                e.preventDefault();
                pagexForm.send(item);
            });
        }
    },

    send: function (form) {
        let submitButton = form.querySelector('.pagex-form-submit-button');
        let data = {
            url: window.location.href,
            action: 'pagex_form_ajax_send_form',
            form_action: form.querySelector('[name="pagex-action"]').value,
            form: []
        };

        let v = form.querySelectorAll('.pagex-form-item');
        for (let i = 0; i < v.length; i++) {
            let item = v[i],
                formItem = {
                    label: '',
                    value: [],
                },
                label = item.querySelector('.pagex-form-label');

            if (label) {
                formItem.label = label.innerHTML;
            } else {
                let placeholder = item.querySelector('[placeholder]');
                if (placeholder) {
                    formItem.label = placeholder.getAttribute('placeholder');
                }
            }

            // radio and checkboxes
            let checkControls = item.querySelectorAll('.pagex-form-check:checked');
            for (let i = 0; i < checkControls.length; i++) {
                formItem.value.push(checkControls[i].value);
            }

            // inputs and select
            let inputControls = item.querySelectorAll('.form-control');
            for (let i = 0; i < inputControls.length; i++) {
                formItem.value.push(inputControls[i].value);
            }

            if (formItem.value.length) {
                data.form.push(formItem);
            }
        }

        submitButton.classList.add('loading');

        let request = new XMLHttpRequest();
        request.open('POST', pagexVars.ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.onload = function () {
            if (this.status >= 200) {

                let res = JSON.parse(this.response);
                if (res.success !== true) {
                    form.querySelector('.pagex-form-response-message').innerHTML = res.data.message;
                }

                form.classList.add('pagex-form-done');
                submitButton.classList.remove('loading');

            } else {
                console.log('fail');
            }
        };

        request.onerror = function () {
            console.log('Connection error');
        };

        request.send(this.serialize(data));
    },

    serialize: function (obj, prefix) {
        let str = [],
            p;
        for (p in obj) {
            if (obj.hasOwnProperty(p)) {
                let k = prefix ? prefix + "[" + p + "]" : p,
                    v = obj[p];
                str.push((v !== null && typeof v === "object") ?
                    this.serialize(v, k) :
                    encodeURIComponent(k) + "=" + encodeURIComponent(v));
            }
        }

        return str.join("&");
    }
};
pagexForm.init();

var pagexLoginForm = {
    init: function () {
        let v = document.querySelectorAll('.pagex-login-form');
        for (let i = 0; i < v.length; i++) {
            v[i].addEventListener("submit", function (e) {
                e.preventDefault();
                pagexLoginForm.send(v[i]);
            });
        }
    },

    send: function (form) {
        let submitButton = form.querySelector('.pagex-login-form-submit-button');
        let data = {action: 'pagex_form_ajax_send_login_form',};

        let v = form.querySelectorAll('[name]');
        for (let i = 0; i < v.length; i++) {
            data[v[i].getAttribute('name')] = v[i].value;
        }

        submitButton.classList.add('loading');

        let request = new XMLHttpRequest();
        request.open('POST', pagexVars.ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.onload = function () {
            if (this.status >= 200) {
                submitButton.classList.remove('loading');
                let res = JSON.parse(this.response);

                if (res.success !== true) {
                    form.querySelector('.pagex-login-form-message').innerHTML = res.data.message;
                } else {
                    location.reload();
                }
            } else {
                console.log('fail');
            }
        };

        request.onerror = function () {
            console.log('Connection error');
        };

        request.send(pagexForm.serialize(data));
    }
};
pagexLoginForm.init();

var pagexModal = {
    modals: {},

    init: function (el) {
        let element = el.closest('.element'),
            data_id = element.getAttribute('data-id'),
            modal = element.querySelector('.pagex-modal'),
            modal_wrapper = modal.parentNode;

        this.modals[data_id] = modal_wrapper;

        if (!modal.getAttribute('data-id')) {
            modal.setAttribute('data-id', data_id);
        }

        document.body.appendChild(modal);

        modal.classList.add('pagex-modal-show');

        // set focus if there is a search input inside modal
        let searchInput = modal.querySelector('.pagex-search-input');

        if (searchInput) {
            searchInput.focus();
        }

        setTimeout(function () {
            modal.classList.add('pagex-modal-fade');
        }, 150);
    },

    // onClick action
    open: function (selector) {
        this.init(document.querySelector(selector + ' .pagex-modal-trigger'));
    },

    close: function (el, timeout) {
        let modalActive = el.closest('.pagex-modal'),
            modalActiveId = modalActive.getAttribute('data-id'),
            initialWrap = this.modals[modalActiveId];

        modalActive.classList.remove('pagex-modal-fade');

        if (timeout === 0) {
            modalActive.classList.remove('pagex-modal-show');

            if (initialWrap.querySelector('.pagex-modal')) {
                modalActive.remove();
            } else {
                initialWrap.appendChild(modalActive);
            }
        } else {
            setTimeout(function () {
                modalActive.classList.remove('pagex-modal-show');

                initialWrap.appendChild(modalActive);
            }, 400);
        }
    },

    closeAll: function () {
        let v = document.querySelectorAll('.pagex-modal-show > .pagex-modal-window-wrapper > .pagex-modal-window');
        for (let i = 0; i < v.length; i++) {
            pagexModal.close(v[i], 0);
        }
    }
};
window.pagexModal = pagexModal;

var pagexScrollTo = {
    scroll: function (el) {
        let href = el.getAttribute('href'),
            hasOffset = href.indexOf(':') !== -1,
            offset = hasOffset ? href.substring(href.indexOf(':') + 1, href.length) : 0;

        let target = document.getElementById(href.substring(11, hasOffset ? href.length - offset.length - 1 : href.length)),
            destination = target.getBoundingClientRect().top + window.pageYOffset - Number(offset),
            duration = 600;

        const start = window.pageYOffset;
        const startTime = 'now' in window.performance ? performance.now() : new Date().getTime();
        const documentHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);
        const windowHeight = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
        const destinationOffset = typeof destination === 'number' ? destination : destination.offsetTop;
        const destinationOffsetToScroll = Math.round(documentHeight - destinationOffset < windowHeight ? documentHeight - windowHeight : destinationOffset);

        if ('requestAnimationFrame' in window === false) {
            window.scroll(0, destinationOffsetToScroll);
            return;
        }

        function scroll() {
            const now = 'now' in window.performance ? performance.now() : new Date().getTime();
            const time = Math.min(1, ((now - startTime) / duration));
            const timeFunction = time * (2 - time);
            window.scroll(0, Math.ceil((timeFunction * (destinationOffsetToScroll - start)) + start));

            if (Math.round(window.pageYOffset) === destinationOffsetToScroll) {
                return;
            }

            requestAnimationFrame(scroll);
        }

        scroll();
    }
};

var pagexAccordion = {
    item: null,
    wrapper: null,
    content: null,
    container: null,

    init: function (el) {
        this.container = el.closest('.pagex-accordion');
        this.item = el.closest('.pagex-accordion-item');
        this.wrapper = this.item.querySelector('.pagex-accordion-item-content-wrapper');
        this.content = this.item.querySelector('.pagex-accordion-item-content');

        let height = this.content.offsetHeight + 'px';

        if (!this.container.matches('.pagex-accordion-toggle-separately') && !this.item.matches('.pagex-item-active')) {
            let activeItem = this.container.querySelector('.pagex-item-active');

            if (activeItem != null) {
                setTimeout(function () {
                    let activeHeight = activeItem.querySelector('.pagex-accordion-item-content').offsetHeight + 'px';
                    let activeWrapper = activeItem.querySelector('.pagex-accordion-item-content-wrapper');
                    activeWrapper.style.maxHeight = activeHeight;
                    activeWrapper.classList.remove('pagex-accordion-active');
                    setTimeout(function () {
                        activeWrapper.style.maxHeight = null;
                        activeItem.classList.remove('pagex-item-active');
                    }, 10);
                }, 100);
            }
        }

        if (this.item.matches('.pagex-item-active')) {
            this.wrapper.style.maxHeight = height;
            this.wrapper.classList.remove('pagex-accordion-active');
            setTimeout(function () {
                pagexAccordion.item.classList.remove('pagex-item-active');
                pagexAccordion.wrapper.style.maxHeight = null;
            }, 10);
        } else {
            this.wrapper.style.maxHeight = height;
            this.item.classList.add('pagex-item-active');
            // remove max height when accordion is opened so we could have dynamic height element
            setTimeout(function () {
                pagexAccordion.wrapper.classList.add('pagex-accordion-active');
            }, 300);
        }
    },
};

var pagexTabs = {
    init: function (el) {
        if (el.matches('.pagex-item-active')) return;

        let element = el.closest('.pagex-tabs'),
            index = Array.from(el.parentNode.children).indexOf(el);

        this.openTab(element, index);
    },

    // custom onClick action
    goto: function (selector, index) {
        let tabs = document.querySelector(selector),
            tabsId = tabs.getAttribute('data-id'),
            idName = 'tab-' + tabsId + '-active',
            link = document.querySelector('.' + idName);

        if (link) link.classList.remove(idName);
        window.event.target.closest('[data-type]').classList.add(idName);

        this.openTab(tabs.querySelector('.pagex-tabs'), index - 1);

        return false;
    },

    openTab: function (element, index) {
        element.querySelector('.pagex-tabs-nav-items .pagex-item-active').classList.remove('pagex-item-active');
        element.querySelector('.pagex-tabs-panes .pagex-item-active').classList.remove('pagex-item-active');

        element.querySelectorAll('.pagex-tabs-nav-items .pagex-tabs-nav-item')[index].classList.add('pagex-item-active');
        element.querySelectorAll('.pagex-tabs-panes .pagex-tabs-pane')[index].classList.add('pagex-item-active');

        document.body.setAttribute('data-tabs-active-' + element.closest('[data-type]').getAttribute('data-id'), index + 1);
    }
};

window.pagexTabs = pagexTabs;

var pagexSlider = {
    builderActive: document.body.matches('.pagex-builder-frame-active'),
    sliders: [],

    initElements: function () {
        let s = document.querySelectorAll('[data-slider]');
        for (let i = 0; i < s.length; i++) {
            this.init(s[i]);
        }

        let g = document.querySelectorAll('.pagex-gallery-slider');
        for (let i = 0; i < g.length; i++) {
            new Swiper(g[i], {
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                a11y: {
                    enabled: false
                },
                loop: true,
                observer: true,
                observeParents: true,
            });
        }
    },

    init: function (el) {
        let slider = JSON.parse(el.getAttribute('data-slider')),
            selector = el.querySelector('.swiper-container'),
            prev = el.querySelector('.swiper-button-prev'),
            next = el.querySelector('.swiper-button-next');

        if (!selector) {
            return;
        }

        slider.pagination = {
            el: slider.pagination_el !== undefined ? slider.pagination_el : '.swiper-pagination',
            clickable: true,
        };

        if (slider.paginationtype) {
            slider.pagination.type = slider.paginationtype;
        }

        slider.navigation = {
            nextEl: next,
            prevEl: prev,
        };

        // mobile first breakpoints
        slider.breakpointsInverse = true;

        if (slider.effect === 'fade') {
            delete slider.breakpointsInverse;
            delete slider.breakpoints;
            slider.slidesPerView = 1;
            slider.slidesPerGroup = 1;
        }

        // use defaults properties for frontend builder
        if (this.builderActive) {
            slider.loop = false;
            slider.effect = 'slide';
            slider.autoplay = false;
            slider.allowTouchMove = false;

            if (selector.swiper !== undefined) {
                selector.swiper.destroy();
            }
        }

        // hide navigation when not enough slides for sliding
        slider.watchOverflow = true;

        // force initialization in hidden divs
        slider.observer = true;
        slider.observeParents = true;

        // add entrance animation for each slide when we have only one slide per view
        if (slider.slidesPerView === 1) {
            slider.on = {
                init: function () {
                    setTimeout(function () {
                        let v = selector.querySelectorAll('.swiper-slide:not(.swiper-slide-active):not(.swiper-slide-duplicate-active) .pagex-animated');
                        for (let i = 0; i < v.length; i++) {
                            v[i].classList.remove('pagex-animated');
                        }
                    }, 1000);
                },
                slideChangeTransitionEnd: function () {
                    let v = selector.querySelectorAll('.swiper-slide:not(.swiper-slide-active):not(.swiper-slide-duplicate-active) .pagex-animated');
                    for (let i = 0; i < v.length; i++) {
                        v[i].classList.remove('pagex-animated');
                    }

                    let a = selector.querySelectorAll('.swiper-slide-active [data-animate]');
                    for (let i = 0; i < a.length; i++) {
                        a[i].classList.add('pagex-animated');
                    }
                }
            }
        }

        slider.a11y = {
            enabled: false
        };

        new Swiper(selector, slider)
    },

    openLightbox: function (el) {
        let index = el.getAttribute('data-gallery-item'),
            slider = document.querySelector('.pagex-modal-show .pagex-gallery-slider').swiper;

        slider.slideTo(index, 0);
    },

    goto: function (selector, to) {
        // all since we can have same controls for multiple sliders
        let sliders = document.querySelectorAll(selector + ' .swiper-container');

        for (let i = 0; i < sliders.length; i++) {
            let slider = sliders[i].swiper;

            switch (to) {
                case 'next':
                    slider.slideNext();
                    break;
                case 'prev':
                    slider.slidePrev();
                    break;
                default:
                    slider.slideTo(Number(to));
            }
        }
    },
};

// make it global for onclick events
window.pagexSlider = pagexSlider;


var pagexCountdown = {
    initElements: function () {
        let v = document.querySelectorAll('.pagex-countdown');
        for (let i = 0; i < v.length; i++) {
            this.init(v[i]);
        }
    },

    init: function (el) {
        let timeInterval,
            endTime = el.getAttribute('data-countdown'),
            elements = {
                daysDiv: el.querySelector('.pagex-countdown-days'),
                hoursDiv: el.querySelector('.pagex-countdown-hours'),
                minutesDiv: el.querySelector('.pagex-countdown-minutes'),
                secondsDiv: el.querySelector('.pagex-countdown-seconds')
            };

        let updateClock = function () {
            let timeRemaining = pagexCountdown.getTimeRemaining(endTime);

            for (let timePart in timeRemaining.parts) {
                let partValue = timeRemaining.parts[timePart].toString();

                if (1 === partValue.length) {
                    partValue = 0 + partValue;
                }

                elements[timePart + 'Div'].innerHTML = partValue;
            }

            if (timeRemaining.total <= 0) {
                clearInterval(timeInterval);
            }
        };

        let initializeClock = function () {
            updateClock();

            timeInterval = setInterval(updateClock, 1000);
        };

        initializeClock();
    },

    getTimeRemaining: function (endTime) {
        let timeRemaining = endTime - new Date(),
            seconds = Math.floor((timeRemaining / 1000) % 60),
            minutes = Math.floor((timeRemaining / 1000 / 60) % 60),
            hours = Math.floor((timeRemaining / (1000 * 60 * 60)) % 24),
            days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));

        if (days < 0 || hours < 0 || minutes < 0) {
            seconds = minutes = hours = days = 0;
        }

        return {
            total: timeRemaining,
            parts: {
                days: days,
                hours: hours,
                minutes: minutes,
                seconds: seconds
            }
        }
    }
};
pagexCountdown.initElements();

var pagexMasonry = {
    init: function (el) {
        if (el.getAttribute('data-columns') == '') {
            salvattore.registerGrid(el);
        }
        salvattore.rescanMediaQueries();
    }
};

var pagexShare = {

    networks: {
        twitter: 'https://twitter.com/intent/tweet?url=' + location.href,
        pinterest: 'https://www.pinterest.com/pin/find/?url=' + location.href,
        facebook: 'https://www.facebook.com/sharer.php?u=' + location.href,
        vk: 'https://vkontakte.ru/share.php?title&description&image&url=' + location.href,
        linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url=' + location.href + '&title&summary&source=' + location.href,
        odnoklassniki: 'http://odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' + location.href,
        tumblr: 'https://tumblr.com/share/link?url=' + location.href,
        delicious: 'https://del.icio.us/save?title&url=' + location.href,
        google: 'https://plus.google.com/share?url=' + location.href,
        digg: 'https://digg.com/submit?url=' + location.href,
        reddit: 'https://reddit.com/submit?title&url=' + location.href,
        stumbleupon: 'https://www.stumbleupon.com/submit?url=' + location.href,
        pocket: 'https://getpocket.com/edit?url=' + location.href,
        whatsapp: 'whatsapp://send?text=' + location.href,
        xing: 'https://www.xing.com/app/user?op=share&url=' + location.href,
        print: 'javascript:print()',
        envelope: 'mailto:?subject=' + document.title + '&body=' + location.href,
        telegram: 'https://telegram.me/share/url?text&url=' + location.href,
        skype: 'https://web.skype.com/share?url=' + location.href,
    },

    init: function (el) {
        let name = el.getAttribute('data-share'),
            link = this.networks[name],
            isPlainLink = link.indexOf('http') !== -1,
            windowName = isPlainLink ? '_blank' : '_self';

        window.open(link, windowName);
    }
};

var pagexUtils = {
    refreshWaypoint: function () {
        let v = document.querySelectorAll('.pagex-section-fixed');
        for (let i = 0; i < v.length; i++) {
            v[i].classList.remove('pagex-section-fixed');
        }

        let a = document.querySelectorAll('.pagex-animated');
        for (let i = 0; i < a.length; i++) {
            a[i].classList.remove('pagex-animated');
        }

        Waypoint.destroyAll();

        this.setupRefresh();
    },

    refreshCurrentElementWaypoint: function () {
        if (typeof pagex !== "undefined") {
            let v = pagex.currentElement.querySelectorAll('.pagex-animated');
            for (let i = 0; i < v.length; i++) {
                v[i].classList.remove('pagex-animated');
            }

            this.setupRefresh();
        }
    },

    setupRefresh: function () {
        setTimeout(function () {
            pagexSticky.initElements();
            pagexEntranceAnimation.initElements();
            pagexSlider.initElements();
            pagexCounter.initElements();
        }, 200);

        setTimeout(function () {
            Waypoint.refreshAll();
        }, 400);
    }
};
window.pagexUtils = pagexUtils;

var pagexSticky = {
    initElements: function () {
        let v = document.querySelectorAll('.pagex-section-position-fixed');
        for (let i = 0; i < v.length; i++) {
            let frontOffset = document.body.matches('.admin-bar') ? 31 : -1;
            new Waypoint({
                element: v[i],
                handler: function (direction) {
                    // prevent wrong calculation of triggerPoint
                    // add +1 to offset 1px when section is next to
                    this.element.classList.remove('pagex-section-fixed');
                    this.triggerPoint = this.element.getBoundingClientRect().top + window.scrollY - frontOffset;

                    if (this.triggerPoint <= window.scrollY) {
                        this.element.classList.add('pagex-section-fixed');
                    } else {
                        this.element.classList.remove('pagex-section-fixed');
                    }

                },
                offset: frontOffset
            });
        }
    },
};
pagexSticky.initElements();

var pagexEntranceAnimation = {
    initElements: function () {
        let v = document.querySelectorAll('[data-animate]');
        for (let i = 0; i < v.length; i++) {
            let item = v[i],
                delay = item.getAttribute('data-animate-delay');

            delay = delay ? Number(delay) : 1;

            item.setAttribute('style', 'animation-delay: ' + delay + 'ms');

            new Waypoint({
                element: item,
                handler: function () {
                    this.element.classList.add('pagex-animated');
                    this.destroy();
                },
                offset: '87%'
            });
        }
    },
};


var pagexGoogleMaps = {
    initElements: function () {
        // init google maps with js API
        let v = document.querySelectorAll('.pagex-google-maps-embed');
        for (let i = 0; i < v.length; i++) {
            this.renderAPIMap(v[i]);
        }

        // init google maps with iframe API
        let a = document.querySelectorAll('.pagex-google-maps-iframe');
        for (let i = 0; i < a.length; i++) {
            a[i].src = a[i].getAttribute('data-lazy-load');
        }
    },

    initAPIscript: function (el) {
        if (document.getElementById('google-maps-api')) return;

        let data = JSON.parse(decodeURIComponent(el.getAttribute('data-google-map'))),
            apiUrl = 'https://maps.googleapis.com/maps/api/js?callback=pagexGoogleMaps.initElements&key=' + data.key,
            script = document.createElement('script'),
            firstScriptTag = document.getElementsByTagName('script')[0];

        script.src = apiUrl;
        script.id = 'google-maps-api';
        firstScriptTag.parentNode.insertBefore(script, firstScriptTag);
    },

    renderAPIMap: function (el) {
        if (!el) return;
        if (typeof(google) === 'undefined') {
            this.initAPIscript(el);
            return;
        }

        let data = JSON.parse(decodeURIComponent(el.getAttribute('data-google-map'))),
            geocoder = new google.maps.Geocoder();

        let mapOptions = {
            zoom: Number(data.zoom),
            scrollwheel: data.scroll,
            disableDefaultUI: data.ui,
        };

        if (data.style) {
            mapOptions.styles = JSON.parse(data.style);
        }

        var map = new google.maps.Map(el, mapOptions);

        geocoder.geocode({'address': data.address}, function (results, status) {
            if (status === 'OK') {
                map.setCenter(results[0].geometry.location);
                new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                console.error('Geocode was not successful for the following reason: ' + status);
            }
        });
    },
};
pagexGoogleMaps.initElements();
window.pagexGoogleMaps = pagexGoogleMaps;

var pagexWooCommerce = {
    applyRating: function (el) {
        let v = document.querySelectorAll('.pagex-star-rating');
        for (let i = 0; i < v.length; i++) {
            v[i].classList.remove('active');
        }

        el.classList.add('active');

        document.getElementById('rating').value = el.getAttribute('data-rating-star');
    }
};

var pagexNavMenu = {
    openSubMenu: function (el) {
        el.classList.toggle('pagex-menu-nav-active');
    }
};

var pagexCookie = {
    applyGDPR: function () {
        let mod = document.getElementById('pagex-gdpr-notice');
        mod.remove();
        this.set('pagex_gdpr', 'yes', 31536000);
    },

    get: function (name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    },

    set: function (name, value, expires, path, domain, secure) {
        var d = new Date();

        if (typeof(expires) === 'object' && expires.toGMTString) {
            expires = expires.toGMTString();
        } else if (parseInt(expires, 10)) {
            d.setTime(d.getTime() + (parseInt(expires, 10) * 1000)); // time must be in milliseconds
            expires = d.toGMTString();
        } else {
            expires = '';
        }

        document.cookie = name + '=' + encodeURIComponent(value) +
            (expires ? '; expires=' + expires : '') +
            (path ? '; path=' + path : '; path=/') +
            (domain ? '; domain=' + domain : '') +
            (secure ? '; secure' : '');
    },

    remove: function (name) {
        this.set(name, '', -1000);
    }
};

var pagexCounter = {
    counters: {},

    initElements: function () {
        let _this = this,
            counters = document.querySelectorAll('[data-counter]');

        for (let i = 0; i < counters.length; i++) {
            let options = JSON.parse(counters[i].getAttribute('data-counter'));

            this.counters[i] = new CountUp(counters[i], options.endVal, options);

            new Waypoint({
                element: counters[i],
                handler: function () {
                    _this.counters[i].start();
                    this.destroy();
                },
                offset: '87%'
            });
        }
    },

    init: function (el) {
        let options = JSON.parse(el.getAttribute('data-counter')),
            counter = new CountUp(el, options.endVal, options);

        counter.start();
    }
};
pagexCounter.initElements();

var pagexAccessibility = {
    visuallyImpaired: {
        init: function (on) {
            if (on === 1) {
                pagexCookie.set('pagex_visually_impaired', 'yes', 31536000);
            } else {
                pagexCookie.remove('pagex_visually_impaired');
            }
            location.reload();
        },

        switch: function (cl, tp) {
            let v = [];
            if (tp === 0) {
                v = ['pagex-vi-fs-is-default', 'pagex-vi-fs-is-big', 'pagex-vi-fs-is-huge'];
            } else if (tp === 1) {
                v = ['pagex-vi-cl-is-wb', 'pagex-vi-cl-is-bw', 'pagex-vi-cl-is-bb'];
            } else {
                v = ['pagex-vi-img-is-on', 'pagex-vi-img-is-off'];
            }

            for (let i = 0; i < v.length; i++) {
                pagexCookie.remove(v[i]);
                document.querySelector('.' + v[i].replace(new RegExp('-is', 'g'), '')).classList.remove('active');
                document.body.classList.remove(v[i]);
            }
            pagexCookie.set(v[cl], 'yes', 31536000);
            document.querySelector('.' + v[cl].replace(new RegExp('-is', 'g'), '')).classList.add('active');
            document.body.classList.add(v[cl]);
        }
    }
};

window.addEventListener('pagexElementUpdated', function (data) {

    let el = data.detail.el,
        elName = el.getAttribute('data-type');

    if (el.querySelector('[data-slider]')) {
        setTimeout(function () {
            pagexSlider.init(el.querySelector('[data-slider]'));
        }, 0);
    }

    if (el.querySelector('.pagex-gallery-slider')) {
        setTimeout(function () {
            pagexSlider.initElements();
        }, 0);
    }

    if (el.querySelector('.pagex-posts-wrapper[data-columns]')) {
        setTimeout(function () {
            pagexMasonry.init(el.querySelector('[data-columns]'));
        }, 0);
    }

    switch (elName) {
        case 'countdown':
            pagexCountdown.init(el.querySelector('[data-countdown]'));
            break;
        case 'google_maps':
            pagexGoogleMaps.renderAPIMap(el.querySelector('[data-google-map]'));
            break;
        case 'menu_cart':
            jQuery(document.body).trigger('wc_fragment_refresh');
            break;
        case 'counter':
            pagexCounter.init(el.querySelector('[data-counter]'));
            break;
    }


    if (el.querySelector('[data-animate]')) {
        pagexUtils.refreshCurrentElementWaypoint();
    }

    if (el.querySelector('.pagex-modal')) {
        pagexModal.closeAll();
    }

    window.dispatchEvent(new Event("resize"));
}, false);

document.addEventListener('click', function (e) {
    if (!e.target) return;
    let el = e.target;

    // video
    if (el.matches('.pagex-video-overlay') || el.matches('.pagex-video-overlay-button')) pagexVideo.initByClick(el);
    // modal
    if (el.matches('.pagex-modal-trigger')) pagexModal.init(el);
    if (el.matches('.pagex-modal-window') || el.matches('.pagex-modal-window-close')) pagexModal.close(el);
    // accordion
    if (el.matches('.pagex-accordion-item-header')) pagexAccordion.init(el);
    // tabs
    if (el.matches('.pagex-tabs-nav-item')) pagexTabs.init(el);
    // share
    if (el.matches('.pagex-share-button')) pagexShare.init(el);
    // menu
    if (el.matches('.menu-item-has-children')) pagexNavMenu.openSubMenu(el);
    // gallery lightbox
    if (el.matches('.pagex-gallery-item')) pagexSlider.openLightbox(el);
    if (el.matches('.pagex-no-event')) pagexModal.close(el);

    // scroll to
    if (el.matches('[href^="#scroll-to"]')) {
        e.preventDefault();
        pagexScrollTo.scroll(el);
    }

    let hr = el.closest('[href]');

    if (hr) {
        if (hr.getAttribute('href').indexOf('scroll-to') !== -1) {
            e.preventDefault();
            pagexScrollTo.scroll(hr);
        }
    }

    // woocommerce rating review
    if (el.matches('.pagex-star-rating')) {
        pagexWooCommerce.applyRating(el);
    }

    // GDPR notice
    if (el.matches('#pagex-gdpr-notice-close')) {
        pagexCookie.applyGDPR();
    }
});

// Custom links handler
window.pagexCustomLink = function pagexCustomLink(el) {
    el.querySelector('.pagex-custom-link-element').click();
};

// Preloader
function pagexHidePreloader() {
    // check in case hide was forced by timeout to prevent second calling
    if (document.body.matches('.pagex-preloader-body-active')) {
        pagexSlider.initElements();
        setTimeout(function () {
            document.body.classList.remove('pagex-preloader-body-active');
        }, 100);
        setTimeout(function () {
            document.body.classList.remove('pagex-preloader-body');
        }, 300);
        setTimeout(function () {
            pagexEntranceAnimation.initElements();
        }, 450);
    }
}

if (document.getElementById('pagex-main-preloader')) {
    document.addEventListener("DOMContentLoaded", function () {
        let pageGoogleFonts = document.getElementById('pagex-google-fonts');
        setTimeout(function () {
            if (pageGoogleFonts && document.fonts !== undefined) {
                let timerId = setInterval(function () {
                    if (document.fonts.status === 'loaded') {
                        pagexHidePreloader();
                        clearInterval(timerId);
                    }
                }, 50);
            } else {
                pagexHidePreloader();
            }
        }, 200);
    });

    //force to hide preloader
    setTimeout(function () {
        pagexHidePreloader();
    }, 5000);
} else {
    pagexSlider.initElements();
    pagexEntranceAnimation.initElements();
}
