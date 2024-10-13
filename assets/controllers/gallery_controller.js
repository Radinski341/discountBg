import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ['galleryImage', 'mainImage']
    showFrom = 0;
    galleryImageTargetsCopy = this.galleryImageTargets
    connect() {
        if(this.galleryImageTargets.length <= 4 ){
            document.querySelector('.arrow-next-image').classList.add('d-none')
            document.querySelector('.arrow-prev-image').classList.add('d-none')
        }
        this.displaySmallIMages()
        this.galleryImageTargets.forEach(image => {
            image.addEventListener('click', () => {
                let newImageUrl = image.src;
                let mainImageUrl = this.mainImageTarget.src;
                this.mainImageTarget.src = newImageUrl
                image.src = mainImageUrl;
            })
        })
    }

    prevImage(){
        this.mainImageTarget.src = this.galleryImageTargetsCopy[this.showFrom].src
        this.showFrom--;
        this.displaySmallIMages()
    }

    nextImage(){
        this.mainImageTarget.src = this.galleryImageTargetsCopy[this.showFrom].src
        this.showFrom++
        this.displaySmallIMages()
    }

    displaySmallIMages(){
        this.galleryImageTargets.forEach(image => {
            image.parentNode.classList.add('d-none')
        })

        let numberOfItems = this.galleryImageTargetsCopy.length - 4
        if(numberOfItems > 0){
            for(let i = 0; i <= 3 ; i++){
                if(this.showFrom ===  this.galleryImageTargetsCopy.length){
                    this.showFrom = 0;
                }else if(this.showFrom < 0){
                    this.showFrom = this.galleryImageTargetsCopy.length - 1
                }
                this.galleryImageTargetsCopy[this.showFrom].parentNode.classList.remove('d-none')
                let parentNode = this.galleryImageTargetsCopy[this.showFrom].parentNode.parentNode;
                let child = this.galleryImageTargetsCopy[this.showFrom].parentNode;
                parentNode.removeChild(child)
                parentNode.insertBefore(child, document.querySelector('.arrow-next-image'))
                if(i < 3) {
                    this.showFrom++
                }else if(i === 3){
                    if(this.showFrom - 3 < 0){
                        this.showFrom =  (this.galleryImageTargetsCopy.length) + this.showFrom - 3
                    }else{
                        this.showFrom -= 3;
                    }
                }
            }
        }else {
            this.galleryImageTargetsCopy.forEach(image => {
                image.parentNode.classList.remove('d-none')
            })
        }
    }


}