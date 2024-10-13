import { Controller } from 'stimulus';
import carousel from "bootstrap/js/src/carousel";

export default class extends Controller {
    static targets = ["slide", "carousel", "prevButton", "nextButton"];

    connect() {
        this.carouselTranslationValue = 0;
        this.carouselTranslation = 0;
        this.resizeHandler = this.handleResize.bind(this);
        window.addEventListener('resize', this.resizeHandler);
        this.handleResize();
    }

    disconnect() {
        window.removeEventListener('resize', this.resizeHandler);
    }

    handleResize() {
        if (window.innerWidth > 600) {
            this.initializeCarousel();
        } else {
            this.destroyCarousel();
        }
    }

    initializeCarousel() {
        let computedStyle = getComputedStyle(this.slideTargets[0]);
        let width = parseInt(computedStyle.width.replace('px', ''));
        let marginRight = parseInt(computedStyle.marginRight.replace('px', ''));
        this.carouselTranslationValue = width + marginRight;
        this.carouselTranslation = 0;

        this.carouselTargets.forEach(carousel => {
            this.updateButtons(carousel.closest("[data-controller='carousel']"));
        });

        this.carouselTargets.forEach(carousel => {
            carousel.querySelector('.carousel-inner').style.transform = `translateX(0px)`;
            carousel.dataset.translation = '0';
        });
    }

    destroyCarousel() {
        this.carouselTargets.forEach(carousel => {
            carousel.querySelector('.carousel-inner').style.transform = `translateX(0px)`;
            carousel.dataset.translation = '0';
            let prevButton = carousel.querySelector('[data-carousel-target="prevButton"]');
            let nextButton = carousel.querySelector('[data-carousel-target="nextButton"]');
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
        });
    }

    next(event) {
        if (window.innerWidth <= 600) return;
        let carousel = event.currentTarget.closest("[data-controller='carousel']");
        let carouselTranslation = parseInt(carousel.dataset.translation || '0');
        carouselTranslation -= this.carouselTranslationValue;
        carousel.querySelector('.carousel-inner').style.transform = `translateX(${carouselTranslation}px)`;
        carousel.dataset.translation = carouselTranslation;
        this.updateButtons(carousel);
    }

    previous(event) {
        if (window.innerWidth <= 600) return;
        let carousel = event.currentTarget.closest("[data-controller='carousel']");
        let carouselTranslation = parseInt(carousel.dataset.translation || '0');
        carouselTranslation += this.carouselTranslationValue;
        carousel.querySelector('.carousel-inner').style.transform = `translateX(${carouselTranslation}px)`;
        carousel.dataset.translation = carouselTranslation;
        this.updateButtons(carousel);
    }

    updateButtons(carousel) {
        if (window.innerWidth <= 600) return;
        let displayedCarouselWidth = parseInt(getComputedStyle(carousel.parentNode).width.replace("px", ''));
        let translation = parseInt(carousel.dataset.translation || '0');
        let totalWidth = this.carouselTranslationValue * (this.slideTargets.length - 1);
        let prevButton = carousel.querySelector('[data-carousel-target="prevButton"]');
        let nextButton = carousel.querySelector('[data-carousel-target="nextButton"]');
        let carouselWidth = parseInt(getComputedStyle(carousel.querySelector('.carousel-inner')).width.replace('px', ''));

        if (translation >= 0) {
            prevButton.style.display = 'none';
        } else {
            prevButton.style.display = 'block';
        }

        if (Math.abs(translation) + displayedCarouselWidth - this.carouselTranslationValue >= totalWidth || carouselWidth < totalWidth) {
            nextButton.style.display = 'none';
        } else {
            nextButton.style.display = 'block';
        }
    }
}
