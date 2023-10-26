<template>
    <div class="video" :dir="dir" :class="[mode, {small: !isDetails}]">
        <media-player
            v-if="mode === 'custom'"
            @click.stop.prevent
            class="media-player"
            :title="title"
            :src="src"
            crossorigin
        >
            <media-poster v-if="poster" class="vds-poster" :src="poster" :alt="title" />
            <media-provider/>
            <media-video-layout/>
        </media-player>

        <video
            v-else
            controls
            :src="src"
            :autoplay="false"
            :poster="poster"
        />
    </div>
</template>

<script setup>
import {computed} from 'vue'
import 'vidstack/player'
import 'vidstack/player/layouts'
import 'vidstack/player/ui'


// variables
const props = defineProps({
    src: {
        type: String,
        required: true
    },
    poster: {
        type: String,
        default: ''
    },
    dir: {
        type: String,
        default: 'ltr',
        validator(value) {
            return ['ltr', 'rtl', 'auto'].includes(value)
        }
    },
    mode: {
        type: String,
        default: 'custom',
        validator(value) {
            return ['custom', 'default'].includes(value)
        }
    },
    isDetails: {
        type: Boolean,
        required: true
    }
})


// computed properties
const title = computed(() => {
    return props.src.split('/').pop()
})
</script>

<style lang="scss" scoped>
.video {
    position: relative;
    width: 100%;
    min-width: 300px;
    display: inline-block;


    &.small {
        max-width: 270px;
    }

    ::v-deep(.vds-poster) {
        img {
            object-fit: cover;
        }
    }

    &.default {
        video {
            width: 100%;
            max-width: 100%;
            object-fit: cover;
            outline: none;
            border: none;
        }
    }
}
</style>
