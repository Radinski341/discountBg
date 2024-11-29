// assets/controllers/ajax_controller.js

import { Controller } from "stimulus";

export default class extends Controller {

    static targets =  ['dataProcess', 'logsContainer']
    static values = {
        getFilesUrl: String,
        processCategoriesUrl: String,
        processProductsUrl: String
    }

    async process(event) {
        // Disable the button and show loading state
        const currentTarget = event.currentTarget
        const buttonText = currentTarget.innerText
        currentTarget.disabled = true;
        currentTarget.innerText = "Processing...";
        let finalLogs = []

        const files = await  this.getFiles(currentTarget);
        if(files.length === 0){
            console.log('No files')
            return;
        }

        this.logsContainerTarget.innerHTML = files.map(file => `<p>${file} ready for proccessig</p>`).join('')
        for (const file of files){
            this.logsContainerTarget.innerHTML += `<p>Proccessing categories of file:  ${file}...</p>`
            const response = await this.processCategories(currentTarget, file)
            this.logsContainerTarget.innerHTML += `<p>Categories of file: ${response}</p>`
        }

        for (const file of files){
            this.logsContainerTarget.innerHTML += `<p>Proccessing products of file: ${file}...</p>`
            const response = await this.processProducts(currentTarget, file)
            this.logsContainerTarget.innerHTML += `<p>Products of ${file} processed successfully</p>`
        }


        console.log()
    }

    async getFiles(currentTarget){
        const getFilesBody = JSON.stringify({ website: currentTarget.dataset.website });

        const response = await fetch(this.getFilesUrlValue, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: getFilesBody
        })

        if(!response.ok){
            this.logsContainerTarget.innerHTML = 'Network response was not ok'
            return []
        }

        const files = await response.json();

        if(!files){
            this.logsContainerTarget.innerHTML = 'No files'
            return []
        }

        return files
    }

    async processCategories(currentTarget, file){
        const processCategoriesBody = JSON.stringify({ website: currentTarget.dataset.website, fileName: file});
        const response = await fetch(this.processCategoriesUrlValue, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: processCategoriesBody
        })

        if(!response.ok){
            this.logsContainerTarget.innerHTML += 'Network response was not ok'
        }

        const data = await response.json();
        return data.message

    }

    async processProducts(currentTarget, file){
        const processProductsBody = JSON.stringify({ website: currentTarget.dataset.website, fileName: file});
        const response = await fetch(this.processProductsUrlValue, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: processProductsBody
        })

        if(!response.ok){
            this.logsContainerTarget.innerHTML += 'Network response was not ok'
        }

        const data = await response.json();
        console.log(data)
        return data

    }
}
