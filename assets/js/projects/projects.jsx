import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Projects(){
    const {items: projects, load, loading, hasMore} = usePaginatedFetch('/api/projects')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="row pt-45">
            {loading && 'loading...'}
            {projects.map(p =>
                <Project
                    key={p.id}
                    project={p}
                />
            )}
            {hasMore && <button disabled={loading} onClick={load}>See more</button> }
        </div>
    )
}

const Project = React.memo(({project}) => {
    return(
        <div className="col-lg-6">
            <div className="recent-job-card">
                <div className="content">
                    <div className="recent-job-img">
                        {project.avatar.map(logo => <img key={logo.id} src={`./uploads/${logo.nom}`} style={{width: 70}} alt="project"/>)}
                    </div>
                    <h3><a href={`/project/${project.slug}`}>{project.nom}</a></h3>
                    <ul className="job-list1">
                        <div className="job-sub-content">
                            <ul className="job-list2">
                                {project.categorie.map(cat => <li className="time" key={cat.id} >{cat.nom}</li>)}
                            </ul>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    )
})

class ProjectsElement extends HTMLElement {

    connectedCallback(){
        render(<Projects/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }

}

customElements.define('home-projects', ProjectsElement)
