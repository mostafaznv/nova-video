import IndexField from './fields/IndexField'
import DetailField from './fields/DetailField'
import FormField from './fields/FormField'

Nova.booting((Vue) => {
    Vue.component('index-video', IndexField);
    Vue.component('detail-video', DetailField);
    Vue.component('form-video', FormField);
})
