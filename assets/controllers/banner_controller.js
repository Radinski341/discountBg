import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["track"];
    static values = { interval: { type: Number, default: 7000 } };

    connect() {
        this.currentIndex = 0;
        this.totalItems = this.trackTarget.children.length;
        this.updateItemsPerView();
        this.maxIndex = Math.ceil(this.totalItems / this.itemsPerView) - 1; // Maximum index based on the number of full views

        window.addEventListener('resize', this.handleResize.bind(this));

        this.startAutoScroll();
    }

    updateItemsPerView() {
        const screenWidth = window.innerWidth;

        if (screenWidth > 900) {
            this.itemsPerView = 3;
        } else if (screenWidth > 550) {
            this.itemsPerView = 2;
        } else {
            this.itemsPerView = 1;
        }

        // Update maxIndex when itemsPerView changes
        this.maxIndex = Math.ceil(this.totalItems / this.itemsPerView) - 1;
    }

    handleResize() {
        const previousItemsPerView = this.itemsPerView;
        this.updateItemsPerView();

        // If itemsPerView changes, reset the view
        if (previousItemsPerView !== this.itemsPerView) {
            this.currentIndex = Math.min(this.currentIndex, this.maxIndex);
            this.updateView();
        }
    }


    startAutoScroll() {
        this.timer = setInterval(() => this.scroll(), this.intervalValue);
    }

    scroll() {
        // Scroll by exactly 1 viewport width at a time
        this.currentIndex += 1;

        // Reset to the start when reaching the last full set of items
        if (this.currentIndex > this.maxIndex) {
            this.currentIndex = 0; // Reset to the first view when reaching the end
        }

        // Scroll by the width of the viewport (100vw * currentIndex)
        this.trackTarget.style.transform = `translateX(-${this.currentIndex * 100}vw)`;
    }

    disconnect() {
        clearInterval(this.timer);
    }
}
