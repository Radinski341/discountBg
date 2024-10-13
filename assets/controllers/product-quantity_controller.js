import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    quantity = 1;
    connect() {
        document.querySelector('.quantity-value').value = this.quantity;
    }

    increaseQuantity(){
        this.quantity++;
        document.querySelector('.quantity-value').value = this.quantity;
    }
    decreaseQuantity(){
        if(this.quantity > 1)this.quantity--;
        document.querySelector('.quantity-value').value = this.quantity;
    }
}
