import {Flipper, spring} from 'flip-toolkit'

/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {number} page
 * @property {boolean} moreNav
 */
export default class Filter {

    /**
     * @param {HTMLElement|null} element
     */
    constructor (element) {
        if (element === null) {
            return
        }
        this.pagination = element.querySelector('.js-filter-pagination')
        this.content = element.querySelector('.js-filter-content')
        if (this.sorting){
            this.sorting = element.querySelector('.js-filter-sorting')
        }
        this.btns = element.querySelector('.js-filter-btns')
        this.error = element.querySelector('.js-filter-error')
        this.form = element.querySelector('.js-filter-form')
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1)
        this.moreNav = this.page === 1
        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents () {
        const aClickListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }
        if(this.sorting){
            this.sorting.querySelector('.btn-selection').addEventListener('click', this.showSelect.bind(this));
            this.sorting.addEventListener('click', e => {
                aClickListener(e)
                this.page = 1
            })
        }

        if (this.moreNav) {
            this.pagination.innerHTML = '<div class="ls-show-more"><button class="show-more">Afficher plus</button></div>'
            this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this))
        } else {
            this.pagination.addEventListener('click', e => {
                aClickListener(e)
                this.page = 1
            })
        }
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        })
    }

    async showSelect () {
        const button = this.sorting.querySelector('.btn-selection')
        button.setAttribute('disabled', 'disabled')
        const url = new URL(window.location.href)
        const params = new URLSearchParams(url.search)
        params.set('likes', "1")
        await this.loadUrl(url.pathname + '?' + params.toString(), false)
        button.removeAttribute('disabled')
    }

    async loadMore () {
        const button = this.pagination.querySelector('button')
        button.setAttribute('disabled', 'disabled')
        this.page++
        const url = new URL(window.location.href)
        const params = new URLSearchParams(url.search)
        params.set('page', this.page)
        await this.loadUrl(url.pathname + '?' + params.toString(), true)
        button.removeAttribute('disabled')
    }

    async loadForm () {
        this.page = 1
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams()
        data.forEach((value, key) => {
            params.append(key, value)
        })
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl (url, append = false) {
        this.showLoader()
        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax', 1)
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.flipContent(data.content, append)
            //this.sorting.innerHTML = data.sorting

            if(data.btns){
                this.btns.innerHTML = data.btns;
            }
            if (!this.moreNav) {
                this.pagination.innerHTML = data.pagination
                this.error.style.display = 'none';
            } else if (this.page === data.pages) {
                this.pagination.style.display = 'none';
            } else if (!data.content) {
                this.pagination.style.display = 'none';
            } else {
                this.pagination.style.display = null;
            }
            if (data.pages == 0){
                this.error.style.display = null;
            }else{
                this.error.style.display = 'none';
            }
            //this.updatePrices(data)
            params.delete('ajax')
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
            /**TODO TRAVAIL SUR LE MODAL LORS DU CHARGEMENT DU CONTENU**/
            //window.location.reload();
            /****/
        } else {
            console.error(response)
        }
        this.hideLoader()
    }

    /**
     * Remplace les éléments de la grille avec un effet d'animation flip
     * @param {string} content
     */
    flipContent (content, append) {
        const springConfig = 'gentle'
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        }
        const appearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 20
            })
        }
        const flipper = new Flipper({
            element: this.content
        })
        //console.log(this.content.children);

        Array.from(this.content.children).forEach(element => {
            //console.log(element)
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            })
        })
        flipper.recordBeforeUpdate()
        if (append) {
            this.content.innerHTML += content
        } else {
            this.content.innerHTML = content
        }
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                onAppear: appearSpring
            })
        })
        flipper.update()
    }

    showLoader () {
        this.form.classList.add('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'false')
        loader.style.display = null
    }

    hideLoader () {
        this.form.classList.remove('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'true')
        loader.style.display = 'none'
    }
}
