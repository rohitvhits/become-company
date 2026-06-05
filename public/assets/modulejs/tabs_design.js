
var $container = $(".js-tabs").parent();
var $tabsToggleGroup = $(".tabs__toggle-group");
var $tabs = $(".tabs__toggle");
var $activeTab = $(".tabs__toggle--active");
var $tabContents = $(".tabs__tab");

var $scrollLeft = $(".js-action--scroll-left");
var $scrollRight = $(".js-action--scroll-right");

var btnScrollLeft = document.querySelector('.js-action--scroll-left');
var btnScrollRight = document.querySelector('.js-action--scroll-right');
var tabsContainer = document.querySelector('.tabs__toggle-group');
var tabs = document.querySelectorAll('.tabs__toggle');
var selectedTabIndex = 0;
var scrollIndex = 0;
var scrollWidth = tabs[0].clientWidth + 5;
var scrollLeft = 0;

// var tabsContainer = document.querySelector('.tabs__toggle-group');
var tabsContainerWidth = tabsContainer.clientWidth;
var tabsScrollableWidth = tabsContainer.scrollWidth;

console.log("Container Width:", tabsContainerWidth, "Tabs Width:", tabsScrollableWidth);

if (tabsScrollableWidth > tabsContainerWidth) { }

$tabContents
	.hide()
	.eq($tabs.index($activeTab))
	.show();

// var btnAddTab = document.querySelector('.js-action--add-tab');
// btnAddTab.addEventListener('click', function() {
// 	var tabName = prompt("What text would you like on the tab?");
// 	addTab(tabName);	
// });

function addTab(tabName) {
	var tabToggleGroup = document.querySelector('.tabs__toggle-group');
	var tabsGroup = document.querySelector('.tabs__tabs-group');
	var newTabToggle = document.createElement('div');
	var newTab = document.createElement('div');

	newTabToggle.classList.add('tabs__toggle');
	newTabToggle.innerText = tabName;
	tabToggleGroup.appendChild(newTabToggle);

	newTab.classList.add('tabs__tab');
	newTab.innerText = tabName + " Content";
	newTab.style.display = 'none';
	tabsGroup.appendChild(newTab);
}

$tabs.on("click", function () {

	var $tabs = $(".tabs__toggle");
	var $activeTab = $(".tabs__toggle--active");
	var $tabContents = $(".tabs__tab");
	var $tab = $(this);
	var tabIndex = $tabs.index($tab);

	selectedTabIndex = tabIndex;
	
	$tab.addClass("tabs__toggle--active");
	$activeTab.removeClass("tabs__toggle--active");

	$tabContents
		.hide()
		.eq(tabIndex)
		.show();

	// debugger;
	var tab = $tab[0];
	var tabWidth = tab.clientWidth;
	// var tabLeft = tab.offsetLeft;
	var tabLeft		= tabsContainer.scrollLeft;
	var tabRight = tabLeft + tabWidth;
	// var 

	if (tabLeft < tabsContainer.scrollLeft) {

		smoothScroll(tabsContainer, { to: tabLeft, callback: checkScrollButtonState });
	}


	// if (tabRight > (tabsContainerWidth - tabsContainer.scrollLeft)) {
	if (tabRight > (tabsContainer.scrollLeft + tabsContainerWidth)) {

		smoothScroll(tabsContainer, { to: (tabRight - tabsContainerWidth), callback: checkScrollButtonState });
	}
});

if (tabsContainer.scrollLeft === 0) {
	btnScrollLeft.setAttribute("disabled", true);
}


btnScrollLeft.addEventListener('click', function () {

	scrollIndex--;
	// console.log("Tab Index to scroll LEFT to", scrollIndex);
	if (tabs[scrollIndex] != undefined) {
		scrollLeft -= tabs[scrollIndex].clientWidth + 50;
	}else{
		scrollLeft -= 50
	}


	smoothScroll(tabsContainer, { to: scrollLeft, callback: checkScrollButtonState });
});

btnScrollRight.addEventListener('click', function () {
	if (tabs[scrollIndex] != undefined) {
		console.log(tabs[scrollIndex].clientWidth)
		scrollLeft += tabs[scrollIndex].clientWidth + 50;
	}else{
		scrollLeft += 50
	}

	scrollIndex++;

	smoothScroll(tabsContainer, { to: scrollLeft, callback: checkScrollButtonState });

});

function checkScrollButtonState() {
	console.log("scrollLeft:", tabsContainer.scrollLeft, "clientWidth:", tabsContainer.clientWidth, "scrollWidth:", tabsContainer.scrollWidth);

	if (tabsContainer.scrollLeft <= 0) {
		btnScrollLeft.setAttribute("disabled", true);
	} else {
		btnScrollLeft.removeAttribute("disabled");
	}

	if (tabsContainer.scrollLeft + tabsContainer.clientWidth + 50 >= tabsContainer.scrollWidth) {
		btnScrollRight.setAttribute("disabled", true);
	} else {
		btnScrollRight.removeAttribute("disabled");
	}
}

function smoothScroll(element, options) {
    var requestAnimFrame = (function () {
        return (
            window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            function (callback) {
                window.setTimeout(callback, 1000 / 60);
            }
        );
    })();

    var defaults = {
        to: 0,
        duration: 250,
        axis: "horizontal",
        easing: "easeInOutQuad",
    };

    var settings = Object.assign({}, defaults, options);

    var direction = settings.axis === "horizontal" ? "scrollLeft" : "scrollTop";

    var start = element[direction],
        change = settings.to - start,
        animationStart = +new Date();
    var animating = true;

    // Check if already at target position
    if (start === settings.to) {
        if (settings.callback) settings.callback();
        return;
    }

    var animateScroll = function () {
        if (!animating) {
            if (settings.callback) settings.callback();
            return;
        }
        requestAnimFrame(animateScroll);

        var now = +new Date();
        var elapsed = now - animationStart;

        // Apply easing
        var val = Math.floor(
            Easing[settings.easing](elapsed, start, change, settings.duration)
        );

        // Ensure element scroll position is updated
        element[direction] = val;

        // Stop if the animation duration is reached
        if (elapsed >= settings.duration || Math.abs(element[direction] - settings.to) < 1) {
            element[direction] = settings.to; // Correct final position
            animating = false;
        }
    };
    requestAnimFrame(animateScroll);
}

/**
 * Easing Functions - inspired from http://gizma.com/easing/
 */
Easing = {

	// no easing, no acceleration
	linear: function (t, b, c, d) {
		return c * t / d + b;
	},

	// accelerating from zero velocity
	easeInQuad: function (t, b, c, d) {
		t /= d;
		return c * t * t + b;
	},

	// decelerating to zero velocity
	easeOutQuad: function (t, b, c, d) {
		t /= d;
		return -c * t * (t - 2) + b;
	},

	// acceleration until halfway, then deceleration
	easeInOutQuad: function (t, b, c, d) {
		t /= d / 2;
		if (t < 1) return c / 2 * t * t + b;
		t--;
		return -c / 2 * (t * (t - 2) - 1) + b;
	},

	// accelerating from zero velocity 
	easeInCubic: function (t, b, c, d) {
		t /= d;
		return c * t * t * t + b;
	},

	// decelerating to zero velocity 
	easeOutCubic: function (t, b, c, d) {
		t /= d;
		t--;
		return c * (t * t * t + 1) + b;
	},

	// acceleration until halfway, then deceleration 
	easeInOutCubic: function (t, b, c, d) {
		t /= d / 2;
		if (t < 1) return c / 2 * t * t * t + b;
		t -= 2;
		return c / 2 * (t * t * t + 2) + b;
	},

	// accelerating from zero velocity 
	easeInQuart: function (t, b, c, d) {
		t /= d;
		return c * t * t * t * t + b;
	},

	// decelerating to zero velocity 
	easeOutQuart: function (t, b, c, d) {
		t /= d;
		t--;
		return -c * (t * t * t * t - 1) + b;
	},

	// acceleration until halfway, then deceleration
	easeInOutQuart: function (t, b, c, d) {
		t /= d / 2;
		if (t < 1) return c / 2 * t * t * t * t + b;
		t -= 2;
		return -c / 2 * (t * t * t * t - 2) + b;
	},

	// accelerating from zero velocity
	easeInQuint: function (t, b, c, d) {
		t /= d;
		return c * t * t * t * t * t + b;
	},

	// decelerating to zero velocity
	easeOutQuint: function (t, b, c, d) {
		t /= d;
		t--;
		return c * (t * t * t * t * t + 1) + b;
	},

	// acceleration until halfway, then deceleration 
	easeInOutQuint: function (t, b, c, d) {
		t /= d / 2;
		if (t < 1) return c / 2 * t * t * t * t * t + b;
		t -= 2;
		return c / 2 * (t * t * t * t * t + 2) + b;
	}

};

/***********************HHA */
if (_PATIENT_ID != "" || _CAREGIVER_ID != "") {
	var $hhaContainer = $(".js-hha-tabs").parent();
	var $hhaTabsToggleGroup = $(".tabs_hha__toggle-group");
	var $hhaTabs = $(".tabs_hha__toggle");
	var $hhaActiveTab = $(".tabs_hha__toggle--active");
	var $hhaTabContents = $(".tabs_hha__tab");

	var $hhaScrollLeft = $(".js-hha-action--scroll-left");
	var $hhaScrollRight = $(".js-hha-action--scroll-right");

	var hhaBtnScrollLeft = document.querySelector('.js-hha-action--scroll-left');
	var hhaBtnScrollRight = document.querySelector('.js-hha-action--scroll-right');
	var hhaTabsContainer = document.querySelector('.tabs_hha__toggle-group');
	var hhaTabs = document.querySelectorAll('.tabs_hha__toggle');
	var hhaSelectedTabIndex = 0;
	var hhaScrollIndex = 0;
	var hhaScrollWidth = hhaTabs[0].clientWidth + 5;
	var hhaScrollLeft = 0;


	var hhaTabsContainerWidth = hhaTabsContainer.clientWidth;
	var hhatabsScrollableWidth  = hhaTabsContainer.ScrollWidth;


	if (hhatabsScrollableWidth  > hhaTabsContainerWidth) {

	}

	$hhaTabContents
		.hide()
		.eq($hhaTabs.index($hhaActiveTab))
		.show();

	function addHHATab1(tabName) {
		var tabToggleGroup = document.querySelector('.tabs_hha__toggle-group');
		var tabsGroup = document.querySelector('.tabs_hha__tabs-group');
		var newTabToggle = document.createElement('div');
		var newTab = document.createElement('div');

		newTabToggle.classList.add('tabs_hha__toggle');
		newTabToggle.innerText = tabName;
		tabToggleGroup.appendChild(newTabToggle);

		newTab.classList.add('tabs_hha__tab');
		newTab.innerText = tabName + " Content";
		newTab.style.display = 'none';
		tabsGroup.appendChild(newTab);

	}

	hhaBtnScrollLeft.addEventListener('click', function () {

		hhaScrollIndex--;

		if (hhaTabs[hhaScrollIndex] != undefined) {
			hhaScrollLeft -= hhaTabs[hhaScrollIndex].clientWidth + 7;
		}
		smoothScroll(hhaTabsContainer, { to: hhaScrollLeft, callback: checkHHAScrollButtonState });
	});

	hhaBtnScrollRight.addEventListener('click', function () {

		if (hhaTabs[hhaScrollIndex] != undefined) {
			hhaScrollLeft += hhaTabs[hhaScrollIndex].clientWidth + 7;
		}

		hhaScrollIndex++;

		smoothScroll(hhaTabsContainer, { to: hhaScrollLeft, callback: checkHHAScrollButtonState });

	});
	$hhaTabs.on("click", function () {

		var $hhaTabsNew = $(".tabs_hha__toggle");
		var $hhaTabContents = $(".tabs_hha__tab");
		var $tabHHA = $(this);


		var hhaTabIndex = $hhaTabsNew.index($tabHHA);

		// Remove active class from all tabs and add it to the clicked one
		$hhaTabsNew.removeClass("tabs_hha__toggle--active");
		$tabHHA.addClass("tabs_hha__toggle--active");

		// Hide all tab contents and show the content of the clicked tab
		$hhaTabContents.hide().eq(hhaTabIndex).show();

		hhaSelectedTabIndex = hhaTabIndex;


		var tabHHAs = $tabHHA[0];
		var tabHHAWidth = tabHHAs.clientWidth;
		var tabHHALeft = tabHHAs.offsetLeft;
		var tabHHARight = tabHHALeft + tabHHAWidth;

		// if (tabHHALeft < hhaTabsContainer.scrollLeft) {
		// 	smoothScroll(hhaTabsContainer, { to: tabHHALeft, callback: checkHHAScrollButtonState });
		// }

		// if (tabHHARight > (hhaTabsContainer.scrollLeft + hhaTabsContainerWidth)) {
		// 	smoothScroll(hhaTabsContainer, { to: (tabHHARight - hhaTabsContainerWidth), callback: checkHHAScrollButtonState });
		// }

		checkHHAScrollButtonState()
	});

	function checkHHAScrollButtonState() {

		if (hhaTabsContainer.scrollLeft <= 0) {
			hhaBtnScrollLeft.setAttribute("disabled", true);
		} else {
			hhaBtnScrollLeft.removeAttribute("disabled");
		}

		if (hhaTabsContainer.scrollLeft + hhaTabsContainer.clientWidth >= hhaTabsContainer.scrollWidth) {
			hhaBtnScrollRight.setAttribute("disabled", true);
		} else {
			hhaBtnScrollRight.removeAttribute("disabled");
		}
	}

	if (hhaTabsContainer.scrollLeft === 0) {
		hhaBtnScrollLeft.setAttribute("disabled", true);
	}
}



/*************************Remote Focus */
if (remoteID != "") {
	var $remoteContainer = $(".js-remote-tabs").parent();
	var $remoteTabsToggleGroup = $(".tabs_remote__toggle-group");
	var $remoteTabs = $(".tabs_remote__toggle");
	var $remoteActiveTab = $(".tabs_remote__toggle--active");
	var $remoteTabContents = $(".tabs_remote__tab");

	var $remoteScrollLeft = $(".js-remote-action--scroll-left");
	var $remoteScrollRight = $(".js-remote-action--scroll-right");

	var remoteBtnScrollLeft = document.querySelector('.js-remote-action--scroll-left');
	var remoteBtnScrollRight = document.querySelector('.js-remote-action--scroll-right');
	var remoteTabsContainer = document.querySelector('.tabs_remote__toggle-group');
	var remoteTabs = document.querySelectorAll('.tabs_remote__toggle');
	var remoteSelectedTabIndex = 0;
	var remoteScrollIndex = 0;
	var remoteScrollWidth = remoteTabs[0].clientWidth + 5;
	var remoteScrollLeft = 0;


	var remoteTabsContainerWidth = remoteTabsContainer.clientWidth;
	var tabsScrollableWidth = remoteTabsContainer.ScrollWidth;


	if (tabsScrollableWidth > remoteTabsContainerWidth) {

	}

	$remoteTabContents
		.hide()
		.eq($remoteTabs.index($remoteActiveTab))
		.show();

	function addHHATab1(tabName) {
		var tabToggleGroup = document.querySelector('.tabs_remote__toggle-group');
		var tabsGroup = document.querySelector('.tabs_remote__tabs-group');
		var newTabToggle = document.createElement('div');
		var newTab = document.createElement('div');

		newTabToggle.classList.add('tabs_remote__toggle');
		newTabToggle.innerText = tabName;
		tabToggleGroup.appendChild(newTabToggle);

		newTab.classList.add('tabs_remote__tab');
		newTab.innerText = tabName + " Content";
		newTab.style.display = 'none';
		tabsGroup.appendChild(newTab);

	}

	remoteBtnScrollLeft.addEventListener('click', function () {

		remoteScrollIndex--;

		if (remoteTabs[remoteScrollIndex] != undefined) {
			remoteScrollLeft -= remoteTabs[remoteScrollIndex].clientWidth + 7;
		}
		smoothScroll(remoteTabsContainer, { to: remoteScrollLeft, callback: checkRemoteScrollButtonState });
	});

	remoteBtnScrollRight.addEventListener('click', function () {

		if (remoteTabs[remoteScrollIndex] != undefined) {
			remoteScrollLeft += remoteTabs[remoteScrollIndex].clientWidth + 7;
		}

		remoteScrollIndex++;

		smoothScroll(remoteTabsContainer, { to: remoteScrollLeft, callback: checkRemoteScrollButtonState });

	});
	$remoteTabs.on("click", function () {

		var $hhaTabsNew = $(".tabs_remote__toggle");
		var $remoteTabContents = $(".tabs_remote__tab");
		var $tabHHA = $(this);


		var hhaTabIndex = $hhaTabsNew.index($tabHHA);

		// Remove active class from all tabs and add it to the clicked one
		$hhaTabsNew.removeClass("tabs_remote__toggle--active");
		$tabHHA.addClass("tabs_remote__toggle--active");

		// Hide all tab contents and show the content of the clicked tab
		$remoteTabContents.hide().eq(hhaTabIndex).show();

		hhaSelectedTabIndex = hhaTabIndex;


		var tabHHAs = $tabHHA[0];
		var tabHHAWidth = tabHHAs.clientWidth;
		var tabHHALeft = tabHHAs.offsetLeft;
		var tabHHARight = tabHHALeft + tabHHAWidth;

		if (tabHHALeft < remoteTabsContainer.scrollLeft) {
			smoothScroll(remoteTabsContainer, { to: tabHHALeft, callback: checkRemoteScrollButtonState });
		}

		if (tabHHARight > (remoteTabsContainer.scrollLeft + remoteTabsContainerWidth)) {
			smoothScroll(remoteTabsContainer, { to: (tabHHARight - remoteTabsContainerWidth), callback: checkRemoteScrollButtonState });
		}
	});

	function checkRemoteScrollButtonState() {

		if (remoteTabsContainer.scrollLeft <= 0) {
			remoteBtnScrollLeft.setAttribute("disabled", true);
		} else {
			remoteBtnScrollLeft.removeAttribute("disabled");
		}

		if (remoteTabsContainer.scrollLeft + remoteTabsContainer.clientWidth >= remoteTabsContainer.scrollWidth) {
			remoteBtnScrollRight.setAttribute("disabled", true);
		} else {
			remoteBtnScrollRight.removeAttribute("disabled");
		}
	}

	if (remoteTabsContainer.scrollLeft === 0) {
		remoteBtnScrollLeft.setAttribute("disabled", true);
	}
}

/************************AlayaCare */
if (_ALAYACAREID.trim() != "") {
	var $alayaCareContainer = $(".js-alaya-tabs").parent();
	var $alayaCareTabsToggleGroup = $(".tabs_alaya__toggle-group");
	var $alayaCareScrollLeft = $(".js-alaya-action--scroll-left");
	var $alayaCareScrollRight = $(".js-alaya-action--scroll-right");
	var alayaCareBtnScrollLeft = document.querySelector('.js-alaya-action--scroll-left');
	var alayaCareBtnScrollRight = document.querySelector('.js-alaya-action--scroll-right');
	var alayaCareSelectedTabIndex = 0;
	var alayaCareScrollIndex = 0;
	var alayaCareScrollWidth = alayaCareTabs[0].clientWidth + 5;
	var alayaCareScrollLeft = 0;
	var alayaCareTabs = document.querySelectorAll('.tabs_alaya__toggle');
	function hideShowData(){
		
		var $alayaCareTabs = $(".tabs_alaya__toggle");
		var $alayaCareActiveTab = $(".tabs_alaya__toggle--active");
		var $alayaCareTabContents = $(".tabs_alaya__tab");
	
		var alayaCareTabsContainer = document.querySelector('.tabs_alaya__toggle-group');
		
		
	
	
		var alayaCareTabsContainerWidth = alayaCareTabsContainer.clientWidth;
		var tabsScrollableWidth = alayaCareTabsContainer.ScrollWidth;
	
	
		if (tabsScrollableWidth > alayaCareTabsContainerWidth) {
	
		}
	
		$alayaCareTabContents
			.hide()
			.eq($alayaCareTabs.index($alayaCareActiveTab))
			.show();
	}
	

	function addHHATab1(tabName) {
		var tabToggleGroup = document.querySelector('.tabs_alaya__toggle-group');
		var tabsGroup = document.querySelector('.tabs_alaya__tabs-group');
		var newTabToggle = document.createElement('div');
		var newTab = document.createElement('div');

		newTabToggle.classList.add('tabs_alaya__toggle');
		newTabToggle.innerText = tabName;
		tabToggleGroup.appendChild(newTabToggle);

		newTab.classList.add('tabs_alaya__tab');
		newTab.innerText = tabName + " Content";
		newTab.style.display = 'none';
		tabsGroup.appendChild(newTab);

	}

	alayaCareBtnScrollLeft.addEventListener('click', function () {

		alayaCareScrollIndex--;

		if (alayaCareTabs[alayaCareScrollIndex] != undefined) {
			alayaCareScrollLeft -= alayaCareTabs[alayaCareScrollIndex].clientWidth + 7;
		}
		smoothScroll(alayaCareTabsContainer, { to: alayaCareScrollLeft, callback: checkAlayaCareScrollButtonState });
	});

	alayaCareBtnScrollRight.addEventListener('click', function () {

		if (alayaCareTabs[alayaCareScrollIndex] != undefined) {
			alayaCareScrollLeft += alayaCareTabs[alayaCareScrollIndex].clientWidth + 7;
		}

		alayaCareScrollIndex++;

		smoothScroll(alayaCareTabsContainer, { to: alayaCareScrollLeft, callback: checkAlayaCareScrollButtonState });

	});
	$alayaCareTabs.on("click", function () {

		var $hhaTabsNew = $(".tabs_alaya__toggle");
		var $alayaCareTabContents = $(".tabs_alaya__tab");
		var $tabHHA = $(this);


		var hhaTabIndex = $hhaTabsNew.index($tabHHA);

		// Remove active class from all tabs and add it to the clicked one
		$hhaTabsNew.removeClass("tabs_alaya__toggle--active");
		$tabHHA.addClass("tabs_alaya__toggle--active");

		// Hide all tab contents and show the content of the clicked tab
		$alayaCareTabContents.hide().eq(hhaTabIndex).show();

		hhaSelectedTabIndex = hhaTabIndex;


		var tabHHAs = $tabHHA[0];
		var tabHHAWidth = tabHHAs.clientWidth;
		var tabHHALeft = tabHHAs.offsetLeft;
		var tabHHARight = tabHHALeft + tabHHAWidth;

		if (tabHHALeft < alayaCareTabsContainer.scrollLeft) {
			smoothScroll(alayaCareTabsContainer, { to: tabHHALeft, callback: checkAlayaCareScrollButtonState });
		}

		if (tabHHARight > (alayaCareTabsContainer.scrollLeft + alayaCareTabsContainerWidth)) {
			smoothScroll(alayaCareTabsContainer, { to: (tabHHARight - alayaCareTabsContainerWidth), callback: checkAlayaCareScrollButtonState });
		}
	});

	function checkAlayaCareScrollButtonState() {

		if (alayaCareTabsContainer.scrollLeft <= 0) {
			alayaCareBtnScrollLeft.setAttribute("disabled", true);
		} else {
			alayaCareBtnScrollLeft.removeAttribute("disabled");
		}

		if (alayaCareTabsContainer.scrollLeft + alayaCareTabsContainer.clientWidth >= alayaCareTabsContainer.scrollWidth) {
			alayaCareBtnScrollRight.setAttribute("disabled", true);
		} else {
			alayaCareBtnScrollRight.removeAttribute("disabled");
		}
	}

	if (alayaCareTabsContainer.scrollLeft === 0) {
		alayaCareBtnScrollLeft.setAttribute("disabled", true);
	}
}

//Esign section /Document
var $esignContainer = $(".js-esign-tabs").parent();
var $esignTabsToggleGroup = $(".tabs_esign__toggle-group");
var $esignScrollLeft = $(".js-esign-action--scroll-left");
var $esignScrollRight = $(".js-esign-action--scroll-right");
var esignBtnScrollLeft = document.querySelector('.js-esign-action--scroll-left');
var esignBtnScrollRight = document.querySelector('.js-esign-action--scroll-right');
var esignTabs = document.querySelectorAll('.tabs_esign__toggle');
var esignSelectedTabIndex = 0;
var esignScrollIndex = 0;
var esignScrollWidth = esignTabs[0].clientWidth + 5;
var esignScrollLeft = 0;


var $esignTabs = $(".tabs_esign__toggle");
var $esignActiveTab = $(".tabs_esign__toggle--active");
var $esignTabContents = $(".tabs_esign__tab");

var esignTabsContainer = document.querySelector('.tabs_esign__toggle-group');

var esignTabsContainerWidth = esignTabsContainer.clientWidth;
var tabsScrollableWidth = esignTabsContainer.ScrollWidth;


if (tabsScrollableWidth > esignTabsContainerWidth) {

}

$esignTabContents
	.hide()
	.eq($esignTabs.index($esignActiveTab))
	.show();

function addHHATab1(tabName) {
	var tabToggleGroup = document.querySelector('.tabs_esign__toggle-group');
	var tabsGroup = document.querySelector('.tabs_esign__tabs-group');
	var newTabToggle = document.createElement('div');
	var newTab = document.createElement('div');

	newTabToggle.classList.add('tabs_esign__toggle');
	newTabToggle.innerText = tabName;
	tabToggleGroup.appendChild(newTabToggle);

	newTab.classList.add('tabs_esign__tab');
	newTab.innerText = tabName + " Content";
	newTab.style.display = 'none';
	tabsGroup.appendChild(newTab);

}

esignBtnScrollLeft.addEventListener('click', function () {

	esignScrollIndex--;

	if (esignTabs[esignScrollIndex] != undefined) {
		esignScrollLeft -= esignTabs[esignScrollIndex].clientWidth + 7;
	}
	smoothScroll(esignTabsContainer, { to: esignScrollLeft, callback: checkAlayaCareScrollButtonState });
});

esignBtnScrollRight.addEventListener('click', function () {

	if (esignTabs[esignScrollIndex] != undefined) {
		esignScrollLeft += esignTabs[esignScrollIndex].clientWidth + 7;
	}

	esignScrollIndex++;

	smoothScroll(esignTabsContainer, { to: esignScrollLeft, callback: checkAlayaCareScrollButtonState });

});
$esignTabs.on("click", function () {

	var $hhaTabsNew = $(".tabs_esign__toggle");
	var $esignTabContents = $(".tabs_esign__tab");
	var $tabHHA = $(this);


	var hhaTabIndex = $hhaTabsNew.index($tabHHA);

	// Remove active class from all tabs and add it to the clicked one
	$hhaTabsNew.removeClass("tabs_esign__toggle--active");
	$tabHHA.addClass("tabs_esign__toggle--active");

	// Hide all tab contents and show the content of the clicked tab
	$esignTabContents.hide().eq(hhaTabIndex).show();

	hhaSelectedTabIndex = hhaTabIndex;


	var tabHHAs = $tabHHA[0];
	var tabHHAWidth = tabHHAs.clientWidth;
	var tabHHALeft = tabHHAs.offsetLeft;
	var tabHHARight = tabHHALeft + tabHHAWidth;

	if (tabHHALeft < esignTabsContainer.scrollLeft) {
		smoothScroll(esignTabsContainer, { to: tabHHALeft, callback: checkAlayaCareScrollButtonState });
	}

	if (tabHHARight > (esignTabsContainer.scrollLeft + esignTabsContainerWidth)) {
		smoothScroll(esignTabsContainer, { to: (tabHHARight - esignTabsContainerWidth), callback: checkAlayaCareScrollButtonState });
	}
});

function checkAlayaCareScrollButtonState() {

	if (esignTabsContainer.scrollLeft <= 0) {
		esignBtnScrollLeft.setAttribute("disabled", true);
	} else {
		esignBtnScrollLeft.removeAttribute("disabled");
	}

	if (esignTabsContainer.scrollLeft + esignTabsContainer.clientWidth >= esignTabsContainer.scrollWidth) {
		esignBtnScrollRight.setAttribute("disabled", true);
	} else {
		esignBtnScrollRight.removeAttribute("disabled");
	}
}

if (esignTabsContainer.scrollLeft === 0) {
	esignBtnScrollLeft.setAttribute("disabled", true);
}
