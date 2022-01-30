import ReactDOM from 'react-dom';
import React from 'react';

import {BrowserRouter, MemoryRouter} from 'react-router-dom';
import App from "./react/app";

ReactDOM.render((
    <BrowserRouter>
        <MemoryRouter>
            <App/>
        </MemoryRouter>
    </BrowserRouter>
), document.getElementById('app'));
