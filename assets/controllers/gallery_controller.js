import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["mainImage", "galleryImage"];

    connect() {
        this.currentIndex = 0; // Track the currently displayed image index
        this.images = this.galleryImageTargets.map((img) => img.src); // Array of all images

        // Set up swipe detection for mobile
        this.touchStartX = 0;
        this.touchEndX = 0;
        this.mainImageTarget.addEventListener("touchstart", this.handleTouchStart.bind(this), false);
        this.mainImageTarget.addEventListener("touchend", this.handleTouchEnd.bind(this), false);

        this.showImage(this.currentIndex); // Display the first image initially
    }

    // Show the previous image
    prevImage() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.showImage(this.currentIndex);
    }

    // Show the next image
    nextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.showImage(this.currentIndex);
    }

    // Show the clicked thumbnail image
    showSelectedImage(event) {
        const selectedIndex = this.galleryImageTargets.indexOf(event.currentTarget);
        if (selectedIndex !== -1) {
            this.currentIndex = selectedIndex;
            this.showImage(this.currentIndex);
        }
    }

    // Display the image at the given index
    showImage(index) {
        this.mainImageTarget.src = this.images[index];

        // Highlight the active thumbnail
        this.galleryImageTargets.forEach((thumb, idx) => {
            thumb.classList.toggle("active-thumbnail", idx === index);
        });
    }

    // Swipe functionality for mobile
    handleTouchStart(event) {
        this.touchStartX = event.changedTouches[0].screenX;
    }

    handleTouchEnd(event) {
        this.touchEndX = event.changedTouches[0].screenX;
        this.handleSwipeGesture();
    }

    handleSwipeGesture() {
        if (this.touchEndX < this.touchStartX) this.nextImage(); // Swipe left to go to the next image
        if (this.touchEndX > this.touchStartX) this.prevImage(); // Swipe right to go to the previous image
    }
}
