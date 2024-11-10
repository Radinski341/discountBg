import {Controller} from "@hotwired/stimulus";
import Swal from "sweetalert2";


export default class extends Controller{
    static targets = ['status', 'statusDescription']
    static values = {
        changeStatusUrl: String,
        orderId: Number
    }
    async changeOrderStatus(event){
        let statusDescription = null;
        let status = event.currentTarget.dataset.statusType
        const { value: text, isConfirmed } = await Swal.fire({
            input: "textarea",
            inputLabel: "Status description",
            inputPlaceholder: "Describe status here if needed ...",
            inputAttributes: {
                "aria-label": "Type your message here"
            },
            showCancelButton: true
        });
        if (text) {
            statusDescription = text;
        }

        if(isConfirmed) {
            fetch(this.changeStatusUrlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status: status,
                    orderId: this.orderIdValue,
                    statusDescription: statusDescription
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if( data.error){
                        Swal.fire({
                            title: data.error,
                            icon: 'error'
                        });
                    }else{
                        Swal.fire({
                            title: 'You successfully changed the status to '+ status,
                            icon: 'success'
                        });
                        location.reload();
                    }

                }) // Log the raw response data
                .catch(error => console.error('Error:', error));
        }
    }

    getOrderStatus(){
        let transactionId = this.orderIdValue
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
                return response.headers; // Use text() instead of json()
            })
            .then(data => {
                Swal.fire({
                    title: 'Status: '+ data.get('status'),
                    text: data.get('statusdescription'),
                    imageUrl: data.get('image'),
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: "Custom image"
                });
            }) // Log the raw response data
            .catch(error => console.error('Error:', error));
    }
}