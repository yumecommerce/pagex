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
    for (let item of document.querySelectorAll('[data-video-bg]')) {
        let id = item.querySelector('.pagex-video-youtube').getAttribute('id'),
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
        for (let item of document.querySelectorAll('.pagex-video')) {
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
        for (let item of document.querySelectorAll('.pagex-video-youtube')) {
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
        for (let form of document.querySelectorAll('.pagex-form')) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                pagexForm.send(form);
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

        for (let item of form.querySelectorAll('.pagex-form-item')) {
            let formItem = {
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
            for (let input of item.querySelectorAll('.pagex-form-check:checked')) {
                formItem.value.push(input.value);
            }

            // inputs and select
            for (let input of item.querySelectorAll('.form-control')) {
                formItem.value.push(input.value);
            }

            let request = new XMLHttpRequest();

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
        for (let form of document.querySelectorAll('.pagex-login-form')) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                pagexLoginForm.send(form);
            });
        }
    },

    send: function (form) {
        let submitButton = form.querySelector('.pagex-login-form-submit-button');
        let data = {action: 'pagex_form_ajax_send_login_form',};

        for (let item of form.querySelectorAll('[name]')) {
            data[item.getAttribute('name')] = item.value;
        }

        console.log(data);

        submitButton.classList.add('loading');

        let request = new XMLHttpRequest();
        request.open('POST', pagexVars.ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.onload = function () {
            if (this.status >= 200) {
                submitButton.classList.remove('loading');
                let res = JSON.parse(this.response);

                if (res.success !== true) {
                    console.log(res);
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

    close: function (el, timeout = 400) {
        let modalActive = el.closest('.pagex-modal'),
            modalActiveId = modalActive.getAttribute('data-id'),
            initialWrap = this.modals[modalActiveId];

        modalActive.classList.remove('pagex-modal-fade');

        if (timeout === 0) {
            modalActive.classList.remove('pagex-modal-show');

            if (document.querySelector('.element[data-id="' + modalActiveId + '"] .pagex-modal')) {
                modalActive.remove();
            } else {
                initialWrap.appendChild(modalActive);
            }

        } else {
            setTimeout(function () {
                modalActive.classList.remove('pagex-modal-show');

                initialWrap.appendChild(modalActive);
            }, timeout);
        }

    },

    closeAll: function () {
        for (let item of document.querySelectorAll('.pagex-modal-show')) {
            pagexModal.close(item.querySelector('.pagex-modal-window'), 0);
        }
    }
};

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

            if (window.pageYOffset === destinationOffsetToScroll) {
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

        element.querySelector('.pagex-tabs-nav-items .pagex-item-active').classList.remove('pagex-item-active');
        element.querySelector('.pagex-tabs-panes .pagex-item-active').classList.remove('pagex-item-active');

        element.querySelectorAll('.pagex-tabs-nav-items .pagex-tabs-nav-item')[index].classList.add('pagex-item-active');
        element.querySelectorAll('.pagex-tabs-panes .pagex-tabs-pane')[index].classList.add('pagex-item-active');

    }
};

var pagexSlider = {
    builderActive: document.body.matches('.pagex-builder-frame-active'),
    sliders: [],

    initElements: function () {
        for (let item of document.querySelectorAll('[data-slider]')) {
            this.init(item);
        }

        for (let item of document.querySelectorAll('.pagex-gallery-slider')) {
            new Swiper(item, {
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
            el: '.swiper-pagination',
            clickable: true,
        };

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


        // add entrance animation for each slide when we have only one slide per view
        if (slider.slidesPerView === 1) {
            slider.on = {
                init: function () {
                    setTimeout(function () {
                        for (let item of selector.querySelectorAll('.swiper-slide:not(.swiper-slide-active):not(.swiper-slide-duplicate-active) .pagex-animated')) {
                            item.classList.remove('pagex-animated');
                        }
                    }, 1000);
                },
                slideChangeTransitionEnd: function () {
                    for (let item of selector.querySelectorAll('.swiper-slide:not(.swiper-slide-active):not(.swiper-slide-duplicate-active) .pagex-animated')) {
                        setTimeout(function () {
                            item.classList.remove('pagex-animated');
                        }, 60);
                    }

                    for (let item of selector.querySelectorAll('.swiper-slide-active [data-animate]')) {
                        item.classList.add('pagex-animated');
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

    next: function (id) {
        let slider = document.getElementById(id).querySelector('.swiper-container').swiper;

        slider.slideNext();
        return false;
    },

    prev: function (id) {
        let slider = document.getElementById(id).querySelector('.swiper-container').swiper;

        slider.slidePrev();
        return false;
    }
};


var pagexCountdown = {
    initElements: function () {
        for (let item of document.querySelectorAll('.pagex-countdown')) {
            this.init(item);
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
        for (let item of document.querySelectorAll('.pagex-section-fixed')) {
            item.classList.remove('pagex-section-fixed');
        }

        for (let item of document.querySelectorAll('.pagex-animated')) {
            item.classList.remove('pagex-animated');
        }

        Waypoint.destroyAll();

        this.setupRefresh();
    },

    refreshCurrentElementWaypoint: function () {
        if (typeof pagex !== "undefined") {
            for (let item of pagex.currentElement.querySelectorAll('.pagex-animated')) {
                item.classList.remove('pagex-animated');
            }

            this.setupRefresh();
        }
    },

    setupRefresh: function () {
        setTimeout(function () {
            pagexSticky.initElements();
            pagexEntranceAnimation.initElements();
            pagexSlider.initElements();
        }, 200);

        setTimeout(function () {
            Waypoint.refreshAll();
        }, 400);
    }
};

var pagexSticky = {
    initElements: function () {
        for (let item of document.querySelectorAll('.pagex-section-position-fixed')) {
            let frontOffset = document.body.matches('.admin-bar') ? 31 : -1;
            new Waypoint({
                element: item,
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
        for (let item of document.querySelectorAll('[data-animate]')) {
            let delay = item.getAttribute('data-animate-delay');
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
    map_styles: {
        black: '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',

        darkblue: '[{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}]',

        greyscale: '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',

        white: '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]',
    },

    initElements: function () {
        // init google maps with js API
        for (let item of document.querySelectorAll('.pagex-google-maps-embed')) {
            this.renderAPIMap(item);
        }

        // init google maps with iframe API
        for (let item of document.querySelectorAll('.pagex-google-maps-iframe')) {
            item.src = item.getAttribute('data-lazy-load');
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
            mapOptions.styles = JSON.parse(this.map_styles[data.style]);
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

var pagexWooCommerce = {
    applyRating: function (el) {
        for (let item of document.querySelectorAll('.pagex-star-rating')) {
            item.classList.remove('active');
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
            (path ? '; path=' + path : '') +
            (domain ? '; domain=' + domain : '') +
            (secure ? '; secure' : '');
    },

    remove: function (name) {
        this.set(name, '', -1000);
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

    if (elName === 'countdown') {
        pagexCountdown.init(el.querySelector('[data-countdown]'));
    }

    if (elName === 'google_maps') {
        if (el.querySelector('[data-google-map]')) {
            pagexGoogleMaps.renderAPIMap(el.querySelector('[data-google-map]'));
        }
    }

    if (elName === 'menu_cart') {
        jQuery(document.body).trigger('wc_fragment_refresh');
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
    if (!e.target) v;
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
    if (hr = el.closest('[href]')) {
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

// Preloader
if (document.getElementById('pagex-main-preloader')) {
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
