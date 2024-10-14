/* static-elements.js:
        This script file, deals with the static banner and controller elements.
        So i can exclude this function from mobile devices, that have limited screen space.
*/
let header, control, sticky;

function initStatic() {
    /* onscroll event and elements, to make the banner and controllers sticky. */
    header = document.getElementById( "sub-grid-1" );
    control = document.getElementById( "sub-grid-2" );

    window.onscroll = function() {
        onScroll();
        return
    }

    if( header.offsetTop ) {
        sticky = header.offsetTop;
        return;
    }
}

/*  onScroll():
        This function makes the Header and Controller sub-grid sticky, so you can still use them while scrolling large collections.
        I do this by comparing the scroll position, against he offsetTop from the header element.
        If the user is on the user or admin page, i also make the controller sticky.
 */
function onScroll() {
    /* If the window scoll is greater then the sticky position: */
    if( window.scrollY > sticky ) {
        /* I check what page the user is on, and sety the controller to be 'sticky', and adjust it to be below the header, then i also make the header 'sticky'. */
        if( window.location.pathname === "/beheer" || window.location.pathname === "/gebruik" ) {
            control.classList.add( "sticky" );
            control.style.top = "5.5REM";
        }

        header.classList.add( "sticky" );

        return;
    /* If it is not, i do the reverse of what i did above, so the page returns to it's normal state again. */
    } else {
        if( window.location.pathname === "/beheer" || window.location.pathname === "/gebruik" ) {
            control.classList.remove( "sticky" );
            control.removeAttribute( "style" );
        }

        header.classList.remove( "sticky" );                                                                                                        

        return;
    }
}