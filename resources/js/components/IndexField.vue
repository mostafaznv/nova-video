<template>
    <p>
        <video
            v-if="field.previewUrl"
            class="block"
            controls
            controlslist="nodownload"
            :src="src"
            :poster="poster"
            :autoplay="autoplay"
        />
        <span v-else>&mdash;</span>
    </p>
</template>

<script>
export default {
    props: ['viaResource', 'viaResourceId', 'resourceName', 'field'],

    data() {
        return {
            src: this.field.previewUrl,
            poster: this.field.thumbnailUrl,
            autoplay: false,
            preload: 'none'
        }
    }
}
</script>

<style lang="scss" scoped>
video {
    max-width: 250px !important;
    object-fit: cover;
    outline: none;
    border: none;
    margin: 5px auto;
}
</style>
