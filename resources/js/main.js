require('./bootstrap');
// window.Vue = require('vue');
import Vue from 'vue';

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
// Vue.component('style-bom-create', require('./components/merch/StyleBOMCreate.vue'));
Vue.component("style-bom-create", () => import("./components/merch/StyleBOMCreate"));

window.onload = function () {
    var app = new Vue({
	    el: '#app',
	    data: {
	        text:'welcome'
	        
	    },
	    http: {
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    },
	    
	    methods: {
	        
	        
	    },
	    mounted() {
	        console.log(this.text);
	    },
	    
	});
}
