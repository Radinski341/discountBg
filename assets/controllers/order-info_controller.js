import {Controller} from "@hotwired/stimulus";
import Swal from "sweetalert2";

export default class extends Controller{

    static values = {
        statusChoiceColors: Object,
        changeStatusUrl: String,
        getStatusDescriptionUrl: String
    }
    connect() {
        let orderItems = document.querySelectorAll('.order-transaction-choice')
        orderItems.forEach(item => {
            item.style.background = `linear-gradient(to right, white, ${this.statusChoiceColorsValue[item.dataset.transactionStatus]})`
        })
    }

    async changeStatusValue(event){
        let currentTarget = event.currentTarget;
        let transactionId = event.currentTarget.dataset.transactionId
        let status = event.currentTarget.value
        let statusDescription = null;

        const { value: text, isConfirmed } = await Swal.fire({
            input: "textarea",
            inputLabel: "Status description",
            inputPlaceholder: "Describe status here if needed ...",
            inputAttributes: {
                "aria-label": "Type your message here"
            },
            showCancelButton: false
        });
        if (text) {
            statusDescription = text;
        }

        if(isConfirmed){
            fetch(this.changeStatusUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status: status,
                    transactionId: transactionId,
                    statusDescription: statusDescription
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text(); // Use text() instead of json()
                })
                .then(data => {
                    Swal.fire('You successfully changed the status to '+ status);
                    currentTarget.style.background = `linear-gradient(to right, white, ${this.statusChoiceColorsValue[currentTarget.value]})`
                }) // Log the raw response data
                .catch(error => console.error('Error:', error));
        }
    }

    getStatusDescription(event){
        let transactionId = event.currentTarget.dataset.transactionId
        fetch(this.getStatusDescriptionUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                transactionId: transactionId,
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json(); // Use text() instead of json()
            })
            .then(data => {
                Swal.fire({
                    title: 'Status: '+ data.status,
                    text: data.statusDescription,
                    imageUrl: data.image,
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: "Custom image"
                });
            }) // Log the raw response data
            .catch(error => console.error('Error:', error));
    }
}