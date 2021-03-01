require('./bootstrap');

import Vue from 'vue';

Vue.component('my-documents', require('./Documents.vue').default);

let vm = new Vue().$mount('#my-documents');
