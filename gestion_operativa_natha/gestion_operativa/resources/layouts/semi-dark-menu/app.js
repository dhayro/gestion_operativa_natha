var App = function() {
    var MediaSize = {
        xl: 1200,
        lg: 992,
        md: 991,
        sm: 576
    };
    var Dom = {
        main: document.querySelector('html, body'),
        id: {
            container: document.querySelector("#container"),
        },
        class: {
            navbar: document.querySelector(".navbar"),
            overlay: document.querySelector('.overlay'),
            search: document.querySelector('.toggle-search'),
            searchOverlay: document.querySelector('.search-overlay'),
            searchForm: document.querySelector('.search-form-control'),
            mainContainer: document.querySelector('.main-container'),
            mainHeader: document.querySelector('.header.navbar')
        }
    }

    var categoryScroll = {
        scrollCat: function() {
            try {
                var sidebarWrapper = document.querySelector('.sidebar-wrapper li.active');
                
                if (sidebarWrapper) {
                    var sidebarWrapperTop = sidebarWrapper.offsetTop - 12;
                    setTimeout(() => {
                        const scroll = document.querySelector('.menu-categories');
                        if (scroll) {
                            scroll.scrollTop = sidebarWrapperTop;
                        }
                    }, 50);
                }
            } catch (error) {
                console.warn('Error in categoryScroll.scrollCat:', error);
            }
        }
    }

    var toggleFunction = {
        sidebar: function($recentSubmenu) {

            var sidebarCollapseEle = document.querySelectorAll('.sidebarCollapse');

            sidebarCollapseEle.forEach(el => {
                el.addEventListener('click', function (sidebar) {
                    sidebar.preventDefault();
                    let getSidebar = document.querySelector('.sidebar-wrapper');

                    if ($recentSubmenu === true) {
                        if (document.querySelector('.collapse.submenu').classList.contains('show')) {
                            document.querySelector('.submenu.show').classList.add('mini-recent-submenu');
                            getSidebar.querySelector('.collapse.submenu').classList.remove('show');
                            getSidebar.querySelector('.collapse.submenu').classList.remove('show');
                            document.querySelector('.collapse.submenu').parentNode.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
                        } else {
                            if (Dom.class.mainContainer.classList.contains('sidebar-closed')) {
                                if (document.querySelector('.collapse.submenu').classList.contains('recent-submenu')) {
                                    getSidebar.querySelector('.collapse.submenu.recent-submenu').classList.add('show');
                                    document.querySelector('.collapse.submenu.recent-submenu').parentNode.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'true');
                                    document.querySelector('.submenu').classList.remove('mini-recent-submenu');
                                } else {
                                    document.querySelector('li.active .submenu').classList.add('recent-submenu');
                                    getSidebar.querySelector('.collapse.submenu.recent-submenu').classList.add('show');
                                    document.querySelector('.collapse.submenu.recent-submenu').parentNode.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'true');
                                    document.querySelector('.submenu').classList.remove('mini-recent-submenu');
                                }
                            }
                        }
                    }
                    Dom.class.mainContainer.classList.toggle("sidebar-closed");
                    Dom.class.mainHeader.classList.toggle('expand-header');
                    Dom.class.mainContainer.classList.toggle("sbar-open");
                    Dom.class.overlay.classList.toggle('show');
                    Dom.main.classList.toggle('sidebar-noneoverflow');
                });
            });
        },
        onToggleSidebarSubmenu: function() {
            var sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (!sidebarWrapper) return; // Exit if sidebar doesn't exist
            
            ['mouseenter', 'mouseleave'].forEach(function(e){
                sidebarWrapper.addEventListener(e, function() {
                    if (document.querySelector('body').classList.contains('alt-menu')) {
                        if (document.querySelector('.main-container').classList.contains('sidebar-closed')) {
                            if (e === 'mouseenter') {
                                var liMenu = document.querySelector('li.menu .submenu');
                                if (liMenu) liMenu.classList.remove('show');
                                
                                var activeSubmenu = document.querySelector('li.menu.active .submenu');
                                if (activeSubmenu) {
                                    activeSubmenu.classList.add('recent-submenu');
                                    var activeMenuEl = document.querySelector('li.menu.active');
                                    if (activeMenuEl) {
                                        var collapseEl = activeMenuEl.querySelector('.collapse.submenu.recent-submenu');
                                        if (collapseEl) {
                                            collapseEl.classList.add('show');
                                            var toggleEl = collapseEl.parentNode.querySelector('.dropdown-toggle');
                                            if (toggleEl) {
                                                toggleEl.setAttribute('aria-expanded', 'true');
                                            }
                                        }
                                    }
                                }
                            } else if (e === 'mouseleave') {
                                let getMenuList = document.querySelectorAll('li.menu');
                                getMenuList.forEach(element => {

                                    var submenuShowEle = element.querySelector('.collapse.submenu.show');

                                    if (submenuShowEle) {
                                        submenuShowEle.classList.remove('show');
                                    }

                                    var submenuExpandedToggleEle = element.querySelector('.dropdown-toggle[aria-expanded="true"]');

                                    if (submenuExpandedToggleEle) {
                                        submenuExpandedToggleEle.setAttribute('aria-expanded', 'false');
                                    }
                                    
                                });
                            }
                        }
                    } else {
                        if (document.querySelector('.main-container').classList.contains('sidebar-closed')) {
                            if (e === 'mouseenter') {
                                var liMenuEl = document.querySelector('li.menu .submenu');
                                if (liMenuEl) liMenuEl.classList.remove('show');

                                if (document.querySelector('li.menu.active .submenu')) {
                                    document.querySelector('li.menu.active .submenu').classList.add('recent-submenu');
                                    var activeEl = document.querySelector('li.menu.active');
                                    if (activeEl) {
                                        var collapseElActive = activeEl.querySelector('.collapse.submenu.recent-submenu');
                                        if (collapseElActive) {
                                            collapseElActive.classList.add('show');
                                            var toggleElActive = collapseElActive.parentNode.querySelector('.dropdown-toggle');
                                            if (toggleElActive) {
                                                toggleElActive.setAttribute('aria-expanded', 'true');
                                            }
                                        }
                                    }
                                }
                                
                            } else if (e === 'mouseleave') {
                                let getMenuList = document.querySelectorAll('li.menu');
                                getMenuList.forEach(element => {

                                    var submenuShowEle = element.querySelector('.collapse.submenu.show');

                                    if (submenuShowEle) {
                                        submenuShowEle.classList.remove('show');
                                    }


                                    var submenuExpandedToggleEle = element.querySelector('.dropdown-toggle[aria-expanded="true"]');

                                    if (submenuExpandedToggleEle) {
                                        submenuExpandedToggleEle.setAttribute('aria-expanded', 'false');
                                    }
                                    
                                });
                            }
                        }
                    }
                    
                });
            });

        },
        offToggleSidebarSubmenu: function () {
            // $('.sidebar-wrapper').off('mouseenter mouseleave');
        },
        overlay: function() {
            // Find overlay element - try both #dismiss and .overlay
            var overlayElement = document.querySelector('#dismiss') || document.querySelector('.overlay');
            
            if (overlayElement) {
                overlayElement.addEventListener('click', function () {
                    // hide sidebar
                    Dom.class.mainContainer.classList.add('sidebar-closed');
                    Dom.class.mainContainer.classList.remove('sbar-open');
                    // hide overlay
                    if (Dom.class.overlay) {
                        Dom.class.overlay.classList.remove('show');
                    }
                    Dom.main.classList.remove('sidebar-noneoverflow');
                });
            }
        },
        search: function() {

            if (Dom.class.search) {
                
                Dom.class.search.addEventListener('click', function(event) {
                    this.classList.add('show-search');
                    if (Dom.class.searchOverlay) {
                        Dom.class.searchOverlay.classList.add('show');
                    }
                    document.querySelector('body').classList.add('search-active');
                });
                
                if (Dom.class.searchOverlay) {
                    Dom.class.searchOverlay.addEventListener('click', function(event) {
                        this.classList.remove('show');
                        Dom.class.search.classList.remove('show-search');
                        document.querySelector('body').classList.remove('search-active');
                    });
                }
                
                var searchClose = document.querySelector('.search-close');
                if (searchClose) {
                    searchClose.addEventListener('click', function(event) {
                        event.stopPropagation();
                        if (Dom.class.searchOverlay) {
                            Dom.class.searchOverlay.classList.remove('show');
                        }
                        Dom.class.search.classList.remove('show-search');
                        document.querySelector('body').classList.remove('search-active');
                        if (Dom.class.searchForm) {
                            Dom.class.searchForm.value = ''
                        }
                    });
                }
            }

        },
        themeToggle: function (layoutName) {

            var togglethemeEl = document.querySelector('.theme-toggle');
            var getBodyEl = document.body;
            
            // Only add event listener if element exists
            if (togglethemeEl) {
                togglethemeEl.addEventListener('click', function() {
                    
                    var getLocalStorage = sessionStorage.getItem("theme");
                    if (!getLocalStorage) return; // Exit if no theme in session
                    
                    var parseObj = JSON.parse(getLocalStorage);

                    if (parseObj.settings.layout.darkMode) {

                        var getObjectSettings = parseObj.settings.layout;

                        var newParseObject = {...getObjectSettings, darkMode: false};

                        var newObject = { ...parseObj, settings: { layout: newParseObject }}

                        sessionStorage.setItem("theme", JSON.stringify(newObject))
                        
                        var getUpdatedLocalObject = sessionStorage.getItem("theme");
                        var getUpdatedParseObject = JSON.parse(getUpdatedLocalObject);

                        if (!getUpdatedParseObject.settings.layout.darkMode) {
                            document.body.classList.remove('dark')
                            let ifStarterKit = document.body.getAttribute('page') === 'starter-pack' ? true : false;
                            if (ifStarterKit) {
                                // document.querySelector('.navbar-logo').setAttribute('src', '../../src/assets/img/logo2.svg')
                            } else {
                                // document.querySelector('.navbar-logo').setAttribute('src', getUpdatedParseObject.settings.layout.logo.lightLogo)
                            }
                        }
                        
                    } else {

                        var getObjectSettings = parseObj.settings.layout;

                        var newParseObject = {...getObjectSettings, darkMode: true};

                        var newObject = { ...parseObj, settings: { layout: newParseObject }}

                        sessionStorage.setItem("theme", JSON.stringify(newObject))
                        
                        var getUpdatedLocalObject = sessionStorage.getItem("theme");
                        var getUpdatedParseObject = JSON.parse(getUpdatedLocalObject);

                        if (getUpdatedParseObject.settings.layout.darkMode) {
                            document.body.classList.add('dark')

                            let ifStarterKit = document.body.getAttribute('page') === 'starter-pack' ? true : false;

                            if (ifStarterKit) {
                                // document.querySelector('.navbar-logo').setAttribute('src', '../../src/assets/img/logo.svg')
                            } else {
                                // document.querySelector('.navbar-logo').setAttribute('src', getUpdatedParseObject.settings.layout.logo.darkLogo)
                            }
                            
                        }
                        
                    }
                    
                    // sessionStorage.clear()
                })
            }
            
        }
    }

    var inBuiltfunctionality = {
        mainCatActivateScroll: function() {

            if (document.querySelector('.menu-categories')) {
            
                const ps = new PerfectScrollbar('.menu-categories', {
                    wheelSpeed:.5,
                    swipeEasing:!0,
                    minScrollbarLength:40,
                    maxScrollbarLength:300
                });

            }
        },
        notificationScroll: function() {

            if (document.querySelector('.notification-scroll')) {
                const notificationS = new PerfectScrollbar('.notification-scroll', {
                    wheelSpeed:.5,
                    swipeEasing:!0,
                    minScrollbarLength:40,
                    maxScrollbarLength:300
                });
            }
            
        },
        preventScrollBody: function() {
            var nonScrollableElement = document.querySelectorAll('#sidebar, .user-profile-dropdown .dropdown-menu, .notification-dropdown .dropdown-menu,  .language-dropdown .dropdown-menu')

            if (nonScrollableElement.length === 0) {
                return; // Exit if no elements found
            }

            var preventScrolling = function(e) {
                e = e || window.event;
                if (e.preventDefault)
                    e.preventDefault();
                e.returnValue = false;  

                nonScrollableElement.forEach(el => {
                    el.scrollTop -= e.wheelDeltaY;
                });
            }

            nonScrollableElement.forEach(preventScroll => {
                preventScroll.addEventListener('mousewheel', preventScrolling);
                preventScroll.addEventListener('DOMMouseScroll', preventScrolling);
            });
        },
        searchKeyBind: function() {

            if (Dom.class.search) {
                // Only bind if Mousetrap is available
                if (typeof Mousetrap !== 'undefined') {
                    Mousetrap.bind('ctrl+/', function() {
                        document.body.classList.add('search-active');
                        Dom.class.search.classList.add('show-search');
                        if (Dom.class.searchOverlay) {
                            Dom.class.searchOverlay.classList.add('show');
                        }
                        if (Dom.class.searchForm) {
                            Dom.class.searchForm.focus();
                        }
                        return false;
                    });
                }
            }

        },
        bsTooltip: function() {
            var bsTooltip = document.querySelectorAll('.bs-tooltip')
            for (let index = 0; index < bsTooltip.length; index++) {
                var tooltip = new bootstrap.Tooltip(bsTooltip[index])
            }
        },
        bsPopover: function() {
            var bsPopover = document.querySelectorAll('.bs-popover')
            for (let index = 0; index < bsPopover.length; index++) {
                var popover = new bootstrap.Popover(bsPopover[index])
            }
        },
        onCheckandChangeSidebarActiveClass: function() {
            if (document.body.classList.contains('alt-menu')) {
                if (document.querySelector('.sidebar-wrapper [aria-expanded="true"]')) {
                    document.querySelector('.sidebar-wrapper li.menu.active [aria-expanded="true"]').setAttribute('aria-expanded', 'false');
                }
            }
        },
        MaterialRippleEffect: function() {
            let getAllBtn = document.querySelectorAll('button.btn, a.btn');
            
            getAllBtn.forEach(btn => {
    
                if (!btn.classList.contains('_no--effects')) {
                    btn.classList.add('_effect--ripple');
                }
                
            });
    
            if (document.querySelector('._effect--ripple')) {
                Waves.attach('._effect--ripple', 'waves-light');
                Waves.init();
            }
        }
    }

    var _mobileResolution = {
        onRefresh: function() {
            var windowWidth = window.innerWidth;
            if ( windowWidth <= MediaSize.md ) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
            }
        },
        
        onResize: function() {
            window.addEventListener('resize', function(event) {
                event.preventDefault();
                var windowWidth = window.innerWidth;
                if ( windowWidth <= MediaSize.md ) {
                    toggleFunction.offToggleSidebarSubmenu();
                }
            });
        }
        
    }

    var _desktopResolution = {
        onRefresh: function() {
            var windowWidth = window.innerWidth;
            if ( windowWidth > MediaSize.md ) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
                toggleFunction.onToggleSidebarSubmenu();
            }
        },
        
        onResize: function() {
            window.addEventListener('resize', function(event) {
                event.preventDefault();
                var windowWidth = window.innerWidth;
                if ( windowWidth > MediaSize.md ) {
                    toggleFunction.onToggleSidebarSubmenu();
                }
            });
        }
        
    }

    function sidebarFunctionality() {
        function sidebarCloser() {

            // Validate that required elements exist
            if (!Dom.id.container || !Dom.class.overlay) {
                return; // Exit if required elements don't exist
            }

            if (window.innerWidth <= 991 ) {

                if (!document.querySelector('body').classList.contains('alt-menu')) {

                    Dom.id.container.classList.add("sidebar-closed");
                    Dom.class.overlay.classList.remove('show');
                } else {
                    if (Dom.class.navbar) {
                        Dom.class.navbar.classList.remove("expand-header");
                    }
                    Dom.class.overlay.classList.remove('show');
                    Dom.id.container.classList.remove('sbar-open');
                    Dom.main.classList.remove('sidebar-noneoverflow');
                }

            } else if (window.innerWidth > 991 ) {

                if (!document.querySelector('body').classList.contains('alt-menu')) {

                    Dom.id.container.classList.remove("sidebar-closed");
                    if (Dom.class.navbar) {
                        Dom.class.navbar.classList.remove("expand-header");
                    }
                    Dom.class.overlay.classList.remove('show');
                    Dom.id.container.classList.remove('sbar-open');
                    Dom.main.classList.remove('sidebar-noneoverflow');
                } else {
                    Dom.main.classList.add('sidebar-noneoverflow');
                    Dom.id.container.classList.add("sidebar-closed");
                    if (Dom.class.navbar) {
                        Dom.class.navbar.classList.add("expand-header");
                    }
                    Dom.class.overlay.classList.add('show');
                    Dom.id.container.classList.add('sbar-open');

                    var expandedElement = document.querySelector('.sidebar-wrapper [aria-expanded="true"]');
                    if (expandedElement && expandedElement.parentNode) {
                        var collapseEl = expandedElement.parentNode.querySelector('.collapse');
                        if (collapseEl) {
                            collapseEl.classList.remove('show');
                        }
                    }

                }
            }
        }

        function sidebarMobCheck() {
            var mainContainer = document.querySelector('.main-container');
            if (!mainContainer) return; // Exit if main-container doesn't exist
            
            if (window.innerWidth <= 991 ) {

                if ( mainContainer.classList.contains('sbar-open') ) {
                    return;
                } else {
                    sidebarCloser()
                }
            } else if (window.innerWidth > 991 ) {
                sidebarCloser();
            }
        }

        sidebarCloser();

        window.addEventListener('resize', function(event) {
            sidebarMobCheck();
        });

    }

    return {
        init: function(Layout) {
            toggleFunction.overlay();
            toggleFunction.search();
            toggleFunction.themeToggle(Layout);
            
            /*
                Desktop Resoltion fn
            */
            _desktopResolution.onRefresh();
            _desktopResolution.onResize();

            /*
                Mobile Resoltion fn
            */
            _mobileResolution.onRefresh();
            _mobileResolution.onResize();

            sidebarFunctionality();

            /*
                In Built Functionality fn
            */
            inBuiltfunctionality.mainCatActivateScroll();
            inBuiltfunctionality.notificationScroll();
            inBuiltfunctionality.preventScrollBody();
            inBuiltfunctionality.searchKeyBind();
            inBuiltfunctionality.bsTooltip();
            inBuiltfunctionality.bsPopover();
            inBuiltfunctionality.onCheckandChangeSidebarActiveClass();
            inBuiltfunctionality.MaterialRippleEffect();
        }
    }

}();

window.addEventListener('load', function() {
    App.init('layout');
})