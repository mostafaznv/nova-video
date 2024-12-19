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
                <Button
                    v-if="field.downloadable"
                    @keydown.enter.prevent="download"
                    @click.prevent="download"
                    variant="ghost"
                    tabindex="0"
                    :dusk="field.attribute + '-download-link'"
                >
                    <Icon name="arrow-down-tray" type="mini" class="mr-2" />
                    <span class="class mt-1">{{ __('Download') }}</span>
                </Button>
            </p>
        </template>
    </panel-item>
</template>

<script>
import {Button, Icon} from 'laravel-nova-ui'
import VideoPlayer from '../components/VideoPlayer.vue'


export default {
    components: {
        VideoPlayer, Button, Icon
    },
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


            if (this.field.mode === 'URL') {
                link.href = this.field.thumbnailUrl
                link.target = '_blank'
                link.download = link.href.split('/').pop()
            }
            else {
                link.href = `/nova-api/${resourceName}/${resourceId}/download/${attribute}`
                link.download = 'download'
            }


            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
        }
    },
}
</script>
