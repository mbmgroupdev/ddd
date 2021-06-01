require('./bootstrap');

window.Vue = require('vue');


// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('job-card', require('./components/Hr/job_card/Index.vue').default);


const app = new Vue({
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

