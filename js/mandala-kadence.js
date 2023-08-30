(function($) {
    // On page load...
    $(document).ready(() => {
        // Highlight Active Hash Links in Primary Menu
        HashMenuActiveLink();  // In case page is loaded from link call at beginning
        CheckForHash();
        ActivateMobileSearchTab();
        ActivateSettingsLink();
        $(window).on("resize", mandalaWindowResize);
        // Otherwise, add event listener for hash changes
        window.addEventListener("hashchange", HashMenuActiveLink, false);
    });


    // Highlights the active link in a menu if it is a Mandala hash which loads content asynchronously
    const HashMenuActiveLink = () => {
        const hash = window.location.hash;
        // if hash is nothing, #, or #/ do nothing
        if (hash.length < 3)  {
            return;
        }
        // Otherwise, go through each primary and subsite menu items and if one matches, then add class to highlight it
        $.each($('#primary-menu .menu-item a, #subsite-menu .menu-item a'), (n, ael) => {
            const mylink = $(ael).attr('href');
            if (mylink === hash) {
                $('.current-menu-item').removeClass('current-menu-item')
                $(ael).parent().addClass('current-menu-item')
            }
        });
    };

    // Checks whether there is a hash and insures that the mandala class is set for the body
    const CheckForHash = () => {
        const hsh = window.location.hash;
        if (hsh?.length > 2 && !$('body').hasClass('mandala')) {
            $('body').addClass('mandala');
        }
        $('body').removeClass('loading');
    };

    /**
     * Moves to banner for mobile sizes
     */
    const mandalaWindowResize = () => {
        const srchport = $('#basicSearchPortal');
        if ($(window).width() > 1000) {
            if (srchport.parents('#basicAndBrowse').length === 0) {
                const port = $('#basicSearchPortal').detach();
                $('#basicAndBrowse')
                    .prepend(port);
            }
        } else {
            $('#l-column__search.closed').removeClass('closed');
            if (srchport.parents('#basicAndBrowse').length > 0) {
                const port = $('#basicSearchPortal').detach();
                if($('.site-header-main-section-left.site-header-section.site-header-section-left #basicSearchPortal').length === 0) {
                    $('.site-header-main-section-left.site-header-section.site-header-section-left')
                        .append(port);
                }

            }

        }
    }

    const ActivateMobileSearchTab = () => {
        mandalaWindowResize();
        const srch_toggle_rep = $('#main-search-tree-toggle-replica');

        if (!srch_toggle_rep.hasClass('processed')) {
            $('#main-search-tree-toggle-replica').click(() => {
                $('#main-search-tree-toggle').click();
                $('.l-content__rightsidebar.closeSideBar').removeClass('closeSideBar');
            });
            srch_toggle_rep.addClass('processed');
        }
    }

    // Settings link in Main Menu has class "mandalaSettings" which is a React portal for the button to show the settings modal.
    // But the button doesn't display in the menu. So need this function to click it when menu link is clicked.
    const ActivateSettingsLink = () => {
        $('nav#mobile-site-navigation').on('click', 'li.mandalaSettings a', () => {
            $('button#advanced-site-settings').click();
            return false;
        });
    }
})(jQuery);