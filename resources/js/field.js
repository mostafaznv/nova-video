Nova.booting((Vue, router) => {
    Vue.component('index-video', require('./components/IndexField'));
    Vue.component('detail-video', require('./components/DetailField'));
    Vue.component('form-video', require('./components/FormField'));
})
