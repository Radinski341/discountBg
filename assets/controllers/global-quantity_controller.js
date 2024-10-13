import {Controller} from "@hotwired/stimulus";

export default class extends Controller{

    static values = {
        cartQuantityItems: Number,
        favouriteTotalItems: Number
    }
    changeCartQuantityFromList(){
        this.cartQuantityItemsValue+= 1
        document.getElementById('total-cart-items').innerHTML = this.cartQuantityItemsValue
    }
    changeCartQuantityFromInfo(event){
        console.log(event.detail.detail.quantityChange)
        this.cartQuantityItemsValue+= parseInt(event.detail.detail.quantityChange)
        document.getElementById('total-cart-items').innerHTML = this.cartQuantityItemsValue
    }

    changeFavouriteTotalFromInfo(event){
        if(event.detail.detail.favouriteAction === 'add'){
            this.favouriteTotalItemsValue++
        }else{
            this.favouriteTotalItemsValue--
        }

        document.getElementById('total-favourite-items').innerHTML = this.favouriteTotalItemsValue
    }

    changeFavouriteTotalFromList(event){
        if(event.detail.detail.favouriteAction === 'add'){
            this.favouriteTotalItemsValue++
        }else{
            this.favouriteTotalItemsValue--
        }

        document.getElementById('total-favourite-items').innerHTML = this.favouriteTotalItemsValue
    }
}