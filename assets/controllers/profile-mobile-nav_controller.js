import {Controller} from "@hotwired/stimulus";

export default class extends Controller{

    static targets = ['nav']

    toggleNav(){
        if(this.navTarget.style.height === '100%'){
            this.navTarget.style.height = 0
        }else{
            this.navTarget.style.height = '100%'
        }
    }
}