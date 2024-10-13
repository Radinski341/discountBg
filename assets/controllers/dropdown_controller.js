import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["subcategories", "arrow"];

    toggle(event) {
        const subcategories = this.subcategoriesTargets[event.currentTarget.dataset.index];
        const arrow = this.arrowTargets[event.currentTarget.dataset.index];

        subcategories.classList.toggle("expanded");
        arrow.classList.toggle("rotated");
    }
}