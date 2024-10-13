//------------------------------------------Navigation-----------------------------------------------------
// Enable dropdown on hover
let dropdowns = document.querySelectorAll('.activate-dropdown');
dropdowns.forEach(function (dropdown) {
    dropdown.addEventListener('mouseenter', function () {
        dropdown.childNodes.item(1).classList.add('show')
        dropdown.childNodes.item(3).classList.add('show')
    });
    dropdown.addEventListener('mouseleave', function () {
        dropdown.childNodes.item(1).classList.remove('show')
        dropdown.childNodes.item(3).classList.remove('show')
    });
});


let responsiveNavItems = document.querySelectorAll('.responsive-nav-item');
const responsiveNav = document.querySelector('.responsive-nav');
const responsiveNavToggleList = document.querySelector('.responsive-nav-toggle-items')
const responsiveNavVisibleList = document.querySelector('.responsive-nav-visible-items')
const resizeObserver = new ResizeObserver(handleResize);
let restOfLinks = document.querySelector('.rest-of-links')
let lastWidth = responsiveNav.clientWidth; // Store the last observed width

// Start observing the resizable element
resizeObserver.observe(responsiveNav);

// Trigger handleResize on page load
let pageLoaded = true;

function handleResize(entries) {
    const navBarWidth = responsiveNav.clientWidth;
    const widthDifference = Math.abs(navBarWidth - lastWidth);

    // Check if the width difference is greater than or equal to 10 pixels
    if (widthDifference >= 16 || widthDifference <= -16 || pageLoaded) {
        let itemsWidthCounter = 0;

        responsiveNavToggleList.classList.remove('show');
        responsiveNavItems.forEach(item => {
            itemsWidthCounter += item.clientWidth;

            if (navBarWidth - itemsWidthCounter - 50 >= 1) {
                item.classList.replace('dropend', 'dropdown');
                item.classList.add('responsive-nav-item');
                responsiveNavVisibleList.appendChild(item);
            } else {
                responsiveNavVisibleList.removeChild(item);
                responsiveNavToggleList.appendChild(item);
                item.classList.remove('responsive-nav-item');
                item.classList.replace('dropdown', 'dropend');
            }

            if (responsiveNavToggleList.childNodes.length === 1) {
                restOfLinks.classList.add('d-none');
            } else {
                restOfLinks.classList.remove('d-none');
            }
        });

        // Update the last observed width
        lastWidth = navBarWidth;
        pageLoaded = false;
    }

}