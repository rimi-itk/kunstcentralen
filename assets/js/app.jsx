import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import PropTypes from 'prop-types'

import Alert from 'react-bootstrap/Alert'
import Form from 'react-bootstrap/Form'
const axios = require('axios')
const CancelToken = axios.CancelToken
const debounce = require('debounce')
// const queryString = require('query-string')

require('../css/app.scss')

// @see https://stackoverflow.com/a/37616104
Object.filter = (obj, predicate) =>
    Object.keys(obj)
        .filter(key => predicate(obj[key], key, obj))
        .reduce((res, key) => Object.assign(res, { [key]: obj[key] }), {})

const isObject = (obj) => obj !== null && typeof obj === 'object'
const isEmptyObject = (obj) => isObject(obj) && Object.entries(obj).length === 0
const isEmpty = (value) => !value || isEmptyObject(value)

const rootElement = document.getElementById('app-root')
const config = JSON.parse(rootElement.getAttribute('data-config') || JSON.stringify({}))

class Item extends Component {
    static get propTypes () {
        return {
            value: PropTypes.any
        }
    }

    render () {
        const value = this.props.value

        return (
            <div className="item">
                <div className="name">{value.name}</div>
                <div className="image"><img src={value.images.thumb}/></div>
                <div className="artist-name">{value.artist.name}</div>
                <div className="location-name">{value.location.name}</div>
            </div>
        )
    }
}

class App extends Component {
    constructor (props) {
        super(props)
        this.state = {
            query: {
                query: '',
                'artist.id': '',
                'location.id': '',
                'categories.id': {}
            },
            error: null,
            isLoading: false,
            items: [],
            artists: [],
            locations: [],
            categories: []
        }
        // Wait 500 ms
        this.search = debounce(this.doSearch, 500)
    }

    cancelSearch = null

    doSearch () {
        const params = {}
        for (const name in this.state.query) {
            let value = this.state.query[name]
            if (isObject(value)) {
                value = Object.keys(value)
            }
            if (!isEmpty(value)) {
                params[name] = value
            }
        }

        const searchUrl = config.search_url

        this.setState({
            isLoading: true
        })

        if (this.cancelSearch !== null) {
            this.cancelSearch()
        }

        const self = this
        axios({
            url: searchUrl,
            params: params,
            cancelToken: new CancelToken(function executor (c) {
                // An executor function receives a cancel function as a parameter
                self.cancelSearch = c
            })
        })
            .then(result => {
                this.setState({
                    isLoading: false,
                    items: result.data['hydra:member']
                })
            })
    }

    loadArtists () {
        const url = config.artists_url

        axios({
            url: url
        })
            .then(result => {
                this.setState({ artists: result.data['hydra:member'] })
            })
    }

    loadLocations () {
        const url = config.locations_url

        axios({
            url: url
        })
            .then(result => {
                this.setState({ locations: result.data['hydra:member'] })
            })
    }

    loadCategories () {
        const url = config.categories_url

        axios({
            url: url
        })
            .then(result => {
                this.setState({ categories: result.data['hydra:member'] })
            })
    }

    componentDidMount () {
        this.loadArtists()
        this.loadLocations()
        this.loadCategories()
        this.search()
    }

    handleChange = (event) => {
        if (event.target.name) {
            const name = event.target.name
            const query = this.state.query
            let value = event.target.value
            if (event.target.type === 'checkbox') {
                value = query[name]
                if (event.target.checked) {
                    value[event.target.value] = event.target.value
                } else {
                    delete value[event.target.value]
                }
            }
            query[name] = value
            this.setState({ query: query }, this.search)
        }
    }

    render () {
        let items = null

        if (this.state.isLoading) {
            items = <Alert variant="info" className="loading">Loading …</Alert>
        } else if (this.state.items.length > 0) {
            items = this.state.items.map((value, index) => <Item key={index} value={value}/>)
        } else {
            items = <Alert variant="warning">No items found</Alert>
        }

        return (
            <div className="app">
                <Form>
                    <Form.Group controlId="query">
                        <Form.Label>Query</Form.Label>
                        <Form.Control type="text" placeholder="Search" name="query" value={this.state.query.query} onChange={this.handleChange}/>
                        <Form.Text className="text-muted">
                    Search for name of work of art, artist or location
                        </Form.Text>
                    </Form.Group>

                    <Form.Group controlId="artist">
                        <Form.Label>Artist</Form.Label>
                        <Form.Control as="select" name="artist.id" value={this.state.query['artist.id']} onChange={this.handleChange}>
                            <option></option>
                            {this.state.artists.map((value, index) => <option key={index} value={value.id}>{value.name}</option>)}
                        </Form.Control>
                    </Form.Group>

                    <Form.Group controlId="location">
                        <Form.Label>Location</Form.Label>
                        <Form.Control as="select" name="location.id" value={this.state.query['location.id']} onChange={this.handleChange}>
                            <option></option>
                            {this.state.locations.map((value, index) => <option key={index} value={value.id}>{value.name}</option>)}
                        </Form.Control>
                    </Form.Group>

                    <Form.Group>
                        <Form.Label>Category</Form.Label>
                        {this.state.categories.map((value, index) => <Form.Check type="checkbox" name="categories.id" key={index} id={`category-${value.id}`} value={value.id} label={value.name} onChange={this.handleChange}/>)}
                    </Form.Group>
                </Form>

                <div className="items">{items}</div>
            </div>
        )
    }
}

ReactDOM.render(<App/>, rootElement)
