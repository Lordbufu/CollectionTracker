/* static-elements.js:
        This script file, deals with the static banner and controller elements.
        So i can exclude this function from mobile devices, that have limited screen space.
*/
let header, control, sticky;

function initStatic() {
    header = document.getElementById('sub-grid-1');
    control = document.getElementById('sub-grid-2');

    window.onscroll = function() { onScroll(); }
    if(header.offsetTop) { sticky = header.offsetTop; }
}

/*  onScroll():
        This function makes the Header and Controller sub-grid sticky, so you can still use them while scrolling large collections.
        I do this by comparing the scroll position, against he offsetTop from the header element.
        If the user is on the user or admin page, i also make the controller sticky.
 */
function onScroll() {
    if(window.scrollY > sticky) {
        if(user === 'User' || user === 'Administrator') {
            control.classList.add('sticky'), control.style.top = '5.5REM';
        }

        return header.classList.add('sticky');
    } else {
        if(user === 'User' || user === 'Administrator') {
            control.classList.remove('sticky'), control.removeAttribute('style');
        }

        return header.classList.remove('sticky');                                                                                                        
    }
}