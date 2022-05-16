<template>
    <default-field :field="field" :errors="errors" :full-width-content="true" :show-help-text="!isReadonly">
        <template #field>
            <div v-if="hasValue" :class="{ 'mb-6': !isReadonly }">
                <video controls v-if="videoUrl" :src="videoUrl" :autoplay="false" class="mt-3" :poster="imageUrl" />

                <p v-if="videoUrl && !isReadonly" class="mt-3 flex items-center text-sm">
                    <delete-button :dusk="field.attribute + '-delete-link'" v-if="shouldShowRemoveButton" @click="confirmRemoval">
                        <span class="class ml-2 mt-1"> {{ __('Delete') }}</span>
                    </delete-button>
                </p>

                <portal to="modals">
                    <confirm-upload-removal-modal @confirm="removeFile" @close="closeRemoveModal" :show="removeModalOpen" />
                </portal>
            </div>

            <p v-if="!hasValue && isReadonly" class="pt-2 text-sm text-90">{{ __('This file field is read-only.') }}</p>

            <div v-if="shouldShowField" class="form-file mr-4" :class="{ 'opacity-75': isReadonly }">
                <div>
                    <div class="form-file inline-block mr-4">
                        <input ref="fileField" :dusk="field.attribute" class="form-file-input select-none" type="file" :id="idAttr" name="name" @change="fileChange" :disabled="isReadonly || uploading" :accept="field.acceptedTypes" />

                        <label :for="labelFor" class="select-none">
                            <outline-button type="button" @click="focusOnFileInput">
                                <span v-if="uploading">{{ __('Uploading') }} ({{ uploadProgress }}%)</span>
                                <span v-else>{{ __('Choose File') }}</span>
                            </outline-button>
                        </label>
                    </div>

                    <span v-if="shouldShowField" class="text-90 text-sm select-none">{{ currentLabel }}</span>
                    <p v-if="hasError" class="text-xs mt-2 text-danger">{{ firstError }}</p>
                </div>

                <div v-if="laruploadIsOn && fileName" class="mt-3">
                    <div class="form-file inline-block mr-4">
                        <input ref="thumbnailField" :dusk="field.attribute + '_image'" class="form-file-input select-none" type="file" :id="idAttr + '-thumbnail'" name="thumbnail" @change="thumbnailFileChange" :disabled="isReadonly || thumbnailUploading" accept="image/jpeg,image/png" />

                        <label :for="labelFor + '-thumbnail'" class="select-none">
                            <outline-button type="button" @click="focusOnCoverInput">
                                <span v-if="thumbnailUploading">{{ __('Uploading') }} ({{ thumbnailUploadProgress }}%)</span>
                                <span v-else>{{ __('Choose Cover') }}</span>
                            </outline-button>
                        </label>
                    </div>

                    <span v-if="shouldShowField" class="text-90 text-sm select-none">{{ currentThumbnailLabel }}</span>
                    <p v-if="hasError" class="text-xs mt-2 text-danger">{{ firstError }}</p>
                </div>
            </div>
        </template>
    </default-field>
</template>

<script>
import Vapor from 'laravel-vapor'
import DeleteButton from './DeleteButton'
import {FormField, HandlesValidationErrors, Errors} from 'laravel-nova'

export default {
    props: ['resourceId', 'relatedResourceName', 'relatedResourceId', 'viaRelationship'],
    mixins: [HandlesValidationErrors, FormField],
    components: {DeleteButton},
    data: () => ({
        file: null,
        thumbnailFile: null,
        fileName: '',
        thumbnailFileName: '',
        removeModalOpen: false,
        missing: false,
        deleted: false,
        src: null,
        uploadErrors: new Errors(),
        vaporFile: {
            key: '',
            uuid: '',
            filename: '',
            extension: '',
        },
        uploading: false,
        thumbnailUploading: false,
        uploadProgress: 0,
        thumbnailUploadProgress: 0,
    }),
    computed: {
        /**
         * Determine if the field has an upload error.
         */
        hasError() {
            return this.uploadErrors.has(this.fieldAttribute)
        },

        /**
         * Return the first error for the field.
         */
        firstError() {
            if (this.hasError) {
                return this.uploadErrors.first(this.fieldAttribute)
            }
        },

        /**
         * The current label of the file field.
         */
        currentLabel() {
            return this.fileName || this.__('no file selected')
        },

        /**
         * The current label of the file field.
         */
        currentThumbnailLabel() {
            return this.thumbnailFileName || this.__('no file selected')
        },

        /**
         * The ID attribute to use for the file field.
         */
        idAttr() {
            return this.labelFor
        },

        /**
         * The label attribute to use for the file field.
         */
        labelFor() {
            return 'file-' + this.field.attribute + '-' + Math.random().toString(36).substr(2)
        },

        /**
         * Determine whether the field has a value.
         */
        hasValue() {
            return (
                Boolean(this.field.value || this.imageUrl) &&
                !Boolean(this.deleted) &&
                !Boolean(this.missing)
            )
        },

        /**
         * Determine whether the field should show the loader component.
         */
        shouldShowLoader() {
            return !Boolean(this.deleted) && Boolean(this.imageUrl)
        },

        /**
         * Determine whether the file field input should be shown.
         */
        shouldShowField() {
            return Boolean(!this.isReadonly)
        },

        /**
         * Determine whether the field should show the remove button.
         */
        shouldShowRemoveButton() {
            return Boolean(this.field.deletable && !this.isReadonly)
        },

        /**
         * Return the preview or thumbnail URL for the field.
         */
        imageUrl() {
            return this.field.thumbnailUrl
        },

        /**
         * Return the preview URL for the field.
         */
        videoUrl() {
            return this.field.previewUrl
        },

        /**
         * Determine the maximum width of the field.
         */
        maxWidth() {
            return this.field.maxWidth || 320
        },

        /**
         * Determing if the field is a Vapor field.
         */
        isVaporField() {
            return this.field.component == 'vapor-file-field'
        },

        laruploadIsOn() {
            return this.field.laruploadIsOn
        }
    },
    methods: {
        /**
         * Respond to the file change
         */
        fileChange(event) {
            let path = event.target.value
            let fileName = path.match(/[^\\/]*$/)[0]
            this.fileName = fileName
            let extension = fileName.split('.').pop()
            this.file = this.$refs.fileField.files[0]

            if (this.isVaporField) {
                this.uploading = true
                this.$emit('file-upload-started')

                Vapor.store(this.$refs.fileField.files[0], {
                    progress: progress => {
                        this.uploadProgress = Math.round(progress * 100)
                    },
                }).then(response => {
                    this.vaporFile.key = response.key
                    this.vaporFile.uuid = response.uuid
                    this.vaporFile.filename = fileName
                    this.vaporFile.extension = extension
                    this.uploading = false
                    this.uploadProgress = 0
                    this.$emit('file-upload-finished')
                })
            }
        },

        focusOnFileInput() {
            this.$refs.fileField.click()
        },

        focusOnCoverInput() {
            this.$refs.thumbnailField.click()
        },

        /**
         * Respond to the thumbnail file change
         */
        thumbnailFileChange(event) {
            let path = event.target.value
            let fileName = path.match(/[^\\/]*$/)[0]
            this.thumbnailFileName = fileName
            this.thumbnailFile = this.$refs.thumbnailField.files[0]
        },

        /**
         * Confirm removal of the linked file
         */
        confirmRemoval() {
            this.removeModalOpen = true
        },

        /**
         * Close the upload removal modal
         */
        closeRemoveModal() {
            this.removeModalOpen = false
        },

        /**
         * Remove the linked file from storage
         */
        async removeFile() {
            this.uploadErrors = new Errors()

            const {
                resourceName,
                resourceId,
                relatedResourceName,
                relatedResourceId,
                viaRelationship,
            } = this
            const attribute = this.field.attribute

            const uri = this.viaRelationship
                ? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
                : `/nova-api/${resourceName}/${resourceId}/field/${attribute}`

            try {
                await Nova.request().delete(uri)
                this.closeRemoveModal()
                this.deleted = true
                this.$emit('file-deleted')
                Nova.success(this.__('The file was deleted!'))
            }
            catch (error) {
                this.closeRemoveModal()

                if (error.response.status == 422) {
                    this.uploadErrors = new Errors(error.response.data.errors)
                }
            }
        },
    },
    mounted() {
        this.field.fill = formData => {
            if (this.file && !this.isVaporField) {
                formData.append(this.field.attribute, this.file, this.fileName)
            }

            if (this.file && this.isVaporField) {
                formData.append(this.field.attribute, this.fileName)
                formData.append('vaporFile[key]', this.vaporFile.key)
                formData.append('vaporFile[uuid]', this.vaporFile.uuid)
                formData.append('vaporFile[filename]', this.vaporFile.filename)
                formData.append('vaporFile[extension]', this.vaporFile.extension)
            }

            if (this.thumbnailFile) {
                formData.append(this.field.attribute + '_larupload_cover', this.thumbnailFile);
            }
        }
    }
}
</script>

<style lang="scss" scoped>
video {
    width: 500px;
    max-width: 100%;
    object-fit: cover;
    outline: none;
    border: none;
}
</style>
