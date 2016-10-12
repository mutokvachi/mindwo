
var box = window.box = {
    activeSlide: 'page-holder',
    dashboardReload: 0,
    nextSlide: function (next, dir) {
        if (next === box.activeSlide) {
            return false;
        }

        var dirHide = "left",
            dirShow = "right";
        if (dir === "prev") {
            dirHide = "right";
            dirShow = "left";
        }
        $("#slide-" + box.activeSlide).hide("slide", {direction: dirHide}, 300);

        setTimeout(function () {
            $("#slide-" + next).show("slide", {direction: dirShow}, 300);
        }, 200);

        box.activeSlide = next;
    },
    updateBreadcrumbs: function (slide, title) {
        var holder = $("#breadcrumbs");
        var link = $('<a href="javascript:;" name="backSlide"></a>'),
            crumb = $('<li id="crumb-' + slide + '"></li>');

        link.html(title);
        link.attr('data-dx-id', slide);

        box.icon.clone().appendTo(crumb);
        crumb.append(link);

        holder.append(crumb);
    },
    goBackOneStep: function () {
        var steps = $("#breadcrumbs li").length, eq;

        if (steps === 1) {
            return;
        }
        /* 
         * -1 because :eq() is Zero-based index of the element to match.
         * and -1 for previous step
         * */
        eq = steps - 1 - 1;

        $("#breadcrumbs li:eq(" + eq + ")").find("a").trigger("click");
    }
};

$(document).ready(function () {
    $('body').on('click', '[name="actionSlide"]', function () {
        var nextSlide = $(this).attr('data-dx-id'), title;

        $(".page-toolbar").attr('name', 'backSlide');

        if (nextSlide === "0") {
            title = $(".page-toolbar span.thin").html();
        } else {
            title = $(this).find("h4").html();
        }

        box.nextSlide(nextSlide, 'next');
        box.updateBreadcrumbs(nextSlide, title);
    });

    $('body').on('click', '[name="backSlide"]', function () {
        var nextSlide = $(this).attr('data-dx-id');

        if (nextSlide === "page-holder") {
            $(".page-toolbar").attr('name', 'actionSlide');

            if (box.dashboardReload === 1) {
                show_page_splash();
                window.location.href = DX_CORE.site_url;
                return;
            }
        }

        box.nextSlide(nextSlide, 'prev');
        $("#crumb-" + nextSlide).nextAll().remove();
    });

    $('body').on('click', '[name="actionUrl"]', function () {
        show_page_splash();
        var url = $(this).attr('data-url');
        window.location.href = url;
    });
});

window.onload = function () {
    if (typeof history.pushState === "function") {
        history.pushState("jibberish", null, null);
        window.onpopstate = function () {


            box.goBackOneStep();

            history.pushState('newjibberish', null, null);
            // Handle the back (or forward) buttons here
            // Will NOT handle refresh, use onbeforeunload for this.

            var url = document.URL;
            var index = url.indexOf("?");
            if (index < -1)
            {
                console.log('index < -1');
                url = url.substr(0, index);
                window.history.pushState("", "", url);
            }
        };
    }
    else {
        var ignoreHashChange = true;
        window.onhashchange = function () {
            if (!ignoreHashChange) {
                ignoreHashChange = true;
                window.location.hash = Math.random();
                // Detect and redirect change here
                // Works in older FF and IE9
                // * it does mess with your hash symbol (anchor?) pound sign
                // delimiter on the end of the URL
            }
            else {
                ignoreHashChange = false;
            }
        };
    }
}