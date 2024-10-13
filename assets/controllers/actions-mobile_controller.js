import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["open", "close", "container"];

    connect() {
        this.openTarget.addEventListener("click", () => this.open());
        this.closeTarget.addEventListener("click", () => this.close());
    }

    open() {
        this.containerTarget.classList.add("active-actions-container");
    }

    close() {
        this.containerTarget.classList.remove("active-actions-container");
    }
}
