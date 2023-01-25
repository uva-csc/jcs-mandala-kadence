(function($) {
    // On page load...
    $(document).ready(() => {
        // Highlight Active Hash Links in Primary Menu
        HashMenuActiveLink();  // In case page is loaded from link call at beginning
        CheckForHash();
        ActivateMobileSearchTab();
        $(window).on("resize", ActivateMobileSearchTab);
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


    const ActivateMobileSearchTab = () => {
       $('#mobile-sidebar-tab').on('click', () => {
           $("aside#secondary").show();
           $('.l-content__rightsidebar.closeSideBar').removeClass('closeSideBar');
           $('#mobile-sidebar-tab').hide();
           $("button.treeNav-header__closeButton .shanticon-cancel").on('click', () => {
               $('#mobile-sidebar-tab').show();

           });
        });

    }
})(jQuery);