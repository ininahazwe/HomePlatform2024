import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Categories(){
    const {items: categories, load, loading, hasMore} = usePaginatedFetch('/api/categories')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="row pt-45 justify-content-center">
            {loading && 'loading...'}
            {categories.map(c =>
                <Categorie
                    key={c.id}
                    categorie={c}
                />
            )}
            {hasMore && <button disabled={loading} onClick={load}>See more</button> }
        </div>
    )
}

const Categorie = React.memo(({categorie}) => {
    return(
            <div className="col-lg-4 col-sm-6">
                <div className="browse-jobs-card">
                    <a href={`/sdg/${categorie.slug}`}>
                        <div className="icon">
                                {categorie.logo.map(logo => <img key={logo.id} src={`./uploads/${logo.nom}`} alt="sdg-icon" />)}
                        </div>
                        <h3>{categorie.nom}</h3>
                        <div className="more-btn"><i className="ri-arrow-right-s-line"/></div>
                    </a>
                </div>
            </div>
    )
})

class CategoriesElement extends HTMLElement {

    connectedCallback(){
        render(<Categories/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }

}

customElements.define('home-categories', CategoriesElement)
