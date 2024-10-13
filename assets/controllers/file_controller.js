import { Controller } from "stimulus";

export default class extends Controller {

    delete(event){

        const listItem = event.currentTarget.parentNode
        const xhr = new XMLHttpRequest();
        xhr.open("DELETE", event.currentTarget.dataset.url, true);

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                // Process completed successfully
                listItem.remove();
                alert('Item successfully deleted');
            } else {
                // Handle errors here
                alert("Error occurred during processing.");
            }
            // Enable the button after completion (success or error)
        };

        xhr.onerror = () => {
            // Handle errors here
            alert("Error occurred during processing.");
            // Enable the button after completion (success or error)
        };
        xhr.send();
    }
}