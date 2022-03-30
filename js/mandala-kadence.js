(function($) {
    // On page load...
    $(document).ready(() => {
        // Highlight Active Hash Links in Primary Menu
        HashMenuActiveLink();  // In case page is loaded from link call at beginning
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
        // Otherwise, go through each primary menu item and if one matches, then add class to highlight it
        $.each($('#primary-menu .menu-item a'), (n, ael) => {
            const mylink = $(ael).attr('href');
            if (mylink == hash) {
                $('.current-menu-item').removeClass('current-menu-item')
                $(ael).parent().addClass('current-menu-item')
            }
        });
    };
})(jQuery);