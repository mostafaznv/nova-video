export default {
    computed: {
        fieldWrapperClasses() {
            return [
                'space-y-2',
                'md:flex @md/modal:flex',
                'md:flex-row @md/modal:flex-row',
                'md:space-y-0 @md/modal:space-y-0',
                this.field.withLabel && !this.field.inline && (this.field.compact ? 'py-3' : 'py-5'),
                this.field.stacked && 'md:flex-col @md/modal:flex-col md:space-y-2 @md/modal:space-y-2',
            ]
        },

        labelClasses() {
            return [
                'w-full',
                this.field.compact ? '!px-3' : 'px-6',
                !this.field.stacked && 'md:mt-2 @md/modal:mt-2',
                this.field.stacked && !this.field.inline && 'md:px-8 @md/modal:px-8',
                !this.field.stacked && !this.field.inline && 'md:px-8 @md/modal:px-8',
                this.field.compact && 'md:!px-6 @md/modal:!px-6',
                !this.field.stacked && !this.field.inline && 'md:w-1/5 @md/modal:w-1/5',
            ]
        },

        controlWrapperClasses() {
            return [
                'w-full space-y-2',
                this.field.compact ? '!px-3' : 'px-6',
                this.field.compact && 'md:!px-4 @md/modal:!px-4',
                this.field.stacked && !this.field.inline && 'md:px-8 @md/modal:px-8',
                !this.field.stacked && !this.field.inline && 'md:px-8 @md/modal:px-8',
                !this.field.stacked && !this.field.inline && !this.field.fullWidth && 'md:w-3/5 @md/modal:w-3/5',
                this.field.stacked && !this.field.inline && !this.field.fullWidth && 'md:w-3/5 @md/modal:w-3/5',
                !this.field.stacked && !this.field.inline && this.field.fullWidth && 'md:w-4/5 @md/modal:w-4/5',
            ]
        },
    },
}
