import {Controller} from "stimulus";

import Swal from "sweetalert2";
export default class extends Controller{
    static values = {
        deleteEndpoint: String,
        changeQuantityEndpoint: String,
        totalQuantity: Number
    }



    removeOrder(event){
        let orderId = event.currentTarget.dataset.removeOrderId
        let productId = event.currentTarget.dataset.productId;
        let productChoiceId = event.currentTarget.dataset.productChoiceId || null;
        let quantity = parseInt(event.currentTarget.dataset.productQuantity);
        let totalQuantity = parseInt(document.getElementById('total-cart-items').innerHTML)
        Swal.fire({
            title: "Сигурни ли сте че искате да истриете този продукт?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Не",
            confirmButtonText: "Да, изтрий"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(this.deleteEndpointValue, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        orderId: orderId,
                        productId: productId,
                        productChoiceId: productChoiceId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    this.element.innerHTML = data
                    document.getElementById('total-cart-items').innerHTML = (totalQuantity - quantity).toString();
                })
                    .catch(error => console.error('Error:', error));
                }
        });
    }

    changeQuantity(event){
        let changeDirection = event.currentTarget.dataset.quantityChangeDirection;
        let orderId = event.currentTarget.dataset.quantityChangeId;
        let productId = event.currentTarget.dataset.productId;
        let productChoiceId = event.currentTarget.dataset.productChoiceId || null;

        fetch(this.changeQuantityEndpointValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                orderId: orderId,
                changeDirection: changeDirection,
                productId: productId,
                productChoiceId: productChoiceId
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                this.element.innerHTML = data
                document.getElementById('total-cart-items').innerHTML = event.target.dataset.quantityTotal
            })
            .catch(error => console.error('Error:', error));
    }
}