<template>
    <panel-item :index="index" :field="field">
        <template #value>
            <video-player
                v-if="shouldShowVideo"
                :src="src"
                :poster="imageUrl ?? ''"
                :type="field.playerType"
                :dir="field.dir"
                :is-details="true"
            />

            <span v-else>&mdash;</span>

            <p v-if="shouldShowToolbar" class="flex items-center text-sm mt-3">
                <link-button v-if="field.downloadable" @keydown.enter.prevent="download" @click.prevent="download" :dusk="field.attribute + '-download-link'" tabindex="0">
                    <icon class="mr-2" type="download" width="16" height="16" />
                    <span class="class mt-1">{{ __('Download') }}</span>
                </link-button>
            </p>
        </template>
    </panel-item>
</template>

<script>
import VideoPlayer from "./VideoPlayer.vue";

export default {
    components: {VideoPlayer},
    props: ['resource', 'resourceName', 'resourceId', 'field', 'index'],
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
    },
}
</script>
