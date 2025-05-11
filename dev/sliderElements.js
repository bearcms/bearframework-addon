/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

var bearCMS = bearCMS || {};
bearCMS.sliderElements = bearCMS.sliderElements || (function () {

    var touchEvents = ivoPetkov.bearFrameworkAddons.touchEvents;

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
        if (time.indexOf('ms') !== -1) {
            return parseInt(time.replace('ms', ''));
        } else if (time.indexOf('s') !== -1) {
            return parseInt(time.replace('s', '')) * 1000;
        }
        return null;
    };

    var setTransitionsStatus = function (element, enabled) {
        if (enabled) {
            element.removeAttribute('data-bearcms-slider-no-transition');
        } else {
            element.setAttribute('data-bearcms-slider-no-transition', '');
        }
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

    var getSize = function (element, includeMargins) {
        if (typeof includeMargins === "undefined") {
            includeMargins = false;
        }
        var rect = element.getBoundingClientRect();
        if (includeMargins) {
            var style = window.getComputedStyle(element);
            return {
                width: rect.width + parseInt(style.marginLeft) + parseInt(style.marginRight), // parseInt removes px
                height: rect.height + parseInt(style.marginTop) + parseInt(style.marginBottom)
            };
        } else {
            return {
                width: rect.width,
                height: rect.height,
            };
        }
    };

    var isVisible = function (targetElement) {
        var rect = targetElement.getBoundingClientRect();
        if (rect.width === 0 && rect.height === 0) { // display = none
            return false;
        }
        var style = getComputedStyle(targetElement);
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
            [right, bottom],
            [(right - left) / 2 + left, (bottom - top) / 2 + top], // center
            [(right - left) / 2 + left, top], // center top
            [(right - left) / 2 + left, bottom], // center top
            [left, (bottom - top) / 2 + top], // center left
            [right, (bottom - top) / 2 + top] // center right
        ];
        for (var i = 0; i < pointsToTest.length; i++) {
            var pointToTest = pointsToTest[i];
            var foundElement = document.elementFromPoint(pointToTest[0], pointToTest[1]);
            if (foundElement === targetElement || isChildOf(targetElement, foundElement)) {
                return true;
            }
        }
        return false;
    };

    var getSlidesElements = function (element) {
        return element.firstChild.childNodes;
    };

    var getSlides = function (element) {
        var result = [];
        var isEditableElement = isEditable(element);
        var slides = getSlidesElements(element);
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
                        prepareInfiniteSlide(element, null, index);
                        showSlide(element, index);
                    });
                })(i);
            }
        }
    };

    var updateIndicators = function (element) {
        var index = getElementData(element, 'index');
        var container = element.querySelector('[data-bearcms-slider-indicators]');
        if (areAllSlidesVisible(element)) {
            container.style.display = 'none';
        } else {
            container.style.removeProperty('display');
        }
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

    var getDirection = function (element) {
        return element.getAttribute('data-bearcms-slider-direction');
    };

    var getAutoplay = function (element) {
        return element.getAttribute('data-bearcms-slider-autoplay');
    };

    var isInfinite = function (element) {
        return element.getAttribute('data-bearcms-slider-infinite') !== null;
    };

    var areAllSlidesVisible = function (element) {
        var direction = getDirection(element);
        if (direction === 'horizontal' || direction === 'vertical') {
            var containerSize = getSize(element.firstChild);
            var allSlidesSize = 0;
            var slides = getSlides(element);
            var slidesCount = slides.length;
            for (var i = 0; i < slidesCount; i++) {
                var slideSize = getSize(slides[i], true);
                if (direction === 'horizontal') {
                    allSlidesSize += slideSize.width;
                } else {
                    allSlidesSize += slideSize.height;
                }
            }
            if (direction === 'horizontal') {
                return allSlidesSize < containerSize.width;
            } else {
                return allSlidesSize < containerSize.height;
            }
        }
        return false;
    }

    var prepareInfiniteSlide = function (element, oldIndex, newIndex) {
        if (oldIndex === null) {
            oldIndex = getElementData(element, 'index');
        }
        var infiniteSlidesData = null;
        if (isInfinite(element) && getDirection(element) === 'horizontal') {
            infiniteSlidesData = {
                firstIndexes: [],
                lastIndexes: [],
                indexes: [],
                offsets: []
            };
            var slides = getSlides(element);
            var slidesCount = slides.length;
            var lastSlideIndex = slidesCount - 1;
            var containerWidth = getSize(element.firstChild).width;
            var allSlidesWidth = 0;
            var slidesWidths = [];
            for (var i = 0; i < slidesCount; i++) {
                slidesWidths[i] = getSize(slides[i], true).width;
                allSlidesWidth += slidesWidths[i];
            }
            if (containerWidth * 3 <= allSlidesWidth && slidesCount >= 3) { // allow infinite if has enough slides
                var slidesSum = 0;
                for (var i = 0; i < slidesCount; i++) {
                    infiniteSlidesData.offsets[i] = containerWidth + 'px';
                    infiniteSlidesData.firstIndexes.push(i);
                    slidesSum += slidesWidths[i];
                    if (slidesSum >= containerWidth) {
                        break;
                    }
                }
                var slidesSum = 0;
                for (var i = lastSlideIndex; i >= 0; i--) {
                    infiniteSlidesData.offsets[i] = -allSlidesWidth + 'px';
                    infiniteSlidesData.lastIndexes.push(i);
                    slidesSum += slidesWidths[i];
                    if (slidesSum >= containerWidth) {
                        break;
                    }
                }
                if (lastSlideIndex === newIndex && newIndex > oldIndex) { // forward to the last one
                    infiniteSlidesData.indexes = infiniteSlidesData.firstIndexes;
                } else if (newIndex === 0 && newIndex < oldIndex) { // back to the first one
                    infiniteSlidesData.indexes = infiniteSlidesData.lastIndexes;
                }
            }
        }
        setElementData(element, 'infiniteSlidesData', infiniteSlidesData);
    };

    var updateElement = function (element) {
        var direction = getDirection(element);
        var autoplay = getAutoplay(element);
        var infinite = isInfinite(element);
        var infiniteSlidesData = infinite ? getElementData(element, 'infiniteSlidesData') : null;

        if (isEditable(element)) {
            var allSlides = getSlidesElements(element);
            var allSlidesCount = allSlides.length;
            var lastSlide = allSlidesCount > 0 ? allSlides[allSlidesCount - 1] : null;
            if (lastSlide === null || lastSlide.childNodes.length > 0) {
                element.firstChild.insertAdjacentHTML("beforeend", "<div></div>");
                rebuildIndicators(element);
            }
            var removeLastSlideIfLastTwoEmpty = function () {
                var allSlides = getSlidesElements(element);
                var allSlidesCount = allSlides.length;
                if (allSlidesCount >= 2) {
                    var lastSlide1 = allSlides[allSlidesCount - 1];
                    var lastSlide2 = allSlides[allSlidesCount - 2];
                    if (lastSlide1.childNodes.length === 0 && lastSlide2.childNodes.length === 0) {
                        lastSlide1.parentNode.removeChild(lastSlide1);
                        rebuildIndicators(element);
                        return true;
                    }
                }
                return false;
            };
            for (var i = 0; i < allSlidesCount; i++) {
                if (!removeLastSlideIfLastTwoEmpty()) {
                    break;
                }
            }
        }

        var slides = getSlides(element);
        var slidesCount = slides.length;
        var index = getElementData(element, 'index');
        var previousButton = getPreviousButton(element);
        var nextButton = getNextButton(element);
        setButtonVisibility(previousButton, infinite ? true : index > 0);
        setButtonVisibility(nextButton, infinite ? true : index + 1 < slidesCount);
        var allSlidesAreVisible = areAllSlidesVisible(element);
        if (allSlidesAreVisible) {
            previousButton.style.display = 'none';
        } else {
            previousButton.style.removeProperty('display');
        }
        if (allSlidesAreVisible) {
            nextButton.style.display = 'none';
        } else {
            nextButton.style.removeProperty('display');
        }
        var slideX = 0;
        var slideY = 0;
        var slidesDefaultY = [0];
        var lastSlideWidth = 0;
        if (direction === 'horizontal' || direction === 'vertical') {
            if (slidesCount > 0) {
                var allSlidesWidth = 0;
                var allSlidesHeight = 0;
                var containerSize = getSize(element.firstChild);
                for (var i = 0; i < slidesCount; i++) {
                    var slide = slides[i];
                    var slideSize = getSize(slide, true);
                    if (i < index) {
                        slideX += slideSize.width;
                        slideY += slideSize.height;
                    }
                    if (i > 0) {
                        for (var j = i; j < slidesCount; j++) {
                            if (typeof slidesDefaultY[j] === 'undefined') {
                                slidesDefaultY[j] = 0;
                            }
                            slidesDefaultY[j] += slideSize.height;
                        }
                    }
                    allSlidesWidth += slideSize.width;
                    allSlidesHeight += slideSize.height;
                    lastSlideWidth = slideSize.width;
                }
                var containerWidth = containerSize.width;
                var containerHeight = containerSize.height;
                if (containerWidth + slideX > allSlidesWidth) {
                    slideX += allSlidesWidth - (containerWidth + slideX);
                }
                if (containerHeight + slideY > allSlidesHeight) {
                    slideY += allSlidesHeight - (containerHeight + slideY);
                }
            }
        }
        var isInfinitePosition = function (value) {
            return value.indexOf('calc(') !== -1;
        };
        for (var i = 0; i < slidesCount; i++) {
            var slide = slides[i];
            var slideSize = getSize(slide, true);
            var x = '0px';
            var y = '0px';
            var opacity = '1';
            var enabled = true;
            if (direction === 'horizontal') {
                if (index > 0) {
                    x = '-' + slideX + 'px';
                }
                if (infiniteSlidesData !== null && infiniteSlidesData.indexes.indexOf(i) !== -1) {
                    x = 'calc(' + infiniteSlidesData.offsets[i] + ' + 0px)';
                }
            } else if (direction === 'vertical') {
                if (i > 0) {
                    x = '-' + (i * 100) + '%';
                }
                y = (slidesDefaultY[i] - slideY) + 'px';
            } else if (direction === 'swap') {
                if (i > 0) {
                    x = '-' + (i * 100) + '%';
                }
                if (i !== index) {
                    opacity = '0';
                    enabled = false;
                }
            }

            var previousX = slide.style.getPropertyValue('--bse-slide-x');
            var forceTransitionUpdate = function (targetElement) {
                var style = window.getComputedStyle(targetElement);
                var temp = style.getPropertyValue('transition'); // force
            };
            var tempDisableTransition = false;
            if (infiniteSlidesData !== null) {
                var lastSlideIndex = slidesCount - 1;
                var infiniteSlidesIndexes = infiniteSlidesData.indexes;
                if (infiniteSlidesIndexes.indexOf(0) !== -1) { // first
                    if (infiniteSlidesIndexes.indexOf(i) !== -1) {
                        if (!isInfinitePosition(previousX) && previousX !== '0px') { // 0px
                            tempDisableTransition = true;
                        }
                    } else if (infiniteSlidesData.firstIndexes.indexOf(i) === -1 && infiniteSlidesData.lastIndexes.indexOf(i) === -1 && previousX === '0px') {
                        tempDisableTransition = true;
                    }
                } else if (infiniteSlidesIndexes.indexOf(lastSlideIndex) !== -1) { // last
                    if (infiniteSlidesIndexes.indexOf(i) !== -1) {
                        if (isInfinitePosition(x) && (previousX === '-' + lastSlideWidth + 'px' || previousX === '')) {
                            tempDisableTransition = true;
                        }
                    } else if (infiniteSlidesData.firstIndexes.indexOf(i) === -1 && infiniteSlidesData.lastIndexes.indexOf(i) === -1 && previousX !== '-' + containerWidth + 'px') {
                        tempDisableTransition = true;
                    }
                } else if (infiniteSlidesIndexes.length === 0) {
                    if (isInfinitePosition(previousX)) {
                        tempDisableTransition = true;
                    }
                }
            }
            if (tempDisableTransition) {
                slide.style.setProperty('transition', 'none', 'important');
                forceTransitionUpdate(slide);
            }
            slide.style.setProperty('--bse-slide-x', x);
            slide.style.setProperty('--bse-slide-y', y);
            slide.style.setProperty('opacity', opacity);
            slide.style.setProperty('pointer-events', enabled ? 'auto' : 'none');
            if (tempDisableTransition) {
                forceTransitionUpdate(slide);
                slide.style.removeProperty('transition');
                forceTransitionUpdate(slide);
            }
        }

        if (getElementData(element, 'autoplayValue') !== autoplay) {
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

    var showSlide = function (element, index, options) {
        var currentIndex = getElementData(element, 'index');
        if (currentIndex === index) {
            return;
        }
        setElementData(element, 'index', index);
        updateElement(element);
        var options = typeof options !== 'undefined' ? options : {};
        var onShowSlide = typeof options.onShowSlide !== 'undefined' ? options.onShowSlide : null;
        if (onShowSlide !== null) {
            var slideSpeed = getSliderSpeed(element);
            if (slideSpeed !== null) {
                setTimeout(onShowSlide, slideSpeed);
            } else {
                onShowSlide();
            }
        }
    };

    var changeSlide = function (element, change) {
        if (change === 0) {
            return;
        }
        var infinite = isInfinite(element);
        var index = getElementData(element, 'index');
        var newIndex = index + change;
        var slidesCount = getSlides(element).length;
        if (newIndex < 0) {
            newIndex = infinite ? slidesCount - 1 : 0;
        } else if (newIndex > slidesCount - 1) {
            newIndex = infinite ? 0 : slidesCount - 1;
        }
        prepareInfiniteSlide(element, index, newIndex);
        showSlide(element, newIndex);
    };

    var setSwipeValue = function (element, x, y) {
        element.style.setProperty('--bse-swipe-x', x);
        element.style.setProperty('--bse-swipe-y', y);
    };

    var slideToChildElement = function (element, childElement, options) {
        var slides = getSlidesElements(element);
        for (var i = 0; i < slides.length; i++) {
            var slide = slides[i];
            var slideChildren = slide.childNodes;
            for (var j = 0; j < slideChildren.length; j++) {
                if (slideChildren[j] === childElement) {
                    showSlide(element, i, options);
                    return;
                }
            }
        }
    };

    var isInVisibleSlide = function (element, childElement) {
        var index = getElementData(element, 'index');
        var slides = getSlidesElements(element);
        if (typeof slides[index] !== 'undefined') {
            var slide = slides[index];
            var slideChildren = slide.childNodes;
            for (var j = 0; j < slideChildren.length; j++) {
                if (slideChildren[j] === childElement) {
                    return true;
                }
            }
        }
        return false;
    };

    var getSliderSpeed = function (element) {
        var speed = getComputedStyle(element).getPropertyValue('--bearcms-slider-element-speed');
        if (speed !== '') {
            return timeToMilliseconds(speed);
        }
        return null;
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
                    var autoplayPausedByTouch = false;
                    element.addEventListener('touchstart', function () {
                        if (!autoplayPausedByTouch) {
                            autoplayPausedByTouch = true;
                            pauseAutoplay(element);
                        }
                    });
                    setElementData(element, 'index', 0);
                    rebuildIndicators(element);
                    updateIndicators(element);
                    prepareInfiniteSlide(element, 1, 0);

                    element.slideTo = function (index) {
                        showSlide(element, index);
                    };

                    element.slideToElement = function (childElement, options) {
                        slideToChildElement(element, childElement, options);
                    };

                    element.isInVisibleSlide = function (childElement) {
                        return isInVisibleSlide(element, childElement);
                    };

                    (new MutationObserver(function () { // editable changed
                        // todo move to adjacent visible slide + update indicators
                        updateElement(element);
                        rebuildIndicators(element);
                        updateIndicators(element);
                    })).observe(element, { attributeFilter: ["data-rvr-editable"] });

                    var swipeEventTarget = touchEvents.addSwipe(element.firstChild, element.ownerDocument.body);
                    swipeEventTarget.addEventListener('start', function (e) {
                        setSwipeValue(element, '0px', '0px');
                        if (element.getAttribute('data-bearcms-slider-swipe') === null) {
                            return;
                        }
                        if (areAllSlidesVisible(element)) {
                            return;
                        }
                        var direction = getDirection(element);
                        if (direction === 'horizontal' || direction === 'vertical') {
                            setTransitionsStatus(element, false);
                        }
                    });
                    swipeEventTarget.addEventListener('change', function (e) {
                        if (element.getAttribute('data-bearcms-slider-swipe') === null) {
                            return;
                        }
                        if (areAllSlidesVisible(element)) {
                            return;
                        }
                        var direction = getDirection(element);
                        if (direction === 'horizontal') {
                            setSwipeValue(element, e.changeX + 'px', '0px');
                        } else if (direction === 'vertical') {
                            setSwipeValue(element, '0px', e.changeY + 'px');
                        }
                    });
                    swipeEventTarget.addEventListener('end', function (e) {
                        if (element.getAttribute('data-bearcms-slider-swipe') === null) {
                            return;
                        }
                        if (areAllSlidesVisible(element)) {
                            return;
                        }
                        setTransitionsStatus(element, true);
                        setSwipeValue(element, '0px', '0px');
                        var direction = getDirection(element);
                        var change = null;
                        if (direction === 'horizontal' || direction === 'swap') {
                            change = e.changeX;
                        } else if (direction === 'vertical') {
                            change = e.changeY;
                        }
                        if (change === null) {
                            return;
                        }
                        var absChange = Math.abs(change);
                        if (absChange > 40) {
                            var isForwardSwipe = change < 0;
                            if (direction === 'swap') {
                                changeSlide(element, isForwardSwipe ? 1 : -1);
                            } else {
                                var infinite = isInfinite(element);
                                var index = getElementData(element, 'index');
                                var slides = getSlides(element);
                                var slidesCount = slides.length;
                                var sizeSum = 0;
                                var newIndex = null;
                                var indexesToCheck = [];
                                if (isForwardSwipe) {
                                    for (var i = index; i < slidesCount; i++) {
                                        indexesToCheck.push(i);
                                    }
                                } else { // move backward
                                    for (var i = index; i >= 0; i--) {
                                        indexesToCheck.push(i);
                                    }
                                }
                                for (var i = 0; i < indexesToCheck.length; i++) {
                                    var indexToCheck = indexesToCheck[i];
                                    var slide = slides[indexToCheck];
                                    var slideSize = getSize(slide, true);
                                    var targetSize = direction === 'horizontal' ? slideSize.width : slideSize.height;
                                    if (sizeSum + (isForwardSwipe ? 0 : targetSize) >= absChange) {
                                        newIndex = indexToCheck + (isForwardSwipe ? 0 : -1);
                                        break;
                                    }
                                    sizeSum += targetSize;
                                }
                                if (newIndex === null) {
                                    newIndex = isForwardSwipe ? slidesCount - 1 : 0;
                                }
                                if (infinite && newIndex === index) {
                                    newIndex = isForwardSwipe ? 0 : slidesCount - 1;
                                }
                                changeSlide(element, newIndex - index);
                            }
                        }
                    });

                    setTransitionsStatus(element, true);

                })(element);
                initializedElements.push(element);
            }
            updateElement(element);
        }
    };

    document.addEventListener('readystatechange', function () { // interactive or complete
        update();
    });

    window.addEventListener('resize', function () {
        queueUpdate();
    });

    if (document.readyState === 'complete') {
        update();
    }

    var requestAnimationFrameFunction = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || function (callback) {
        window.setTimeout(callback, 1000 / 60);
    };

    var updateQueued = false;
    var queueUpdate = function () {
        if (!updateQueued) {
            updateQueued = true;
            requestAnimationFrameFunction(function () {
                updateQueued = false;
                update();
            });
        }
    };

    return {
        'update': update,
        'queueUpdate': queueUpdate
    };

}());