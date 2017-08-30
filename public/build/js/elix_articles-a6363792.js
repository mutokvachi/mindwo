/*!
 * Cube Portfolio - Responsive jQuery Grid Plugin
 *
 * version: 3.2.1 (14 October, 2015)
 * require: jQuery v1.7+
 *
 * Copyright 2013-2015, Mihai Buricea (http://scriptpie.com/cubeportfolio/live-preview/)
 * Licensed under CodeCanyon License (http://codecanyon.net/licenses)
 *
 */

(function($, window, document, undefined) {
    'use strict';

    function CubePortfolio(obj, options, callback) {
        /*jshint validthis: true */
        var t = this,
            initialCls = 'cbp',
            children;

        if ($.data(obj, 'cubeportfolio')) {
            throw new Error('cubeportfolio is already initialized. Destroy it before initialize again!');
        }

        // attached this instance to obj
        $.data(obj, 'cubeportfolio', t);

        // extend options
        t.options = $.extend({}, $.fn.cubeportfolio.options, options);

        // store the state of the animation used for filters
        t.isAnimating = true;

        // default filter for plugin
        t.defaultFilter = t.options.defaultFilter;

        // registered events (observator & publisher pattern)
        t.registeredEvents = [];

        // queue for this plugin
        t.queue = [];

        // has wrapper
        t.addedWrapp = false;

        // register callback function
        if ($.isFunction(callback)) {
            t.registerEvent('initFinish', callback, true);
        }

        // js element
        t.obj = obj;

        // jquery element
        t.$obj = $(obj);

        // when there are no .cbp-item
        children = t.$obj.children();

        // if caption is active
        if (t.options.caption) {
            if (t.options.caption !== 'expand' && !CubePortfolio.Private.modernBrowser) {
                t.options.caption = 'minimal';
            }

            // .cbp-caption-active is used only for css
            // so it will not generate a big css from sass if a caption is set
            initialCls += ' cbp-caption-active cbp-caption-' + t.options.caption;
        }

        t.$obj.addClass(initialCls);

        if (children.length === 0 || children.first().hasClass('cbp-item')) {
            t.wrapInner(t.obj, 'cbp-wrapper');
            t.addedWrapp = true;
        }

        // jquery wrapper element
        t.$ul = t.$obj.children().addClass('cbp-wrapper');

        // wrap the $ul in a outside wrapper
        t.wrapInner(t.obj, 'cbp-wrapper-outer');

        t.wrapper = t.$obj.children('.cbp-wrapper-outer');

        t.blocks = t.$ul.children('.cbp-item');
        t.blocksOn = t.blocks;

        // wrap .cbp-item-wrap div inside .cbp-item
        t.wrapInner(t.blocks, 'cbp-item-wrapper');

        // plugins
        t.plugins = $.map(CubePortfolio.Plugins, function(pluginName) {
            return pluginName(t);
        });

        // wait to load all images and then go further
        t.loadImages(t.$obj, t.display);
    }


    $.extend(CubePortfolio.prototype, {
        storeData: function(blocks, indexStart) {
            var t = this;

            indexStart = indexStart || 0; // used by loadMore

            blocks.each(function(index, el) {
                var item = $(el),
                    width = item.width(),
                    height = item.height();

                item.data('cbp', {
                    index: indexStart + index, // used when I sort the items and I need them to revert that sorting
                    wrapper: item.children('.cbp-item-wrapper'),

                    widthInitial: width,
                    heightInitial: height,

                    width: width, // used by drag & drop wp @todo - maybe I will use widthAndGap
                    height: height,

                    widthAndGap: width + t.options.gapVertical,
                    heightAndGap: height + t.options.gapHorizontal,

                    left: null,
                    leftNew: null,
                    top: null,
                    topNew: null,

                    pack: false,
                });
            });
        },


        // http://bit.ly/pure-js-wrap
        wrapInner: function(items, classAttr) {
            var t = this,
                item, i, div;

            classAttr = classAttr || '';

            if (items.length && items.length < 1) {
                return; // there are no .cbp-item
            } else if (items.length === undefined) {
                items = [items];
            }

            for (i = items.length - 1; i >= 0; i--) {
                item = items[i];

                div = document.createElement('div');

                div.setAttribute('class', classAttr);

                while (item.childNodes.length) {
                    div.appendChild(item.childNodes[0]);
                }

                item.appendChild(div);
            }
        },


        /**
         * Wait to load all images
         */
        loadImages: function(elems, callback) {
            var t = this;

            // wait a frame (Safari bug)
            requestAnimationFrame(function() {
                var src = elems.find('img').map(function(index, el) {
                    return t.checkSrc(el.src);
                });

                var srcLength = src.length;

                if (srcLength === 0) {
                    callback.call(t);
                    return;
                }

                $.each(src, function(i, el) {
                    $('<img>').on('load.cbp error.cbp', function() {
                        srcLength--;

                        if (srcLength === 0) {
                            callback.call(t);
                        }
                    }).attr('src', el); // for ie8
                });
            });
        },


        checkSrc: function(src) {
            if (src === '') {
                return null;
            }

            var img = new Image();
            img.src = src;

            if (img.complete && img.naturalWidth !== undefined && img.naturalWidth !== 0) {
                return null;
            }

            return src;
        },


        /**
         * Show the plugin
         */
        display: function() {
            var t = this;

            // store main container width
            t.width = t.$obj.outerWidth();

            // store to data some values of t.blocks
            t.storeData(t.blocks);

            t.triggerEvent('initStartRead');
            t.triggerEvent('initStartWrite');

            // create mark-up for slider layout
            if (t.options.layoutMode === 'slider') {
                t.registerEvent('gridAdjust', function() {
                    t.sliderMarkup();
                }, true);
            }

            // make layout
            t.layoutAndAdjustment();

            t.triggerEvent('initEndRead');
            t.triggerEvent('initEndWrite');

            // plugin is ready to show and interact
            t.$obj.addClass('cbp-ready');

            t.runQueue('delayFrame', t.delayFrame);
        },


        delayFrame: function() {
            var t = this;

            requestAnimationFrame(function() {
                t.resizeEvent();

                t.triggerEvent('initFinish');

                // animating is now false
                t.isAnimating = false;

                // trigger public event initComplete
                t.$obj.trigger('initComplete.cbp');
            });
        },


        /**
         * Add resize event when browser width changes
         */
        resizeEvent: function() {
            var t = this,
                gridWidth;

            CubePortfolio.Private.initResizeEvent({
                instance: t,
                fn: function() {
                    var tt = this;

                    // used by wp fullWidth force option
                    tt.triggerEvent('beforeResizeGrid');

                    gridWidth = tt.$obj.outerWidth();

                    if (tt.width !== gridWidth) {

                        if (tt.options.gridAdjustment === 'alignCenter') {
                            tt.wrapper[0].style.maxWidth = '';
                        }

                        // update the current grid width
                        tt.width = gridWidth;

                        // reposition the blocks with gridAdjustment set to true
                        tt.layoutAndAdjustment();

                        if (tt.options.layoutMode === 'slider') {
                            tt.updateSlider();
                        }

                        tt.triggerEvent('resizeGrid');
                    }

                    tt.triggerEvent('resizeWindow');
                }
            });
        },


        gridAdjust: function() {
            var t = this;

            // if responsive
            if (t.options.gridAdjustment === 'responsive') {
                t.responsiveLayout();
            } else {
                // reset the style attribute for all blocks so I can read a new width & height
                // for the current grid width. This is usefull for the styles defined in css
                // to create a custom responsive system.
                // Note: reset height if it was set for addHeightToBlocks
                t.blocks.removeAttr('style');

                t.blocks.each(function(index, el) {
                    var data = $(el).data('cbp'),
                        bound = el.getBoundingClientRect(),
                        width = t.columnWidthTruncate(bound.right - bound.left),
                        height = Math.round(bound.bottom - bound.top);

                    data.height = height;
                    data.heightAndGap = height + t.options.gapHorizontal;

                    data.width = width;
                    data.widthAndGap = width + t.options.gapVertical;
                });

                t.widthAvailable = t.width + t.options.gapVertical;
            }

            // used by slider layoutMode
            t.triggerEvent('gridAdjust');
        },


        layoutAndAdjustment: function() {
            var t = this;

            t.gridAdjust();

            t.layout();
        },


        /**
         * Build the layout
         */
        layout: function() {
            var t = this;

            t.computeBlocks(t.filterConcat(t.defaultFilter));

            if (t.options.layoutMode === 'slider') {
                t.sliderLayoutReset();
                t.sliderLayout();
            } else {
                t.mosaicLayoutReset();
                t.mosaicLayout();
            }


            // positionate the blocks
            t.positionateItems();

            // resize main container height
            t.resizeMainContainer();
        },


        computeFilter: function(expression) {
            var t = this;

            t.computeBlocks(expression);

            t.mosaicLayoutReset();
            t.mosaicLayout();

            // filter call layout
            t.filterLayout();
        },


        /**
         *  Default filter layout if nothing overrides
         */
        filterLayout: function() {
            var t = this;

            t.blocksOff.addClass('cbp-item-off');

            t.blocksOn.removeClass('cbp-item-off')
                .each(function(index, el) {
                    var data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';
                });

            // resize main container height
            t.resizeMainContainer();

            t.filterFinish();
        },


        /**
         *  Trigger when a filter is finished
         */
        filterFinish: function() {
            var t = this;

            // if blocks are sorted (the index ascending is broken) revert
            // this state so the index is ascending again
            if (t.blocksAreSorted) {
                t.sortBlocks(t.blocks, 'index');
            }

            t.isAnimating = false;

            t.$obj.trigger('filterComplete.cbp');
            t.triggerEvent('filterFinish');
        },


        computeBlocks: function(expression) {
            var t = this;

            // blocks that are visible before applying the filter
            t.blocksOnInitial = t.blocksOn;

            // blocks visible after applying the filter
            t.blocksOn = t.blocks.filter(expression);

            // blocks off after applying the filter
            t.blocksOff = t.blocks.not(expression);

            t.triggerEvent('computeBlocksFinish', expression);
        },


        /**
         * Make this plugin responsive
         */
        responsiveLayout: function() {
            var t = this;

            // calculate numbers of cols
            t.cols = t[($.isArray(t.options.mediaQueries) ? 'getColumnsBreakpoints' : 'getColumnsAuto')]();

            t.columnWidth = t.columnWidthTruncate((t.width + t.options.gapVertical) / t.cols);

            t.widthAvailable = t.columnWidth * t.cols;

            if (t.options.layoutMode === 'mosaic') {
                t.getMosaicWidthReference();
            }

            t.blocks.each(function(index, el) {                
                var data = $(el).data('cbp'),
                    cols = 1, // grid & slider layoutMode must be 1
                    width;

                if (t.options.layoutMode === 'mosaic') {
                    cols = t.getColsMosaic(data.widthInitial);
                }

                width = t.columnWidth * cols - t.options.gapVertical;

                el.style.width = width + 'px';
                data.width = width;
                data.widthAndGap = width + t.options.gapVertical;

                // reset height if it was set for addHeightToBlocks
                el.style.height = '';               
            });

            t.blocks.each(function(index, el) {
                var data = $(el).data('cbp'),
                    bound = el.getBoundingClientRect(),
                    height = Math.round(bound.bottom - bound.top);

                data.height = height;
                data.heightAndGap = height + t.options.gapHorizontal;
            });
        },


        getMosaicWidthReference: function() {
            var t = this,
                arrWidth = [];

            t.blocks.each(function(index, el) {
                var data = $(el).data('cbp');
                arrWidth.push(data.widthInitial);
            });

            arrWidth.sort(function(a, b) {
                return a - b;
            });

            if (arrWidth[0]) {
                t.mosaicWidthReference = arrWidth[0];
            } else {
                t.mosaicWidthReference = t.columnWidth;
            }
        },


        getColsMosaic: function(widthInitial) {
            var t = this;

            if (widthInitial === t.width) {
                return t.cols;
            }

            var ratio = widthInitial / t.mosaicWidthReference;

            if (ratio % 1 >= 0.79) {
                ratio = Math.ceil(ratio);
            } else {
                ratio = Math.floor(ratio);
            }

            return Math.min(Math.max(ratio, 1), t.cols);
        },


        /**
         * Get numbers of columns when t.options.mediaQueries is not an array
         */
        getColumnsAuto: function() {
            var t = this;

            if (t.blocks.length === 0) {
                return 1;
            }

            var columnWidth = t.blocks.first().data('cbp').widthInitial + t.options.gapVertical;

            return Math.max(Math.round(t.width / columnWidth), 1);
        },


        /**
         * Get numbers of columns if t.options.mediaQueries is an array
         */
        getColumnsBreakpoints: function() {
            var t = this,
                gridWidth = t.width,
                columns;

            $.each(t.options.mediaQueries, function(index, val) {
                if (gridWidth >= val.width) {
                    columns = val.cols;
                    return false;
                }
            });

            if (columns === undefined) {
                columns = t.options.mediaQueries[t.options.mediaQueries.length - 1].cols;
            }

            return columns;
        },


        /**
         *  Defines how the columns dimension & position (width, left) will be truncated
         *
         *  If you use `Math.*` there could be some issues with the items on the right side
         *  that can have some pixels hidden(1 or 2, depends on the number of columns)
         *  but this is a known limitation.
         *
         *  If you don't use the built-in captions effects (overlay at hover over an item) returning
         *  the possibly floated values may be a solution for the pixels hidden on the right side.
         *
         *  The column width must be an integer because browsers have some visual issues
         *  with transform properties for caption effects.
         *
         *  The initial behaviour was return Math.floor
         *
         */
        columnWidthTruncate: function(value) {
            return Math.floor(value);
        },


        positionateItems: function() {
            var t = this,
                data;

            t.blocksOn.removeClass('cbp-item-off')
                .each(function(index, el) {
                    data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';
                });

            t.blocksOff.addClass('cbp-item-off');

            // if blocks are sorted (the index ascending is broken) revert
            // this state so the index is ascending again
            if (t.blocksAreSorted) {
                t.sortBlocks(t.blocks, 'index');
            }
        },


        /**
         * Resize main container vertically
         */
        resizeMainContainer: function() {
            var t = this,
                height = Math.max(t.freeSpaces.slice(-1)[0].topStart - t.options.gapHorizontal, 0),
                maxWidth;

            // set max-width to center the grid if I need to
            if (t.options.gridAdjustment === 'alignCenter') {
                maxWidth = 0;

                t.blocksOn.each(function(index, el) {
                    var data = $(el).data('cbp'),
                        rightEdge = data.left + data.width;

                    if (rightEdge > maxWidth) {
                        maxWidth = rightEdge;
                    }
                });

                t.wrapper[0].style.maxWidth = maxWidth + 'px';
            }

            // set container height for `overflow: hidden` to be applied
            if (height === t.height) {
                return;
            }

            t.obj.style.height = height + 'px';

            // if resizeMainContainer is called for the first time skip this event trigger
            if (t.height !== undefined) {
                if (CubePortfolio.Private.modernBrowser) {
                    t.$obj.one(CubePortfolio.Private.transitionend, function() {
                        t.$obj.trigger('pluginResize.cbp');
                    });
                } else {
                    t.$obj.trigger('pluginResize.cbp');
                }
            }

            t.height = height;

            t.triggerEvent('resizeMainContainer');
        },


        filterConcat: function(filter) {
            return filter.replace(/\|/gi, '');
        },


        pushQueue: function(name, deferred) {
            var t = this;

            t.queue[name] = t.queue[name] || [];
            t.queue[name].push(deferred);
        },


        runQueue: function(name, fn) {
            var t = this,
                queue = t.queue[name] || [];

            $.when.apply($, queue).then($.proxy(fn, t));
        },


        clearQueue: function(name) {
            var t = this;

            t.queue[name] = [];
        },


        /**
         *  Register event
         */
        registerEvent: function(name, callbackFunction, oneTime) {
            var t = this;

            if (!t.registeredEvents[name]) {
                t.registeredEvents[name] = [];
            }

            t.registeredEvents[name].push({
                func: callbackFunction,
                oneTime: oneTime || false
            });
        },


        /**
         *  Trigger event
         */
        triggerEvent: function(name, param) {
            var t = this,
                i, len;

            if (t.registeredEvents[name]) {
                for (i = 0, len = t.registeredEvents[name].length; i < len; i++) {
                    t.registeredEvents[name][i].func.call(t, param);

                    if (t.registeredEvents[name][i].oneTime) {
                        t.registeredEvents[name].splice(i, 1);
                        // function splice change the t.registeredEvents[name] array
                        // if event is one time you must set the i to the same value
                        // next time and set the length lower
                        i--;
                        len--;
                    }
                }
            }
        },


        addItems: function(items, callback) {
            var t = this;

            // wrap .cbp-item-wrap div inside .cbp-item
            t.wrapInner(items, 'cbp-item-wrapper');

            items.addClass('cbp-item-loading').css({
                top: '100%',
                left: 0
            }).appendTo(t.$ul);

            if (CubePortfolio.Private.modernBrowser) {
                items.last().one(CubePortfolio.Private.animationend, function() {
                    t.addItemsFinish(items, callback);
                });
            } else {
                t.addItemsFinish(items, callback); // @todo - on ie8 & ie9 callback trigger to early
            }

            t.loadImages(items, function() {
                t.$obj.addClass('cbp-addItems');

                // push to data values of items
                t.storeData(items, t.blocks.length);

                // push the new items to t.blocks
                $.merge(t.blocks, items);

                t.triggerEvent('addItemsToDOM', items);

                t.layoutAndAdjustment();

                if (t.options.layoutMode === 'slider') {
                    t.updateSlider();
                }

                // if show count was actived, call show count function again
                if (t.elems) {
                    CubePortfolio.Public.showCounter.call(t.obj, t.elems);
                }
            });
        },


        addItemsFinish: function(items, callback) {
            var t = this;

            t.isAnimating = false;

            t.$obj.removeClass('cbp-addItems');
            items.removeClass('cbp-item-loading');

            if ($.isFunction(callback)) {
                callback.call(t);
            }
        }
    });


    /**
     * jQuery plugin initializer
     */
    $.fn.cubeportfolio = function(method, options, callback) {
        return this.each(function() {
            if (typeof method === 'object' || !method) {
                return CubePortfolio.Public.init.call(this, method, options);
            } else if (CubePortfolio.Public[method]) {
                return CubePortfolio.Public[method].call(this, options, callback);
            }

            throw new Error('Method ' + method + ' does not exist on jquery.cubeportfolio.js');
        });
    };

    CubePortfolio.Plugins = {};
    $.fn.cubeportfolio.Constructor = CubePortfolio;
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function Filters(parent) {
        var t = this;

        t.parent = parent;

        t.filters = $(parent.options.filters);
        t.filterData = [];

        // set default filter if it's present in url
        t.filterFromUrl();

        t.registerFilter();
    }

    Filters.prototype.registerFilter = function() {
        var t = this,
            parent = t.parent,
            filtersCallback,
            arr = parent.defaultFilter.split('|');

        t.wrap = t.filters.find('.cbp-l-filters-dropdownWrap')
            .on({
                'mouseover.cbp': function() {
                    $(this).addClass('cbp-l-filters-dropdownWrap-open');
                },
                'mouseleave.cbp': function() {
                    $(this).removeClass('cbp-l-filters-dropdownWrap-open');
                }
            });

        t.filters.each(function(index, el) {
            var filter = $(el),
                filterName = '*',
                items = filter.find('.cbp-filter-item'),
                dropdown = {};

            if (filter.hasClass('cbp-l-filters-dropdown')) {
                dropdown.wrap = filter.find('.cbp-l-filters-dropdownWrap');
                dropdown.header = filter.find('.cbp-l-filters-dropdownHeader');
                dropdown.headerText = dropdown.header.text();
            }

            // activate counter for filters
            parent.$obj.cubeportfolio('showCounter', items);

            $.each(arr, function(index, val) {
                if (items.filter('[data-filter="' + val + '"]').length) {
                    filterName = val;
                    arr.splice(index, 1);
                    return false;
                }
            });

            $.data(el, 'filterName', filterName);
            t.filterData.push(el);

            t.filtersCallback(dropdown, items.filter('[data-filter="' + filterName + '"]'));

            items.on('click.cbp', function() {
                var item = $(this);

                if (item.hasClass('cbp-filter-item-active') || parent.isAnimating) {
                    return;
                }

                t.filtersCallback(dropdown, item);

                $.data(el, 'filterName', item.data('filter'));

                var name = $.map(t.filterData, function(el, index) {
                    var f = $.data(el, 'filterName');
                    return (f !== "" && f !== '*') ? f : null;
                });

                if (name.length < 1) {
                    name = ['*'];
                }

                var filterJoin = name.join('|');

                if (parent.defaultFilter !== filterJoin) {
                    // filter the items
                    parent.$obj.cubeportfolio('filter', filterJoin);
                }
            });
        });
    };

    Filters.prototype.filtersCallback = function(dropdown, item) {
        if (!$.isEmptyObject(dropdown)) {
            dropdown.wrap.trigger('mouseleave.cbp');

            if (dropdown.headerText) {
                dropdown.headerText = '';
            } else {
                dropdown.header.text(item.text());
            }
        }

        item.addClass('cbp-filter-item-active').siblings().removeClass('cbp-filter-item-active');
    };

    /**
     * Check if filters is present in url
     */
    Filters.prototype.filterFromUrl = function() {
        var match = /#cbpf=(.*?)([#\?&]|$)/gi.exec(location.href);

        if (match !== null) {
            this.parent.defaultFilter = decodeURIComponent(match[1]);
        }
    };

    Filters.prototype.destroy = function() {
        var t = this;

        t.filters.find('.cbp-filter-item').off('.cbp');
        t.wrap.off('.cbp');
    };

    CubePortfolio.Plugins.Filters = function(parent) {
        if (parent.options.filters === '') {
            return null;
        }

        return new Filters(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function LoadMore(parent) {
        var t = this;

        t.parent = parent;

        t.loadMore = $(parent.options.loadMore).find('.cbp-l-loadMore-link');

        // load click or auto action
        if (parent.options.loadMoreAction.length) {
            t[parent.options.loadMoreAction]();
        }

    }

    LoadMore.prototype.click = function() {
        var t = this,
            numberOfClicks = 0;

        t.loadMore.on('click.cbp', function(e) {
            var item = $(this);

            e.preventDefault();

            if (t.parent.isAnimating || item.hasClass('cbp-l-loadMore-stop')) {
                return;
            }

            // set loading status
            item.addClass('cbp-l-loadMore-loading');

            numberOfClicks++;

            // perform ajax request
            $.ajax({
                url: t.loadMore.attr('href'),
                type: 'GET',
                dataType: 'HTML'
            }).done(function(result) {
                var items, itemsNext;

                // find current container
                items = $(result).filter(function() {
                    return $(this).is('div' + '.cbp-loadMore-block' + numberOfClicks);
                });

                t.parent.$obj.cubeportfolio('appendItems', items.children(), function() {

                    // put the original message back
                    item.removeClass('cbp-l-loadMore-loading');

                    // check if we have more works
                    itemsNext = $(result).filter(function() {
                        return $(this).is('div' + '.cbp-loadMore-block' + (numberOfClicks + 1));
                    });

                    if (itemsNext.length === 0) {
                        item.addClass('cbp-l-loadMore-stop');
                    }
                });

            }).fail(function() {
                // error
            });

        });
    };


    LoadMore.prototype.auto = function() {
        var t = this;

        t.parent.$obj.on('initComplete.cbp', function() {
            Object.create({
                init: function() {
                    var self = this;

                    // the job inactive
                    self.isActive = false;

                    self.numberOfClicks = 0;

                    // set loading status
                    t.loadMore.addClass('cbp-l-loadMore-loading');

                    // cache window selector
                    self.window = $(window);

                    // add events for scroll
                    self.addEvents();

                    // trigger method on init
                    self.getNewItems();
                },

                addEvents: function() {
                    var self = this,
                        timeout;

                    t.loadMore.on('click.cbp', function(e) {
                        e.preventDefault();
                    });

                    self.window.on('scroll.loadMoreObject', function() {

                        clearTimeout(timeout);

                        timeout = setTimeout(function() {
                            if (!t.parent.isAnimating) {
                                // get new items on scroll
                                self.getNewItems();
                            }
                        }, 80);

                    });

                    // when the filter is completed
                    t.parent.$obj.on('filterComplete.cbp', function() {
                        self.getNewItems();
                    });
                },

                getNewItems: function() {
                    var self = this,
                        topLoadMore, topWindow;

                    if (self.isActive || t.loadMore.hasClass('cbp-l-loadMore-stop')) {
                        return;
                    }

                    topLoadMore = t.loadMore.offset().top;
                    topWindow = self.window.scrollTop() + self.window.height();

                    if (topLoadMore > topWindow) {
                        return;
                    }

                    // this job is now busy
                    self.isActive = true;

                    // increment number of clicks
                    self.numberOfClicks++;

                    // perform ajax request
                    $.ajax({
                            url: t.loadMore.attr('href'),
                            type: 'GET',
                            dataType: 'HTML',
                            cache: true
                        })
                        .done(function(result) {
                            var items, itemsNext;

                            // find current container
                            items = $(result).filter(function() {
                                return $(this).is('div' + '.cbp-loadMore-block' + self.numberOfClicks);
                            });

                            t.parent.$obj.cubeportfolio('appendItems', items.html(), function() {
                                // check if we have more works
                                itemsNext = $(result).filter(function() {
                                    return $(this).is('div' + '.cbp-loadMore-block' + (self.numberOfClicks + 1));
                                });

                                if (itemsNext.length === 0) {
                                    t.loadMore.addClass('cbp-l-loadMore-stop');

                                    // remove events
                                    self.window.off('scroll.loadMoreObject');
                                    t.parent.$obj.off('filterComplete.cbp');
                                } else {
                                    // make the job inactive
                                    self.isActive = false;

                                    self.window.trigger('scroll.loadMoreObject');
                                }
                            });
                        })
                        .fail(function() {
                            // make the job inactive
                            self.isActive = false;
                        });
                }
            }).init();
        });

    };


    LoadMore.prototype.destroy = function() {
        var t = this;

        t.loadMore.off('.cbp');

        $(window).off('scroll.loadMoreObject');
    };

    CubePortfolio.Plugins.LoadMore = function(parent) {

        if (parent.options.loadMore === '') {
            return null;
        }

        return new LoadMore(parent);
    };

})(jQuery, window, document);

// Plugin default options
jQuery.fn.cubeportfolio.options = {
    /**
     *  Define the wrapper for filters
     *  Values: strings that represent the elements in the document (DOM selector).
     */
    filters: '',

    /**
     *  Define the wrapper for loadMore
     *  Values: strings that represent the elements in the document (DOM selector).
     */
    loadMore: '',

    /**
     *  How the loadMore functionality should behave. Load on click on the button or
     *  automatically when you scroll the page
     *  Values: - click
     *          - auto
     */
    loadMoreAction: 'click',

    /**
     *  Define the search input element
     *  Values: strings that represent the element in the document (DOM selector).
     */
    search: '',

    /**
     *  Layout Mode for this instance
     *  Values: 'grid', 'mosaic' or 'slider'
     */
    layoutMode: 'grid',

    /**
     *  Sort the items (bigger to smallest) if there are gaps in grid
     *  Option available only for `layoutMode: 'mosaic'`
     *  Values: true or false
     */
    sortToPreventGaps: false,

    /**
     *  Mouse and touch drag support
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    drag: true,

    /**
     *  Autoplay the slider
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    auto: false,

    /**
     *  Autoplay interval timeout. Time is set in milisecconds
     *  1000 milliseconds equals 1 second.
     *  Option available only for `layoutMode: 'slider'`
     *  Values: only integers (ex: 1000, 2000, 5000)
     */
    autoTimeout: 5000,

    /**
     *  Stops autoplay when user hover the slider
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    autoPauseOnHover: true,

    /**
     *  Show `next` and `prev` buttons for slider
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    showNavigation: true,

    /**
     *  Show pagination for slider
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    showPagination: true,

    /**
     *  Enable slide to first item (last item)
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    rewindNav: true,

    /**
     *  Scroll by page and not by item. This option affect next/prev buttons and drag support
     *  Option available only for `layoutMode: 'slider'`
     *  Values: true or false
     */
    scrollByPage: false,

    /**
     *  Default filter for plugin
     *  Option available only for `layoutMode: 'grid'`
     *  Values: strings that represent the filter name(ex: *, .logo, .web-design, .design)
     */
    defaultFilter: '*',

    /**
     *  Enable / disable the deeplinking feature when you click on filters
     *  Option available only for `layoutMode: 'grid'`
     *  Values: true or false
     */
    filterDeeplinking: false,

    /**
     *  Defines which animation to use for items that will be shown or hidden after a filter has been activated.
     *  Option available only for `layoutMode: 'grid'`
     *  The plugin use the best browser features available (css3 transitions and transform, GPU acceleration).
     *  Values: - fadeOut
     *          - quicksand
     *          - bounceLeft
     *          - bounceTop
     *          - bounceBottom
     *          - moveLeft
     *          - slideLeft
     *          - fadeOutTop
     *          - sequentially
     *          - skew
     *          - slideDelay
     *          - rotateSides
     *          - flipOutDelay
     *          - flipOut
     *          - unfold
     *          - foldLeft
     *          - scaleDown
     *          - scaleSides
     *          - frontRow
     *          - flipBottom
     *          - rotateRoom
     */
    animationType: 'fadeOut',

    /**
     *  Adjust the layout grid
     *  Values: - default (no adjustment applied)
     *          - alignCenter (align the grid on center of the page)
     *          - responsive (use a fluid algorithm to resize the grid)
     */
    gridAdjustment: 'responsive',

    /**
     * Define `media queries` for columns layout.
     * Format: [{width: a, cols: d}, {width: b, cols: e}, {width: c, cols: f}],
     * where a, b, c are the grid width and d, e, f are the columns displayed.
     * e.g. [{width: 1100, cols: 4}, {width: 800, cols: 3}, {width: 480, cols: 2}] means
     * if (gridWidth >= 1100) => show 4 columns,
     * if (gridWidth >= 800 && gridWidth < 1100) => show 3 columns,
     * if (gridWidth >= 480 && gridWidth < 800) => show 2 columns,
     * if (gridWidth < 480) => show 2 columns
     * Keep in mind that a > b > c
     * This option is available only when `gridAdjustment: 'responsive'`
     * Values:  - array of objects of format: [{width: a, cols: d}, {width: b, cols: e}]
     *          - you can define as many objects as you want
     *          - if this option is `false` Cube Portfolio will adjust the items
     *            width automatically (default option for backward compatibility)
     */
    mediaQueries: false,

    /**
     *  Horizontal gap between items
     *  Values: only integers (ex: 1, 5, 10)
     */
    gapHorizontal: 10,

    /**
     *  Vertical gap between items
     *  Values: only integers (ex: 1, 5, 10)
     */
    gapVertical: 10,

    /**
     *  Caption - the overlay that is shown when you put the mouse over an item
     *  NOTE: If you don't want to have captions set this option to an empty string ( caption: '')
     *  Values: - pushTop
     *          - pushDown
     *          - revealBottom
     *          - revealTop
     *          - revealLeft
     *          - moveRight
     *          - overlayBottom
     *          - overlayBottomPush
     *          - overlayBottomReveal
     *          - overlayBottomAlong
     *          - overlayRightAlong
     *          - minimal
     *          - fadeIn
     *          - zoom
     *          - opacity
     *          - ''
     */
    caption: 'pushTop',

    /**
     *  The plugin will display his content based on the following values.
     *  Values: - default (the content will be displayed as soon as possible)
     *          - lazyLoading (the plugin will fully preload the images before displaying the items with a fadeIn effect)
     *          - fadeInToTop (the plugin will fully preload the images before displaying the items with a fadeIn effect from bottom to top)
     *          - sequentially (the plugin will fully preload the images before displaying the items with a sequentially effect)
     *          - bottomToTop (the plugin will fully preload the images before displaying the items with an animation from bottom to top)
     */
    displayType: 'lazyLoading',

    /**
     *  Defines the speed of displaying the items (when `displayType == default` this option will have no effect)
     *  Values: only integers, values in ms (ex: 200, 300, 500)
     */
    displayTypeSpeed: 400,

    /**
     *  This is used to define any clickable elements you wish to use to trigger lightbox popup on click.
     *  Values: strings that represent the elements in the document (DOM selector)
     */
    lightboxDelegate: '.cbp-lightbox',

    /**
     *  Enable / disable gallery mode
     *  Values: true or false
     */
    lightboxGallery: true,

    /**
     *  Attribute of the delegate item that contains caption for lightbox
     *  Values: html atributte
     */
    lightboxTitleSrc: 'data-title',

    /**
     *  Markup of the lightbox counter
     *  Values: html markup
     */
    lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',

    /**
     *  This is used to define any clickable elements you wish to use to trigger singlePage popup on click.
     *  Values: strings that represent the elements in the document (DOM selector)
     */
    singlePageDelegate: '.cbp-singlePage',

    /**
     *  Enable / disable the deeplinking feature for singlePage popup
     *  Values: true or false
     */
    singlePageDeeplinking: true,

    /**
     *  Enable / disable the sticky navigation for singlePage popup
     *  Values: true or false
     */
    singlePageStickyNavigation: true,

    /**
     *  Markup of the singlePage counter
     *  Values: html markup
     */
    singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',

    /**
     *  Defines which animation to use when singlePage appear
     *  Values: - left
     *          - fade
     *          - right
     */
    singlePageAnimation: 'left',

    /**
     *  Use this callback to update singlePage content.
     *  The callback will trigger after the singlePage popup will open.
     *  @param url = the href attribute of the item clicked
     *  @param element = the item clicked
     *  Values: function
     */
    singlePageCallback: function(url, element) {
        // to update singlePage content use the following method: this.updateSinglePage(yourContent)
    },

    /**
     *  This is used to define any clickable elements you wish to use to trigger singlePage Inline on click.
     *  Values: strings that represent the elements in the document (DOM selector)
     */
    singlePageInlineDelegate: '.cbp-singlePageInline',

    /**
     *  This is used to define the position of singlePage Inline block
     *  Values: - above ( above current element )
     *          - below ( below current elemnet)
     *          - top ( positon top )
     *          - bottom ( positon bottom )
     */
    singlePageInlinePosition: 'top',

    /**
     *  Push the open panel in focus and at close go back to the former stage
     *  Values: true or false
     */
    singlePageInlineInFocus: true,

    /**
     *  Use this callback to update singlePage Inline content.
     *  The callback will trigger after the singlePage Inline will open.
     *  @param url = the href attribute of the item clicked
     *  @param element = the item clicked
     *  Values: function
     */
    singlePageInlineCallback: function(url, element) {
        // to update singlePage Inline content use the following method: this.updateSinglePageInline(yourContent)
    },
};

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    var popup = {

        /**
         * init function for popup
         * @param cubeportfolio = cubeportfolio instance
         * @param type =  'lightbox' or 'singlePage'
         */
        init: function(cubeportfolio, type) {
            var t = this,
                currentBlock;

            // remember cubeportfolio instance
            t.cubeportfolio = cubeportfolio;

            // remember if this instance is for lightbox or for singlePage
            t.type = type;

            // remember if the popup is open or not
            t.isOpen = false;

            t.options = t.cubeportfolio.options;

            if (type === 'lightbox') {
                t.cubeportfolio.registerEvent('resizeWindow', function() {
                    t.resizeImage();
                });
            }

            if (type === 'singlePageInline') {

                t.startInline = -1;

                t.height = 0;

                // create markup, css and add events for SinglePageInline
                t.createMarkupSinglePageInline();

                t.cubeportfolio.registerEvent('resizeGrid', function() {
                    if (t.isOpen) {
                        // @todo must add support for this features in the future
                        t.close(); // workaround
                    }
                });

                return;
            }

            // create markup, css and add events for lightbox and singlePage
            t.createMarkup();

            if (type === 'singlePage') {

                t.cubeportfolio.registerEvent('resizeWindow', function() {
                    if (t.options.singlePageStickyNavigation) {

                        var width = t.wrap[0].clientWidth;

                        if (width > 0) {
                            t.navigationWrap.width(width);

                            // set navigation width='window width' to center the divs
                            t.navigation.width(width);
                        }

                    }
                });

                if (t.options.singlePageDeeplinking) {
                    t.url = location.href;

                    if (t.url.slice(-1) === '#') {
                        t.url = t.url.slice(0, -1);
                    }

                    var links = t.url.split('#cbp=');
                    var url = links.shift(); // remove first item

                    $.each(links, function(index, link) {

                        t.cubeportfolio.blocksOn.each(function(index1, el) {
                            var singlePage = $(el).find(t.options.singlePageDelegate + '[href="' + link + '"]');

                            if (singlePage.length) {
                                currentBlock = singlePage;
                                return false;
                            }

                        });

                        if (currentBlock) {
                            return false;
                        }

                    });

                    if (currentBlock) {

                        t.url = url;

                        var self = currentBlock,
                            gallery = self.attr('data-cbp-singlePage'),
                            blocks = [];

                        if (gallery) {
                            blocks = self.closest($('.cbp-item')).find('[data-cbp-singlePage="' + gallery + '"]');
                        } else {
                            t.cubeportfolio.blocksOn.each(function(index, el) {
                                var item = $(el);

                                if (item.not('.cbp-item-off')) {
                                    item.find(t.options.singlePageDelegate).each(function(index2, el2) {
                                        if (!$(el2).attr('data-cbp-singlePage')) {
                                            blocks.push(el2);
                                        }
                                    });
                                }
                            });
                        }

                        t.openSinglePage(blocks, currentBlock[0]);

                    } else if (links.length) { // @todo - hack to load items from loadMore
                        var fakeLink = document.createElement('a');
                        fakeLink.setAttribute('href', links[0]);
                        t.openSinglePage([fakeLink], fakeLink);
                    }

                }
            }

        },

        /**
         * Create markup, css and add events
         */
        createMarkup: function() {
            var t = this,
                animationCls = '';

            if (t.type === 'singlePage') {
                if (t.options.singlePageAnimation !== 'left') {
                    animationCls = ' cbp-popup-singlePage-' + t.options.singlePageAnimation;
                }
            }

            // wrap element
            t.wrap = $('<div/>', {
                'class': 'cbp-popup-wrap cbp-popup-' + t.type + animationCls,
                'data-action': (t.type === 'lightbox') ? 'close' : ''
            }).on('click.cbp', function(e) {
                if (t.stopEvents) {
                    return;
                }

                var action = $(e.target).attr('data-action');

                if (t[action]) {
                    t[action]();
                    e.preventDefault();
                }
            });

            // content element
            t.content = $('<div/>', {
                'class': 'cbp-popup-content'
            }).appendTo(t.wrap);

            // append loading div
            $('<div/>', {
                'class': 'cbp-popup-loadingBox'
            }).appendTo(t.wrap);

            // add background only for ie8
            if (CubePortfolio.Private.browser === 'ie8') {
                t.bg = $('<div/>', {
                    'class': 'cbp-popup-ie8bg',
                    'data-action': (t.type === 'lightbox') ? 'close' : ''
                }).appendTo(t.wrap);
            }

            // create navigation wrap
            t.navigationWrap = $('<div/>', {
                'class': 'cbp-popup-navigation-wrap'
            }).appendTo(t.wrap);

            // create navigation block
            t.navigation = $('<div/>', {
                'class': 'cbp-popup-navigation'
            }).appendTo(t.navigationWrap);

            // close
            t.closeButton = $('<div/>', {
                'class': 'cbp-popup-close',
                'title': 'Close (Esc arrow key)',
                'data-action': 'close'
            }).appendTo(t.navigation);

            // next
            t.nextButton = $('<div/>', {
                'class': 'cbp-popup-next',
                'title': 'Next (Right arrow key)',
                'data-action': 'next'
            }).appendTo(t.navigation);


            // prev
            t.prevButton = $('<div/>', {
                'class': 'cbp-popup-prev',
                'title': 'Previous (Left arrow key)',
                'data-action': 'prev'
            }).appendTo(t.navigation);


            if (t.type === 'singlePage') {

                if (t.options.singlePageCounter) {
                    // counter for singlePage
                    t.counter = $(t.options.singlePageCounter).appendTo(t.navigation);
                    t.counter.text('');
                }

                t.content.on('click.cbp', t.options.singlePageDelegate, function(e) {
                    e.preventDefault();
                    var i,
                        len = t.dataArray.length,
                        href = this.getAttribute('href');

                    for (i = 0; i < len; i++) {

                        if (t.dataArray[i].url === href) {
                            break;
                        }
                    }

                    t.singlePageJumpTo(i - t.current);

                });

                // if there are some events than overrides the default scroll behaviour don't go to them
                t.wrap.on('mousewheel.cbp' + ' DOMMouseScroll.cbp', function(e) {
                    e.stopImmediatePropagation();
                });

            }

            $(document).on('keydown.cbp', function(e) {

                // if is not open => return
                if (!t.isOpen) {
                    return;
                }

                // if all events are stopped => return
                if (t.stopEvents) {
                    return;
                }

                if (e.keyCode === 37) { // prev key
                    t.prev();
                } else if (e.keyCode === 39) { // next key
                    t.next();
                } else if (e.keyCode === 27) { //esc key
                    t.close();
                }
            });

        },

        createMarkupSinglePageInline: function() {
            var t = this;

            // wrap element
            t.wrap = $('<div/>', {
                'class': 'cbp-popup-singlePageInline'
            }).on('click.cbp', function(e) {
                if (t.stopEvents) {
                    return;
                }

                var action = $(e.target).attr('data-action');

                if (action && t[action]) {
                    t[action]();
                    e.preventDefault();
                }
            });

            // content element
            t.content = $('<div/>', {
                'class': 'cbp-popup-content'
            }).appendTo(t.wrap);

            // append loading div
            // $('<div/>', {
            //     'class': 'cbp-popup-loadingBox'
            // }).appendTo(t.wrap);

            // create navigation block
            t.navigation = $('<div/>', {
                'class': 'cbp-popup-navigation'
            }).appendTo(t.wrap);

            // close
            t.closeButton = $('<div/>', {
                'class': 'cbp-popup-close',
                'title': 'Close (Esc arrow key)',
                'data-action': 'close'
            }).appendTo(t.navigation);

        },

        destroy: function() {
            var t = this,
                body = $('body');

            // remove off key down
            $(document).off('keydown.cbp');

            // external lightbox and singlePageInline
            body.off('click.cbp', t.options.lightboxDelegate);
            body.off('click.cbp', t.options.singlePageDelegate);

            t.content.off('click.cbp', t.options.singlePageDelegate);

            t.cubeportfolio.$obj.off('click.cbp', t.options.singlePageInlineDelegate);
            t.cubeportfolio.$obj.off('click.cbp', t.options.lightboxDelegate);
            t.cubeportfolio.$obj.off('click.cbp', t.options.singlePageDelegate);

            t.cubeportfolio.$obj.removeClass('cbp-popup-isOpening');

            t.cubeportfolio.$obj.find('.cbp-item').removeClass('cbp-singlePageInline-active');

            t.wrap.remove();
        },

        openLightbox: function(blocks, currentBlock) {
            var t = this,
                i = 0,
                currentBlockHref, tempHref = [],
                element;

            if (t.isOpen) {
                return;
            }

            // remember that the lightbox is open now
            t.isOpen = true;

            // remember to stop all events after the lightbox has been shown
            t.stopEvents = false;

            // array with elements
            t.dataArray = [];

            // reset current
            t.current = null;

            currentBlockHref = currentBlock.getAttribute('href');
            if (currentBlockHref === null) {
                throw new Error('HEI! Your clicked element doesn\'t have a href attribute.');
            }

            $.each(blocks, function(index, item) {
                var href = item.getAttribute('href'),
                    src = href, // default if element is image
                    type = 'isImage', // default if element is image
                    videoLink;

                if ($.inArray(href, tempHref) === -1) {

                    if (currentBlockHref === href) {
                        t.current = i;
                    } else if (!t.options.lightboxGallery) {
                        return;
                    }

                    if (/youtube/i.test(href)) {

                        videoLink = href.substring(href.lastIndexOf('v=') + 2);

                        if (!(/autoplay=/i.test(videoLink))) {
                            videoLink += '&autoplay=1';
                        }

                        videoLink = videoLink.replace(/\?|&/, '?');

                        // create new href
                        src = '//www.youtube.com/embed/' + videoLink;

                        type = 'isYoutube';

                    } else if (/vimeo\.com/i.test(href)) {

                        videoLink = href.substring(href.lastIndexOf('/') + 1);

                        if (!(/autoplay=/i.test(videoLink))) {
                            videoLink += '&autoplay=1';
                        }

                        videoLink = videoLink.replace(/\?|&/, '?');

                        // create new href
                        src = '//player.vimeo.com/video/' + videoLink;

                        type = 'isVimeo';

                    } else if (/www\.ted\.com/i.test(href)) {

                        // create new href
                        src = 'http://embed.ted.com/talks/' + href.substring(href.lastIndexOf('/') + 1) + '.html';

                        type = 'isTed';

                    } else if (/soundcloud\.com/i.test(href)) {

                        // create new href
                        src = href;

                        type = 'isSoundCloud';

                    } else if (/(\.mp4)|(\.ogg)|(\.ogv)|(\.webm)/i.test(href)) {

                        if (href.indexOf('|') !== -1) {
                            // create new href
                            src = href.split('|');
                        } else {
                            // create new href
                            src = href.split('%7C');
                        }

                        type = 'isSelfHostedVideo';

                    } else if (/\.mp3$/i.test(href)) {
                        src = href;
                        type = 'isSelfHostedAudio';
                    }

                    t.dataArray.push({
                        src: src,
                        title: item.getAttribute(t.options.lightboxTitleSrc),
                        type: type
                    });

                    i++;
                }

                tempHref.push(href);
            });


            // total numbers of elements
            t.counterTotal = t.dataArray.length;

            if (t.counterTotal === 1) {
                t.nextButton.hide();
                t.prevButton.hide();
                t.dataActionImg = '';
            } else {
                t.nextButton.show();
                t.prevButton.show();
                t.dataActionImg = 'data-action="next"';
            }

            // append to body
            t.wrap.appendTo(document.body);

            t.scrollTop = $(window).scrollTop();

            t.originalStyle = $('html').attr('style');

            $('html').css({
                overflow: 'hidden',
                paddingRight: window.innerWidth - $(document).width()
            });

            // show the wrapper (lightbox box)
            t.wrap.show();

            // get the current element
            element = t.dataArray[t.current];

            // call function if current element is image or video (iframe)
            t[element.type](element);

        },

        openSinglePage: function(blocks, currentBlock) {
            var t = this,
                i = 0,
                currentBlockHref, tempHref = [];

            if (t.isOpen) {
                return;
            }

            // check singlePageInline and close it
            if (t.cubeportfolio.singlePageInline && t.cubeportfolio.singlePageInline.isOpen) {
                t.cubeportfolio.singlePageInline.close();
            }

            // remember that the lightbox is open now
            t.isOpen = true;

            // remember to stop all events after the popup has been showing
            t.stopEvents = false;

            // array with elements
            t.dataArray = [];

            // reset current
            t.current = null;

            currentBlockHref = currentBlock.getAttribute('href');
            if (currentBlockHref === null) {
                throw new Error('HEI! Your clicked element doesn\'t have a href attribute.');
            }


            $.each(blocks, function(index, item) {
                var href = item.getAttribute('href');

                if ($.inArray(href, tempHref) === -1) {

                    if (currentBlockHref === href) {
                        t.current = i;
                    }

                    t.dataArray.push({
                        url: href,
                        element: item
                    });

                    i++;
                }

                tempHref.push(href);
            });

            // total numbers of elements
            t.counterTotal = t.dataArray.length;

            if (t.counterTotal === 1) {
                t.nextButton.hide();
                t.prevButton.hide();
            } else {
                t.nextButton.show();
                t.prevButton.show();
            }

            // append to body
            t.wrap.appendTo(document.body);

            t.scrollTop = $(window).scrollTop();

            $('html').css({
                overflow: 'hidden',
                paddingRight: window.innerWidth - $(document).width()
            });

            // go to top of the page (reset scroll)
            t.wrap.scrollTop(0);

            // show the wrapper
            t.wrap.show();

            // finish the open animation
            t.finishOpen = 2;

            // if transitionend is not fulfilled
            t.navigationMobile = $();
            t.wrap.one(CubePortfolio.Private.transitionend, function() {
                var width;

                // make the navigation sticky
                if (t.options.singlePageStickyNavigation) {

                    t.wrap.addClass('cbp-popup-singlePage-sticky');

                    width = t.wrap[0].clientWidth;
                    t.navigationWrap.width(width);

                    if (CubePortfolio.Private.browser === 'android' || CubePortfolio.Private.browser === 'ios') {
                        // wrap element
                        t.navigationMobile = $('<div/>', {
                            'class': 'cbp-popup-singlePage cbp-popup-singlePage-sticky',
                            'id': t.wrap.attr('id')
                        }).on('click.cbp', function(e) {
                            if (t.stopEvents) {
                                return;
                            }

                            var action = $(e.target).attr('data-action');

                            if (t[action]) {
                                t[action]();
                                e.preventDefault();
                            }
                        });

                        t.navigationMobile.appendTo(document.body).append(t.navigationWrap);
                    }

                }

                t.finishOpen--;
                if (t.finishOpen <= 0) {
                    t.updateSinglePageIsOpen.call(t);
                }

            });

            if (CubePortfolio.Private.browser === 'ie8' || CubePortfolio.Private.browser === 'ie9') {

                // make the navigation sticky
                if (t.options.singlePageStickyNavigation) {
                    var width = t.wrap[0].clientWidth;

                    t.navigationWrap.width(width);

                    setTimeout(function() {
                        t.wrap.addClass('cbp-popup-singlePage-sticky');
                    }, 1000);

                }

                t.finishOpen--;
            }

            t.wrap.addClass('cbp-popup-loading');

            // force reflow and then add class
            t.wrap.offset();
            t.wrap.addClass('cbp-popup-singlePage-open');

            // change link
            if (t.options.singlePageDeeplinking) {
                // ignore old #cbp from href
                t.url = t.url.split('#cbp=')[0];
                location.href = t.url + '#cbp=' + t.dataArray[t.current].url;
            }

            // run callback function
            if ($.isFunction(t.options.singlePageCallback)) {
                t.options.singlePageCallback.call(t, t.dataArray[t.current].url, t.dataArray[t.current].element);
            }

        },


        openSinglePageInline: function(blocks, currentBlock, fromOpen) {
            var t = this,
                start = 0,
                currentBlockHref,
                tempCurrent,
                cbpitem,
                parentElement;

            fromOpen = fromOpen || false;

            t.fromOpen = fromOpen;

            t.storeBlocks = blocks;
            t.storeCurrentBlock = currentBlock;

            // check singlePageInline and close it
            if (t.isOpen) {

                tempCurrent = $(currentBlock).closest('.cbp-item').index();

                if ((t.dataArray[t.current].url !== currentBlock.getAttribute('href')) || (t.current !== tempCurrent)) {
                    t.cubeportfolio.singlePageInline.close('open', {
                        blocks: blocks,
                        currentBlock: currentBlock,
                        fromOpen: true
                    });

                } else {
                    t.close();
                }

                return;
            }

            // remember that the lightbox is open now
            t.isOpen = true;

            // remember to stop all events after the popup has been showing
            t.stopEvents = false;

            // array with elements
            t.dataArray = [];

            // reset current
            t.current = null;

            currentBlockHref = currentBlock.getAttribute('href');
            if (currentBlockHref === null) {
                throw new Error('HEI! Your clicked element doesn\'t have a href attribute.');
            }

            cbpitem = $(currentBlock).closest('.cbp-item')[0];

            blocks.each(function(index, el) {
                if (cbpitem === el) {
                    t.current = index;
                }
            });

            t.dataArray[t.current] = {
                url: currentBlockHref,
                element: currentBlock
            };

            parentElement = $(t.dataArray[t.current].element).parents('.cbp-item').addClass('cbp-singlePageInline-active');

            // total numbers of elements
            t.counterTotal = blocks.length;

            t.wrap.insertBefore(t.cubeportfolio.wrapper);

            if (t.options.singlePageInlinePosition === 'top') {
                t.startInline = 0;
                t.top = 0;

                t.firstRow = true;
                t.lastRow = false;
            } else if (t.options.singlePageInlinePosition === 'bottom') {
                t.startInline = t.counterTotal;
                t.top = t.cubeportfolio.height;

                t.firstRow = false;
                t.lastRow = true;
            } else if (t.options.singlePageInlinePosition === 'above') {
                t.startInline = t.cubeportfolio.cols * Math.floor(t.current / t.cubeportfolio.cols);
                t.top = $(blocks[t.current]).data('cbp').top;

                if (t.startInline === 0) {
                    t.firstRow = true;
                } else {
                    t.top -= t.options.gapHorizontal;
                    t.firstRow = false;
                }

                t.lastRow = false;
            } else { // below
                t.top = $(blocks[t.current]).data('cbp').top + $(blocks[t.current]).data('cbp').height;
                t.startInline = Math.min(t.cubeportfolio.cols *
                    (Math.floor(t.current / t.cubeportfolio.cols) + 1),
                    t.counterTotal);

                t.firstRow = false;
                t.lastRow = (t.startInline === t.counterTotal) ? true : false;
            }

            t.wrap[0].style.height = t.wrap.outerHeight(true) + 'px';

            // debouncer for inline content
            t.deferredInline = $.Deferred();

            if (t.options.singlePageInlineInFocus) {

                t.scrollTop = $(window).scrollTop();

                var goToScroll = t.cubeportfolio.$obj.offset().top + t.top - 100;

                if (t.scrollTop !== goToScroll) {
                    $('html,body').animate({
                            scrollTop: goToScroll
                        }, 350)
                        .promise()
                        .then(function() {
                            t.resizeSinglePageInline();
                            t.deferredInline.resolve();
                        });
                } else {
                    t.resizeSinglePageInline();
                    t.deferredInline.resolve();
                }
            } else {
                t.resizeSinglePageInline();
                t.deferredInline.resolve();
            }

            t.cubeportfolio.$obj.addClass('cbp-popup-singlePageInline-open');

            t.wrap.css({
                top: t.top
            });

            // register callback function
            if ($.isFunction(t.options.singlePageInlineCallback)) {
                t.options.singlePageInlineCallback.call(t, t.dataArray[t.current].url, t.dataArray[t.current].element);
            }
        },

        resizeSinglePageInline: function() {
            var t = this;

            t.height = (t.firstRow || t.lastRow) ? t.wrap.outerHeight(true) : t.wrap.outerHeight(true) - t.options.gapHorizontal;

            t.storeBlocks.each(function(index, el) {
                if (index < t.startInline) {
                    if (CubePortfolio.Private.modernBrowser) {
                        el.style[CubePortfolio.Private.transform] = '';
                    } else {
                        el.style.marginTop = '';
                    }
                } else {
                    if (CubePortfolio.Private.modernBrowser) {
                        el.style[CubePortfolio.Private.transform] = 'translate3d(0px, ' + t.height + 'px, 0)';
                    } else {
                        el.style.marginTop = t.height + 'px';
                    }
                }
            });

            t.cubeportfolio.obj.style.height = t.cubeportfolio.height + t.height + 'px';
        },

        revertResizeSinglePageInline: function() {
            var t = this;

            // reset deferred object
            t.deferredInline = $.Deferred();

            t.storeBlocks.each(function(index, el) {
                if (CubePortfolio.Private.modernBrowser) {
                    el.style[CubePortfolio.Private.transform] = '';
                } else {
                    el.style.marginTop = '';
                }
            });

            t.cubeportfolio.obj.style.height = t.cubeportfolio.height + 'px';
        },

        appendScriptsToWrap: function(scripts) {
            var t = this,
                index = 0,
                loadScripts = function(item) {
                    var script = document.createElement('script'),
                        src = item.src;

                    script.type = 'text/javascript';

                    if (script.readyState) { // ie
                        script.onreadystatechange = function() {
                            if (script.readyState == 'loaded' || script.readyState == 'complete') {
                                script.onreadystatechange = null;
                                index++;
                                if (scripts[index]) {
                                    loadScripts(scripts[index]);
                                }
                            }
                        };
                    } else {
                        script.onload = function() {
                            index++;
                            if (scripts[index]) {
                                loadScripts(scripts[index]);
                            }
                        };
                    }

                    if (src) {
                        script.src = src;
                    } else {
                        script.text = item.text;
                    }

                    t.content[0].appendChild(script);

                };

            loadScripts(scripts[0]);
        },

        updateSinglePage: function(html, scripts, isWrap) {
            var t = this,
                counterMarkup,
                animationFinish;

            t.content.addClass('cbp-popup-content').removeClass('cbp-popup-content-basic');

            if (isWrap === false) {
                t.content.removeClass('cbp-popup-content').addClass('cbp-popup-content-basic');
            }

            // update counter navigation
            if (t.counter) {
                counterMarkup = $(t.getCounterMarkup(t.options.singlePageCounter, t.current + 1, t.counterTotal));
                t.counter.text(counterMarkup.text());
            }

            t.content.html(html);

            if (scripts) {
                t.appendScriptsToWrap(scripts);
            }

            // trigger public event
            t.cubeportfolio.$obj.trigger('updateSinglePageStart.cbp');

            t.finishOpen--;

            if (t.finishOpen <= 0) {
                t.updateSinglePageIsOpen.call(t);
            }
        },

        updateSinglePageIsOpen: function() {
            var t = this,
                selectorSlider;

            t.wrap.addClass('cbp-popup-ready');
            t.wrap.removeClass('cbp-popup-loading');

            // instantiate slider if exists
            selectorSlider = t.content.find('.cbp-slider');
            if (selectorSlider) {
                selectorSlider.find('.cbp-slider-item').addClass('cbp-item');
                t.slider = selectorSlider.cubeportfolio({
                    layoutMode: 'slider',
                    mediaQueries: [{
                        width: 1,
                        cols: 1
                    }],
                    gapHorizontal: 0,
                    gapVertical: 0,
                    caption: '',
                    coverRatio: '', // wp version only
                });
            } else {
                t.slider = null;
            }

            // scroll bug on android and ios
            if (CubePortfolio.Private.browser === 'android' || CubePortfolio.Private.browser === 'ios') {
                $('html').css({
                    position: 'fixed'
                });
            }

            // trigger public event
            t.cubeportfolio.$obj.trigger('updateSinglePageComplete.cbp');

        },


        updateSinglePageInline: function(html, scripts) {
            var t = this;

            t.content.html(html);

            if (scripts) {
                t.appendScriptsToWrap(scripts);
            }
            // trigger public event
            t.cubeportfolio.$obj.trigger('updateSinglePageInlineStart.cbp');

            t.singlePageInlineIsOpen.call(t);

        },

        singlePageInlineIsOpen: function() {
            var t = this;

            function finishLoading() {
                t.wrap.addClass('cbp-popup-singlePageInline-ready');
                t.wrap[0].style.height = '';

                t.resizeSinglePageInline();

                // trigger public event
                t.cubeportfolio.$obj.trigger('updateSinglePageInlineComplete.cbp');
            }

            // wait to load all images
            t.cubeportfolio.loadImages(t.wrap, function() {
                // instantiate slider if exists
                var selectorSlider = t.content.find('.cbp-slider');

                if (selectorSlider.length) {
                    selectorSlider.find('.cbp-slider-item').addClass('cbp-item');

                    selectorSlider.one('initComplete.cbp', function() {
                        t.deferredInline.done(finishLoading);
                    });

                    selectorSlider.on('pluginResize.cbp', function() {
                        t.deferredInline.done(finishLoading);
                    });

                    t.slider = selectorSlider.cubeportfolio({
                        layoutMode: 'slider',
                        displayType: 'default',
                        mediaQueries: [{
                            width: 1,
                            cols: 1
                        }],
                        gapHorizontal: 0,
                        gapVertical: 0,
                        caption: '',
                        coverRatio: '', // wp version only
                    });
                } else {
                    t.slider = null;
                    t.deferredInline.done(finishLoading);
                }
            });
        },


        isImage: function(el) {
            var t = this,
                img = new Image();

            t.tooggleLoading(true);

            t.cubeportfolio.loadImages($('<div><img src="' + el.src + '"></div>'), function() {
                t.updateImagesMarkup(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));

                t.tooggleLoading(false);
            });
        },

        isVimeo: function(el) {
            var t = this;

            t.updateVideoMarkup(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));
        },

        isYoutube: function(el) {
            var t = this;
            t.updateVideoMarkup(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));

        },

        isTed: function(el) {
            var t = this;
            t.updateVideoMarkup(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));
        },

        isSoundCloud: function(el) {
            var t = this;
            t.updateVideoMarkup(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));
        },

        isSelfHostedVideo: function(el) {
            var t = this;
            t.updateSelfHostedVideo(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));
        },

        isSelfHostedAudio: function(el) {
            var t = this;
            t.updateSelfHostedAudio(el.src, el.title, t.getCounterMarkup(t.options.lightboxCounter, t.current + 1, t.counterTotal));
        },

        getCounterMarkup: function(markup, current, total) {
            if (!markup.length) {
                return '';
            }

            var mapObj = {
                current: current,
                total: total
            };

            return markup.replace(/\{\{current}}|\{\{total}}/gi, function(matched) {
                return mapObj[matched.slice(2, -2)];
            });
        },

        updateSelfHostedVideo: function(src, title, counter) {
            var t = this,
                i;

            t.wrap.addClass('cbp-popup-lightbox-isIframe');

            var markup = '<div class="cbp-popup-lightbox-iframe">' +
                '<video controls="controls" height="auto" style="width: 100%">';

            for (i = 0; i < src.length; i++) {
                if (/(\.mp4)/i.test(src[i])) {
                    markup += '<source src="' + src[i] + '" type="video/mp4">';
                } else if (/(\.ogg)|(\.ogv)/i.test(src[i])) {
                    markup += '<source src="' + src[i] + '" type="video/ogg">';
                } else if (/(\.webm)/i.test(src[i])) {
                    markup += '<source src="' + src[i] + '" type="video/webm">';
                }
            }

            markup += 'Your browser does not support the video tag.' +
                '</video>' +
                '<div class="cbp-popup-lightbox-bottom">' +
                ((title) ? '<div class="cbp-popup-lightbox-title">' + title + '</div>' : '') +
                counter +
                '</div>' +
                '</div>';

            t.content.html(markup);

            t.wrap.addClass('cbp-popup-ready');

            t.preloadNearbyImages();
        },

        updateSelfHostedAudio: function(src, title, counter) {
            var t = this,
                i;

            t.wrap.addClass('cbp-popup-lightbox-isIframe');

            var markup = '<div class="cbp-popup-lightbox-iframe">' +
                '<audio controls="controls" height="auto" style="width: 100%">' +
                '<source src="' + src + '" type="audio/mpeg">' +
                'Your browser does not support the audio tag.' +
                '</audio>' +
                '<div class="cbp-popup-lightbox-bottom">' +
                ((title) ? '<div class="cbp-popup-lightbox-title">' + title + '</div>' : '') +
                counter +
                '</div>' +
                '</div>';

            t.content.html(markup);

            t.wrap.addClass('cbp-popup-ready');

            t.preloadNearbyImages();
        },

        updateVideoMarkup: function(src, title, counter) {
            var t = this;
            t.wrap.addClass('cbp-popup-lightbox-isIframe');

            var markup = '<div class="cbp-popup-lightbox-iframe">' +
                '<iframe src="' + src + '" frameborder="0" allowfullscreen scrolling="no"></iframe>' +
                '<div class="cbp-popup-lightbox-bottom">' +
                ((title) ? '<div class="cbp-popup-lightbox-title">' + title + '</div>' : '') +
                counter +
                '</div>' +
                '</div>';

            t.content.html(markup);
            t.wrap.addClass('cbp-popup-ready');
            t.preloadNearbyImages();
        },

        updateImagesMarkup: function(src, title, counter) {
            var t = this;

            t.wrap.removeClass('cbp-popup-lightbox-isIframe');

            var markup = '<div class="cbp-popup-lightbox-figure">' +
                '<img src="' + src + '" class="cbp-popup-lightbox-img" ' + t.dataActionImg + ' />' +
                '<div class="cbp-popup-lightbox-bottom">' +
                ((title) ? '<div class="cbp-popup-lightbox-title">' + title + '</div>' : '') +
                counter +
                '</div>' +
                '</div>';

            t.content.html(markup);

            t.wrap.addClass('cbp-popup-ready');

            t.resizeImage();

            t.preloadNearbyImages();
        },

        next: function() {
            var t = this;
            t[t.type + 'JumpTo'](1);
        },

        prev: function() {
            var t = this;
            t[t.type + 'JumpTo'](-1);
        },

        lightboxJumpTo: function(index) {
            var t = this,
                el;

            t.current = t.getIndex(t.current + index);

            // get the current element
            el = t.dataArray[t.current];

            // call function if current element is image or video (iframe)
            t[el.type](el);
        },


        singlePageJumpTo: function(index) {
            var t = this;

            t.current = t.getIndex(t.current + index);

            // register singlePageCallback function
            if ($.isFunction(t.options.singlePageCallback)) {
                t.resetWrap();

                // go to top of the page (reset scroll)
                t.wrap.scrollTop(0);

                t.wrap.addClass('cbp-popup-loading');
                t.options.singlePageCallback.call(t, t.dataArray[t.current].url, t.dataArray[t.current].element);

                if (t.options.singlePageDeeplinking) {
                    location.href = t.url + '#cbp=' + t.dataArray[t.current].url;
                }
            }
        },

        resetWrap: function() {
            var t = this;

            if (t.type === 'singlePage' && t.options.singlePageDeeplinking) {
                location.href = t.url + '#';
            }
        },

        getIndex: function(index) {
            var t = this;

            // go to interval [0, (+ or -)this.counterTotal.length - 1]
            index = index % t.counterTotal;

            // if index is less then 0 then go to interval (0, this.counterTotal - 1]
            if (index < 0) {
                index = t.counterTotal + index;
            }

            return index;
        },

        close: function(method, data) {
            var t = this;

            function finishClose() {
                // reset content
                t.content.html('');

                // hide the wrap
                t.wrap.detach();

                t.cubeportfolio.$obj.removeClass('cbp-popup-singlePageInline-open cbp-popup-singlePageInline-close');

                if (method === 'promise') {
                    if ($.isFunction(data.callback)) {
                        data.callback.call(t.cubeportfolio);
                    }
                }
            }

            function checkFocusInline() {
                if (t.options.singlePageInlineInFocus && method !== 'promise') {
                    $('html,body').animate({
                            scrollTop: t.scrollTop
                        }, 350)
                        .promise()
                        .then(function() {
                            finishClose();
                        });
                } else {
                    finishClose();
                }
            }

            // now the popup is closed
            t.isOpen = false;

            if (t.type === 'singlePageInline') {

                if (method === 'open') {

                    t.wrap.removeClass('cbp-popup-singlePageInline-ready');

                    $(t.dataArray[t.current].element).closest('.cbp-item').removeClass('cbp-singlePageInline-active');

                    t.openSinglePageInline(data.blocks, data.currentBlock, data.fromOpen);

                } else {

                    t.height = 0;

                    t.revertResizeSinglePageInline();

                    t.wrap.removeClass('cbp-popup-singlePageInline-ready');

                    t.cubeportfolio.$obj.addClass('cbp-popup-singlePageInline-close');

                    t.startInline = -1;

                    t.cubeportfolio.$obj.find('.cbp-item').removeClass('cbp-singlePageInline-active');

                    if (CubePortfolio.Private.modernBrowser) {
                        t.wrap.one(CubePortfolio.Private.transitionend, function() {
                            checkFocusInline();
                        });
                    } else {
                        checkFocusInline();
                    }
                }

            } else if (t.type === 'singlePage') {

                t.resetWrap();

                t.wrap.removeClass('cbp-popup-ready');

                // scroll bug on android and ios
                if (CubePortfolio.Private.browser === 'android' || CubePortfolio.Private.browser === 'ios') {
                    $('html').css({
                        position: ''
                    });

                    t.navigationWrap.appendTo(t.wrap);
                    t.navigationMobile.remove();
                }

                $(window).scrollTop(t.scrollTop);

                // weird bug on mozilla. fixed with setTimeout
                setTimeout(function() {
                    t.stopScroll = true;

                    t.navigationWrap.css({
                        top: t.wrap.scrollTop()
                    });

                    t.wrap.removeClass('cbp-popup-singlePage-open cbp-popup-singlePage-sticky');

                    if (CubePortfolio.Private.browser === 'ie8' || CubePortfolio.Private.browser === 'ie9') {
                        // reset content
                        t.content.html('');

                        // hide the wrap
                        t.wrap.detach();

                        $('html').css({
                            overflow: '',
                            paddingRight: '',
                            position: ''
                        });

                        t.navigationWrap.removeAttr('style');
                    }

                }, 0);

                t.wrap.one(CubePortfolio.Private.transitionend, function() {

                    // reset content
                    t.content.html('');

                    // hide the wrap
                    t.wrap.detach();

                    $('html').css({
                        overflow: '',
                        paddingRight: '',
                        position: ''
                    });

                    t.navigationWrap.removeAttr('style');

                });

            } else {

                if (t.originalStyle) {
                    $('html').attr('style', t.originalStyle);
                } else {
                    $('html').css({
                        overflow: '',
                        paddingRight: ''
                    });
                }

                $(window).scrollTop(t.scrollTop);

                // reset content
                t.content.html('');

                // hide the wrap
                t.wrap.detach();

            }
        },

        tooggleLoading: function(state) {
            var t = this;

            t.stopEvents = state;
            t.wrap[(state) ? 'addClass' : 'removeClass']('cbp-popup-loading');
        },

        resizeImage: function() {
            // if lightbox is not open go out
            if (!this.isOpen) {
                return;
            }

            var height = $(window).height(),
                img = this.content.find('img'),
                padding = parseInt(img.css('margin-top'), 10) + parseInt(img.css('margin-bottom'), 10);

            img.css('max-height', (height - padding) + 'px');
        },

        preloadNearbyImages: function() {
            var arr = [],
                img, t = this,
                src;

            arr.push(t.getIndex(t.current + 1));
            arr.push(t.getIndex(t.current + 2));
            arr.push(t.getIndex(t.current + 3));
            arr.push(t.getIndex(t.current - 1));
            arr.push(t.getIndex(t.current - 2));
            arr.push(t.getIndex(t.current - 3));

            for (var i = arr.length - 1; i >= 0; i--) {
                if (t.dataArray[arr[i]].type === 'isImage') {
                    t.cubeportfolio.checkSrc(t.dataArray[arr[i]].src);
                }
            }
        }

    };

    function PopUp(parent) {
        var t = this;

        t.parent = parent;

        // if lightboxShowCounter is false, put lightboxCounter to ''
        if (parent.options.lightboxShowCounter === false) {
            parent.options.lightboxCounter = '';
        }

        // if singlePageShowCounter is false, put singlePageCounter to ''
        if (parent.options.singlePageShowCounter === false) {
            parent.options.singlePageCounter = '';
        }

        // @todo - schedule this in  future
        parent.registerEvent('initStartRead', function() {
            t.run();
        }, true);
    }

    var lightboxInit = false,
        singlePageInit = false;

    PopUp.prototype.run = function() {
        var t = this,
            p = t.parent,
            body = $(document.body);

        // default value for lightbox
        p.lightbox = null;

        // LIGHTBOX
        if (p.options.lightboxDelegate && !lightboxInit) {

            // init only one time @todo
            lightboxInit = true;

            p.lightbox = Object.create(popup);

            p.lightbox.init(p, 'lightbox');

            body.on('click.cbp', p.options.lightboxDelegate, function(e) {
                e.preventDefault();

                var self = $(this),
                    gallery = self.attr('data-cbp-lightbox'),
                    scope = t.detectScope(self),
                    cbp = scope.data('cubeportfolio'),
                    blocks = [];

                // is inside a cbp
                if (cbp) {

                    cbp.blocksOn.each(function(index, el) {
                        var item = $(el);

                        if (item.not('.cbp-item-off')) {
                            item.find(p.options.lightboxDelegate).each(function(index2, el2) {
                                if (gallery) {
                                    if ($(el2).attr('data-cbp-lightbox') === gallery) {
                                        blocks.push(el2);
                                    }
                                } else {
                                    blocks.push(el2);
                                }
                            });
                        }
                    });

                } else {

                    if (gallery) {
                        blocks = scope.find(p.options.lightboxDelegate + '[data-cbp-lightbox=' + gallery + ']');
                    } else {
                        blocks = scope.find(p.options.lightboxDelegate);
                    }
                }

                p.lightbox.openLightbox(blocks, self[0]);
            });
        }

        // default value for singlePage
        p.singlePage = null;

        // SINGLEPAGE
        if (p.options.singlePageDelegate && !singlePageInit) {

            // init only one time @todo
            singlePageInit = true;

            p.singlePage = Object.create(popup);

            p.singlePage.init(p, 'singlePage');

            body.on('click.cbp', p.options.singlePageDelegate, function(e) {
                e.preventDefault();

                var self = $(this),
                    gallery = self.attr('data-cbp-singlePage'),
                    scope = t.detectScope(self),
                    cbp = scope.data('cubeportfolio'),
                    blocks = [];

                // is inside a cbp
                if (cbp) {
                    cbp.blocksOn.each(function(index, el) {
                        var item = $(el);

                        if (item.not('.cbp-item-off')) {
                            item.find(p.options.singlePageDelegate).each(function(index2, el2) {
                                if (gallery) {
                                    if ($(el2).attr('data-cbp-singlePage') === gallery) {
                                        blocks.push(el2);
                                    }
                                } else {
                                    blocks.push(el2);
                                }
                            });
                        }
                    });

                } else {

                    if (gallery) {
                        blocks = scope.find(p.options.singlePageDelegate + '[data-cbp-singlePage=' + gallery + ']');
                    } else {
                        blocks = scope.find(p.options.singlePageDelegate);
                    }

                }

                p.singlePage.openSinglePage(blocks, self[0]);
            });
        }

        // default value for singlePageInline
        p.singlePageInline = null;

        // SINGLEPAGEINLINE
        if (p.options.singlePageDelegate) {

            p.singlePageInline = Object.create(popup);

            p.singlePageInline.init(p, 'singlePageInline');

            p.$obj.on('click.cbp', p.options.singlePageInlineDelegate, function(e) {
                e.preventDefault();
                p.singlePageInline.openSinglePageInline(p.blocksOn, this);
            });

        }
    };

    PopUp.prototype.detectScope = function(item) {
        var singlePageInline,
            singlePage,
            cbp;

        singlePageInline = item.closest('.cbp-popup-singlePageInline');
        if (singlePageInline.length) {
            cbp = item.closest('.cbp', singlePageInline[0]);
            return (cbp.length) ? cbp : singlePageInline;
        }

        singlePage = item.closest('.cbp-popup-singlePage');
        if (singlePage.length) {
            cbp = item.closest('.cbp', singlePage[0]);
            return (cbp.length) ? cbp : singlePage;
        }

        cbp = item.closest('.cbp');
        return (cbp.length) ? cbp : $(document.body);

    };

    PopUp.prototype.destroy = function() {
        var p = this.parent;

        $(document.body).off('click.cbp');

        // @todo - remove these from here
        lightboxInit = false;
        singlePageInit = false;

        // destroy lightbox if enabled
        if (p.lightbox) {
            p.lightbox.destroy();
        }

        // destroy singlePage if enabled
        if (p.singlePage) {
            p.singlePage.destroy();
        }

        // destroy singlePage inline if enabled
        if (p.singlePageInline) {
            p.singlePageInline.destroy();
        }
    };

    CubePortfolio.Plugins.PopUp = function(parent) {
        return new PopUp(parent);
    };

})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    CubePortfolio.Private = {
        // array or objects: {instance: instance, fn: fn}
        resizeEventArray: [],

        initResizeEvent: function(obj) {
            var t = CubePortfolio.Private;

            if (t.resizeEventArray.length === 0) {
                t.resizeEvent();
            }

            t.resizeEventArray.push(obj);
        },

        destroyResizeEvent: function(instance) {
            var t = CubePortfolio.Private;

            var newResizeEvent = $.map(t.resizeEventArray, function(val, index) {
                if (val.instance !== instance) {
                    return val;
                }
            });

            t.resizeEventArray = newResizeEvent;

            if (t.resizeEventArray.length === 0) {
                // remove off resize event
                $(window).off('resize.cbp');
            }
        },

        resizeEvent: function() {
            var t = CubePortfolio.Private,
                timeout;

            // resize
            $(window).on('resize.cbp', function() {
                clearTimeout(timeout);

                timeout = setTimeout(function() {
                    if (window.innerHeight == screen.height) {
                        // this is fulll screen mode. don't need to trigger a resize
                        return;
                    }

                    $.each(t.resizeEventArray, function(index, val) {
                        val.fn.call(val.instance);
                    });
                }, 50);
            });
        },

        /**
         * Check if cubeportfolio instance exists on current element
         */
        checkInstance: function(method) {
            var t = $.data(this, 'cubeportfolio');

            if (!t) {
                throw new Error('cubeportfolio is not initialized. Initialize it before calling ' + method + ' method!');
            }

            t.triggerEvent('publicMethod');

            return t;
        },

        /**
         * Get info about client browser
         */
        browserInfo: function() {
            var t = CubePortfolio.Private,
                appVersion = navigator.appVersion,
                transition, animation, perspective;

            if (appVersion.indexOf('MSIE 8.') !== -1) { // ie8
                t.browser = 'ie8';
            } else if (appVersion.indexOf('MSIE 9.') !== -1) { // ie9
                t.browser = 'ie9';
            } else if (appVersion.indexOf('MSIE 10.') !== -1) { // ie10
                t.browser = 'ie10';
            } else if (window.ActiveXObject || 'ActiveXObject' in window) { // ie11
                t.browser = 'ie11';
            } else if ((/android/gi).test(appVersion)) { // android
                t.browser = 'android';
            } else if ((/iphone|ipad|ipod/gi).test(appVersion)) { // ios
                t.browser = 'ios';
            } else if ((/chrome/gi).test(appVersion)) {
                t.browser = 'chrome';
            } else {
                t.browser = '';
            }

            // check if perspective is available
            perspective = t.styleSupport('perspective');

            // if perspective is not available => no modern browser
            if (typeof perspective === undefined) {
                return;
            }

            transition = t.styleSupport('transition');

            t.transitionend = {
                WebkitTransition: 'webkitTransitionEnd',
                transition: 'transitionend'
            }[transition];

            animation = t.styleSupport('animation');

            t.animationend = {
                WebkitAnimation: 'webkitAnimationEnd',
                animation: 'animationend'
            }[animation];

            t.animationDuration = {
                WebkitAnimation: 'webkitAnimationDuration',
                animation: 'animationDuration'
            }[animation];

            t.animationDelay = {
                WebkitAnimation: 'webkitAnimationDelay',
                animation: 'animationDelay'
            }[animation];

            t.transform = t.styleSupport('transform');

            if (transition && animation && t.transform) {
                t.modernBrowser = true;
            }

        },


        /**
         * Feature testing for css3
         */
        styleSupport: function(prop) {
            var supportedProp,
                // capitalize first character of the prop to test vendor prefix
                webkitProp = 'Webkit' + prop.charAt(0).toUpperCase() + prop.slice(1),
                div = document.createElement('div');

            // browser supports standard CSS property name
            if (prop in div.style) {
                supportedProp = prop;
            } else if (webkitProp in div.style) {
                supportedProp = webkitProp;
            }

            // avoid memory leak in IE
            div = null;

            return supportedProp;
        },

    };

    CubePortfolio.Private.browserInfo();

})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    CubePortfolio.Public = {

        /*
         * Init the plugin
         */
        init: function(options, callback) {
            new CubePortfolio(this, options, callback);
        },

        /*
         * Destroy the plugin
         */
        destroy: function(callback) {
            var t = CubePortfolio.Private.checkInstance.call(this, 'destroy');

            t.triggerEvent('beforeDestroy');

            // remove data
            $.removeData(this, 'cubeportfolio');

            // remove data from blocks
            t.blocks.removeData('cbp');

            // remove loading class and .cbp on container
            t.$obj.removeClass('cbp-ready').removeAttr('style');

            // remove class from ul
            t.$ul.removeClass('cbp-wrapper');

            // remove resize event
            CubePortfolio.Private.destroyResizeEvent(t);

            t.$obj.off('.cbp');

            // reset blocks
            t.blocks.removeClass('cbp-item-off').removeAttr('style');

            t.blocks.find('.cbp-item-wrapper').children().unwrap();

            if (t.options.caption) {
                t.$obj.removeClass('cbp-caption-active cbp-caption-' + t.options.caption);
            }

            t.destroySlider();

            // remove .cbp-wrapper-outer
            t.$ul.unwrap();

            // remove .cbp-wrapper
            if (t.addedWrapp) {
                t.blocks.unwrap();
            }

            $.each(t.plugins, function(i, item) {
                if (typeof item.destroy === 'function') {
                    item.destroy();
                }
            });

            if ($.isFunction(callback)) {
                callback.call(t);
            }

            t.triggerEvent('afterDestroy');
        },

        /*
         * Filter the plugin by filterName
         */
        filter: function(param, callback) {
            var t = CubePortfolio.Private.checkInstance.call(this, 'filter'),
                expression;

            if (t.isAnimating) {
                return;
            }

            t.isAnimating = true;

            // register callback function
            if ($.isFunction(callback)) {
                t.registerEvent('filterFinish', callback, true);
            }

            if ($.isFunction(param)) {
                expression = param.call(t, t.blocks);

                if(expression === undefined) {
                    throw new Error('When you call cubeportfolio API `filter` method with a param of type function you must return the blocks that will be visible.');
                }
            } else {
                if (t.options.filterDeeplinking) {
                    var url = location.href.replace(/#cbpf=(.*?)([#\?&]|$)/gi, '');
                    location.href = url + '#cbpf=' + encodeURIComponent(param);

                    if (t.singlePage && t.singlePage.url) {
                        t.singlePage.url = location.href;
                    }
                }

                t.defaultFilter = param;
                expression = t.filterConcat(t.defaultFilter);
            }

            if (t.singlePageInline && t.singlePageInline.isOpen) {
                t.singlePageInline.close('promise', {
                    callback: function() {
                        t.computeFilter(expression);
                    }
                });
            } else {
                t.computeFilter(expression);
            }
        },

        /*
         * Show counter for filters
         */
        showCounter: function(elems, callback) {
            var t = CubePortfolio.Private.checkInstance.call(this, 'showCounter');

            t.elems = elems;

            $.each(elems, function() {
                var el = $(this),
                    filterName = el.data('filter'),
                    count;

                count = t.blocks.filter(filterName).length;
                el.find('.cbp-filter-counter').text(count);
            });

            if ($.isFunction(callback)) {
                callback.call(t);
            }
        },

        /*
         * ApendItems elements
         */
        appendItems: function(els, callback) {
            var t = CubePortfolio.Private.checkInstance.call(this, 'appendItems'),
                items = $(els).filter('.cbp-item');

            if (t.isAnimating || items.length < 1) {
                if ($.isFunction(callback)) {
                    callback.call(t);
                }

                return;
            }

            t.isAnimating = true;

            if (t.singlePageInline && t.singlePageInline.isOpen) {
                t.singlePageInline.close('promise', {
                    callback: function() {
                        t.addItems(items, callback);
                    }
                });
            } else {
                t.addItems(items, callback);
            }
        },

    };

})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function Search(parent) {
        var t = this;

        t.parent = parent;

        t.searchInput = $(parent.options.search);

        t.searchInput.each(function(index, el) {
            var selector = el.getAttribute('data-search');

            if (!selector) {
                selector = '*';
            }

            $.data(el, 'searchData', {
                value: el.value,
                el: selector
            });
        });

        var timeout = null;

        t.searchInput.on('keyup.cbp paste.cbp', function(e) {
            e.preventDefault();

            var el = $(this);

            clearTimeout(timeout);
            timeout = setTimeout(function() {
                t.runEvent.call(t, el);
            }, 300);
        });

        t.searchNothing = t.searchInput.siblings('.cbp-search-nothing').detach();
        t.searchNothingHeight = null;
        t.searchNothingHTML = t.searchNothing.html();

        t.searchInput.siblings('.cbp-search-icon').on('click.cbp', function(e) {
            e.preventDefault();

            t.runEvent.call(t, $(this).prev().val(''));
        });
    }

    Search.prototype.runEvent = function(el) {
        var t = this,
            value = el.val(),
            searchData = el.data('searchData'),
            reg = new RegExp(value, 'i');

        if (searchData.value === value || t.parent.isAnimating) {
            return;
        }

        searchData.value = value;

        if (value.length > 0) {
            el.attr('value', value);
        } else {
            el.removeAttr('value');
        }

        t.parent.$obj.cubeportfolio('filter', function(blocks) {
            var blocksNew = blocks.filter(function(index, block) {
                var text = $(block).find(searchData.el).text();

                if (text.search(reg) > -1) {
                    return true;
                }
            });

            if (blocksNew.length === 0 && t.searchNothing.length) {
                var innerText = t.searchNothingHTML.replace('{{query}}', value);
                t.searchNothing.html(innerText);

                t.searchNothing.appendTo(t.parent.$obj);

                if (t.searchNothingHeight === null) {
                    t.searchNothingHeight = t.searchNothing.outerHeight(true);
                }

                t.parent.registerEvent('resizeMainContainer', function() {
                    t.parent.height = t.parent.height + t.searchNothingHeight;
                    t.parent.obj.style.height = t.parent.height + 'px';
                }, true);
            } else {
                t.searchNothing.detach();
            }

            return blocksNew;
        }, function() {
            el.trigger('keyup.cbp');
        });
    };

    Search.prototype.destroy = function() {
        var t = this;

        t.searchInput.off('.cbp');
        t.searchInput.next('.cbp-search-icon').off('.cbp');

        t.searchInput.each(function(index, el) {
            $.removeData(el);
        });
    };

    CubePortfolio.Plugins.Search = function(parent) {
        if (parent.options.search === '') {
            return null;
        }

        return new Search(parent);
    };
})(jQuery, window, document);

if (typeof Object.create !== 'function') {
    Object.create = function(obj) {
        function F() {}
        F.prototype = obj;
        return new F();
    };
}

// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// requestAnimationFrame polyfill by Erik M�ller. fixes from Paul Irish and Tino Zijdel

// MIT license

(function() {
    var lastTime = 0;
    var vendors = ['moz', 'webkit'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() {
                    callback(currTime + timeToCall);
                },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function AnimationClassic(parent) {
        var t = this;

        t.parent = parent;

        parent.filterLayout = t.filterLayout;

        parent.registerEvent('computeBlocksFinish', function(expression) {
            parent.blocksOn2On = parent.blocksOnInitial.filter(expression);
            parent.blocksOn2Off = parent.blocksOnInitial.not(expression);
        });
    }

    // here this value point to parent grid
    AnimationClassic.prototype.filterLayout = function() {
        var t = this;

        t.$obj.addClass('cbp-animation-' + t.options.animationType);

        // [1] - blocks that are only moving with translate
        t.blocksOn2On.addClass('cbp-item-on2on')
            .each(function(index, el) {
                var data = $(el).data('cbp');
                el.style[CubePortfolio.Private.transform] = 'translate3d(' + (data.leftNew - data.left) + 'px, ' + (data.topNew - data.top) + 'px, 0)';
            });

        // [2] - blocks than intialy are on but after applying the filter are off
        t.blocksOn2Off.addClass('cbp-item-on2off');

        // [3] - blocks that are off and it will be on
        t.blocksOff2On = t.blocksOn
            .filter('.cbp-item-off')
            .removeClass('cbp-item-off')
            .addClass('cbp-item-off2on')
            .each(function(index, el) {
                var data = $(el).data('cbp');

                el.style.left = data.leftNew + 'px';
                el.style.top = data.topNew + 'px';
            });

        if (t.blocksOn2Off.length) {
            t.blocksOn2Off.last().data('cbp').wrapper.one(CubePortfolio.Private.animationend, animationend);
        } else if (t.blocksOff2On.length) {
            t.blocksOff2On.last().data('cbp').wrapper.one(CubePortfolio.Private.animationend, animationend);
        } else {
            animationend();
        }

        // resize main container height
        t.resizeMainContainer();

        function animationend() {
            t.blocks.removeClass('cbp-item-on2off cbp-item-off2on cbp-item-on2on')
                .each(function(index, el) {
                    var data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';

                    el.style[CubePortfolio.Private.transform] = '';
                });

            t.blocksOff.addClass('cbp-item-off');

            t.$obj.removeClass('cbp-animation-' + t.options.animationType);

            t.filterFinish();
        }
    };

    AnimationClassic.prototype.destroy = function() {
        var parent = this.parent;
        parent.$obj.removeClass('cbp-animation-' + parent.options.animationType);
    };

    CubePortfolio.Plugins.AnimationClassic = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || $.inArray(parent.options.animationType, ['boxShadow', 'fadeOut', 'flipBottom', 'flipOut', 'quicksand', 'scaleSides', 'skew']) < 0) {
            return null;
        }

        return new AnimationClassic(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function AnimationClone(parent) {
        var t = this;

        t.parent = parent;

        parent.filterLayout = t.filterLayout;
    }

    // here this value point to parent grid
    AnimationClone.prototype.filterLayout = function() {
        var t = this,
            ulClone = t.$ul[0].cloneNode(true);

        ulClone.setAttribute('class', 'cbp-wrapper-helper');
        t.wrapper[0].insertBefore(ulClone, t.$ul[0]);

        requestAnimationFrame(function() {
            t.$obj.addClass('cbp-animation-' + t.options.animationType);

            t.blocksOff.addClass('cbp-item-off');

            t.blocksOn.removeClass('cbp-item-off')
                .each(function(index, el) {
                    var data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';

                    if (t.options.animationType === 'sequentially') {
                        data.wrapper[0].style[CubePortfolio.Private.animationDelay] = (index * 60) + 'ms';
                    }
                });

            if (t.blocksOn.length) {
                t.blocksOn.last().data('cbp').wrapper.one(CubePortfolio.Private.animationend, animationend);
            } else if (t.blocksOnInitial.length) {
                t.blocksOnInitial.last().data('cbp').wrapper.one(CubePortfolio.Private.animationend, animationend);
            } else {
                animationend();
            }

            // resize main container height
            t.resizeMainContainer();
        });

        function animationend() {
            t.wrapper[0].removeChild(ulClone);

            if (t.options.animationType === 'sequentially') {
                t.blocksOn.each(function(index, el) {
                    $(el).data('cbp').wrapper[0].style[CubePortfolio.Private.animationDelay] = '';
                });
            }

            t.$obj.removeClass('cbp-animation-' + t.options.animationType);

            t.filterFinish();
        }
    };

    AnimationClone.prototype.destroy = function() {
        var parent = this.parent;
        parent.$obj.removeClass('cbp-animation-' + parent.options.animationType);
    };

    CubePortfolio.Plugins.AnimationClone = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || $.inArray(parent.options.animationType, ['fadeOutTop', 'slideLeft', 'sequentially']) < 0) {
            return null;
        }

        return new AnimationClone(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function AnimationCloneDelay(parent) {
        var t = this;

        t.parent = parent;

        parent.filterLayout = t.filterLayout;
    }

    // here this value point to parent grid
    AnimationCloneDelay.prototype.filterLayout = function() {
        var t = this,
            ulClone = t.$ul.clone(true, true);

        ulClone[0].setAttribute('class', 'cbp-wrapper-helper');
        t.wrapper[0].insertBefore(ulClone[0], t.$ul[0]);

        // hack for safari osx because it doesn't want to work if I set animationDelay
        // on cbp-item-wrapper before I clone the t.$ul
        var items = ulClone.find('.cbp-item').not('.cbp-item-off');
        t.sortBlocks(items, 'top');
        items.children('.cbp-item-wrapper').each(function(index, el) {
            el.style[CubePortfolio.Private.animationDelay] = (index * 50) + 'ms';
        });

        requestAnimationFrame(function() {
            t.$obj.addClass('cbp-animation-' + t.options.animationType);

            t.blocksOff.addClass('cbp-item-off');

            t.blocksOn.removeClass('cbp-item-off')
                .each(function(index, el) {
                    var data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';

                    data.wrapper[0].style[CubePortfolio.Private.animationDelay] = (index * 50) + 'ms';
                });

            var onLength = t.blocksOn.length,
                offLength = items.length;

            if (onLength === 0 && offLength === 0) {
                animationend();
            } else if (onLength < offLength) {
                items.last().children('.cbp-item-wrapper').one(CubePortfolio.Private.animationend, animationend);
            } else {
                t.blocksOn.last().data('cbp').wrapper.one(CubePortfolio.Private.animationend, animationend);
            }

            // resize main container height
            t.resizeMainContainer();
        });

        function animationend() {
            t.wrapper[0].removeChild(ulClone[0]);

            t.$obj.removeClass('cbp-animation-' + t.options.animationType);

            t.blocks.each(function(index, el) {
                $(el).data('cbp').wrapper[0].style[CubePortfolio.Private.animationDelay] = '';
            });

            t.filterFinish();
        }
    };

    AnimationCloneDelay.prototype.destroy = function() {
        var parent = this.parent;
        parent.$obj.removeClass('cbp-animation-' + parent.options.animationType);
    };

    CubePortfolio.Plugins.AnimationCloneDelay = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || $.inArray(parent.options.animationType, ['3dflip', 'flipOutDelay', 'foldLeft', 'frontRow', 'rotateRoom', 'rotateSides', 'scaleDown', 'slideDelay', 'unfold']) < 0) {
            return null;
        }

        return new AnimationCloneDelay(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function AnimationWrapper(parent) {
        var t = this;

        t.parent = parent;

        parent.filterLayout = t.filterLayout;
    }

    // here this value point to parent grid
    AnimationWrapper.prototype.filterLayout = function() {
        var t = this,
            ulClone = t.$ul[0].cloneNode(true);

        ulClone.setAttribute('class', 'cbp-wrapper-helper');
        t.wrapper[0].insertBefore(ulClone, t.$ul[0]);

        requestAnimationFrame(function() {
            t.$obj.addClass('cbp-animation-' + t.options.animationType);

            t.blocksOff.addClass('cbp-item-off');

            t.blocksOn.removeClass('cbp-item-off')
                .each(function(index, el) {
                    var data = $(el).data('cbp');

                    data.left = data.leftNew;
                    data.top = data.topNew;

                    el.style.left = data.left + 'px';
                    el.style.top = data.top + 'px';
                });

            if (t.blocksOn.length) {
                t.$ul.one(CubePortfolio.Private.animationend, animationend);
            } else if (t.blocksOnInitial.length) {
                $(ulClone).one(CubePortfolio.Private.animationend, animationend);
            } else {
                animationend();
            }

            // resize main container height
            t.resizeMainContainer();
        });

        function animationend() {
            t.wrapper[0].removeChild(ulClone);

            t.$obj.removeClass('cbp-animation-' + t.options.animationType);

            t.filterFinish();
        }
    };

    AnimationWrapper.prototype.destroy = function() {
        var parent = this.parent;
        parent.$obj.removeClass('cbp-animation-' + parent.options.animationType);
    };

    CubePortfolio.Plugins.AnimationWrapper = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || $.inArray(parent.options.animationType, ['bounceBottom', 'bounceLeft', 'bounceTop', 'moveLeft']) < 0) {
            return null;
        }

        return new AnimationWrapper(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function CaptionExpand(parent) {
        var t = this;

        t.parent = parent;

        parent.registerEvent('initFinish', function() {
            parent.$obj.on('click.cbp', '.cbp-caption-defaultWrap', function(e) {
                e.preventDefault();

                if (parent.isAnimating) {
                    return;
                }

                parent.isAnimating = true;

                var defaultWrap = $(this),
                    activeWrap = defaultWrap.next(),
                    caption = defaultWrap.parent(),
                    endStyle = {
                        position: 'relative',
                        height: activeWrap.outerHeight(true)
                    },
                    startStyle = {
                        position: 'relative',
                        height: 0
                    };

                parent.$obj.addClass('cbp-caption-expand-active');

                // swap endStyle & startStyle
                if (caption.hasClass('cbp-caption-expand-open')) {
                    var temp = startStyle;
                    startStyle = endStyle;
                    endStyle = temp;
                    caption.removeClass('cbp-caption-expand-open');
                }

                activeWrap.css(endStyle);

                parent.$obj.one('pluginResize.cbp', function() {
                    parent.isAnimating = false;
                    parent.$obj.removeClass('cbp-caption-expand-active');

                    if (endStyle.height === 0) {
                        caption.removeClass('cbp-caption-expand-open');
                        activeWrap.attr('style', '');
                    }
                });

                // reposition the blocks
                parent.layoutAndAdjustment();

                // set activeWrap to 0 so I can start animation in the next frame
                activeWrap.css(startStyle);

                // delay animation
                requestAnimationFrame(function() {
                    caption.addClass('cbp-caption-expand-open');

                    activeWrap.css(endStyle);

                    if (parent.options.layoutMode === 'slider') {
                        parent.updateSlider();
                    }

                    parent.triggerEvent('resizeGrid');
                });
            });
        }, true);
    }

    CaptionExpand.prototype.destroy = function() {
        this.parent.$obj.find('.cbp-caption-defaultWrap').off('click.cbp').parent().removeClass('cbp-caption-expand-active');
    };

    CubePortfolio.Plugins.CaptionExpand = function(parent) {

        if (parent.options.caption !== 'expand') {
            return null;
        }

        return new CaptionExpand(parent);
    };

})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function BottomToTop(parent) {
        var deferred = $.Deferred();

        parent.pushQueue('delayFrame', deferred);

        parent.registerEvent('initEndWrite', function() {
            parent.blocksOn.each(function(index, el) {
                el.style[CubePortfolio.Private.animationDelay] = (index * parent.options.displayTypeSpeed) + 'ms';
            });

            parent.$obj.addClass('cbp-displayType-bottomToTop');

            // get last element
            parent.blocksOn.last().one(CubePortfolio.Private.animationend, function() {
                parent.$obj.removeClass('cbp-displayType-bottomToTop');

                parent.blocksOn.each(function(index, el) {
                    el.style[CubePortfolio.Private.animationDelay] = '';
                });

                // resolve event after the animation is finished
                deferred.resolve();
            });
        }, true);
    }

    CubePortfolio.Plugins.BottomToTop = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || parent.options.displayType !== 'bottomToTop' || parent.blocksOn.length === 0) {
            return null;
        }

        return new BottomToTop(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function FadeInToTop(parent) {
        var deferred = $.Deferred();

        parent.pushQueue('delayFrame', deferred);

        parent.registerEvent('initEndWrite', function() {
            parent.obj.style[CubePortfolio.Private.animationDuration] = parent.options.displayTypeSpeed + 'ms';

            parent.$obj.addClass('cbp-displayType-fadeInToTop');

            parent.$obj.one(CubePortfolio.Private.animationend, function() {
                parent.$obj.removeClass('cbp-displayType-fadeInToTop');

                parent.obj.style[CubePortfolio.Private.animationDuration] = '';

                // resolve event after the animation is finished
                deferred.resolve();
            });
        }, true);
    }

    CubePortfolio.Plugins.FadeInToTop = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || parent.options.displayType !== 'fadeInToTop' || parent.blocksOn.length === 0) {
            return null;
        }

        return new FadeInToTop(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function LazyLoading(parent) {
        var deferred = $.Deferred();

        parent.pushQueue('delayFrame', deferred);

        parent.registerEvent('initEndWrite', function() {
            parent.obj.style[CubePortfolio.Private.animationDuration] = parent.options.displayTypeSpeed + 'ms';

            parent.$obj.addClass('cbp-displayType-lazyLoading');

            parent.$obj.one(CubePortfolio.Private.animationend, function() {
                parent.$obj.removeClass('cbp-displayType-lazyLoading');

                parent.obj.style[CubePortfolio.Private.animationDuration] = '';

                // resolve event after the animation is finished
                deferred.resolve();
            });
        }, true);
    }

    CubePortfolio.Plugins.LazyLoading = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || (parent.options.displayType !== 'lazyLoading' && parent.options.displayType !== 'fadeIn') || parent.blocksOn.length === 0) {
            return null;
        }

        return new LazyLoading(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    function DisplaySequentially(parent) {
        var deferred = $.Deferred();

        parent.pushQueue('delayFrame', deferred);

        parent.registerEvent('initEndWrite', function() {
            parent.blocksOn.each(function(index, el) {
                el.style[CubePortfolio.Private.animationDelay] = (index * parent.options.displayTypeSpeed) + 'ms';
            });

            parent.$obj.addClass('cbp-displayType-sequentially');

            // get last element
            parent.blocksOn.last().one(CubePortfolio.Private.animationend, function() {
                parent.$obj.removeClass('cbp-displayType-sequentially');

                parent.blocksOn.each(function(index, el) {
                    el.style[CubePortfolio.Private.animationDelay] = '';
                });

                // resolve event after the animation is finished
                deferred.resolve();
            });
        }, true);
    }

    CubePortfolio.Plugins.DisplaySequentially = function(parent) {
        if (!CubePortfolio.Private.modernBrowser || parent.options.displayType !== 'sequentially' || parent.blocksOn.length === 0) {
            return null;
        }

        return new DisplaySequentially(parent);
    };
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    $.extend(CubePortfolio.prototype, {
        mosaicLayoutReset: function() {
            var t = this;

            // flag to be set after the blocks sorting is done
            t.blocksAreSorted = false;

            // when I start layout all blocks must not be positionated
            t.blocksOn.each(function(index, el) {
                $(el).data('cbp').pack = false;
            });
        },


        mosaicLayout: function() {
            var t = this,
                blocksLen = t.blocksOn.length,
                i, spaceIndexAndBlock = {},
                leftEnd;

            // array of objects where I keep the spaces available in the grid
            t.freeSpaces = [{
                leftStart: 0,
                leftEnd: t.widthAvailable,
                topStart: 0,
                topEnd: Math.pow(2, 18) // @todo - optimize
            }];

            for (i = 0; i < blocksLen; i++) {
                spaceIndexAndBlock = t.getSpaceIndexAndBlock();

                // if space or block are null then the sorting must be done
                if (spaceIndexAndBlock === null) {
                    // sort blocks
                    t.sortBlocksToPreventGaps();

                    // after the sort is done start the layout again
                    t.mosaicLayout();

                    return;
                }

                t.generateF1F2(spaceIndexAndBlock.spaceIndex, spaceIndexAndBlock.dataBlock);

                t.generateG1G2G3G4(spaceIndexAndBlock.dataBlock);

                t.cleanFreeSpaces();

                t.addHeightToBlocks();
            }

            // sort the blocks from top to bottom to add properly displayAnimation and animationType
            if (t.blocksAreSorted) {
                t.sortBlocks(t.blocksOn, 'topNew');
            }
        },


        /**
         * Chose from freeSpaces the best space available
         * Find block by verifying if it can fit in bestSpace(top-left space available)
         * If block don't fit in the first space available & t.options.sortToPreventGaps
         * is set to true then sort the blocks and start the layout once again
         * Decide the free rectangle Fi from F to pack the rectangle R into.
         */
        getSpaceIndexAndBlock: function() {
            var t = this,
                spaceIndexAndBlock = null;

            $.each(t.freeSpaces, function(index1, space) {
                var widthSpace = space.leftEnd - space.leftStart,
                    heightSpace = space.topEnd - space.topStart;

                t.blocksOn.each(function(index2, block) {
                    var data = $(block).data('cbp');

                    if (data.pack === true) {
                        return;
                    }

                    if (data.widthAndGap <= widthSpace && data.heightAndGap <= heightSpace) {
                        // now the rectagle can be positioned
                        data.pack = true;

                        spaceIndexAndBlock = {
                            spaceIndex: index1,
                            dataBlock: data
                        };

                        data.leftNew = space.leftStart;
                        data.topNew = space.topStart;

                        // if the block is founded => return from this loop
                        return false;
                    }
                });

                // if first space don't have a block and sortToPreventGaps is true => return from loop
                if (!t.blocksAreSorted && t.options.sortToPreventGaps && index1 > 0) {
                    spaceIndexAndBlock = null;

                    return false;
                }

                // if space & block is founded => return from loop
                if (spaceIndexAndBlock !== null) {
                    return false;
                }
            });

            return spaceIndexAndBlock;
        },


        /**
         * Use the MAXRECTS split scheme to subdivide Fi(space) into F1 and F2 and
         * then remove that space from spaces
         * Insert F1 & F2 in F in place of Fi
         */
        generateF1F2: function(spaceIndex, block) {
            var t = this,
                space = t.freeSpaces[spaceIndex];

            var F1 = {
                leftStart: space.leftStart + block.widthAndGap,
                leftEnd: space.leftEnd,
                topStart: space.topStart,
                topEnd: space.topEnd
            };

            var F2 = {
                leftStart: space.leftStart,
                leftEnd: space.leftEnd,
                topStart: space.topStart + block.heightAndGap,
                topEnd: space.topEnd
            };

            // remove Fi from F
            t.freeSpaces.splice(spaceIndex, 1);

            if (F1.leftEnd > F1.leftStart && F1.topEnd > F1.topStart) {
                t.freeSpaces.splice(spaceIndex, 0, F1);
                spaceIndex++;
            }

            if (F2.leftEnd > F2.leftStart && F2.topEnd > F2.topStart) {
                t.freeSpaces.splice(spaceIndex, 0, F2);
            }
        },


        /**
         * Generate G1, G2, G3, G4 from intersaction of t.freeSpaces with block
         */
        generateG1G2G3G4: function(block) {
            var t = this;

            var spaces = [];

            $.each(t.freeSpaces, function(index, space) {
                var intersectSpace = t.intersectSpaces(space, block);

                // if block & space are the same push space in spaces and return
                if (intersectSpace === null) {
                    spaces.push(space);
                    return;
                }

                t.generateG1(space, intersectSpace, spaces);
                t.generateG2(space, intersectSpace, spaces);
                t.generateG3(space, intersectSpace, spaces);
                t.generateG4(space, intersectSpace, spaces);
            });

            t.freeSpaces = spaces;
        },


        /**
         * Return the intersected rectagle of Fi and block
         * If the two spaces don't intersect or are the same return null
         */
        intersectSpaces: function(space1, block) {
            var t = this,
                space2 = {
                    leftStart: block.leftNew,
                    leftEnd: block.leftNew + block.widthAndGap,
                    topStart: block.topNew,
                    topEnd: block.topNew + block.heightAndGap,
                };

            if (space1.leftStart === space2.leftStart &&
                space1.leftEnd === space2.leftEnd &&
                space1.topStart === space2.topStart &&
                space1.topEnd === space2.topEnd) {
                return null;
            }

            var leftStart = Math.max(space1.leftStart, space2.leftStart),
                leftEnd = Math.min(space1.leftEnd, space2.leftEnd),
                topStart = Math.max(space1.topStart, space2.topStart),
                topEnd = Math.min(space1.topEnd, space2.topEnd);

            if (leftEnd <= leftStart || topEnd <= topStart) {
                return null;
            }

            return {
                leftStart: leftStart,
                leftEnd: leftEnd,
                topStart: topStart,
                topEnd: topEnd
            };
        },


        /**
         * The top subdivide space
         */
        generateG1: function(space, intersectSpace, spaces) {
            if (space.topStart === intersectSpace.topStart) {
                return;
            }

            spaces.push({
                leftStart: space.leftStart,
                leftEnd: space.leftEnd,
                topStart: space.topStart,
                topEnd: intersectSpace.topStart
            });
        },


        /**
         * The right subdivide space
         */
        generateG2: function(space, intersectSpace, spaces) {
            if (space.leftEnd === intersectSpace.leftEnd) {
                return;
            }

            spaces.push({
                leftStart: intersectSpace.leftEnd,
                leftEnd: space.leftEnd,
                topStart: space.topStart,
                topEnd: space.topEnd
            });
        },


        /**
         * The bottom subdivide space
         */
        generateG3: function(space, intersectSpace, spaces) {
            if (space.topEnd === intersectSpace.topEnd) {
                return;
            }

            spaces.push({
                leftStart: space.leftStart,
                leftEnd: space.leftEnd,
                topStart: intersectSpace.topEnd,
                topEnd: space.topEnd
            });
        },


        /**
         * The left subdivide space
         */
        generateG4: function(space, intersectSpace, spaces) {
            if (space.leftStart === intersectSpace.leftStart) {
                return;
            }

            spaces.push({
                leftStart: space.leftStart,
                leftEnd: intersectSpace.leftStart,
                topStart: space.topStart,
                topEnd: space.topEnd
            });
        },


        /**
         * For every Fi check if is another Fj so Fj contains Fi
         * @todo - refactor
         */
        cleanFreeSpaces: function() {
            var t = this;

            // sort space from top to bottom and left to right
            t.freeSpaces.sort(function(space1, space2) {
                if (space1.topStart > space2.topStart) {
                    return 1;
                } else if (space1.topStart < space2.topStart) {
                    return -1;
                } else {
                    if (space1.leftStart > space2.leftStart) {
                        return 1;
                    } else if (space1.leftStart < space2.leftStart) {
                        return -1;
                    } else {
                        return 0;
                    }
                }
            });

            t.correctSubPixelValues();

            t.removeNonMaximalFreeSpaces();
        },


        /**
         * If topStart values for spaces are <= 1px then align those spaces
         */
        correctSubPixelValues: function() {
            var t = this,
                i, len, diff, space1, space2;

            for (i = 0, len = t.freeSpaces.length - 1; i < len; i++) {
                space1 = t.freeSpaces[i];
                space2 = t.freeSpaces[i + 1];

                if ((space2.topStart - space1.topStart) <= 1) {
                    space2.topStart = space1.topStart;
                }
            }
        },


        /**
         * Remove spaces that are not maximal
         * If Fi contains Fj then remove Fj from F
         */
        removeNonMaximalFreeSpaces: function() {
            var t = this;

            t.uniqueFreeSpaces();

            t.freeSpaces = $.map(t.freeSpaces, function(space1, index1) {
                $.each(t.freeSpaces, function(index2, space2) {
                    // don't compare the same free spaces
                    if (index1 === index2) {
                        return;
                    }

                    if (space2.leftStart <= space1.leftStart &&
                        space2.leftEnd >= space1.leftEnd &&
                        space2.topStart <= space1.topStart &&
                        space2.topEnd >= space1.topEnd) {

                        space1 = null;
                        return false;
                    }
                });

                return space1;
            });
        },


        /**
         * Remove duplicates spaces from freeSpaces
         */
        uniqueFreeSpaces: function() {
            var t = this,
                result = [];

            $.each(t.freeSpaces, function(index1, space1) {
                $.each(result, function(index2, space2) {
                    if (space2.leftStart === space1.leftStart &&
                        space2.leftEnd === space1.leftEnd &&
                        space2.topStart === space1.topStart &&
                        space2.topEnd === space1.topEnd) {

                        space1 = null;
                        return false;
                    }
                });

                if (space1 !== null) {
                    result.push(space1);
                }
            });

            t.freeSpaces = result;
        },


        /**
         * If freeSpaces have only one space and that space overlap the
         * height of the bottom blocks with 1px cut those blocks
         */
        addHeightToBlocks: function() {
            var t = this;

            if (t.freeSpaces.length > 1) {
                return;
            }

            var topStart = t.freeSpaces[0].topStart;

            t.blocksOn.each(function(index, block) {
                var data = $(block).data('cbp');

                if (data.pack !== true) {
                    return;
                }

                var diff = topStart - data.topNew - data.heightAndGap;

                if (diff < 0) {
                    block.style.height = (data.height + diff) + 'px';
                }
            });
        },


        /**
         * Sort by the longer width first, followed by a comparison of the shorter height
         */
        sortBlocksToPreventGaps: function() {
            var t = this;

            t.blocksAreSorted = true;

            // sort based on timestamp attribute
            t.blocksOn.sort(function(block1, block2) {
                var data1 = $(block1).data('cbp'),
                    data2 = $(block2).data('cbp');

                // order desc by width
                if (data1.widthAndGap < data2.widthAndGap) {
                    return 1;
                } else if (data1.widthAndGap > data2.widthAndGap) {
                    return -1;
                } else {
                    // order desc by height
                    if (data1.heightAndGap < data2.heightAndGap) {
                        return 1;
                    } else if (data1.heightAndGap > data2.heightAndGap) {
                        return -1;
                    } else {
                        // order asc by index
                        if (data1.index > data2.index) {
                            return 1;
                        } else if (data1.index < data2.index) {
                            return -1;
                        }
                    }
                }
            });

            // when I start the layout again all blocks must not be positionated
            // reset height if it was set for addHeightToBlocks
            t.blocksOn.each(function(index, el) {
                $(el).data('cbp').pack = false;
                el.style.height = '';
            });
        },


        /**
         * Generic sort block function from lower to highest values
         */
        sortBlocks: function(blocks, compare) {
            var t = this;

            blocks.sort(function(block1, block2) {
                var data1 = $(block1).data('cbp'),
                    data2 = $(block2).data('cbp');

                // if the items are equally order them from left to right
                if (data1[compare] > data2[compare]) {
                    return 1;
                } else if (data1[compare] < data2[compare]) {
                    return -1;
                } else {
                    if (data1.leftNew > data2.leftNew) {
                        return 1;
                    } else if (data1.leftNew < data2.leftNew) {
                        return -1;
                    } else {
                        return 0;
                    }
                }
            });
        }
    });
})(jQuery, window, document);

(function($, window, document, undefined) {
    'use strict';

    var CubePortfolio = $.fn.cubeportfolio.Constructor;

    // @todo - gandit cum ar trebui sa fac aici ca nu prea ar merge un plugin
    // pt slider ca as extinde pe CubePortfolio.prototype la fiecare initializare
    $.extend(CubePortfolio.prototype, {
        // create mark
        sliderMarkup: function() {
            var t = this;

            t.sliderStopEvents = false;

            t.sliderActive = 0;

            t.$obj.one('initComplete.cbp', function() {
                t.$obj.addClass('cbp-mode-slider');
            });

            t.nav = $('<div/>', {
                'class': 'cbp-nav'
            });

            t.nav.on('click.cbp', '[data-slider-action]', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();

                if (t.sliderStopEvents) {
                    return;
                }

                var el = $(this),
                    action = el.attr('data-slider-action');

                if (t[action + 'Slider']) {
                    t[action + 'Slider'](el);
                }
            });

            if (t.options.showNavigation) {
                t.controls = $('<div/>', {
                    'class': 'cbp-nav-controls'
                });

                t.navPrev = $('<div/>', {
                    'class': 'cbp-nav-prev',
                    'data-slider-action': 'prev'
                }).appendTo(t.controls);

                t.navNext = $('<div/>', {
                    'class': 'cbp-nav-next',
                    'data-slider-action': 'next'
                }).appendTo(t.controls);

                t.controls.appendTo(t.nav);
            }

            if (t.options.showPagination) {
                t.navPagination = $('<div/>', {
                    'class': 'cbp-nav-pagination'
                }).appendTo(t.nav);
            }

            if (t.controls || t.navPagination) {
                t.nav.appendTo(t.$obj);
            }

            t.updateSliderPagination();

            if (t.options.auto) {
                if (t.options.autoPauseOnHover) {
                    t.mouseIsEntered = false;
                    t.$obj.on('mouseenter.cbp', function(e) {
                        t.mouseIsEntered = true;
                        t.stopSliderAuto();
                    }).on('mouseleave.cbp', function(e) {
                        t.mouseIsEntered = false;
                        t.startSliderAuto();
                    });
                }

                t.startSliderAuto();
            }

            if (t.options.drag && CubePortfolio.Private.modernBrowser) {
                t.dragSlider();
            }
        },

        updateSlider: function() {
            var t = this;

            t.updateSliderPosition();

            t.updateSliderPagination();
        },

        updateSliderPagination: function() {
            var t = this,
                pages,
                i;

            if (t.options.showPagination) {
                // get number of pages
                pages = Math.ceil(t.blocksOn.length / t.cols);
                t.navPagination.empty();

                for (i = pages - 1; i >= 0; i--) {
                    $('<div/>', {
                        'class': 'cbp-nav-pagination-item',
                        'data-slider-action': 'jumpTo'
                    }).appendTo(t.navPagination);
                }

                t.navPaginationItems = t.navPagination.children();
            }

            // enable disable the nav
            t.enableDisableNavSlider();
        },

        destroySlider: function() {
            var t = this;

            if (t.options.layoutMode !== 'slider') {
                return;
            }

            t.$obj.off('.cbp');

            t.$obj.removeClass('cbp-mode-slider');

            if (t.options.showNavigation) {
                t.nav.off('.cbp');
                t.nav.remove();
            }

            if (t.navPagination) {
                t.navPagination.remove();
            }

            t.$ul.removeAttr('style');

            t.$ul.off('.cbp');

            $(document).off('.cbp'); // @todo - don't interfer with the lightbox

            if (t.options.auto) {
                t.stopSliderAuto();
            }
        },

        nextSlider: function(el) {
            var t = this;

            if (t.isEndSlider()) {
                if (t.isRewindNav()) {
                    t.sliderActive = 0;
                } else {
                    return;
                }
            } else {
                if (t.options.scrollByPage) {
                    t.sliderActive = Math.min(t.sliderActive + t.cols, t.blocksOn.length - t.cols);
                } else {
                    t.sliderActive += 1;
                }
            }

            t.goToSlider();
        },

        prevSlider: function(el) {
            var t = this;

            if (t.isStartSlider()) {
                if (t.isRewindNav()) {
                    t.sliderActive = t.blocksOn.length - t.cols;
                } else {
                    return;
                }
            } else {
                if (t.options.scrollByPage) {
                    t.sliderActive = Math.max(0, t.sliderActive - t.cols);
                } else {
                    t.sliderActive -= 1;
                }
            }

            t.goToSlider();
        },

        jumpToSlider: function(el) {
            var t = this,
                index = Math.min(el.index() * t.cols, t.blocksOn.length - t.cols);

            if (index === t.sliderActive) {
                return;
            }

            t.sliderActive = index;

            t.goToSlider();
        },

        jumpDragToSlider: function(pos) {
            var t = this,
                jumpWidth,
                offset,
                condition,
                index,
                dragLeft = (pos > 0) ? true : false;

            if (t.options.scrollByPage) {
                jumpWidth = t.cols * t.columnWidth;
                offset = t.cols;
            } else {
                jumpWidth = t.columnWidth;
                offset = 1;
            }

            pos = Math.abs(pos);
            index = Math.floor(pos / jumpWidth) * offset;

            if (pos % jumpWidth > 20) {
                index += offset;
            }

            if (dragLeft) { // drag to left
                t.sliderActive = Math.min(t.sliderActive + index, t.blocksOn.length - t.cols);
            } else { // drag to right
                t.sliderActive = Math.max(0, t.sliderActive - index);
            }

            t.goToSlider();
        },

        isStartSlider: function() {
            return this.sliderActive === 0;
        },

        isEndSlider: function() {
            var t = this;
            return (t.sliderActive + t.cols) > t.blocksOn.length - 1;
        },

        goToSlider: function() {
            var t = this;

            // enable disable the nav
            t.enableDisableNavSlider();

            t.updateSliderPosition();
        },

        startSliderAuto: function() {
            var t = this;

            if (t.isDrag) {
                t.stopSliderAuto();
                return;
            }

            t.timeout = setTimeout(function() {
                // go to next slide
                t.nextSlider();

                // start auto
                t.startSliderAuto();

            }, t.options.autoTimeout);
        },

        stopSliderAuto: function() {
            clearTimeout(this.timeout);
        },

        enableDisableNavSlider: function() {
            var t = this,
                page,
                method;

            if (!t.isRewindNav()) {
                method = (t.isStartSlider()) ? 'addClass' : 'removeClass';
                t.navPrev[method]('cbp-nav-stop');

                method = (t.isEndSlider()) ? 'addClass' : 'removeClass';
                t.navNext[method]('cbp-nav-stop');
            }

            if (t.options.showPagination) {
                if (t.options.scrollByPage) {
                    page = Math.ceil(t.sliderActive / t.cols);
                } else {
                    if (t.isEndSlider()) {
                        page = t.navPaginationItems.length - 1;
                    } else {
                        page = Math.floor(t.sliderActive / t.cols);
                    }
                }

                // add class active on pagination's items
                t.navPaginationItems.removeClass('cbp-nav-pagination-active')
                    .eq(page)
                    .addClass('cbp-nav-pagination-active');
            }
        },

        /**
         * If slider loop is enabled don't add classes to `next` and `prev` buttons
         */
        isRewindNav: function() {
            var t = this;

            if (!t.options.showNavigation) {
                return true;
            }

            if (t.blocksOn.length <= t.cols) {
                return false;
            }

            if (t.options.rewindNav) {
                return true;
            }

            return false;
        },

        sliderItemsLength: function() {
            return this.blocksOn.length <= this.cols;
        },

        /**
         * Arrange the items in a slider layout
         */
        sliderLayout: function() {
            var t = this;

            t.blocksOn.each(function(index, el) {
                var data = $(el).data('cbp');

                // update the values with the new ones
                data.leftNew = t.columnWidth * index;
                data.topNew = 0;

                t.sliderFreeSpaces.push({
                    topStart: data.heightAndGap
                });
            });

            t.getFreeSpacesForSlider();

            t.$ul.width(t.columnWidth * t.blocksOn.length - t.options.gapVertical);
        },

        getFreeSpacesForSlider: function() {
            var t = this;

            t.freeSpaces = t.sliderFreeSpaces.slice(t.sliderActive, t.sliderActive + t.cols);

            t.freeSpaces.sort(function(space1, space2) {
                if (space1.topStart > space2.topStart) {
                    return 1;
                } else if (space1.topStart < space2.topStart) {
                    return -1;
                }
            });
        },

        updateSliderPosition: function() {
            var t = this,
                value = -t.sliderActive * t.columnWidth;

            if (CubePortfolio.Private.modernBrowser) {
                t.$ul[0].style[CubePortfolio.Private.transform] = 'translate3d(' + value + 'px, 0px, 0)';
            } else {
                t.$ul[0].style.left = value + 'px';
            }

            t.getFreeSpacesForSlider();

            t.resizeMainContainer();
        },

        dragSlider: function() {
            var t = this,
                $document = $(document),
                posInitial,
                pos,
                target,
                ulPosition,
                ulMaxWidth,
                isAnimating = false,
                events = {},
                isTouch = false,
                touchStartEvent,
                isHover = false;

            t.isDrag = false;

            if (('ontouchstart' in window) ||
                (navigator.maxTouchPoints > 0) ||
                (navigator.msMaxTouchPoints > 0)) {

                events = {
                    start: 'touchstart.cbp',
                    move: 'touchmove.cbp',
                    end: 'touchend.cbp'
                };

                isTouch = true;
            } else {
                events = {
                    start: 'mousedown.cbp',
                    move: 'mousemove.cbp',
                    end: 'mouseup.cbp'
                };
            }

            function dragStart(e) {
                if (t.sliderItemsLength()) {
                    return;
                }

                if (!isTouch) {
                    e.preventDefault();
                } else {
                    touchStartEvent = e;
                }

                if (t.options.auto) {
                    t.stopSliderAuto();
                }

                if (isAnimating) {
                    $(target).one('click.cbp', function() {
                        return false;
                    });
                    return;
                }

                target = $(e.target);
                posInitial = pointerEventToXY(e).x;
                pos = 0;
                ulPosition = -t.sliderActive * t.columnWidth;
                ulMaxWidth = t.columnWidth * (t.blocksOn.length - t.cols);

                $document.on(events.move, dragMove);
                $document.on(events.end, dragEnd);

                t.$obj.addClass('cbp-mode-slider-dragStart');
            }

            function dragEnd(e) {
                t.$obj.removeClass('cbp-mode-slider-dragStart');

                // put the state to animate
                isAnimating = true;

                if (pos !== 0) {
                    target.one('click.cbp', function() {
                        return false;
                    });

                    t.jumpDragToSlider(pos);

                    t.$ul.one(CubePortfolio.Private.transitionend, afterDragEnd);
                } else {
                    afterDragEnd.call(t);
                }

                $document.off(events.move);
                $document.off(events.end);
            }

            function dragMove(e) {
                pos = posInitial - pointerEventToXY(e).x;

                if (pos > 8 || pos < -8) {
                    e.preventDefault();
                }

                t.isDrag = true;

                var value = ulPosition - pos;

                if (pos < 0 && pos < ulPosition) { // to right
                    value = (ulPosition - pos) / 5;
                } else if (pos > 0 && (ulPosition - pos) < -ulMaxWidth) { // to left
                    value = -ulMaxWidth + (ulMaxWidth + ulPosition - pos) / 5;
                }

                if (CubePortfolio.Private.modernBrowser) {
                    t.$ul[0].style[CubePortfolio.Private.transform] = 'translate3d(' + value + 'px, 0px, 0)';
                } else {
                    t.$ul[0].style.left = value + 'px';
                }
            }

            function afterDragEnd() {
                isAnimating = false;
                t.isDrag = false;

                if (t.options.auto) {
                    if (t.mouseIsEntered) {
                        return;
                    }

                    t.startSliderAuto();
                }
            }

            function pointerEventToXY(e) {
                if (e.originalEvent !== undefined && e.originalEvent.touches !== undefined) {
                    e = e.originalEvent.touches[0];
                }

                return {
                    x: e.pageX,
                    y: e.pageY
                };
            }

            t.$ul.on(events.start, dragStart);
        },

        /**
         * Reset the slider layout
         */
        sliderLayoutReset: function() {
            var t = this;

            t.freeSpaces = [];

            t.sliderFreeSpaces = [];
        },
    });
})(jQuery, window, document);

/*!
 * jScroll - jQuery Plugin for Infinite Scrolling / Auto-Paging
 * http://jscroll.com/
 *
 * Copyright 2011-2013, Philip Klauzinski
 * http://klauzinski.com/
 * Dual licensed under the MIT and GPL Version 2 licenses.
 * http://jscroll.com/#license
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @author Philip Klauzinski
 * @version 2.3.4
 * @requires jQuery v1.4.3+
 * @preserve
 */
(function($) {

    'use strict';

    // Define the jscroll namespace and default settings
    $.jscroll = {
        defaults: {
            debug: false,
            autoTrigger: true,
            autoTriggerUntil: false,
            loadingHtml: '<small>Loading...</small>',
            padding: 0,
            nextSelector: 'a:last',
            contentSelector: '',
            pagingSelector: '',
            callback: false,
            force_load: false
        }
    };

    // Constructor
    var jScroll = function($e, options) {

        // Private vars and methods
        var _data = $e.data('jscroll'),
            _userOptions = (typeof options === 'function') ? { callback: options } : options,
            _options = $.extend({}, $.jscroll.defaults, _userOptions, _data || {}),
            _isWindow = ($e.css('overflow-y') === 'visible'),
            _$next = $e.find(_options.nextSelector).first(),
            _$window = $(window),
            _$body = $('body'),
            _$scroll = _isWindow ? _$window : $e,
            _nextHref = $.trim(_$next.attr('href') + ' ' + _options.contentSelector),

            // Check if a loading image is defined and preload
            _preloadImage = function() {
                var src = $(_options.loadingHtml).filter('img').attr('src');
                if (src) {
                    var image = new Image();
                    image.src = src;
                }
            },

            // Wrap inner content, if it isn't already
            _wrapInnerContent = function() {
                if (!$e.find('.jscroll-inner').length) {
                    $e.contents().wrapAll('<div class="jscroll-inner" />');
                }
            },

            // Find the next link's parent, or add one, and hide it
            _nextWrap = function($next) {
                var $parent;
                if (_options.pagingSelector) {
                    $next.closest(_options.pagingSelector).hide();
                } else {
                    $parent = $next.parent().not('.jscroll-inner,.jscroll-added').addClass('jscroll-next-parent').hide();
                    if (!$parent.length) {
                        $next.wrap('<div class="jscroll-next-parent" />').parent().hide();
                    }
                }
            },

            // Remove the jscroll behavior and data from an element
            _destroy = function() {
                return _$scroll.unbind('.jscroll')
                    .removeData('jscroll')
                    .find('.jscroll-inner').children().unwrap()
                    .filter('.jscroll-added').children().unwrap();
            },

            // Observe the scroll event for when to trigger the next load
            _observe = function() {
                if ($e.find('.dx-disable-scroll').length) {                    
                    return;
                }
               
                _wrapInnerContent();
                
                var $inner = $e.find('div.jscroll-inner').first(),
                    data = $e.data('jscroll'),
                    borderTopWidth = parseInt($e.css('borderTopWidth'), 10),
                    borderTopWidthInt = isNaN(borderTopWidth) ? 0 : borderTopWidth,
                    iContainerTop = parseInt($e.css('paddingTop'), 10) + borderTopWidthInt,
                    iTopHeight = _isWindow ? _$scroll.scrollTop() : $e.offset().top,
                    innerTop = $inner.length ? $inner.offset().top : 0,
                    iTotalHeight = Math.ceil(iTopHeight - innerTop + _$scroll.height() + iContainerTop);

                if ((!data.waiting && iTotalHeight + _options.padding >= $inner.outerHeight()) || _options.force_load) {
                    //data.nextHref = $.trim(data.nextHref + ' ' + _options.contentSelector);
                    _debug('info', 'jScroll:', $inner.outerHeight() - iTotalHeight, 'from bottom. Loading next request...');
                    _options.force_load = false;
                    
                    return _load();
                }
            },

            // Check if the href for the next set of content has been set
            _checkNextHref = function(data) {
                data = data || $e.data('jscroll');
                if (!data || !data.nextHref) {
                    _debug('warn', 'jScroll: nextSelector not found - destroying');
                    _destroy();
                    return false;
                } else {
                    _setBindings();
                    return true;
                }
            },

            _setBindings = function() {
                var $next = $e.find(_options.nextSelector).first();
                if (!$next.length) {
                    return;
                }
                if (_options.autoTrigger && (_options.autoTriggerUntil === false || _options.autoTriggerUntil > 0)) {
                    _nextWrap($next);
                    if (_$body.height() <= _$window.height()) {
                        _observe();
                    }
                    _$scroll.unbind('.jscroll').bind('scroll.jscroll', function() {
                        return _observe();
                    });
                    if (_options.autoTriggerUntil > 0) {
                        _options.autoTriggerUntil--;
                    }
                } else {
                    _$scroll.unbind('.jscroll');
                    $next.bind('click.jscroll', function() {
                        _nextWrap($next);
                        _load();
                        return false;
                    });
                }
            },

            // Load the next set of content, if available
            _load = function() {
                var $inner = $e.find('div.jscroll-inner').first(),
                    data = $e.data('jscroll');

                data.waiting = true;
                $inner.append('<div class="jscroll-added" />')
                    .children('.jscroll-added').last()
                    .html('<div class="jscroll-loading">' + _options.loadingHtml + '</div>');

                return $e.animate({scrollTop: $inner.outerHeight()}, 0, function() {
                    $inner.find('div.jscroll-added').last().load(data.nextHref, function(r, status) {
                        if (status === 'error') {
                            // added/changed by Janis Supe - to handle relogin
                            $inner.find('div.jscroll-added').last().remove();
                            $(".pager").last().remove(); // to reset paging
                            if (_options.callback) {
                                _options.callback.call(this);
                            }
                            return;
                        }
                        var $next = $(this).find(_options.nextSelector).first();
                        data.waiting = false;
                        data.nextHref = $next.attr('href') ? $.trim($next.attr('href') + ' ' + _options.contentSelector) : false;
                        $('.jscroll-next-parent', $e).remove(); // Remove the previous next link now that we have a new one
                        _checkNextHref();
                        if (_options.callback) {
                            _options.callback.call(this);
                        }
                        _debug('dir', data);
                    });
                });
            },

            // Safe console debug - http://klauzinski.com/javascript/safe-firebug-console-in-javascript
            _debug = function(m) {
                if (_options.debug && typeof console === 'object' && (typeof m === 'object' || typeof console[m] === 'function')) {
                    if (typeof m === 'object') {
                        var args = [];
                        for (var sMethod in m) {
                            if (typeof console[sMethod] === 'function') {
                                args = (m[sMethod].length) ? m[sMethod] : [m[sMethod]];
                                console[sMethod].apply(console, args);
                            } else {
                                console.log.apply(console, args);
                            }
                        }
                    } else {
                        console[m].apply(console, Array.prototype.slice.call(arguments, 1));
                    }
                }
            };

        // Initialization
        $e.data('jscroll', $.extend({}, _data, {initialized: true, waiting: false, nextHref: _nextHref}));
        _wrapInnerContent();
        _preloadImage();
        _setBindings();
        
        if ( _options.force_load)
        {
            _observe();
        }
        
        // Expose API methods via the jQuery.jscroll namespace, e.g. $('sel').jscroll.method()
        $.extend($e.jscroll, {
            destroy: _destroy
        });
        return $e;
    };

    // Define the jscroll plugin method and loop
    $.fn.jscroll = function(m) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('jscroll'), jscroll;

            // Instantiate jScroll on this element if it hasn't been already
            /*
            if (data && data.initialized) {
                
                return;
            }
            */
            jscroll = new jScroll($this, m);
        });
    };

})(jQuery);
/**
* @version: 2.1.13
* @author: Dan Grossman http://www.dangrossman.info/
* @copyright: Copyright (c) 2012-2015 Dan Grossman. All rights reserved.
* @license: Licensed under the MIT license. See http://www.opensource.org/licenses/mit-license.php
* @website: https://www.improvely.com/
*/

(function(root, factory) {

  if (typeof define === 'function' && define.amd) {
    define(['moment', 'jquery', 'exports'], function(momentjs, $, exports) {
      root.daterangepicker = factory(root, exports, momentjs, $);
    });

  } else if (typeof exports !== 'undefined') {
      var momentjs = require('moment');
      var jQuery = (typeof window != 'undefined') ? window.jQuery : undefined;  //isomorphic issue
      if (!jQuery) {
          try {
              jQuery = require('jquery');
              if (!jQuery.fn) jQuery.fn = {}; //isomorphic issue
          } catch (err) {
              if (!jQuery) throw new Error('jQuery dependency not found');
          }
      }

    factory(root, exports, momentjs, jQuery);

  // Finally, as a browser global.
  } else {
    root.daterangepicker = factory(root, {}, root.moment || moment, (root.jQuery || root.Zepto || root.ender || root.$));
  }

}(this || {}, function(root, daterangepicker, moment, $) { // 'this' doesn't exist on a server

    var DateRangePicker = function(element, options, cb) {

        //default settings for options
        this.parentEl = 'body';
        this.element = $(element);
        this.startDate = moment().startOf('day');
        this.endDate = moment().endOf('day');
        this.minDate = false;
        this.maxDate = false;
        this.dateLimit = false;
        this.autoApply = false;
        this.singleDatePicker = false;
        this.showDropdowns = false;
        this.showWeekNumbers = false;
        this.timePicker = false;
        this.timePicker24Hour = false;
        this.timePickerIncrement = 1;
        this.timePickerSeconds = false;
        this.linkedCalendars = true;
        this.autoUpdateInput = true;
        this.ranges = {};

        this.opens = 'right';
        if (this.element.hasClass('pull-right'))
            this.opens = 'left';

        this.drops = 'down';
        if (this.element.hasClass('dropup'))
            this.drops = 'up';

        this.buttonClasses = 'btn btn-sm';
        this.applyClass = 'btn-success';
        this.cancelClass = 'btn-default';

        this.locale = {
            format: 'MM/DD/YYYY',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            weekLabel: 'W',
            customRangeLabel: 'Custom Range',
            daysOfWeek: moment.weekdaysMin(),
            monthNames: moment.monthsShort(),
            firstDay: moment.localeData().firstDayOfWeek()
        };

        this.callback = function() { };

        //some state information
        this.isShowing = false;
        this.leftCalendar = {};
        this.rightCalendar = {};

        //custom options from user
        if (typeof options !== 'object' || options === null)
            options = {};

        //allow setting options with data attributes
        //data-api options will be overwritten with custom javascript options
        options = $.extend(this.element.data(), options);

        //html template for the picker UI
        if (typeof options.template !== 'string')
            options.template = '<div class="daterangepicker dropdown-menu">' +
                '<div class="calendar left">' +
                    '<div class="daterangepicker_input">' +
                      '<input class="input-mini" type="text" name="daterangepicker_start" value="" />' +
                      '<i class="fa fa-calendar"></i>' +
                      '<div class="calendar-time">' +
                        '<div></div>' +
                        '<i class="fa fa-clock-o"></i>' +
                      '</div>' +
                    '</div>' +
                    '<div class="calendar-table"></div>' +
                '</div>' +
                '<div class="calendar right">' +
                    '<div class="daterangepicker_input">' +
                      '<input class="input-mini" type="text" name="daterangepicker_end" value="" />' +
                      '<i class="fa fa-calendar"></i>' +
                      '<div class="calendar-time">' +
                        '<div></div>' +
                        '<i class="fa fa-clock-o"></i>' +
                      '</div>' +
                    '</div>' +
                    '<div class="calendar-table"></div>' +
                '</div>' +
                '<div class="ranges">' +
                    '<div class="range_inputs">' +
                        '<button class="applyBtn" disabled="disabled" type="button"></button> ' +
                        '<button class="cancelBtn" type="button"></button>' +
                    '</div>' +
                '</div>' +
            '</div>';

        this.parentEl = (options.parentEl && $(options.parentEl).length) ? $(options.parentEl) : $(this.parentEl);
        this.container = $(options.template).appendTo(this.parentEl);

        //
        // handle all the possible options overriding defaults
        //

        if (typeof options.locale === 'object') {

            if (typeof options.locale.format === 'string')
                this.locale.format = options.locale.format;

            if (typeof options.locale.separator === 'string')
                this.locale.separator = options.locale.separator;

            if (typeof options.locale.daysOfWeek === 'object')
                this.locale.daysOfWeek = options.locale.daysOfWeek.slice();

            if (typeof options.locale.monthNames === 'object')
              this.locale.monthNames = options.locale.monthNames.slice();

            if (typeof options.locale.firstDay === 'number')
              this.locale.firstDay = options.locale.firstDay;

            if (typeof options.locale.applyLabel === 'string')
              this.locale.applyLabel = options.locale.applyLabel;

            if (typeof options.locale.cancelLabel === 'string')
              this.locale.cancelLabel = options.locale.cancelLabel;

            if (typeof options.locale.weekLabel === 'string')
              this.locale.weekLabel = options.locale.weekLabel;

            if (typeof options.locale.customRangeLabel === 'string')
              this.locale.customRangeLabel = options.locale.customRangeLabel;

        }

        if (typeof options.startDate === 'string')
            this.startDate = moment(options.startDate, this.locale.format);

        if (typeof options.endDate === 'string')
            this.endDate = moment(options.endDate, this.locale.format);

        if (typeof options.minDate === 'string')
            this.minDate = moment(options.minDate, this.locale.format);

        if (typeof options.maxDate === 'string')
            this.maxDate = moment(options.maxDate, this.locale.format);

        if (typeof options.startDate === 'object')
            this.startDate = moment(options.startDate);

        if (typeof options.endDate === 'object')
            this.endDate = moment(options.endDate);

        if (typeof options.minDate === 'object')
            this.minDate = moment(options.minDate);

        if (typeof options.maxDate === 'object')
            this.maxDate = moment(options.maxDate);

        // sanity check for bad options
        if (this.minDate && this.startDate.isBefore(this.minDate))
            this.startDate = this.minDate.clone();

        // sanity check for bad options
        if (this.maxDate && this.endDate.isAfter(this.maxDate))
            this.endDate = this.maxDate.clone();

        if (typeof options.applyClass === 'string')
            this.applyClass = options.applyClass;

        if (typeof options.cancelClass === 'string')
            this.cancelClass = options.cancelClass;

        if (typeof options.dateLimit === 'object')
            this.dateLimit = options.dateLimit;

        if (typeof options.opens === 'string')
            this.opens = options.opens;

        if (typeof options.drops === 'string')
            this.drops = options.drops;

        if (typeof options.showWeekNumbers === 'boolean')
            this.showWeekNumbers = options.showWeekNumbers;

        if (typeof options.buttonClasses === 'string')
            this.buttonClasses = options.buttonClasses;

        if (typeof options.buttonClasses === 'object')
            this.buttonClasses = options.buttonClasses.join(' ');

        if (typeof options.showDropdowns === 'boolean')
            this.showDropdowns = options.showDropdowns;

        if (typeof options.singleDatePicker === 'boolean') {
            this.singleDatePicker = options.singleDatePicker;
            if (this.singleDatePicker)
                this.endDate = this.startDate.clone();
        }

        if (typeof options.timePicker === 'boolean')
            this.timePicker = options.timePicker;

        if (typeof options.timePickerSeconds === 'boolean')
            this.timePickerSeconds = options.timePickerSeconds;

        if (typeof options.timePickerIncrement === 'number')
            this.timePickerIncrement = options.timePickerIncrement;

        if (typeof options.timePicker24Hour === 'boolean')
            this.timePicker24Hour = options.timePicker24Hour;

        if (typeof options.autoApply === 'boolean')
            this.autoApply = options.autoApply;

        if (typeof options.autoUpdateInput === 'boolean')
            this.autoUpdateInput = options.autoUpdateInput;

        if (typeof options.linkedCalendars === 'boolean')
            this.linkedCalendars = options.linkedCalendars;

        if (typeof options.isInvalidDate === 'function')
            this.isInvalidDate = options.isInvalidDate;

        // update day names order to firstDay
        if (this.locale.firstDay != 0) {
            var iterator = this.locale.firstDay;
            while (iterator > 0) {
                this.locale.daysOfWeek.push(this.locale.daysOfWeek.shift());
                iterator--;
            }
        }

        var start, end, range;

        //if no start/end dates set, check if an input element contains initial values
        if (typeof options.startDate === 'undefined' && typeof options.endDate === 'undefined') {
            if ($(this.element).is('input[type=text]')) {
                var val = $(this.element).val(),
                    split = val.split(this.locale.separator);

                start = end = null;

                if (split.length == 2) {
                    start = moment(split[0], this.locale.format);
                    end = moment(split[1], this.locale.format);
                } else if (this.singleDatePicker && val !== "") {
                    start = moment(val, this.locale.format);
                    end = moment(val, this.locale.format);
                }
                if (start !== null && end !== null) {
                    this.setStartDate(start);
                    this.setEndDate(end);
                }
            }
        }

        if (typeof options.ranges === 'object') {
            for (range in options.ranges) {

                if (typeof options.ranges[range][0] === 'string')
                    start = moment(options.ranges[range][0], this.locale.format);
                else
                    start = moment(options.ranges[range][0]);

                if (typeof options.ranges[range][1] === 'string')
                    end = moment(options.ranges[range][1], this.locale.format);
                else
                    end = moment(options.ranges[range][1]);

                // If the start or end date exceed those allowed by the minDate or dateLimit
                // options, shorten the range to the allowable period.
                if (this.minDate && start.isBefore(this.minDate))
                    start = this.minDate.clone();

                var maxDate = this.maxDate;
                if (this.dateLimit && start.clone().add(this.dateLimit).isAfter(maxDate))
                    maxDate = start.clone().add(this.dateLimit);
                if (maxDate && end.isAfter(maxDate))
                    end = maxDate.clone();

                // If the end of the range is before the minimum or the start of the range is
                // after the maximum, don't display this range option at all.
                if ((this.minDate && end.isBefore(this.minDate)) || (maxDate && start.isAfter(maxDate)))
                    continue;
                
                //Support unicode chars in the range names.
                var elem = document.createElement('textarea');
                elem.innerHTML = range;
                rangeHtml = elem.value;

                this.ranges[rangeHtml] = [start, end];
            }

            var list = '<ul>';
            for (range in this.ranges) {
                list += '<li>' + range + '</li>';
            }
            list += '<li>' + this.locale.customRangeLabel + '</li>';
            list += '</ul>';
            this.container.find('.ranges').prepend(list);
        }

        if (typeof cb === 'function') {
            this.callback = cb;
        }

        if (!this.timePicker) {
            this.startDate = this.startDate.startOf('day');
            this.endDate = this.endDate.endOf('day');
            this.container.find('.calendar-time').hide();
        }

        //can't be used together for now
        if (this.timePicker && this.autoApply)
            this.autoApply = false;

        if (this.autoApply && typeof options.ranges !== 'object') {
            this.container.find('.ranges').hide();
        } else if (this.autoApply) {
            this.container.find('.applyBtn, .cancelBtn').addClass('hide');
        }

        if (this.singleDatePicker) {
            this.container.addClass('single');
            this.container.find('.calendar.left').addClass('single');
            this.container.find('.calendar.left').show();
            this.container.find('.calendar.right').hide();
            this.container.find('.daterangepicker_input input, .daterangepicker_input i').hide();
            if (!this.timePicker) {
                this.container.find('.ranges').hide();
            }
        }

        if (typeof options.ranges === 'undefined' && !this.singleDatePicker) {
            this.container.addClass('show-calendar');
        }

        this.container.addClass('opens' + this.opens);

        //swap the position of the predefined ranges if opens right
        if (typeof options.ranges !== 'undefined' && this.opens == 'right') {
            var ranges = this.container.find('.ranges');
            var html = ranges.clone();
            ranges.remove();
            this.container.find('.calendar.left').parent().prepend(html);
        }

        //apply CSS classes and labels to buttons
        this.container.find('.applyBtn, .cancelBtn').addClass(this.buttonClasses);
        if (this.applyClass.length)
            this.container.find('.applyBtn').addClass(this.applyClass);
        if (this.cancelClass.length)
            this.container.find('.cancelBtn').addClass(this.cancelClass);
        this.container.find('.applyBtn').html(this.locale.applyLabel);
        this.container.find('.cancelBtn').html(this.locale.cancelLabel);

        //
        // event listeners
        //

        this.container.find('.calendar')
            .on('click.daterangepicker', '.prev', $.proxy(this.clickPrev, this))
            .on('click.daterangepicker', '.next', $.proxy(this.clickNext, this))
            .on('click.daterangepicker', 'td.available', $.proxy(this.clickDate, this))
            .on('mouseenter.daterangepicker', 'td.available', $.proxy(this.hoverDate, this))
            .on('mouseleave.daterangepicker', 'td.available', $.proxy(this.updateFormInputs, this))
            .on('change.daterangepicker', 'select.yearselect', $.proxy(this.monthOrYearChanged, this))
            .on('change.daterangepicker', 'select.monthselect', $.proxy(this.monthOrYearChanged, this))
            .on('change.daterangepicker', 'select.hourselect,select.minuteselect,select.secondselect,select.ampmselect', $.proxy(this.timeChanged, this))
            .on('click.daterangepicker', '.daterangepicker_input input', $.proxy(this.showCalendars, this))
            //.on('keyup.daterangepicker', '.daterangepicker_input input', $.proxy(this.formInputsChanged, this))
            .on('change.daterangepicker', '.daterangepicker_input input', $.proxy(this.formInputsChanged, this));

        this.container.find('.ranges')
            .on('click.daterangepicker', 'button.applyBtn', $.proxy(this.clickApply, this))
            .on('click.daterangepicker', 'button.cancelBtn', $.proxy(this.clickCancel, this))
            .on('click.daterangepicker', 'li', $.proxy(this.clickRange, this))
            .on('mouseenter.daterangepicker', 'li', $.proxy(this.hoverRange, this))
            .on('mouseleave.daterangepicker', 'li', $.proxy(this.updateFormInputs, this));

        if (this.element.is('input')) {
            this.element.on({
                'click.daterangepicker': $.proxy(this.show, this),
                'focus.daterangepicker': $.proxy(this.show, this),
                'keyup.daterangepicker': $.proxy(this.elementChanged, this),
                'keydown.daterangepicker': $.proxy(this.keydown, this)
            });
        } else {
            this.element.on('click.daterangepicker', $.proxy(this.toggle, this));
        }

        //
        // if attached to a text input, set the initial value
        //

        if (this.element.is('input') && !this.singleDatePicker && this.autoUpdateInput) {
            this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
            this.element.trigger('change');
        } else if (this.element.is('input') && this.autoUpdateInput) {
            this.element.val(this.startDate.format(this.locale.format));
            this.element.trigger('change');
        }

    };

    DateRangePicker.prototype = {

        constructor: DateRangePicker,

        setStartDate: function(startDate) {
            if (typeof startDate === 'string')
                this.startDate = moment(startDate, this.locale.format);

            if (typeof startDate === 'object')
                this.startDate = moment(startDate);

            if (!this.timePicker)
                this.startDate = this.startDate.startOf('day');

            if (this.timePicker && this.timePickerIncrement)
                this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);

            if (this.minDate && this.startDate.isBefore(this.minDate))
                this.startDate = this.minDate;

            if (this.maxDate && this.startDate.isAfter(this.maxDate))
                this.startDate = this.maxDate;

            if (!this.isShowing)
                this.updateElement();

            this.updateMonthsInView();
        },

        setEndDate: function(endDate) {
            if (typeof endDate === 'string')
                this.endDate = moment(endDate, this.locale.format);

            if (typeof endDate === 'object')
                this.endDate = moment(endDate);

            if (!this.timePicker)
                this.endDate = this.endDate.endOf('day');

            if (this.timePicker && this.timePickerIncrement)
                this.endDate.minute(Math.round(this.endDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);

            if (this.endDate.isBefore(this.startDate))
                this.endDate = this.startDate.clone();

            if (this.maxDate && this.endDate.isAfter(this.maxDate))
                this.endDate = this.maxDate;

            if (this.dateLimit && this.startDate.clone().add(this.dateLimit).isBefore(this.endDate))
                this.endDate = this.startDate.clone().add(this.dateLimit);

            if (!this.isShowing)
                this.updateElement();

            this.updateMonthsInView();
        },

        isInvalidDate: function() {
            return false;
        },

        updateView: function() {
            if (this.timePicker) {
                this.renderTimePicker('left');
                this.renderTimePicker('right');
                if (!this.endDate) {
                    this.container.find('.right .calendar-time select').attr('disabled', 'disabled').addClass('disabled');
                } else {
                    this.container.find('.right .calendar-time select').removeAttr('disabled').removeClass('disabled');
                }
            }
            if (this.endDate) {
                this.container.find('input[name="daterangepicker_end"]').removeClass('active');
                this.container.find('input[name="daterangepicker_start"]').addClass('active');
            } else {
                this.container.find('input[name="daterangepicker_end"]').addClass('active');
                this.container.find('input[name="daterangepicker_start"]').removeClass('active');
            }
            this.updateMonthsInView();
            this.updateCalendars();
            this.updateFormInputs();
        },

        updateMonthsInView: function() {
            if (this.endDate) {

                //if both dates are visible already, do nothing
                if (!this.singleDatePicker && this.leftCalendar.month && this.rightCalendar.month &&
                    (this.startDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.startDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM'))
                    &&
                    (this.endDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.endDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM'))
                    ) {
                    return;
                }

                this.leftCalendar.month = this.startDate.clone().date(2);
                if (!this.linkedCalendars && (this.endDate.month() != this.startDate.month() || this.endDate.year() != this.startDate.year())) {
                    this.rightCalendar.month = this.endDate.clone().date(2);
                } else {
                    this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
                }
                
            } else {
                if (this.leftCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM') && this.rightCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM')) {
                    this.leftCalendar.month = this.startDate.clone().date(2);
                    this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
                }
            }
        },

        updateCalendars: function() {

            if (this.timePicker) {
                var hour, minute, second;
                if (this.endDate) {
                    hour = parseInt(this.container.find('.left .hourselect').val(), 10);
                    minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
                    second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.left .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                } else {
                    hour = parseInt(this.container.find('.right .hourselect').val(), 10);
                    minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
                    second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.right .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                }
                this.leftCalendar.month.hour(hour).minute(minute).second(second);
                this.rightCalendar.month.hour(hour).minute(minute).second(second);
            }

            this.renderCalendar('left');
            this.renderCalendar('right');

            //highlight any predefined range matching the current start and end dates
            this.container.find('.ranges li').removeClass('active');
            if (this.endDate == null) return;

            var customRange = true;
            var i = 0;
            for (var range in this.ranges) {
                if (this.timePicker) {
                    if (this.startDate.isSame(this.ranges[range][0]) && this.endDate.isSame(this.ranges[range][1])) {
                        customRange = false;
                        this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').html();
                        break;
                    }
                } else {
                    //ignore times when comparing dates if time picker is not enabled
                    if (this.startDate.format('YYYY-MM-DD') == this.ranges[range][0].format('YYYY-MM-DD') && this.endDate.format('YYYY-MM-DD') == this.ranges[range][1].format('YYYY-MM-DD')) {
                        customRange = false;
                        this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').html();
                        break;
                    }
                }
                i++;
            }
            if (customRange) {
                this.chosenLabel = this.container.find('.ranges li:last').addClass('active').html();
                this.showCalendars();
            }

        },

        renderCalendar: function(side) {

            //
            // Build the matrix of dates that will populate the calendar
            //

            var calendar = side == 'left' ? this.leftCalendar : this.rightCalendar;
            var month = calendar.month.month();
            var year = calendar.month.year();
            var hour = calendar.month.hour();
            var minute = calendar.month.minute();
            var second = calendar.month.second();
            var daysInMonth = moment([year, month]).daysInMonth();
            var firstDay = moment([year, month, 1]);
            var lastDay = moment([year, month, daysInMonth]);
            var lastMonth = moment(firstDay).subtract(1, 'month').month();
            var lastYear = moment(firstDay).subtract(1, 'month').year();
            var daysInLastMonth = moment([lastYear, lastMonth]).daysInMonth();
            var dayOfWeek = firstDay.day();

            //initialize a 6 rows x 7 columns array for the calendar
            var calendar = [];
            calendar.firstDay = firstDay;
            calendar.lastDay = lastDay;

            for (var i = 0; i < 6; i++) {
                calendar[i] = [];
            }

            //populate the calendar with date objects
            var startDay = daysInLastMonth - dayOfWeek + this.locale.firstDay + 1;
            if (startDay > daysInLastMonth)
                startDay -= 7;

            if (dayOfWeek == this.locale.firstDay)
                startDay = daysInLastMonth - 6;

            var curDate = moment([lastYear, lastMonth, startDay, 12, minute, second]);

            var col, row;
            for (var i = 0, col = 0, row = 0; i < 42; i++, col++, curDate = moment(curDate).add(24, 'hour')) {
                if (i > 0 && col % 7 === 0) {
                    col = 0;
                    row++;
                }
                calendar[row][col] = curDate.clone().hour(hour).minute(minute).second(second);
                curDate.hour(12);

                if (this.minDate && calendar[row][col].format('YYYY-MM-DD') == this.minDate.format('YYYY-MM-DD') && calendar[row][col].isBefore(this.minDate) && side == 'left') {
                    calendar[row][col] = this.minDate.clone();
                }

                if (this.maxDate && calendar[row][col].format('YYYY-MM-DD') == this.maxDate.format('YYYY-MM-DD') && calendar[row][col].isAfter(this.maxDate) && side == 'right') {
                    calendar[row][col] = this.maxDate.clone();
                }

            }

            //make the calendar object available to hoverDate/clickDate
            if (side == 'left') {
                this.leftCalendar.calendar = calendar;
            } else {
                this.rightCalendar.calendar = calendar;
            }

            //
            // Display the calendar
            //

            var minDate = side == 'left' ? this.minDate : this.startDate;
            var maxDate = this.maxDate;
            var selected = side == 'left' ? this.startDate : this.endDate;

            var html = '<table class="table-condensed">';
            html += '<thead>';
            html += '<tr>';

            // add empty cell for week number
            if (this.showWeekNumbers)
                html += '<th></th>';

            if ((!minDate || minDate.isBefore(calendar.firstDay)) && (!this.linkedCalendars || side == 'left')) {
                html += '<th class="prev available"><i class="fa fa-angle-left"></i></th>';
            } else {
                html += '<th></th>';
            }

            var dateHtml = this.locale.monthNames[calendar[1][1].month()] + calendar[1][1].format(" YYYY");

            if (this.showDropdowns) {
                var currentMonth = calendar[1][1].month();
                var currentYear = calendar[1][1].year();
                var maxYear = (maxDate && maxDate.year()) || (currentYear + 5);
                var minYear = (minDate && minDate.year()) || (currentYear - 50);
                var inMinYear = currentYear == minYear;
                var inMaxYear = currentYear == maxYear;

                var monthHtml = '<select class="monthselect">';
                for (var m = 0; m < 12; m++) {
                    if ((!inMinYear || m >= minDate.month()) && (!inMaxYear || m <= maxDate.month())) {
                        monthHtml += "<option value='" + m + "'" +
                            (m === currentMonth ? " selected='selected'" : "") +
                            ">" + this.locale.monthNames[m] + "</option>";
                    } else {
                        monthHtml += "<option value='" + m + "'" +
                            (m === currentMonth ? " selected='selected'" : "") +
                            " disabled='disabled'>" + this.locale.monthNames[m] + "</option>";
                    }
                }
                monthHtml += "</select>";

                var yearHtml = '<select class="yearselect">';
                for (var y = minYear; y <= maxYear; y++) {
                    yearHtml += '<option value="' + y + '"' +
                        (y === currentYear ? ' selected="selected"' : '') +
                        '>' + y + '</option>';
                }
                yearHtml += '</select>';

                dateHtml = monthHtml + yearHtml;
            }

            html += '<th colspan="5" class="month">' + dateHtml + '</th>';
            if ((!maxDate || maxDate.isAfter(calendar.lastDay)) && (!this.linkedCalendars || side == 'right' || this.singleDatePicker)) {
                html += '<th class="next available"><i class="fa fa-angle-right"></i></th>';
            } else {
                html += '<th></th>';
            }

            html += '</tr>';
            html += '<tr>';

            // add week number label
            if (this.showWeekNumbers)
                html += '<th class="week">' + this.locale.weekLabel + '</th>';

            $.each(this.locale.daysOfWeek, function(index, dayOfWeek) {
                html += '<th>' + dayOfWeek + '</th>';
            });

            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            //adjust maxDate to reflect the dateLimit setting in order to
            //grey out end dates beyond the dateLimit
            if (this.endDate == null && this.dateLimit) {
                var maxLimit = this.startDate.clone().add(this.dateLimit).endOf('day');
                if (!maxDate || maxLimit.isBefore(maxDate)) {
                    maxDate = maxLimit;
                }
            }

            for (var row = 0; row < 6; row++) {
                html += '<tr>';

                // add week number
                if (this.showWeekNumbers)
                    html += '<td class="week">' + calendar[row][0].week() + '</td>';

                for (var col = 0; col < 7; col++) {

                    var classes = [];

                    //highlight today's date
                    if (calendar[row][col].isSame(new Date(), "day"))
                        classes.push('today');

                    //highlight weekends
                    if (calendar[row][col].isoWeekday() > 5)
                        classes.push('weekend');

                    //grey out the dates in other months displayed at beginning and end of this calendar
                    if (calendar[row][col].month() != calendar[1][1].month())
                        classes.push('off');

                    //don't allow selection of dates before the minimum date
                    if (this.minDate && calendar[row][col].isBefore(this.minDate, 'day'))
                        classes.push('off', 'disabled');

                    //don't allow selection of dates after the maximum date
                    if (maxDate && calendar[row][col].isAfter(maxDate, 'day'))
                        classes.push('off', 'disabled');

                    //don't allow selection of date if a custom function decides it's invalid
                    if (this.isInvalidDate(calendar[row][col]))
                        classes.push('off', 'disabled');

                    //highlight the currently selected start date
                    if (calendar[row][col].format('YYYY-MM-DD') == this.startDate.format('YYYY-MM-DD'))
                        classes.push('active', 'start-date');

                    //highlight the currently selected end date
                    if (this.endDate != null && calendar[row][col].format('YYYY-MM-DD') == this.endDate.format('YYYY-MM-DD'))
                        classes.push('active', 'end-date');

                    //highlight dates in-between the selected dates
                    if (this.endDate != null && calendar[row][col] > this.startDate && calendar[row][col] < this.endDate)
                        classes.push('in-range');

                    var cname = '', disabled = false;
                    for (var i = 0; i < classes.length; i++) {
                        cname += classes[i] + ' ';
                        if (classes[i] == 'disabled')
                            disabled = true;
                    }
                    if (!disabled)
                        cname += 'available';

                    html += '<td class="' + cname.replace(/^\s+|\s+$/g, '') + '" data-title="' + 'r' + row + 'c' + col + '">' + calendar[row][col].date() + '</td>';

                }
                html += '</tr>';
            }

            html += '</tbody>';
            html += '</table>';

            this.container.find('.calendar.' + side + ' .calendar-table').html(html);

        },

        renderTimePicker: function(side) {

            var html, selected, minDate, maxDate = this.maxDate;

            if (this.dateLimit && (!this.maxDate || this.startDate.clone().add(this.dateLimit).isAfter(this.maxDate)))
                maxDate = this.startDate.clone().add(this.dateLimit);

            if (side == 'left') {
                selected = this.startDate.clone();
                minDate = this.minDate;
            } else if (side == 'right') {
                selected = this.endDate ? this.endDate.clone() : this.startDate.clone();
                minDate = this.startDate;
            }

            //
            // hours
            //

            html = '<select class="hourselect">';

            var start = this.timePicker24Hour ? 0 : 1;
            var end = this.timePicker24Hour ? 23 : 12;

            for (var i = start; i <= end; i++) {
                var i_in_24 = i;
                if (!this.timePicker24Hour)
                    i_in_24 = selected.hour() >= 12 ? (i == 12 ? 12 : i + 12) : (i == 12 ? 0 : i);

                var time = selected.clone().hour(i_in_24);
                var disabled = false;
                if (minDate && time.minute(59).isBefore(minDate))
                    disabled = true;
                if (maxDate && time.minute(0).isAfter(maxDate))
                    disabled = true;

                if (i_in_24 == selected.hour() && !disabled) {
                    html += '<option value="' + i + '" selected="selected">' + i + '</option>';
                } else if (disabled) {
                    html += '<option value="' + i + '" disabled="disabled" class="disabled">' + i + '</option>';
                } else {
                    html += '<option value="' + i + '">' + i + '</option>';
                }
            }

            html += '</select> ';

            //
            // minutes
            //

            html += ': <select class="minuteselect">';

            for (var i = 0; i < 60; i += this.timePickerIncrement) {
                var padded = i < 10 ? '0' + i : i;
                var time = selected.clone().minute(i);

                var disabled = false;
                if (minDate && time.second(59).isBefore(minDate))
                    disabled = true;
                if (maxDate && time.second(0).isAfter(maxDate))
                    disabled = true;

                if (selected.minute() == i && !disabled) {
                    html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
                } else if (disabled) {
                    html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
                } else {
                    html += '<option value="' + i + '">' + padded + '</option>';
                }
            }

            html += '</select> ';

            //
            // seconds
            //

            if (this.timePickerSeconds) {
                html += ': <select class="secondselect">';

                for (var i = 0; i < 60; i++) {
                    var padded = i < 10 ? '0' + i : i;
                    var time = selected.clone().second(i);

                    var disabled = false;
                    if (minDate && time.isBefore(minDate))
                        disabled = true;
                    if (maxDate && time.isAfter(maxDate))
                        disabled = true;

                    if (selected.second() == i && !disabled) {
                        html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
                    } else if (disabled) {
                        html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
                    } else {
                        html += '<option value="' + i + '">' + padded + '</option>';
                    }
                }

                html += '</select> ';
            }

            //
            // AM/PM
            //

            if (!this.timePicker24Hour) {
                html += '<select class="ampmselect">';

                var am_html = '';
                var pm_html = '';

                if (minDate && selected.clone().hour(12).minute(0).second(0).isBefore(minDate))
                    am_html = ' disabled="disabled" class="disabled"';

                if (maxDate && selected.clone().hour(0).minute(0).second(0).isAfter(maxDate))
                    pm_html = ' disabled="disabled" class="disabled"';

                if (selected.hour() >= 12) {
                    html += '<option value="AM"' + am_html + '>AM</option><option value="PM" selected="selected"' + pm_html + '>PM</option>';
                } else {
                    html += '<option value="AM" selected="selected"' + am_html + '>AM</option><option value="PM"' + pm_html + '>PM</option>';
                }

                html += '</select>';
            }

            this.container.find('.calendar.' + side + ' .calendar-time div').html(html);

        },

        updateFormInputs: function() {

            //ignore mouse movements while an above-calendar text input has focus
            if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
                return;

            this.container.find('input[name=daterangepicker_start]').val(this.startDate.format(this.locale.format));
            if (this.endDate)
                this.container.find('input[name=daterangepicker_end]').val(this.endDate.format(this.locale.format));

            if (this.singleDatePicker || (this.endDate && (this.startDate.isBefore(this.endDate) || this.startDate.isSame(this.endDate)))) {
                this.container.find('button.applyBtn').removeAttr('disabled');
            } else {
                this.container.find('button.applyBtn').attr('disabled', 'disabled');
            }

        },

        move: function() {
            var parentOffset = { top: 0, left: 0 },
                containerTop;
            var parentRightEdge = $(window).width();
            if (!this.parentEl.is('body')) {
                parentOffset = {
                    top: this.parentEl.offset().top - this.parentEl.scrollTop(),
                    left: this.parentEl.offset().left - this.parentEl.scrollLeft()
                };
                parentRightEdge = this.parentEl[0].clientWidth + this.parentEl.offset().left;
            }

            if (this.drops == 'up')
                containerTop = this.element.offset().top - this.container.outerHeight() - parentOffset.top;
            else
                containerTop = this.element.offset().top + this.element.outerHeight() - parentOffset.top;
            this.container[this.drops == 'up' ? 'addClass' : 'removeClass']('dropup');

            if (this.opens == 'left') {
                this.container.css({
                    top: containerTop,
                    right: parentRightEdge - this.element.offset().left - this.element.outerWidth(),
                    left: 'auto'
                });
                if (this.container.offset().left < 0) {
                    this.container.css({
                        right: 'auto',
                        left: 9
                    });
                }
            } else if (this.opens == 'center') {
                this.container.css({
                    top: containerTop,
                    left: this.element.offset().left - parentOffset.left + this.element.outerWidth() / 2
                            - this.container.outerWidth() / 2,
                    right: 'auto'
                });
                if (this.container.offset().left < 0) {
                    this.container.css({
                        right: 'auto',
                        left: 9
                    });
                }
            } else {
                this.container.css({
                    top: containerTop,
                    left: this.element.offset().left - parentOffset.left,
                    right: 'auto'
                });
                if (this.container.offset().left + this.container.outerWidth() > $(window).width()) {
                    this.container.css({
                        left: 'auto',
                        right: 0
                    });
                }
            }
        },

        show: function(e) {
            if (this.isShowing) return;

            // Create a click proxy that is private to this instance of datepicker, for unbinding
            this._outsideClickProxy = $.proxy(function(e) { this.outsideClick(e); }, this);

            // Bind global datepicker mousedown for hiding and
            $(document)
              .on('mousedown.daterangepicker', this._outsideClickProxy)
              // also support mobile devices
              .on('touchend.daterangepicker', this._outsideClickProxy)
              // also explicitly play nice with Bootstrap dropdowns, which stopPropagation when clicking them
              .on('click.daterangepicker', '[data-toggle=dropdown]', this._outsideClickProxy)
              // and also close when focus changes to outside the picker (eg. tabbing between controls)
              .on('focusin.daterangepicker', this._outsideClickProxy);

            // Reposition the picker if the window is resized while it's open
            $(window).on('resize.daterangepicker', $.proxy(function(e) { this.move(e); }, this));

            this.oldStartDate = this.startDate.clone();
            this.oldEndDate = this.endDate.clone();

            this.updateView();
            this.container.show();
            this.move();
            this.element.trigger('show.daterangepicker', this);
            this.isShowing = true;
        },

        hide: function(e) {
            if (!this.isShowing) return;

            //incomplete date selection, revert to last values
            if (!this.endDate) {
                this.startDate = this.oldStartDate.clone();
                this.endDate = this.oldEndDate.clone();
            }

            //if a new date range was selected, invoke the user callback function
            if (!this.startDate.isSame(this.oldStartDate) || !this.endDate.isSame(this.oldEndDate))
                this.callback(this.startDate, this.endDate, this.chosenLabel);

            //if picker is attached to a text input, update it
            this.updateElement();

            $(document).off('.daterangepicker');
            $(window).off('.daterangepicker');
            this.container.hide();
            this.element.trigger('hide.daterangepicker', this);
            this.isShowing = false;
        },

        toggle: function(e) {
            if (this.isShowing) {
                this.hide();
            } else {
                this.show();
            }
        },

        outsideClick: function(e) {
            var target = $(e.target);
            // if the page is clicked anywhere except within the daterangerpicker/button
            // itself then call this.hide()
            if (
                // ie modal dialog fix
                e.type == "focusin" ||
                target.closest(this.element).length ||
                target.closest(this.container).length ||
                target.closest('.calendar-table').length
                ) return;
            this.hide();
        },

        showCalendars: function() {
            this.container.addClass('show-calendar');
            this.move();
            this.element.trigger('showCalendar.daterangepicker', this);
        },

        hideCalendars: function() {
            this.container.removeClass('show-calendar');
            this.element.trigger('hideCalendar.daterangepicker', this);
        },

        hoverRange: function(e) {

            //ignore mouse movements while an above-calendar text input has focus
            if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
                return;

            var label = e.target.innerHTML;
            if (label == this.locale.customRangeLabel) {
                this.updateView();
            } else {
                var dates = this.ranges[label];
                this.container.find('input[name=daterangepicker_start]').val(dates[0].format(this.locale.format));
                this.container.find('input[name=daterangepicker_end]').val(dates[1].format(this.locale.format));
            }
            
        },

        clickRange: function(e) {
            var label = e.target.innerHTML;
            this.chosenLabel = label;
            if (label == this.locale.customRangeLabel) {
                this.showCalendars();
            } else {
                var dates = this.ranges[label];
                this.startDate = dates[0];
                this.endDate = dates[1];

                if (!this.timePicker) {
                    this.startDate.startOf('day');
                    this.endDate.endOf('day');
                }

                this.hideCalendars();
                this.clickApply();
            }
        },

        clickPrev: function(e) {
            var cal = $(e.target).parents('.calendar');
            if (cal.hasClass('left')) {
                this.leftCalendar.month.subtract(1, 'month');
                if (this.linkedCalendars)
                    this.rightCalendar.month.subtract(1, 'month');
            } else {
                this.rightCalendar.month.subtract(1, 'month');
            }
            this.updateCalendars();
        },

        clickNext: function(e) {
            var cal = $(e.target).parents('.calendar');
            if (cal.hasClass('left')) {
                this.leftCalendar.month.add(1, 'month');
            } else {
                this.rightCalendar.month.add(1, 'month');
                if (this.linkedCalendars)
                    this.leftCalendar.month.add(1, 'month');
            }
            this.updateCalendars();
        },

        hoverDate: function(e) {

            //ignore mouse movements while an above-calendar text input has focus
            if (this.container.find('input[name=daterangepicker_start]').is(":focus") || this.container.find('input[name=daterangepicker_end]').is(":focus"))
                return;

            //ignore dates that can't be selected
            if (!$(e.target).hasClass('available')) return;

            //have the text inputs above calendars reflect the date being hovered over
            var title = $(e.target).attr('data-title');
            var row = title.substr(1, 1);
            var col = title.substr(3, 1);
            var cal = $(e.target).parents('.calendar');
            var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col];

            if (this.endDate) {
                this.container.find('input[name=daterangepicker_start]').val(date.format(this.locale.format));
            } else {
                this.container.find('input[name=daterangepicker_end]').val(date.format(this.locale.format));
            }

            //highlight the dates between the start date and the date being hovered as a potential end date
            var leftCalendar = this.leftCalendar;
            var rightCalendar = this.rightCalendar;
            var startDate = this.startDate;
            if (!this.endDate) {
                this.container.find('.calendar td').each(function(index, el) {

                    //skip week numbers, only look at dates
                    if ($(el).hasClass('week')) return;

                    var title = $(el).attr('data-title');
                    var row = title.substr(1, 1);
                    var col = title.substr(3, 1);
                    var cal = $(el).parents('.calendar');
                    var dt = cal.hasClass('left') ? leftCalendar.calendar[row][col] : rightCalendar.calendar[row][col];

                    if (dt.isAfter(startDate) && dt.isBefore(date)) {
                        $(el).addClass('in-range');
                    } else {
                        $(el).removeClass('in-range');
                    }

                });
            }

        },

        clickDate: function(e) {

            if (!$(e.target).hasClass('available')) return;

            var title = $(e.target).attr('data-title');
            var row = title.substr(1, 1);
            var col = title.substr(3, 1);
            var cal = $(e.target).parents('.calendar');
            var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col];

            //
            // this function needs to do a few things:
            // * alternate between selecting a start and end date for the range,
            // * if the time picker is enabled, apply the hour/minute/second from the select boxes to the clicked date
            // * if autoapply is enabled, and an end date was chosen, apply the selection
            // * if single date picker mode, and time picker isn't enabled, apply the selection immediately
            //

            if (this.endDate || date.isBefore(this.startDate)) {
                if (this.timePicker) {
                    var hour = parseInt(this.container.find('.left .hourselect').val(), 10);
                    if (!this.timePicker24Hour) {
                        var ampm = cal.find('.ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                    var minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
                    var second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;
                    date = date.clone().hour(hour).minute(minute).second(second);
                }
                this.endDate = null;
                this.setStartDate(date.clone());
            } else {
                if (this.timePicker) {
                    var hour = parseInt(this.container.find('.right .hourselect').val(), 10);
                    if (!this.timePicker24Hour) {
                        var ampm = this.container.find('.right .ampmselect').val();
                        if (ampm === 'PM' && hour < 12)
                            hour += 12;
                        if (ampm === 'AM' && hour === 12)
                            hour = 0;
                    }
                    var minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
                    var second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;
                    date = date.clone().hour(hour).minute(minute).second(second);
                }
                this.setEndDate(date.clone());
                if (this.autoApply)
                    this.clickApply();
            }

            if (this.singleDatePicker) {
                this.setEndDate(this.startDate);
                if (!this.timePicker)
                    this.clickApply();
            }

            this.updateView();

        },

        clickApply: function(e) {
            this.hide();
            this.element.trigger('apply.daterangepicker', this);
        },

        clickCancel: function(e) {
            this.startDate = this.oldStartDate;
            this.endDate = this.oldEndDate;
            this.hide();
            this.element.trigger('cancel.daterangepicker', this);
        },

        monthOrYearChanged: function(e) {
            var isLeft = $(e.target).closest('.calendar').hasClass('left'),
                leftOrRight = isLeft ? 'left' : 'right',
                cal = this.container.find('.calendar.'+leftOrRight);

            // Month must be Number for new moment versions
            var month = parseInt(cal.find('.monthselect').val(), 10);
            var year = cal.find('.yearselect').val();

            if (!isLeft) {
                if (year < this.startDate.year() || (year == this.startDate.year() && month < this.startDate.month())) {
                    month = this.startDate.month();
                    year = this.startDate.year();
                }
            }

            if (this.minDate) {
                if (year < this.minDate.year() || (year == this.minDate.year() && month < this.minDate.month())) {
                    month = this.minDate.month();
                    year = this.minDate.year();
                }
            }

            if (this.maxDate) {
                if (year > this.maxDate.year() || (year == this.maxDate.year() && month > this.maxDate.month())) {
                    month = this.maxDate.month();
                    year = this.maxDate.year();
                }
            }

            if (isLeft) {
                this.leftCalendar.month.month(month).year(year);
                if (this.linkedCalendars)
                    this.rightCalendar.month = this.leftCalendar.month.clone().add(1, 'month');
            } else {
                this.rightCalendar.month.month(month).year(year);
                if (this.linkedCalendars)
                    this.leftCalendar.month = this.rightCalendar.month.clone().subtract(1, 'month');
            }
            this.updateCalendars();
        },

        timeChanged: function(e) {

            var cal = $(e.target).closest('.calendar'),
                isLeft = cal.hasClass('left');

            var hour = parseInt(cal.find('.hourselect').val(), 10);
            var minute = parseInt(cal.find('.minuteselect').val(), 10);
            var second = this.timePickerSeconds ? parseInt(cal.find('.secondselect').val(), 10) : 0;

            if (!this.timePicker24Hour) {
                var ampm = cal.find('.ampmselect').val();
                if (ampm === 'PM' && hour < 12)
                    hour += 12;
                if (ampm === 'AM' && hour === 12)
                    hour = 0;
            }

            if (isLeft) {
                var start = this.startDate.clone();
                start.hour(hour);
                start.minute(minute);
                start.second(second);
                this.setStartDate(start);
                if (this.singleDatePicker) {
                    this.endDate = this.startDate.clone();
                } else if (this.endDate && this.endDate.format('YYYY-MM-DD') == start.format('YYYY-MM-DD') && this.endDate.isBefore(start)) {
                    this.setEndDate(start.clone());
                }
            } else if (this.endDate) {
                var end = this.endDate.clone();
                end.hour(hour);
                end.minute(minute);
                end.second(second);
                this.setEndDate(end);
            }

            //update the calendars so all clickable dates reflect the new time component
            this.updateCalendars();

            //update the form inputs above the calendars with the new time
            this.updateFormInputs();

            //re-render the time pickers because changing one selection can affect what's enabled in another
            this.renderTimePicker('left');
            this.renderTimePicker('right');

        },

        formInputsChanged: function(e) {
            var isRight = $(e.target).closest('.calendar').hasClass('right');
            var start = moment(this.container.find('input[name="daterangepicker_start"]').val(), this.locale.format);
            var end = moment(this.container.find('input[name="daterangepicker_end"]').val(), this.locale.format);

            if (start.isValid() && end.isValid()) {

                if (isRight && end.isBefore(start))
                    start = end.clone();

                this.setStartDate(start);
                this.setEndDate(end);

                if (isRight) {
                    this.container.find('input[name="daterangepicker_start"]').val(this.startDate.format(this.locale.format));
                } else {
                    this.container.find('input[name="daterangepicker_end"]').val(this.endDate.format(this.locale.format));
                }

            }

            this.updateCalendars();
            if (this.timePicker) {
                this.renderTimePicker('left');
                this.renderTimePicker('right');
            }
        },

        elementChanged: function() {
            if (!this.element.is('input')) return;
            if (!this.element.val().length) return;
            if (this.element.val().length < this.locale.format.length) return;

            var dateString = this.element.val().split(this.locale.separator),
                start = null,
                end = null;

            if (dateString.length === 2) {
                start = moment(dateString[0], this.locale.format);
                end = moment(dateString[1], this.locale.format);
            }

            if (this.singleDatePicker || start === null || end === null) {
                start = moment(this.element.val(), this.locale.format);
                end = start;
            }

            if (!start.isValid() || !end.isValid()) return;

            this.setStartDate(start);
            this.setEndDate(end);
            this.updateView();
        },

        keydown: function(e) {
            //hide on tab or enter
            if ((e.keyCode === 9) || (e.keyCode === 13)) {
                this.hide();
            }
        },

        updateElement: function() {
            if (this.element.is('input') && !this.singleDatePicker && this.autoUpdateInput) {
                this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                this.element.trigger('change');
            } else if (this.element.is('input') && this.autoUpdateInput) {
                this.element.val(this.startDate.format(this.locale.format));
                this.element.trigger('change');
            }
        },

        remove: function() {
            this.container.remove();
            this.element.off('.daterangepicker');
            this.element.removeData();
        }

    };

    $.fn.daterangepicker = function(options, callback) {
        this.each(function() {
            var el = $(this);
            if (el.data('daterangepicker'))
                el.data('daterangepicker').remove();
            el.data('daterangepicker', new DateRangePicker(el, options, callback));
        });
        return this;
    };

}));

/**
 * Datumu intervāla uzstādīšanas komponentes JavaScript funkcionalitāte
 * Lapā pieļaujama tikai 1 datumu intervāla uzstādīšanas komponente
 * 
 * @type _L4.Anonym$0|Function
 */
var DateRange = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     *  Masīvs ar mēneša nosaukumiem latviski
     * @type Array
     */
    var arr_month = [Lang.get('date_range.m_jan'), Lang.get('date_range.m_feb'), Lang.get('date_range.m_mar'), Lang.get('date_range.m_apr'), Lang.get('date_range.m_may'), Lang.get('date_range.m_jun'), Lang.get('date_range.m_jul'), Lang.get('date_range.m_aug'), Lang.get('date_range.m_sep'), Lang.get('date_range.m_oct'), Lang.get('date_range.m_nov'), Lang.get('date_range.m_dec')];
    
    /**
     * Masīvs ar komonentes uzstādījumiem
     * 
     * @type Array
     */
    var arr_params = [];
    
    /**
     * Funkcija, kas parāda vai paslēpj meklešanas rīku blokā saiti "Notīrīt"
     * @type type
     */
    var clearLinkShowHide = null;
    
    /**
     * Formatē datumu no yyyy-mm-dd uz formātu dd.mm.yyyy
     * 
     * @param {string} dat Formatējamais datums
     * @returns {String} Datums formatēts dd.mm.yyyy
     */
    var getDateLatvian = function(dat) {
        
        if (dat.length == 0) {
            return '';
        }
        
        var arr_parts = dat.split('-');
        return arr_parts[2] + "." + arr_parts[1] + "." + arr_parts[0];
    };
    
    /**
     * Formatē datumu no yyyy-mm-dd uz formātu 24 Jūlijs, 2016
     * 
     * @param {String} dat Formatējamais datums
     * @returns {String} Datums formatēts 24 Jūlijs, 2016
     */
    var getDateLatvianLong = function(dat) {
        if (dat.length == 0) {
            return '';
        }
        
        var arr_parts = dat.split('-');
        
        return parseInt(arr_parts[2]) + " " + arr_month[parseInt(arr_parts[1])-1] + ", " + arr_parts[0];
    };
    
    /**
     * Izgūst datuma intervālu latviešu valodā, piemēram, 24 Jūlijs, 2016 - 28 Jūlijs, 2016
     * @param {String} start_date Sākuma datums formātā yyyy-mm-dd
     * @param {String} end_date Beigu datums formātā yyyy-mm-dd
     * @returns {String} Datuma intervāls
     */
    var getDateIntervalLV = function (start_date, end_date) {
        
        if (start_date.length == 0 || end_date.length == 0) {
            return '';
        }
        
        return getDateLatvianLong(start_date) + " - " + getDateLatvianLong(end_date);
        
    }
    
    /**
     * Uzstāda datuma intervāla izveles funkcionalitāti
     * 
     * @returns {undefined}
     */
    var handleDateRangePickers = function () {
        if (!jQuery().daterangepicker) {
            return;
        }
        
        $('#' + arr_params['range_id']).daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                locale: {
                    "format": "DD.MM.YYYY",
                    "separator": " - ",
                    "applyLabel": Lang.get('date_range.btn_set'),
                    "cancelLabel": Lang.get('date_range.btn_cancel'),
                    "fromLabel": Lang.get('date_range.lbl_from'),
                    "toLabel": Lang.get('date_range.lbl_to'),
                    "customRangeLabel": Lang.get('date_range.lbl_interval'),
                    "daysOfWeek": [
                        Lang.get('date_range.d_7'),
                        Lang.get('date_range.d_1'),
                        Lang.get('date_range.d_2'),
                        Lang.get('date_range.d_3'),
                        Lang.get('date_range.d_4'),
                        Lang.get('date_range.d_5'),
                        Lang.get('date_range.d_6')
                    ],
                    "monthNames": arr_month,
                    "firstDay": 1
                },
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                ranges: arr_params['arr_ranges'],
                autoUpdateInput: true,
                alwaysShowCalendars: true,
                linkedCalendars: false
            },
            function (start, end) {                
                $('#' + arr_params['range_id'] + ' input').val(getDateIntervalLV(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')));
            }
        );
        
        $('#' + arr_params['range_id']).on('apply.daterangepicker', function(ev, picker) {
            form_elem.find('[name=' + arr_params['el_date_from'] + ']').val(picker.startDate.format('YYYY-MM-DD'));
            form_elem.find('[name=' + arr_params['el_date_to'] + ']').val(picker.endDate.format('YYYY-MM-DD'));
            clearLinkShowHide();
        });
        
        var start_date = form_elem.find('[name=' + arr_params['el_date_from'] + ']').val();
        var end_date = form_elem.find('[name=' + arr_params['el_date_to'] + ']').val();
        
        if (start_date && end_date)
        {
            $('#' + arr_params['range_id']).data('daterangepicker').setStartDate(getDateLatvian(start_date));
            $('#' + arr_params['range_id']).data('daterangepicker').setEndDate(getDateLatvian(end_date));
            $('#' + arr_params['range_id'] + ' input').val(getDateIntervalLV(start_date, end_date));
        }

    };
    
    /**
     * Inicializē komponentes JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initComponent = function(arr, clearLinkShowHide_callback) {               
        
        arr_params = arr;
        clearLinkShowHide = clearLinkShowHide_callback;
        
        var page_elem = $(arr_params["page_selector"]);
        form_elem = page_elem.find(arr_params["form_selector"]);
        
        handleDateRangePickers();
       
    };
    

    return {
        init: function(arr, clearLinkShowHide_callback) {
            initComponent(arr, clearLinkShowHide_callback);
        }
    };
}();
/**
 * Meklēšanas rīku pogas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var SearchTools = function()
{    
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Funkcija, kas parāda vai paslēp saiti "Notīrīt"
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearLinkShowHide = null;
    
    /**
     * Funkcija, kas notīra meklēšanas lauku vērtības.
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearFields = null;
    
    /**
     * Parāda vai paslēp meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var showHideTools = function() {

        var tools_btn = form_elem.find('.search-tools-btn');

        if (tools_btn.find('i').hasClass('fa-caret-down'))
        {
            tools_btn.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
            tools_btn.removeClass('btn-primary').addClass('btn-default');
            form_elem.removeClass('search-tools-hiden').addClass('search-tools-shown');
        }
        else
        {
            tools_btn.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
            tools_btn.removeClass('btn-default').addClass('btn-primary');

            form_elem.removeClass('search-tools-shown').addClass('search-tools-hiden');

            var phrase = form_elem.find('input[name=criteria]').val();
            
            clearFields();
            
            form_elem.find('input[name=criteria]').val(phrase);
            
            clearLinkShowHide();
        }
    };

    /**
     * Nodrošina meklēšanas pogas nospiešanas funkcionalitāti - veic meklēšanu
     * 
     * @returns {undefined}
     */
    var handleBtnSearch = function() {
        form_elem.find(".search-simple-btn").click(function(e) {
            
            show_page_splash();
            form_elem.submit();
        });
    };

    /**
     * Nodrošina meklēšanas rīku pogas nospiešanas funkcionalitāti - parāda vai paslēpj meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var handleBtnTools = function() {
        form_elem.find('.search-tools-btn').click(function(e) {
            showHideTools();
        });
    }; 
    
    /**
     * Inicializē meklēšanas rīku JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initTools = function(callback_clearLinkShowHide, callback_clearFields) {        

        form_elem = $('.search-tools-form');
        
        clearLinkShowHide = callback_clearLinkShowHide;
        clearFields = callback_clearFields;
        
        handleBtnSearch();
        handleBtnTools();
    };

    return {
        init: function(callback_clearLinkShowHide, callback_clearFields) {
            initTools(callback_clearLinkShowHide, callback_clearFields);
        },
        showHideTools: function() {
            showHideTools();
        }
    };
}();

/**
 * Portāla ziņu meklēšanas lapas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageArticles = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Portāla ziņu meklēšanas bloka HTML elements
     * 
     * @type type
     */
    var page_elem = null;
    
    /**
     * Unikālais lapas bloka ID
     * 
     * @type String
     */
    var block_guid = "";
    
    /**
     * Pašlai ielādētais ziņu tips
     * 
     * @type Number
     */
    var current_loaded_type = 0;
    
    /**
     * Masīvs ar tipam atbilstoši ielādēto pēdejo lapu numuriem
     * 
     * @type Array
     */
    var type_page_arr = [];

    /**
     * Uzstāda pazīmi, vai tiek ielādēti dati - lai izvairītos no n reizes saklikšķināšanas
     * 
     * @type Number
     */
    var is_loading = 0;
    /**
     * Iespējo ziņu pielādi uz lapas ritināšanu
     * 
     * @returns {undefined}
     */
    var enableScroll = function() {
        /*
        if (!$("#feed_area_" + block_guid + " .pager").length) {
            return;
        }
        */
        $("#feed_area_" + block_guid).jscroll({
            loadingHtml: getProgressInfo(),
            padding: 20,
            nextSelector: 'ul.pager li > a[rel=next]',
            callback: function() {
                        $("#feed_area_" + block_guid + " .pager").hide();
            },
            contentSelector: ".article_row_area"
        });

        $("#feed_area_" + block_guid + " .pager").hide();
    };
    
    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarībā ir vai nav norādīts vismaz viens kritērijs
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                getElementVal(page_elem, 'input[name=criteria]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_from]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_to]', "") 
                )
        {
            page_elem.find(".dx-clear-link").show();
        }
        else
        {
            page_elem.find(".dx-clear-link").hide();
        }
    };

    /**
     * Notīra meklēšanas kritēriju lauku vērtības
     * 
     * @returns {undefined}
     */
    var clearFields = function() {
        form_elem.find('input[name=pick_date_from]').val('');
        form_elem.find('input[name=pick_date_to]').val('');
        form_elem.find('input[name=criteria]').val('');
        form_elem.find('#defaultrange input').val('');
    };    

    /**
     * Nodrošina saites "Notīrīt" funkcionalitāti - dzēš formas lauku vērtības
     *
     * @returns {undefined}
     */
    var handleLinkClear = function() {
        page_elem.find(".dx-clear-link").click(function() {
            clearFields();
            clearLinkShowHide();
        });
    };

    /**
     * Inicializē vērtību notīrīšanas saites parādīšanu/paslēpšanu, mainoties formas lauku vērtībām
     * 
     * @returns {undefined}
     */
    var handleValueChange = function() {
        form_elem.find("input").keyup(function() {
            clearLinkShowHide();
        });
    };
    
    /**
     * Parāda vai paslēpj ziņu ierakstus atkarībā no filtrā izvēlētā tipa
     * 
     * @param {integer} type Ziņas tipa ID, ja 0, tad jārāda visas ziņas
     * @returns {undefined}
     */
    var showHideContent = function(type) {
        if (type == 0) {
            $("[class^='panel article_item_row type_']").show();
        }
        else {
            $("[class^='panel article_item_row type_']").hide();            
            $(".type_" + type).show();
        }
    };
    
    /**
     * Izgūst parametra vērtību no URL
     * 
     * @param {string} url POST saites URL ar parametriem
     * @param {string} param_name Parametra nosaukums
     * @returns {string} Parametra vērtība
     */
    var getUrlParamVal = function(url, param_name) {
        var parts_arr = url.split('&' + param_name + '=');
        
        var val_arr = parts_arr[1].split('&');
        
        return val_arr[0];
    };
    
    /**
     * Atgriež tipam atbilstoši ielādēto lapas numuru
     * 
     * @param {integer} type Tipa ID
     * @returns {integer} Lapas nr
     */
    var getTypePageNr = function(type) {
        if (type_page_arr[type] == null) {
            return 0;
        }
        
        return type_page_arr[type];
    }
    
    /**
     * Nodrošina atrasto rakstu grupēšanu pa veidiem
     * 
     * @returns {undefined}
     */
    var handleFiltering = function() {
        
        if (page_elem.attr("dx_articles_count") == "0") {            
            return;
        }
        
        $('.cbp-filter-item').click(function(e){
            if (is_loading == 1) {
                return false;
            }
            
            e.preventDefault();

            var type = $(this).attr('dx_id');
            
            if (current_loaded_type == type)
            {
                return;
            }
            
            if (type == 0 || current_loaded_type == 0) {
                // visu jādzēš un jālādē no jauna - lai nelādētu dubultā
                $(".article_row_area").html('');
                type_page_arr=[];
            }
            
            $('.cbp-filter-item').removeClass('cbp-filter-item-active');
            $(this).addClass('cbp-filter-item-active');
            
            showHideContent(type);
            
            current_loaded_type = type;
            
            var cnt = parseInt($(this).attr('dx_count'));
            
            if ($(".type_" + type).length >= cnt) {                
                return; // viss ielādēts                
            }
            
            is_loading = 1;
            
            if ($('.pager a[rel=next]').length == 0) {

                var htm = "<ul class='pager'><li><a href='" + window.location.href + 
                        "?type=" + type + 
                        "&page=" + getTypePageNr(type) + 
                        "&criteria=" + page_elem.attr('dx_search_criteria') +
                        "&searchType=" + page_elem.attr('dx_search_searchType') +
                        "&pick_date_from=" + page_elem.attr('dx_search_pick_date_from') +
                        "&pick_date_to=" + page_elem.attr('dx_search_pick_date_to') +        
                        "' rel='next'></a></li></ul>";
                
                $( "#feed_area_" + block_guid ).append(htm);
                
            }
            else {
                var pager_url = $('.pager a[rel=next]').attr('href');
                
                var url_type = getUrlParamVal(pager_url, 'type');
                var url_page = getUrlParamVal(pager_url, 'page');                
                type_page_arr[url_type] = url_page;
                
                if (url_type != type) {                    
                    
                    pager_url = pager_url.replace("&type=" + url_type, "&type=" + type);
                    pager_url = pager_url.replace("&page=" + url_page, "&page=0");
                    $('.pager a[rel=next]').attr('href', pager_url);
                    
                }
            }
            
            $("#feed_area_" + block_guid).jscroll({
                loadingHtml: getProgressInfo(),
                padding: 20,
                nextSelector: 'ul.pager li > a[rel=next]',
                callback: function() {
                            $("#feed_area_" + block_guid + " .pager").hide();
                            showHideContent(type);
                            is_loading = 0;
                },
                contentSelector: ".article_row_area",
                force_load: true
            });
            
        });        
    };

    /**
     * Inicializē datuma intervāla uzstādīšanas komponenti
     * 
     * @returns {undefined}
     */
    var initDateRange = function() {                
    
        var arr_date_param = {
            range_id: "defaultrange",
            el_date_from: "pick_date_from",
            el_date_to: "pick_date_to",
            page_selector: ".dx-articles-page",
            form_selector: ".search-tools-form",
            arr_ranges: {}
        };
        
        arr_date_param["arr_ranges"][Lang.get('date_range.flt_today')] = [moment(), moment()];
        arr_date_param["arr_ranges"][Lang.get('date_range.flt_yesterday')] = [moment().subtract('days', 1), moment().subtract('days', 1)];
        arr_date_param["arr_ranges"][Lang.get('date_range.flt_7')] = [moment().subtract('days', 6), moment()];
        arr_date_param["arr_ranges"][Lang.get('date_range.flt_thism')] = [moment().startOf('month'), moment().endOf('month')];
        arr_date_param["arr_ranges"][Lang.get('date_range.flt_prevm')] = [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')];
        
        DateRange.init(arr_date_param, clearLinkShowHide);
    
    }
    
    /**
     * Inicializē meklēšnas rīku parādīšanu pie lapas ielādes - parāda, ja bija meklēšanas pēc kāda no redzamajiem formas laukiem
     * 
     * @returns {undefined}
     */
    var initSearchTools = function()
    {
        if (
                getElementVal(page_elem, 'input[name=pick_date_from]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_to]', "") 
                )
        {
            SearchTools.showHideTools();
        }
    };
    
    /**
     * Paslēpj filtrus pēc tipa, ja ir tikai 1 tips
     * 
     * @returns {undefined}
     */
    var initTypeFilters = function() {
        
        if ($('#js-filters-juicy-projects .cbp-filter-item').length < 3) {
            $('#js-filters-juicy-projects').hide();
        }
    };
    
    /**
     * Inicializē portāla ziņu lapas JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {        
        
        page_elem = $(".dx-articles-page");
        
        form_elem = page_elem.find('.search-tools-form');
        
        block_guid = page_elem.attr("dx_block_guid");
        current_loaded_type = page_elem.attr('dx_current_type');
        
        var mode = page_elem.attr("dx_mode");
        
        SearchTools.init(clearLinkShowHide, clearFields);               

        handleValueChange();
        handleLinkClear();

        clearLinkShowHide();
        
        if (mode == "search") {
            initSearchTools();
            initDateRange();
        }
        
        enableScroll();
        handleFiltering();
        initTypeFilters();
    };

    return {
        init: function() {
            initPage();
        }
    };
}();

$(document).ready(function() {
    PageArticles.init();
});
//# sourceMappingURL=elix_articles.js.map
