/* Elements */
var $sidebar = $("#navSidebarNeverland");
var $footer = $(".navbar-bottom");
var $tableFilter = $(".table-filtered");
var $popup = $("#modal-onload");
var $clear = $(".input-clear");

/* Components */
var componentFooter = true;
var componentSidebar = true;
var componentTableFilter = true;
var componentPopup = true;
var componentClearInput = true;

/* Variables */
var viewportHeight;
var footerHeight;
var footerTop;
var footerMargin;
var sidebarHeight;
var sidebarFloatPoint;
var sidebarFixPoint;

/* Triggers */
$(window).on("load", onLoadHandler);
$(window).on("resize", onResizeHandler);
$(window).on("scroll", onScrollHandler);

/* Event handlers */
function onLoadHandler() {
	doCheckComponents();
	doCaptureScreenData();
	doScrollSpy();
	doFloatingFooter();
	doFloatingSidebar();
	doTableFilters();
	doMessagePopup();
	doClearInput();
	doSmoothScroll();
}

function onResizeHandler() {
	doCaptureScreenData();
	doFloatingFooter();
	doFloatingSidebar();
}

function onScrollHandler() {
	doCaptureScreenData();
	doFloatingFooter();
	doFloatingSidebar();
}

/* Action methods */
function doCheckComponents() {
	componentPopup = componentPopup ? $popup.length != 0 : false;
	componentFooter = componentFooter ? $footer.length != 0 :false;
	componentSidebar = componentSidebar ? $sidebar.length != 0 : false;
	componentClearInput = componentClearInput ? $clear.length != 0 : false;
	componentTableFilter = componentTableFilter ? $tableFilter.length != 0 && typeof($.fn.columnFilters) == 'function'
												: false;
}

function doCaptureScreenData() {
	try {
		viewportHeight = $(window).height();

		if (componentFooter) {
			footerHeight = $footer.height();
			footerTop = $footer.prev().offset().top + $footer.prev().outerHeight();
			footerTop = footerTop + 30 + footerHeight;
		}

		if (componentFooter && componentSidebar) {
			sidebarHeight = $sidebar.height();
			sidebarFloatPoint = $sidebar.position().top;
			sidebarFixPoint = footerTop - sidebarHeight - 20;
		}
	} catch(e) {
		console.log("Error: " + e.message);
	}
}

function doScrollSpy() {
	try {
		if (componentSidebar) {
			$sidebar.scrollspy();
		}
	} catch(e) {
		console.log("Error: " + e.message);
	}
}

function doFloatingFooter() {
	try {
		if (componentFooter) {
			var windowTop = $(window).scrollTop();
			var windowBottom = windowTop + viewportHeight;

			if (windowBottom > footerTop) {
				$footer.removeClass("fixed");
			} else {
				$footer.addClass("fixed");
			}
		}
	} catch(e) {
		console.log("Error: " + e.message);
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
		console.log("Error: " + e.message);
	}
}

function doTableFilters() {
	try {
		if (componentTableFilter) {
			var excludedColumns = new Array();

			$tableFilter.each(function() {
				var classesAry = $(this).attr('class').split(' ');

				for (classIndex in classesAry) {
					if (classesAry[classIndex].trim().indexOf('filter-skip') == 0) {
						var skipColumns = classesAry[classIndex].split('-');

						for (skipIndex in skipColumns) {
							if (!isNaN(skipColumns[skipIndex])) {
								excludedColumns.push(parseInt(skipColumns[skipIndex]));
							}
						}
					}
				}
			});

			$tableFilter.columnFilters({excludeColumns: excludedColumns});
		}
	} catch(e) {
		console.log("Error: " + e.message);
	}
}

function doMessagePopup() {
	try {
		if (componentPopup) {
			$popup.modal();
		}
	} catch(e) {
		console.log("Error: " + e.message);
	}
}

function doClearInput() {
	try {
		if (componentClearInput) {
			var counter = 1;

			$clear.each(function() {
				var group = "input-clear-group-" + counter.toString();
				var boxTop = $(this).offset().top;
				var boxLeft = $(this).offset().left;
				var boxHeight = $(this).innerHeight();
				var boxWidth = $(this).innerWidth();
				var padding = (boxHeight / 2) - 5;

				var clearTop = boxTop + padding;
				var clearLeft = boxLeft + boxWidth - padding - 10;
				var clearElement = '<i class="icon-remove input-clear-trigger ' + group + '" ' +
								   'style="position:absolute; cursor:pointer; top:' + clearTop +
								   'px; left:' + clearLeft + 'px; display:none;" data-target="' +
								   group + '"></i>';

				$(this)
					.addClass(group)
					.attr("data-group", group)
					.after(clearElement)
					.keydown(function() {
						if ($(this).val() != "") {
							$("i." + group).fadeIn();
						} else {
							$("i." + group).fadeOut();
						}
					});

				counter++;
			});

			$("i.input-clear-trigger").click(function() {
				var target = $(this).attr("data-target");

				$("input." + target)
					.val("")
					.focus();

				$(this).fadeOut("fast");
			});
		}
	} catch(e) {
		console.log("Error: " + e.message);
	}
}

function doSmoothScroll() {
	try {
		$("a.smooth").on('click', function() {
			var href = $(this).attr("href");
			var pos = href.indexOf("#");
			var offset = 0;

			if (pos == 0) {
				offset = $(href).offset().top;

				$("html, body").animate({
					scrollTop: offset
				});
			}
		});
	} catch(e) {
		console.log("Error: " + e.message);
	}
}