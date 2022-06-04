import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Teams(){
    const {items: teams, load, loading, hasMore} = usePaginatedFetch('/api/teams')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="row pt-45 justify-content-center">
            {loading && 'loading...'}
            {teams.map(t =>
                <Team
                    key={t.id}
                    team={t}
                />
            )}
            {hasMore && <button disabled={loading} onClick={load}>Charger plus de commentaire</button> }
        </div>
    )
}

const Team = React.memo(({team}) => {
    return(
        <div className="col-lg-3 col-sm-6">
            <div className="freelancers-card">
                {team.photo.map(photo => <img key={photo.id} src={`./uploads/${photo.nom}`} alt="team"/>)}
                <div className="title">
                    <h3>{team.nom} {team.prenom}</h3>
                </div>
                <p>{team.role}</p>
            </div>
        </div>
    )
})

class TeamsElement extends HTMLElement {

    connectedCallback(){
        render(<Teams/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }


}

customElements.define('home-teams', TeamsElement)
