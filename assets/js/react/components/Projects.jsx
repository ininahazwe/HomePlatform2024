import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Projects(){
    const {items: projects, load, loading, count, hasMore} = usePaginatedFetch('/api/projects')

    useEffect(() => {
        load()
    }, []);

    return <div>
        {loading && 'Chargement...'}
        {JSON.stringify(projects)}
        <Title count={count}></Title>
        <button onClick={load}>Charger les projects</button>
        {hasMore && <button className="btn btn-primary" onClick={load}>Load m projects</button>}
    </div>
}

function Title ({count}){
    return <h3>{count} Project {count > 1 ? 's' : ''}</h3>
}

class ProjectsElement extends HTMLElement {
    connectedCallback(){
        render(<Projects />, this)
    }

    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}
customElements.define('projects-import', ProjectsElement)
