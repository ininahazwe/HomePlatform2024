import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "./hooks";

function Abouts(){
    const {items: abouts, load, loading, hasMore} = usePaginatedFetch('/api/abouts')

    useEffect(() => {
        load()
    }, [])

    return(
        <div className="career-area pt-100 pb-70">
            <div className="container">
                <div className="row align-items-center">
                    {loading && 'loading...'}
                    {abouts.map(a =>
                        <About
                            key={a.id}
                            about={a}
                        />
                    )}
                </div>
            </div>
        </div>
    )
}

const About = React.memo(({about}) => {
    return(
        <div>
            <div className="col-lg-6">
                <div className="career-img">
                    <div className="row align-items-center">
                        <div className="col-6">
                            <div className="row">
                                {about.images.map(image =>
                                    <div className="col-12">
                                        <div className="images1 wow fadeInDown" data-wow-delay="000ms" data-wow-duration="1000ms">
                                            <img key={image.id} src={`./uploads/${image.nom}`} alt="avatar"/>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="row">
                                {about.images.map(image =>
                                    <div className="col-12">
                                        <div className="images1 wow fadeInDown" data-wow-delay="200ms" data-wow-duration="1000ms">
                                            <img key={image.id} src={`./uploads/${image.nom}`} alt="avatar"/>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="col-lg-6">
                <div className="career-content pl-20 wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1000ms">
                    <div className="section-title">
                        <h2>{about.titre}</h2>
                        <div className="bar"/>
                    </div>
                    <p>{about.intro}</p>
                    <div className="optional-item">
                        <a href="" className="default-btn two border-radius-5">Learn more <i
                            className="ri-arrow-right-line"/></a>
                    </div>
                </div>
            </div>
        </div>
    )

})

class AboutsElement extends HTMLElement {

    connectedCallback(){
        render(<Abouts/>, this)
    }

    disconnectCallback() {
        unmountComponentAtNode(this)
    }


}

customElements.define('home-about', AboutsElement)
