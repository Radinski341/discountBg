import { Controller } from '@hotwired/stimulus';
import Swal from "sweetalert2";

export default class extends Controller {

    static values = {
        allQueries: Object,
        slug: String
    }

    static targets = ['quantity', 'choice']
    connect() {
        this.dispatch(this, {debug: true})
        let allQueriesArray = Object.keys(this.allQueriesValue).map((key) => [key, this.allQueriesValue[key]])

        allQueriesArray.forEach(query => {
            let options = document.querySelectorAll('.'+query[0]+ ' option');
            options.forEach(option => {
                if(option.innerHTML === query[1]){
                    option.setAttribute('selected', 'selected')
                }
            })
        })
    }


    addToCart(event){

        let quantity = this.quantityTarget.value
        let urlPath = window.location.pathname
        let productSlug = this.findSlugFromUrlPath(urlPath);
        let urlParams = window.location.search

        let productChoiceType = this.findProductChoiceTypeAndValue(urlParams).choiceType;
        let productChoiceValue =this.findProductChoiceTypeAndValue(urlParams).choiceValue;

            fetch(event.currentTarget.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                slug: productSlug,
                productChoiceType: productChoiceType,
                productChoice: productChoiceValue,
                quantity: quantity
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                this.dispatch('quantity-change', {detail: {quantityChange: quantity, data: data}})
            }) // Log the raw response data
            .catch(error => console.error('Error:', error));

    }

    addToCartFromList(){
        fetch(event.currentTarget.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                slug: this.slugValue,
                productChoice: null,
                quantity: 1
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text(); // Use text() instead of json()
            })
            .then(data => {
                this.dispatch('quantity-change', {detail: {quantityChange: 1, data: data}})
            }) // Log the raw response data
            .catch(error => console.error('Error:', error));

    }

    addToFavourite(event){
        console.log('here')
        let urlPath = window.location.pathname
        let productSlug = this.findSlugFromUrlPath(urlPath);
        let urlParams = window.location.search

        let productChoiceType = this.findProductChoiceTypeAndValue(urlParams).choiceType;
        let productChoiceValue =this.findProductChoiceTypeAndValue(urlParams).choiceValue;

        fetch(event.currentTarget.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                slug: productSlug,
                productChoiceType: productChoiceType,
                productChoice: productChoiceValue
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                let favouriteAction = event.target.dataset.favouriteAction
                this.dispatch('favourite-total-change', {detail: {favouriteAction: favouriteAction}})
                if(favouriteAction === 'add'){
                    event.target.dataset.favouriteAction = 'remove'
                    event.target.classList.add('btn-danger')
                    event.target.classList.remove('btn-outline-danger')
                    event.target.innerText = 'Изтрий од любими'
                }else{
                    event.target.dataset.favouriteAction = 'add'
                    event.target.classList.remove('btn-danger')
                    event.target.classList.add('btn-outline-danger')
                    event.target.innerText = 'Добави в люубими'
                }
            })
            .catch(error => console.error('Error:', error));
    }

    addToFavouriteFromList(event){
        fetch(event.currentTarget.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                slug: this.slugValue,
                productChoice: null,
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text(); // Use text() instead of json()
            })
            .then(data => {
                let favouriteAction = event.target.dataset.favouriteAction
                this.dispatch('favourite-total-change', {detail: {favouriteAction: favouriteAction}})
                if(favouriteAction === 'add'){
                    event.target.dataset.favouriteAction = 'remove'
                    event.target.classList.add('bi-heart-fill')
                    event.target.classList.remove('bi-heart')
                }else{
                    event.target.dataset.favouriteAction = 'add'
                    event.target.classList.remove('bi-heart-fill')
                    event.target.classList.add('bi-heart')
                }
            }) // Log the raw response data
            .catch(error => console.error('Error:', error));
    }

    removeFromFavourite(event){
        let favouriteOrder = document.getElementById('favourite-order-id-'+event.currentTarget.dataset.orderId)
        let favouriteOrderParent = favouriteOrder.parentNode;
        let url = event.currentTarget.dataset.url
        let orderId = event.currentTarget.dataset.orderId
        let productId = event.currentTarget.dataset.productId
        let productChoiceId = event.currentTarget.dataset.productChoiceId
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
                fetch(url, {
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
                        return response.text(); // Use text() instead of json()
                    })
                    .then(data => {
                        favouriteOrderParent.removeChild(favouriteOrder);
                        let totalFavouriteItems = parseInt(document.getElementById('total-favourite-items').innerHTML) - 1
                        document.getElementById('total-favourite-items').innerHTML = totalFavouriteItems.toString()
                    }) // Log the raw response data
                    .catch(error => console.error('Error:', error));
            }
        })

    }

    findSlugFromUrlPath(urlPath){
        let productSlug = urlPath.split('?')[0]
        productSlug = productSlug.replace('/product/', '');
        return productSlug
    }

    findProductChoiceTypeAndValue(urlParams){
        let urlParamsKeyValue = urlParams.split('=')
        let productChoiceType = urlParamsKeyValue[0].replace('?', '') || null
        let productChoiceValue = decodeURIComponent(urlParamsKeyValue[1])
        productChoiceValue = productChoiceValue.split('&')[0] === 'undefined' ? null : productChoiceValue.split('&')[0]

        return {
            choiceType: productChoiceType,
            choiceValue: productChoiceValue
        }
    }

}




