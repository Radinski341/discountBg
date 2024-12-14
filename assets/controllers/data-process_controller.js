// assets/controllers/ajax_controller.js

import { Controller } from "stimulus";

export default class extends Controller {

    static targets =  ['dataProcess', 'logsContainer']
    static values = {
        getFilesUrl: String,
        processCategoriesUrl: String,
        processProductsUrl: String,
        processChoicesUrl: String,
        deleteProductsAndChoicesUrl: String
    }

    async process(event) {
        const start = Date.now();
        // Helper function to get the current time in HH.MM.SS format
        const getTime = () => {
            return new Date().toLocaleTimeString('en-GB', { hour12: false });
        };

        const scrollToBottom = () => {
            this.logsContainerTarget.parentNode.scrollTop = this.logsContainerTarget.clientHeight;
        };


        // Disable the button and show loading state
        const currentTarget = event.currentTarget;
        const buttonText = currentTarget.innerText;
        currentTarget.disabled = true;
        currentTarget.innerText = "Processing...";
        let choicesArray = [];
        let totalProducts = 0
        const files = await this.getFiles(currentTarget);

        if (files.length === 0) {
            let removedProducts = 0
            let removedChoices = 0
            let productsForDelete = true
            while (productsForDelete){
                const deletionResponse = await this.deleteProductsAndChoices(currentTarget);
                removedProducts += deletionResponse['removedProducts']
                removedChoices += deletionResponse['removedChoices']
                this.logsContainerTarget.innerHTML += `<p class="text-success">${getTime()} - Removed | Products: ${removedProducts} | Choices: ${removedChoices}`
                if(deletionResponse['productsForDeleteLeft'] === 0){
                    productsForDelete = false
                }
            }
            this.logsContainerTarget.innerHTML += `<p>--------------------------------------RESULT---------------------------------------------------</p>`
            this.logsContainerTarget.innerHTML += `<p><span class="text-success"> NEW PRODUCTS - 0</span> | <span class="text-danger"> REMOVED PRODUCTS - ${removedProducts}</span></p>`
            this.logsContainerTarget.innerHTML += `<p><span class="text-success"> NEW CHOICES  - 0</span> | <span class="text-danger"> REMOVED CHOICES: ${removedChoices}</span></p>`

            const finish = Date.now(); // Record the end time in milliseconds
            const durationInSeconds = (finish - start) / 1000;
            this.logsContainerTarget.innerHTML += `<p>Finished in ${durationInSeconds} seconds</p>`
            currentTarget.disabled = false;
            currentTarget.innerText = buttonText;
            return;
        }

        // Initial log for files ready for processing
        this.logsContainerTarget.innerHTML = files.map(file => `<p>${getTime()} - ${file} ready for processing</p>`).join('');

        for (const file of files) {
            this.logsContainerTarget.innerHTML += `<p>${getTime()} - Processing categories of file: ${file}...</p>`;
            const response = await this.processCategories(currentTarget, file);
            this.logsContainerTarget.innerHTML += `<p>${getTime()} - Categories of file: ${response}</p>`;
        }

        for (const file of files) {
            this.logsContainerTarget.innerHTML += `<p>${getTime()} - Processing products of file: ${file}...</p>`;
            scrollToBottom()
            const response = await this.processProducts(currentTarget, file);
            choicesArray = choicesArray.concat(response['choiceProductsArray']);
            this.logsContainerTarget.innerHTML += `<p class="text-success">${getTime()} - ${file} | New: ${response['newProducts']} | Updated: ${response['existingProducts']} | Choices: ${response['choiceProductsArray'].length}</p>`;
            scrollToBottom()
            if(response['exceptionLogs'].length > 0){
                response['exceptionLogs'].forEach(log => {
                    this.logsContainerTarget.innerHTML += `<p class="text-danger">${getTime()} - ${log.message}</p>`
                    scrollToBottom()
                })
            }
            totalProducts += response['newProducts'];
        }
        let totalNewChoices = 0;
        if(choicesArray.length > 0){
            let choicesBatch = 500
            let batchCount = 1;
            while(choicesArray.length > choicesBatch * (batchCount - 1)){
                let choicesForProcess = choicesArray.slice(choicesBatch * (batchCount - 1), choicesBatch * batchCount)
                this.logsContainerTarget.innerHTML += `<p>${getTime()} - Proccessing batch ${batchCount} / ${Math.ceil(choicesArray.length / choicesBatch)} with ${choicesForProcess.length} choices...</p>`
                let choicesResponse = await this.processChoices(currentTarget, choicesForProcess);
                console.log(choicesResponse)
                totalNewChoices += choicesResponse['newChoices'];
                this.logsContainerTarget.innerHTML += `<p class="text-success">${getTime()} - Choices | New: ${choicesResponse['newChoices']} | Updated: ${choicesResponse['existingChoices']}</p>`;
                if(choicesResponse['exceptionLogs'].length > 0){
                    choicesResponse['exceptionLogs'].forEach(log => {
                        this.logsContainerTarget.innerHTML += `<p class="text-danger">${getTime()} - ${log.message}</p>`
                    })
                }
                scrollToBottom()
                batchCount++
            }

        }
        let removedProducts = 0
        let removedChoices = 0
        let productsForDelete = true
        while (productsForDelete){
            const deletionResponse = await this.deleteProductsAndChoices(currentTarget);
            removedProducts += deletionResponse['removedProducts']
            removedChoices += deletionResponse['removedChoices']
            this.logsContainerTarget.innerHTML += `<p class="text-success">${getTime()} - Removed | Products: ${removedProducts} | Choices: ${removedChoices}`
            if(deletionResponse['productsForDeleteLeft'] === 0){
                productsForDelete = false
            }
        }



        this.logsContainerTarget.innerHTML += `<p>--------------------------------------------RESULT----------------------------------------------------</p>`
        this.logsContainerTarget.innerHTML += `<p><span class="text-success"> NEW PRODUCTS - ${totalProducts}</span> | <span class="text-danger"> REMOVED PRODUCTS - ${removedProducts}</span></p>`
        if(choicesArray.length > 0){
            this.logsContainerTarget.innerHTML += `<p><span class="text-success"> NEW CHOICES  - ${totalNewChoices}</span> | <span class="text-danger"> REMOVED CHOICES: ${removedChoices}</span></p>`

        }

        const finish = Date.now(); // Record the end time in milliseconds
        const durationInSeconds = (finish - start) / 1000;
        this.logsContainerTarget.innerHTML += `<p>Finished in ${durationInSeconds} seconds</p>`
        scrollToBottom()
        currentTarget.disabled = false;
        currentTarget.innerText = buttonText;
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
        try {
            const processProductsBody = JSON.stringify({website: currentTarget.dataset.website, fileName: file});
            const response = await fetch(this.processProductsUrlValue, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: processProductsBody
            })
            const data = await response.json()
            if (!response.ok) {
                this.logsContainerTarget.innerHTML += 'Network response was not ok'
            }
    console.log(data)
            return data
        } catch (err){
            console.error(err.message)
        }
    }

    async processChoices(currentTarget, choicesArray){
        try {
            const processChoicesBody = JSON.stringify({choiceProducts: choicesArray})
            const response = await fetch(this.processChoicesUrlValue, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: processChoicesBody
            })

            if(!response.ok){
                this.logsContainerTarget.innerHTML += 'Network response was not ok while processing choices'
            }

            return await response.json();
        } catch (err){
            console.error(err.message)
        }

    }

    async deleteProductsAndChoices(currentTarget){
        const deleteProductsAndChoicesBody = JSON.stringify({ website: currentTarget.dataset.website});
        const response = await fetch(this.deleteProductsAndChoicesUrlValue, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: deleteProductsAndChoicesBody
        })

        if(!response.ok){
            this.logsContainerTarget.innerHTML += 'Network response was not ok while processing choices'
        }

        return await response.json();
    }
}
