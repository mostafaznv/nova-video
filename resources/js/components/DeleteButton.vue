<template>
    <danger-button
        type="button"
        @keydown.enter.prevent="$emit('click')"
        @click.prevent="$emit('click')"
        tabindex="0"
        class="cursor-pointer dim btn btn-link text-primary inline-flex items-center"
    >
        <icon type="trash" class="inline-block" width="20" style="top:4px;"/>
        <slot />
    </danger-button>
</template>

<script>
export default {}
</script>
