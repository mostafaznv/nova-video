import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((Vue) => {
    Vue.component('index-video', IndexField);
    Vue.component('detail-video', DetailField);
    Vue.component('form-video', FormField);
})
