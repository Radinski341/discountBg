// assets/controllers/ajax_controller.js

import { Controller } from "stimulus";

export default class extends Controller {

    static targets =  ['dataProcess']

    process(event) {
        // Disable the button and show loading state
        const currentTarget = event.currentTarget
        const buttonText = currentTarget.innerText
        currentTarget.disabled = true;
        currentTarget.innerText = "Processing...";
        const body = JSON.stringify({ website: currentTarget.dataset.website });

        fetch(currentTarget.dataset.url, {
            method: "POST",
            body: body
        }).then(response => {
            if(response.ok){
                console.log(response)
            }else{
                throw new Error('Network response was not ok')
            }
        }).then(data => {
            currentTarget.disabled = false;
            currentTarget.innerText = buttonText;
        }).catch(error => {
            currentTarget.innerText = buttonText;
            currentTarget.disabled = false;
            console.log(error)
            alert("Error occurred during processing.");
        })
    }
}
