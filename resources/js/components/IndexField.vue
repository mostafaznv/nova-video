<template>
    <p>
        <video-player
            v-if="field.previewUrl"
            :src="src"
            :poster="poster"
            :type="field.playerType"
            :dir="field.dir"
            :is-details="false"
        />

        <span v-else>&mdash;</span>
    </p>
</template>

<script>
import VideoPlayer from "./VideoPlayer.vue";

export default {
    components: {VideoPlayer},
    props: ['viaResource', 'viaResourceId', 'resourceName', 'field'],

    data() {
        return {
            src: this.field.previewUrl,
            poster: this.field.thumbnailUrl ?? '',
            autoplay: false,
            preload: 'none'
        }
    }
}
</script>
