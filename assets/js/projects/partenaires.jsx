import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Partenaires(){
    const {items: partenaires, load, loading, hasMore} = usePaginatedFetch('/api/partenaires?status=1&order[ordre]=ASC')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="row pt-45 justify-content-center">
            {loading && 'loading...'}
            {partenaires.map(p =>
                <Partenaire
                    key={p.id}
                    partenaire={p}
                />
            )}
            {hasMore && <button disabled={loading} onClick={load}>Charger plus de commentaire</button> }
        </div>
    )
}

const Partenaire = React.memo(({partenaire}) => {
    return(
        <div className="col-lg-3 col-sm-6">
            <div className="freelancers-card">
                {partenaire.logo.map(photo => <img key={photo.id} src={`./uploads/${photo.nom}`} alt="partenaire"/>)}
                <div className="title">
                    <h3>{partenaire.nom}</h3>
                </div>
            </div>
        </div>
    )
})

class PartenairesElement extends HTMLElement {

    connectedCallback(){
        render(<Partenaires/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }


}

customElements.define('home-partenaires', PartenairesElement)
