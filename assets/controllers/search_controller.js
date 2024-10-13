import {Controller} from "@hotwired/stimulus";
import {useClickOutside} from "stimulus-use";

export default class extends Controller{

    static targets = ['searchInput', 'searchPreviewProducts']
    static values = {
        url: String
    }
    connect() {
        useClickOutside(this);
    }

    onInputChange(event){
        fetch(this.urlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                query: this.searchInputTarget.value
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                this.searchPreviewProductsTarget.innerHTML = data
            })
            .catch(error => console.error('Error:', error));
    }

    clickOutside(event){
        this.searchPreviewProductsTarget.innerHTML = ""
        this.searchPreviewProductsTarget.style.border = 'none'
    }
}