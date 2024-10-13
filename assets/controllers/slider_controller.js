import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["value1", "value2", "slider1", "slider2", "highlight", "hiddenSlider1", "hiddenSlider2"];

    connect() {
        this.maxValue = this.slider1Target.max;
        this.slider1Target.value = this.hiddenSlider1Target.value
        this.slider2Target.value = this.hiddenSlider2Target.value
        this.value1Target.textContent = this.hiddenSlider1Target.value + 'Лв'
        this.value2Target.textContent = this.hiddenSlider2Target.value + 'Лв'
        this.updateBackground();

        // console.log(this.slider1Target)
        // console.log(this.hiddenSlider1Target.value)
        // console.log(this.slider2Target)
        // console.log(this.hiddenSlider2Target)
    }

    updateSlider1(event) {
        const newValue = Math.min(Number(event.target.value), Number(this.slider2Target.value));
        this.slider1Target.value = newValue;
        this.value1Target.textContent = `${newValue} Лв`;
        this.hiddenSlider1Target.value = newValue;
        this.updateBackground();
    }

    updateSlider2(event) {
        const newValue = Math.max(Number(event.target.value), Number(this.slider1Target.value));
        this.slider2Target.value = newValue;
        this.value2Target.textContent = `${newValue} Лв`;
        this.hiddenSlider2Target.value = newValue;
        this.updateBackground();
    }

    updateBackground() {
        const value1 = Number(this.slider1Target.value);
        const value2 = Number(this.slider2Target.value);
        const maxValue = Number(this.maxValue);

        const percentage1 = (value1 / maxValue) * 100;
        const percentage2 = (value2 / maxValue) * 100;

        this.highlightTarget.style.left = `${percentage1}%`;
        this.highlightTarget.style.width = `${percentage2 - percentage1}%`;
    }
}
