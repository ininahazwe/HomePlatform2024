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
        {projects.map(project => <Project key={project.id} project={project} />)}
        <Title count={count}/>
        <button disabled={loading} onClick={load}>Charger les projects</button>
        {hasMore && <button disabled={loading} className="btn btn-primary" onClick={load}>Load more projects</button>}
    </div>
 }

function Title ({count}){
    return <h3>{count} Project {count > 1 ? 's' : ''}</h3>
}

function Project({project}){
    return (
        <div className="job-listing-area pt-50 pb-70">
            <div className="container">
                <div className="job-listing-top">
                </div>
                <div className="row">
                    <div className="col-lg-6">
                        <div className="recent-job-card box-shadow">
                            <div className="content">
                                <div className="recent-job-img">
                                    {project.description}
                                </div>

                                <h3><a href=""></a></h3>
                                <div className="job-sub-content">
                                    <ul className="job-list2">
                                        <li className="time">{project.category}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
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
