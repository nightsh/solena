/* Elements */
var $sidebar = $("#navSidebarNeverland");
var $footer = $(".navbar-bottom");
var $tableFilter = $(".table-filtered");

/* Components */
var componentFooter;
var componentSidebar;
var componentTableFilter;

/* Variables */
var viewportHeight;
var footerHeight;
var footerTop;
var sidebarHeight;
var sidebarFloatPoint;
var sidebarFixPoint;

/* Triggers */
$(window).on("load", onLoadHandler);
$(window).on("scroll", onScrollHandler);

/* Event handlers */
function onLoadHandler() {
    doCheckComponents();
    doCaptureScreenData();
    doScrollSpy();
    doFloatingFooter();
    doFloatingSidebar();
    doTableFilters();
}

function onScrollHandler() {
    doFloatingFooter();
    doFloatingSidebar();
}

/* Action methods */
function doCheckComponents() {
    componentFooter = $footer.length != 0;
    componentSidebar = $sidebar.length != 0;
    componentTableFilter = $tableFilter.length != 0 &&
                           typeof($.fn.columnFilters) == 'function';
}

function doCaptureScreenData() {
    try {
        viewportHeight = $(window).height();

        if (componentFooter) {
            footerHeight = $footer.height();
            footerTop = $footer.offset().top;
        }

        if (componentSidebar) {
            sidebarHeight = $sidebar.height();
            sidebarFloatPoint = $sidebar.position().top;
            sidebarFixPoint = footerTop - sidebarHeight - 20;
        }
    } catch(e) {
        console.log('Error: ' + e.message);
    }
}

function doScrollSpy() {
    try {
        if (componentSidebar) {
            $sidebar.scrollspy();
        }
    } catch(e) {
        console.log('Error: ' + e.message);
    }
}

function doFloatingFooter() {
    try {
        if (componentFooter) {
            var windowTop = $(window).scrollTop();
            var windowBottom = windowTop + viewportHeight - footerHeight;

            if (windowBottom > footerTop) {
                $footer.removeClass("fixed");
            } else {
                $footer.addClass("fixed");
            }
        }
    } catch(e) {
        console.log('Error: ' + e.message);
    }
}

function doFloatingSidebar() {
    try {
        if (componentSidebar) {
            var windowTop = $(window).scrollTop();
            var sidebarTop = windowTop + sidebarFloatPoint;

            if (sidebarTop > sidebarFixPoint) {
                $sidebar.css({
                    position: "absolute",
                    top: sidebarFixPoint.toString() + "px"
                });
            } else {
                $sidebar.css({
                    position: "fixed",
                    top: sidebarFloatPoint.toString() + "px"
                });
            }
        }
    } catch(e) {
        console.log('Error: ' + e.message);
    }
}

function doTableFilters() {
    try {
        if (componentTableFilter) {
            $tableFilter.columnFilters();
        }
    } catch(e) {
        console.log('Error: ' + e.message);
    }
}