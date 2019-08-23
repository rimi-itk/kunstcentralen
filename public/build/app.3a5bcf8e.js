(window.webpackJsonp=window.webpackJsonp||[]).push([["app"],{XENs:function(e,t,a){},jwo3:function(e,t,a){"use strict";a.r(t);var n=a("q1tI"),r=a.n(n),o=a("i8i4"),i=a.n(o),c=a("17x9"),l=a.n(c),s=a("A+0y"),u=a.n(s),m=a("vKLW"),h=a.n(m);function f(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function d(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function y(e,t,a){return t&&d(e.prototype,t),a&&d(e,a),e}function p(e,t){return!t||"object"!==k(t)&&"function"!=typeof t?v(e):t}function v(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function g(e){return(g=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function b(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&E(e,t)}function E(e,t){return(E=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function k(e){return(k="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function S(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}var w=a("vDqi"),C=w.CancelToken,O=a("sBL/");a("XENs"),Object.filter=function(e,t){return Object.keys(e).filter(function(a){return t(e[a],a,e)}).reduce(function(t,a){return Object.assign(t,S({},a,e[a]))},{})};var j=function(e){return null!==e&&"object"===k(e)},q=function(e){return!e||j(t=e)&&0===Object.entries(t).length;var t},L=document.getElementById("app-root"),N=JSON.parse(L.getAttribute("data-config")||JSON.stringify({})),_=function(e){function t(){return f(this,t),p(this,g(t).apply(this,arguments))}return b(t,n["Component"]),y(t,[{key:"render",value:function(){var e=this.props.value;return r.a.createElement("div",{className:"item"},r.a.createElement("div",{className:"name"},e.name),r.a.createElement("div",{className:"image"},r.a.createElement("img",{src:e.image})),r.a.createElement("div",{className:"artist-name"},e.artist.name),r.a.createElement("div",{className:"location-name"},e.location.name))}}],[{key:"propTypes",get:function(){return{value:l.a.any}}}]),t}(),x=function(e){function t(e){var a;return f(this,t),S(v(a=p(this,g(t).call(this,e))),"cancelSearch",null),S(v(a),"handleChange",function(e){if(e.target.name){var t=e.target.name,n=a.state.query,r=e.target.value;"checkbox"===e.target.type&&(r=n[t],e.target.checked?r[e.target.value]=e.target.value:delete r[e.target.value]),n[t]=r,a.setState({query:n},a.search)}}),a.state={query:{query:"","artist.id":"","location.id":"","categories.id":{}},error:null,isLoading:!1,items:[],artists:[],locations:[],categories:[]},a.search=O(a.doSearch,500),a}return b(t,n["Component"]),y(t,[{key:"doSearch",value:function(){var e=this,t={};for(var a in this.state.query){var n=this.state.query[a];j(n)&&(n=Object.keys(n)),q(n)||(t[a]=n)}var r=N.search_url;this.setState({isLoading:!0}),null!==this.cancelSearch&&this.cancelSearch();var o=this;w({url:r,params:t,cancelToken:new C(function(e){o.cancelSearch=e})}).then(function(t){e.setState({isLoading:!1,items:t.data["hydra:member"]})})}},{key:"loadArtists",value:function(){var e=this,t=N.artists_url;w({url:t}).then(function(t){e.setState({artists:t.data["hydra:member"]})})}},{key:"loadLocations",value:function(){var e=this,t=N.locations_url;w({url:t}).then(function(t){e.setState({locations:t.data["hydra:member"]})})}},{key:"loadCategories",value:function(){var e=this,t=N.categories_url;w({url:t}).then(function(t){e.setState({categories:t.data["hydra:member"]})})}},{key:"componentDidMount",value:function(){this.loadArtists(),this.loadLocations(),this.loadCategories(),this.search()}},{key:"render",value:function(){var e=this,t=null;return t=this.state.isLoading?r.a.createElement(u.a,{variant:"info",className:"loading"},"Loading …"):this.state.items.length>0?this.state.items.map(function(e,t){return r.a.createElement(_,{key:t,value:e})}):r.a.createElement(u.a,{variant:"warning"},"No items found"),r.a.createElement("div",{className:"app"},r.a.createElement(h.a,null,r.a.createElement(h.a.Group,{controlId:"query"},r.a.createElement(h.a.Label,null,"Query"),r.a.createElement(h.a.Control,{type:"text",placeholder:"Search",name:"query",value:this.state.query.query,onChange:this.handleChange}),r.a.createElement(h.a.Text,{className:"text-muted"},"Search for name of work of art, artist or location")),r.a.createElement(h.a.Group,{controlId:"artist"},r.a.createElement(h.a.Label,null,"Artist"),r.a.createElement(h.a.Control,{as:"select",name:"artist.id",value:this.state.query["artist.id"],onChange:this.handleChange},r.a.createElement("option",null),this.state.artists.map(function(e,t){return r.a.createElement("option",{key:t,value:e.id},e.name)}))),r.a.createElement(h.a.Group,{controlId:"location"},r.a.createElement(h.a.Label,null,"Location"),r.a.createElement(h.a.Control,{as:"select",name:"location.id",value:this.state.query["location.id"],onChange:this.handleChange},r.a.createElement("option",null),this.state.locations.map(function(e,t){return r.a.createElement("option",{key:t,value:e.id},e.name)}))),r.a.createElement(h.a.Group,null,r.a.createElement(h.a.Label,null,"Category"),this.state.categories.map(function(t,a){return r.a.createElement(h.a.Check,{type:"checkbox",name:"categories.id",key:a,id:"category-".concat(t.id),value:t.id,label:t.name,onChange:e.handleChange})}))),r.a.createElement("div",{className:"items"},t))}}]),t}();i.a.render(r.a.createElement(x,null),L)}},[["jwo3","runtime",0]]]);