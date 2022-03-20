import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Editions(){
    const {items: editions, load, loading, hasMore} = usePaginatedFetch('/api/editions')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="row pt-45 justify-content-center">
            {loading && 'loading...'}
            {editions.map(p =>
                <Edition
                    key={p.id}
                    edition={p}
                />
            )}
            {hasMore && <button disabled={loading} onClick={load}>Charger plus de commentaire</button> }
        </div>
    )
}

function Edition({edition}){
    return(
        <div className="col-lg-4 col-md-6">
            <div className="blog-card">
                <div className="blog-img">
                    {edition.avatar.map(avatar => <img key={avatar.id} src={`./uploads/${avatar.nom}`} alt={avatar.nom} />)}
                    <a className="tag">{edition.city}</a>
                </div>
                <div className="content">
                    <h3>{edition.nom}</h3>
                </div>
            </div>
        </div>
    )

}

class EditionsElement extends HTMLElement {

    connectedCallback(){
        render(<Editions/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }

}

customElements.define('home-editions', EditionsElement)
