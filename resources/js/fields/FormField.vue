<template>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        <DefaultField
            :field="currentField"
            :label-for="labelFor"
            :errors="errors"
            :show-errors="false"
            :show-help-text="!isReadonly && showHelpText"
            :full-width-content="fullWidthContent"
        >
            <template #field>
                <file-picker
                    v-model="file"
                    v-bind="$props"
                    @file-deleted="onDeleteOriginalFile"
                    :cover="cover"
                    :larupload-is-on="laruploadIsOn"
                    :errors="errors"
                />
            </template>
        </DefaultField>

        <div v-show="laruploadIsOn && (file || isEditable) && field.displayCoverUploader && currentlyIsVisible" :class="fieldWrapperClasses">
            <div :class="labelClasses">
                <FormLabel class="space-x-1" :label-for="labelFor + '-cover'">
                    <span>{{ field.indexName + ' (' + __('Cover') + ')' }}</span>
                </FormLabel>
            </div>

            <div :class="controlWrapperClasses">
                <file-picker
                    ref="cover"
                    v-model="cover"
                    v-bind="$props"
                    @file-deleted="onDeleteFile"
                    :larupload-is-on="laruploadIsOn"
                    :errors="errors"
                    :is-cover="true"
                />
            </div>
        </div>
    </div>
</template>

<script>
import {DependentFormField} from 'laravel-nova'
import FilePicker from '../components/FilePicker.vue'
import FormFieldMixin from '../mixins/form-field'


export default {
    mixins: [
        DependentFormField, FormFieldMixin
    ],
    props: [
        'resourceName', 'resourceId', 'relatedResourceName',
        'relatedResourceId', 'viaRelationship', 'field', 'errors'
    ],
    components: {
        FilePicker
    },
    data: () => ({
        file: null,
        cover: null,
        originalFileDeleted: false,
    }),
    computed: {
        labelFor() {
            let name = this.resourceName

            if (this.relatedResourceName) {
                name += '-' + this.relatedResourceName
            }

            return `file-${name}-${this.fieldAttribute}`
        },

        isEditable() {
            return this.resourceId
                && this.field.previewUrl !== null
                && !this.originalFileDeleted
        },

        laruploadIsOn() {
            return this.currentField.laruploadIsOn
        }
    },
    watch: {
        file(value) {
            this.emitFieldValueChange(this.currentField.attribute, value)
        }
    },
    methods: {
        onDeleteFile() {
            this.$emit('file-deleted')
            this.$emit('field-changed')
        },

        onDeleteOriginalFile() {
            this.$refs.cover.afterRemove()
            this.onDeleteFile()

            this.originalFileDeleted = true
        }
    },
    async mounted() {
        this.field.fill = formData => {
            let attribute = this.fieldAttribute

            if (this.laruploadIsOn) {
                if (this.file) {
                    formData.append(attribute + '[original]', this.file.originalFile, this.file.name)
                }

                if (this.cover) {
                    formData.append(attribute + '[cover]', this.cover.originalFile, this.cover.name)
                }
            }
            else if (this.file) {
                formData.append(attribute, this.file.originalFile, this.file.name)
            }
        }
    },
}
</script>
