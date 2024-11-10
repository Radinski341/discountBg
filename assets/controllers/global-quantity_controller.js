import {Controller} from "@hotwired/stimulus";

export default class extends Controller{

    static values = {
        cartQuantityItems: Number,
        favouriteTotalItems: Number,
        getCartUrl: String
    }

    static targets = ['favouriteCount', 'cartCount', 'cartPreview']

    closeCartPreview() {
        this.cartPreviewTarget.classList.add('d-none');
    }

    changeCartQuantity(event){
        this.cartQuantityItemsValue+= parseInt(event.detail.quantityChange)
        if(this.cartQuantityItemsValue < 1 && !this.cartCountTarget.classList.contains('d-none')){
            this.cartCountTarget.classList.add('d-none')
        } else if(this.cartQuantityItemsValue > 0 && this.cartCountTarget.classList.contains('d-none')){
            this.cartCountTarget.classList.remove('d-none')
        }
        this.cartCountTarget.innerHTML = this.cartQuantityItemsValue
        this.cartPreviewTarget.innerHTML = event.detail.data
        this.cartPreviewTarget.classList.remove('d-none');
        setTimeout(() => {
            this.cartPreviewTarget.classList.add('d-none');
        }, 5000)
    }

    changeFavouriteTotal(event){
        if(event.detail.favouriteAction === 'add'){
            this.favouriteTotalItemsValue++
            if(this.favouriteCountTarget.classList.contains('d-none')){
                this.favouriteCountTarget.classList.remove('d-none')
            }
        }else{
            this.favouriteTotalItemsValue--
            if(this.favouriteTotalItemsValue < 1 && !this.favouriteCountTarget.classList.contains('d-none')){
                this.favouriteCountTarget.classList.add('d-none')
            }
        }
        this.favouriteCountTarget.innerHTML = this.favouriteTotalItemsValue
    }
}