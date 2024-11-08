import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["category", "dropdown", "dropdownItems"];

    connect() {
        // Run the calculation initially on load
        this.updateVisibleCategories();
        // Debounce the resize event listener for efficient resizing
        this.debouncedUpdate = this.debounce(this.updateVisibleCategories.bind(this), 100);
        window.addEventListener("resize", this.debouncedUpdate);

        // Add event listeners for hover to show/hide dropdown
        this.categoryTargets.forEach(category => {
            category.addEventListener("mouseenter", () => this.showDropdown(category));
            category.addEventListener("mouseleave", () => this.hideDropdown(category));
        })

    }

    disconnect() {
        window.removeEventListener("resize", this.debouncedUpdate);
        this.categoryTargets.forEach(category => {
            category.removeEventListener("mouseenter", () => this.showDropdown(category));
            category.removeEventListener("mouseleave", () => this.hideDropdown(category));
        })
    }

    updateVisibleCategories() {
        const dropdown = this.dropdownTarget;
        const dropdownItems = this.dropdownItemsTarget;

        // Clear previous dropdown items and reset visibility
        dropdown.classList.add("d-none");
        dropdownItems.innerHTML = "";

        let availableWidth = window.innerWidth; // Use window.innerWidth to account for screen width
        if(availableWidth < 950){
            availableWidth -= 400;
        }
        let usedWidth = 0;
        const dropdownEntries = [];

        // Reset visibility on all categories before calculating
        this.categoryTargets.forEach((category) => {
            category.classList.remove("d-none");
        });

        // Calculate visible categories based on available width
        this.categoryTargets.forEach((category) => {
            usedWidth += category.offsetWidth;

            if (usedWidth + 200 > availableWidth) {
                // If this category exceeds space, hide it and add to dropdown
                category.classList.add("d-none");
                dropdownEntries.push(category.cloneNode(true)); // Clone for dropdown
            }
        });

        // Show the dropdown if there are hidden entries
        if (dropdownEntries.length > 0) {
            dropdown.classList.remove("d-none");
            dropdownEntries.forEach((entry) => {
                entry.classList.remove("d-none");
                dropdownItems.appendChild(entry); // Append to dropdown
            });
        }
    }

    showDropdown(target) {
        target.childNodes[3]?.classList.add("show");
    }

    hideDropdown(target) {
        target.childNodes[3]?.classList.remove("show");
    }

    debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
}
