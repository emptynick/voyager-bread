<template>
    <input type="text" v-bind:value="computedValue" v-on:input="setValue($event.target.value)" :class="classes" :placeholder="placeholder">
</template>

<script>
export default {
    props: [
        'name',
        'value',
        'classes',
        'placeholder',
        'slug'
    ],
    data: function () {
        return {
            translatedValue: ''
        };
    },
    methods: {
        setValue: function(value) {
            if (this.$eventHub.translatable) {
                Vue.set(this.translatedValue, this.$eventHub.locale, value);
            } else {
                this.translatedValue = value;
            }

            this.$emit('input', this.translatedValue);
        },
    },
    computed: {
        computedValue: function () {
            if (this.$eventHub.translatable) {
                if (typeof this.translatedValue !== 'object' && typeof this.value !== 'object') {
                    this.translatedValue = JSON.parse(this.value || '{}');
                }

                return this.translatedValue[this.$eventHub.locale];
            }
            return this.translatedValue;
        }
    },
    mounted: function () {
        this.translatedValue = this.value;
    },
    watch: {
        slug: {
            handler: function (value) {
                if (typeof value === 'object') {
                    this.setValue(this.slugify(value[this.$eventHub.locale]));
                } else {
                    this.setValue(this.slugify(value));
                }
            },
            deep: true
        }
    }
};
</script>

<style scoped>
</style>