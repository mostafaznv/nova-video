<template>
    <div class="space-y-4">
        <div v-if="hasValue && previewFile && files.length === 0" class="grid grid-cols-4 gap-x-6 gap-y-2">
            <FilePreviewBlock
                v-if="previewFile"
                :file="previewFile"
                :removable="shouldShowRemoveButton"
                @removed="confirmRemoval"
                :rounded="field.rounded"
            />
        </div>

        <ConfirmUploadRemovalModal
            :show="removeModalOpen"
            @confirm="removeUploadedFile"
            @close="closeRemoveModal"
        />

        <DropZone
            v-if="shouldShowField"
            :files="files"
            @file-changed="handleFileChange"
            @file-removed="file = null"
            :rounded="field.rounded"
            :accepted-types="acceptedTypes"
            :disabled="file?.processing"
        />

        <HelpText class="help-text-error" v-if="hasError">
            {{ firstError }}
        </HelpText>
    </div>
</template>

<script>
import {HandlesValidationErrors, Errors} from 'laravel-nova'


function createFile(file) {
    return {
        name: file.name,
        extension: file.name.split('.').pop(),
        type: file.type,
        originalFile: file,
        processing: false,
        progress: 0,
    }
}

export default {
    name: 'FilePicker',
    mixins: [
        HandlesValidationErrors
    ],
    emits: [
        'update:modelValue'
    ],
    props: [
        'resourceId', 'resourceName', 'relatedResourceName', 'relatedResourceId',
        'viaRelationship', 'field', 'cover', 'isCover', 'errors', 'laruploadIsOn',
        'isReadonly'
    ],
    inject: [
        'removeFile'
    ],
    expose: [
        'beforeRemove',
        'afterRemove'
    ],
    data: () => ({
        file: null,
        previewFile: null,
        thumbnailFile: null,
        fileName: '',
        thumbnailFileName: '',
        removeModalOpen: false,
        missing: false,
        deleted: false,
        src: null,
        uploadErrors: new Errors(),
        uploading: false,
        thumbnailUploading: false,
        uploadProgress: 0,
        thumbnailUploadProgress: 0,
        startedDrag: false,
        uploadModalShown: false,
    }),
    computed: {
        value() {
            if (this.isCover) {
                return this.field.thumbnailUrl
            }

            return this.field.value
        },

        previewUrl() {
            if (this.isCover) {
                return this.field.thumbnailUrl
            }

            return this.field.previewUrl
        },

        fieldAttribute() {
            return this.field.attribute
        },

        acceptedTypes() {
            if (this.isCover) {
                return 'image/png, image/jpeg'
            }
            else if (this.field.acceptedTypes) {
                return this.field.acceptedTypes
            }

            return 'video/mp4, video/webm, video/ogg'
        },

        files() {
            return this.file ? [this.file] : []
        },

        hasError() {
            return this.uploadErrors.has(this.fieldAttribute)
        },

        firstError() {
            if (this.hasError) {
                return this.uploadErrors.first(this.fieldAttribute)
            }
        },

        hasValue() {
            return (
                Boolean(this.value || this.imageUrl)
                && !Boolean(this.deleted)
                && !Boolean(this.missing)
            )
        },

        shouldShowField() {
            return Boolean(!this.isReadonly)
        },

        shouldShowRemoveButton() {
            return Boolean(this.field.deletable && !this.isReadonly)
        },

        imageUrl() {
            return this.previewUrl || this.field.thumbnailUrl
        }
    },
    watch: {
        file: {
            immediate: true,
            handler(file) {
                this.$emit('update:modelValue', file)
            }
        },
        errors: {
            immediate: true,
            handler(e) {
                const attribute =
                    this.laruploadIsOn
                        ? this.isCover
                            ? this.fieldAttribute + '.cover'
                            : this.fieldAttribute + '.original'
                        : this.fieldAttribute

                if (e?.errors[attribute]) {
                    const errors = {}

                    errors[this.fieldAttribute] = e.errors[attribute]

                    this.uploadErrors = new Errors(errors)
                }
                else {
                    this.uploadErrors = new Errors()
                }
            }
        }
    },
    methods: {
        preparePreviewImage() {
            if (this.hasValue && this.imageUrl) {
                this.fetchPreviewImage()
            }

            if (this.hasValue && !this.imageUrl) {
                this.previewFile = createFile({
                    name: this.value,
                    type: this.value.split('.').pop(),
                })
            }
        },

        async fetchPreviewImage() {
            let response = await fetch(this.imageUrl)
            let data = await response.blob()

            const name = this.value.split('/').pop()

            this.previewFile = createFile(
                new File([data], name, {type: data.type})
            )
        },

        handleFileChange(newFiles) {
            this.file = createFile(newFiles[0])
        },

        confirmRemoval() {
            this.removeModalOpen = true
        },

        closeRemoveModal() {
            this.removeModalOpen = false
        },

        beforeRemove() {
            this.removeUploadedFile()
        },

        afterRemove() {
            this.deleted = true
            this.file = null
        },

        async removeUploadedFile() {
            try {
                await this.removeFile()

                this.afterRemove()

                this.$emit('file-deleted')
                Nova.success(this.__('The file was deleted!'))
            }
            catch (error) {
                if (error.response?.status === 422) {
                    this.uploadErrors = new Errors(error.response.data.errors)
                }
            }
            finally {
                this.closeRemoveModal()
            }
        },

        async removeFile() {
            this.uploadErrors = new Errors()

            const {
                resourceName,
                fieldAttribute,
                resourceId,
                relatedResourceName,
                relatedResourceId,
                viaRelationship
            } = this

            const uri = this.viaRelationship
                ? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${fieldAttribute}?viaRelationship=${viaRelationship}`
                : `/nova-api/${resourceName}/${resourceId}/field/${fieldAttribute}`

            const query = '?cover=' + (this.isCover ? 'true' : 'false')

            await Nova.request()
                .delete(uri + query)
                .finally(() => {
                    this.deleted = true
                })

        }
    },
    async mounted() {
        this.preparePreviewImage()
    },
}
</script>
