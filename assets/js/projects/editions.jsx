import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Editions(){
    const {items: editions, load, loading, hasMore} = usePaginatedFetch('/api/editions?status=true&order[year]=DESC')

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

const Edition = React.memo(({edition}) => {
    return(
        <div className="col-lg-4 col-md-6">
            <div className="blog-card">
                <a href={`/editions/${edition.slug}`}>
                    <div className="blog-img">
                        {edition.avatar.map(avatar => <img key={avatar.id} src={`./uploads/${avatar.nom}`} alt={avatar.nom} />)}
                        <div className="tag">{edition.city}</div>
                    </div>
                    <div className="content">
                        <h3>{edition.nom}</h3>
                    </div>
                </a>
            </div>
        </div>
    )
})

class EditionsElement extends HTMLElement {

    connectedCallback(){
        render(<Editions/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }

}

customElements.define('home-editions', EditionsElement)
