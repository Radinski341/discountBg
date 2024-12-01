// expand_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["content", "toggleButton"];

    connect() {
        // Set initial state
        this.isExpanded = false;
        this.updateToggleButtonText();
    }

    toggle() {
        this.isExpanded = !this.isExpanded;
        this.contentTarget.style.display = this.isExpanded ? "block" : "none";
        this.updateToggleButtonText();
    }

    updateToggleButtonText() {
        this.toggleButtonTarget.innerText = this.isExpanded ? "Hide" : "Expand";
    }
}
