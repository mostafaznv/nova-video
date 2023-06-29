<template>
    <panel-item :field="field">
        <template #value>
            <template v-if="shouldShowVideo">
                <video controls :src="src" :autoplay="autoplay" :poster="imageUrl" />
            </template>

            <span v-else>&mdash;</span>

            <p v-if="shouldShowToolbar" class="flex items-center text-sm mt-3">
                <a v-if="field.downloadable" :dusk="field.attribute + '-download-link'"
                   tabindex="0" class="cursor-pointer dim btn btn-link text-primary inline-flex items-center"
                   @keydown.enter.prevent="download"
                   @click.prevent="download">
                    <icon class="mr-2" type="download" view-box="0 0 24 24" width="16" height="16" />
                    <span class="class mt-1">{{ __('Download') }}</span>
                </a>
            </p>
        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
    data() {
        return {
            src: this.field.previewUrl,
            autoplay: false,
        }
    },
    computed: {
        shouldShowVideo() {
            return Boolean(this.field.previewUrl)
        },

        shouldShowToolbar() {
            return Boolean(this.field.downloadable && this.field.thumbnailUrl)
        },

        imageUrl() {
            return this.field.thumbnailUrl
        },
    },
    methods: {
        download() {
            const {resourceName, resourceId} = this
            const attribute = this.field.attribute

            let link = document.createElement('a')
            link.href = `/nova-api/${resourceName}/${resourceId}/download/${attribute}`
            link.download = 'download'
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
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
