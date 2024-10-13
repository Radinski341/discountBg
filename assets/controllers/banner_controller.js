import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["track"];
    static values = { interval: { type: Number, default: 7000 } };

    connect() {
        this.currentIndex = 0;
        this.totalItems = this.trackTarget.children.length;
        this.itemsPerView = 3; // We are showing 3 items per view
        this.maxIndex = Math.ceil(this.totalItems / this.itemsPerView) - 1; // Maximum index based on the number of full views

        this.startAutoScroll();
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
