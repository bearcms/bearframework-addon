/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

/* global clientPackages */

var bearCMS = bearCMS || {};
bearCMS.sliderElements = bearCMS.sliderElements || (function () {

    var initializedElements = [];

    var elementsData = [];

    var setElementData = function (element, name, value) {
        var done = false;
        for (var i = 0; i < elementsData.length; i++) {
            var elementData = elementsData[i];
            if (elementData[0] === element) {
                elementData[1][name] = value;
                done = true;
                break;
            }
        }
        if (!done) {
            var data = {};
            data[name] = value;
            elementsData.push([element, data]);
        }
    };

    var getElementData = function (element, name) {
        for (var i = 0; i < elementsData.length; i++) {
            var elementData = elementsData[i];
            if (elementData[0] === element) {
                return typeof elementData[1][name] !== 'undefined' ? elementData[1][name] : null;
            }
        }
        return null;
    };

    var getPreviousButton = function (element) {
        return element.querySelector('[data-bearcms-slider-button-previous]');
    };

    var getNextButton = function (element) {
        return element.querySelector('[data-bearcms-slider-button-next]');
    };

    var setButtonVisibility = function (button, visible) {
        if (visible) {
            button.setAttribute('data-bearcms-slider-button-visible', '1');
        } else {
            button.removeAttribute('data-bearcms-slider-button-visible');
        }
    };

    var timeToMilliseconds = function (time) {
        if (time.indexOf('ms')) {
            return parseInt(time.replace('ms', '')) * 1000;
        } else if (time.indexOf('s')) {
            return parseInt(time.replace('s', '')) * 1000 * 1000;
        }
        return null;
    };

    var isEditable = function (element) {
        return element.getAttribute('data-rvr-editable') === '1'; // temp
    };

    var isChildOf = function (parentElement, childElement) {
        if (childElement !== null && parentElement !== null) {
            var node = childElement.parentNode;
            while (node !== null) {
                if (node === parentElement) {
                    return true;
                }
                node = node.parentNode;
            }
        }
        return false;
    };

    var isVisible = function (element) {
        var rect = element.getBoundingClientRect();
        if (rect.width === 0 && rect.height === 0) { // display = none
            return false;
        }
        var style = getComputedStyle(element);
        var getBorderWidth = function (name) {
            return parseInt(style.getPropertyValue('border-' + name + '-width').replace('px', ''));
        };
        // check if is behind other element or overflown
        var left = rect.left + 1 + getBorderWidth('left');
        var right = rect.right - 1 + getBorderWidth('right');
        var top = rect.top + 1 + getBorderWidth('top');
        var bottom = rect.bottom - 1 + getBorderWidth('bottom');
        var pointsToTest = [
            [left, top],
            [right, top],
            [left, bottom],
            [right, bottom]
        ];
        for (var i = 0; i < pointsToTest.length; i++) {
            var pointToTest = pointsToTest[i];
            var foundElement = document.elementFromPoint(pointToTest[0], pointToTest[1]);
            if (foundElement === element || isChildOf(element, foundElement)) {
                return true;
            }
        }
        return false;
    };

    var getSlides = function (element) {
        var result = [];
        var isEditableElement = isEditable(element);
        var slides = element.firstChild.childNodes;
        for (var i = 0; i < slides.length; i++) {
            var slide = slides[i];
            if (!isEditableElement && slide.childNodes.length === 0) {
                continue;
            }
            result.push(slide);
        }
        return result;
    };

    var rebuildIndicators = function (element) {
        var container = element.querySelector('[data-bearcms-slider-indicators]');
        var slides = getSlides(element);
        var slidesCount = slides.length;
        var html = '';
        if (slidesCount > 1) {
            for (var i = 0; i < slidesCount; i++) {
                html += '<span data-bearcms-slider-indicator></span>';
            }
        }
        container.innerHTML = html;
        if (slidesCount > 1) {
            for (var i = 0; i < slidesCount; i++) {
                (function (index) {
                    container.childNodes[index].addEventListener('click', function () {
                        showSlide(element, index);
                    });
                })(i);
            }
        }
    };

    var updateIndicators = function (element) {
        var index = getElementData(element, 'index');
        var container = element.querySelector('[data-bearcms-slider-indicators]');
        var buttons = container.childNodes;
        for (var i = 0; i < buttons.length; i++) {
            if (typeof buttons[i] !== 'undefined') {
                var button = buttons[i];
                if (i === index) {
                    button.setAttribute('data-bearcms-slider-indicator-selected', '');
                } else {
                    button.removeAttribute('data-bearcms-slider-indicator-selected');
                }
            }
        };
    };

    var updateElement = function (element) {
        var direction = element.getAttribute('data-bearcms-slider-direction');
        var autoplay = element.getAttribute('data-bearcms-slider-autoplay');
        //var swipe = element.getAttribute('data-bearcms-slider-swipe');

        if (isEditable(element)) {
            var allSlides = element.firstChild.childNodes;
            var lastSlide = allSlides[allSlides.length - 1];
            if (lastSlide.childNodes.length > 0) {
                lastSlide.parentNode.insertAdjacentHTML("beforeend", "<div></div>");
                rebuildIndicators(element);
            }
        }

        var slides = getSlides(element);
        var slidesCount = slides.length;
        var index = getElementData(element, 'index');
        var previousButton = getPreviousButton(element);
        var nextButton = getNextButton(element);
        setButtonVisibility(previousButton, index > 0);
        setButtonVisibility(nextButton, index + 1 < slidesCount);

        for (var i = 0; i < slidesCount; i++) {
            var slide = slides[i];
            var x = '0';
            var y = '0';
            var opacity = '1';
            var enabled = true;
            if (direction === 'horizontal') {
                if (index > 0) {
                    x = '-' + (index * 100) + '%';
                }
                if (i !== index) {
                    enabled = false;
                }
            } else if (direction === 'vertical') {
                if (i > 0) {
                    x = '-' + (i * 100) + '%';
                }
                y = ((i - index) * 100) + '%';
                if (i !== index) {
                    enabled = false;
                }
            } else if (direction === 'swap') {
                if (i > 0) {
                    x = '-' + (i * 100) + '%';
                }
                if (i !== index) {
                    opacity = '0';
                    enabled = false;
                }
            }
            slide.style.setProperty('transform', 'translateX(' + x + ') translateY(' + y + ')');
            slide.style.setProperty('opacity', opacity);
            slide.style.setProperty('pointer-events', enabled ? 'all' : 'none');
        }

        if (getElementData(element, 'autoplayValue') !== autoplay) {// && !isAutoplayDisabled(element)
            clearAutoplay(element);
            setElementData(element, 'autoplayValue', autoplay);
            if (autoplay !== null) {
                var autoplayTime = timeToMilliseconds(autoplay);
                if (autoplayTime !== null) {
                    var timeout = window.setInterval(function () {
                        if (isAutoplayPaused(element)) {
                            return;
                        }
                        if (!isVisible(element)) {
                            return;
                        }
                        changeSlide(element, 1);
                    }, autoplayTime);
                    setElementData(element, 'autoplayInterval', timeout);
                }
            }
        }

        updateIndicators(element);
    };

    // var isAutoplayDisabled = function (element) {
    //     return getElementData(element, 'autoplayDisabled') !== null;
    // };

    // var disableAutoplay = function (element) {
    //     setElementData(element, 'autoplayDisabled', 1);
    // };

    var isAutoplayPaused = function (element) {
        return getElementData(element, 'autoplayPaused') !== null;
    };

    var pauseAutoplay = function (element) {
        setElementData(element, 'autoplayPaused', 1);
    };

    var resumeAutoplay = function (element) {
        setElementData(element, 'autoplayPaused', null);
    };

    var clearAutoplay = function (element) {
        window.clearInterval(getElementData(element, 'autoplayInterval'));
    };

    var showSlide = function (element, index) {
        setElementData(element, 'index', index);
        updateElement(element);
    };

    var changeSlide = function (element, change) {
        var index = getElementData(element, 'index');
        index += change;
        var slidesCount = getSlides(element).length;
        if (index < 0) {
            index = 0;
        } else if (index > slidesCount - 1) {
            index = slidesCount - 1;
        }
        showSlide(element, index);
    };

    var update = function (element) {
        if (typeof element !== 'undefined') {
            var elements = [element];
        } else {
            var elements = document.querySelectorAll('.bearcms-slider-element');
        }
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (initializedElements.indexOf(element) === -1) {
                (function (element) {
                    var previousButton = getPreviousButton(element);
                    var nextButton = getNextButton(element);
                    previousButton.addEventListener('click', function () {
                        changeSlide(element, -1);
                    });
                    nextButton.addEventListener('click', function () {
                        changeSlide(element, 1);
                    });
                    element.addEventListener('mouseenter', function () {
                        pauseAutoplay(element);
                    });
                    element.addEventListener('mouseleave', function () {
                        resumeAutoplay(element);
                    });
                    setElementData(element, 'index', 0);
                    rebuildIndicators(element);
                    updateIndicators(element);
                    (new MutationObserver(function () { // editable changed
                        // todo move to adjacent visible slide + update indicators
                        updateElement(element);
                        rebuildIndicators(element);
                        updateIndicators(element);
                    })).observe(element, { attributeFilter: ["data-rvr-editable"] });
                })(element);
                initializedElements.push(element);
            }
            updateElement(element);
        }
    };

    document.addEventListener('readystatechange', function () { // interactive or complete
        update();
    });

    if (document.readyState === 'complete') {
        update();
    }

    return {
        'update': update
    };

}());