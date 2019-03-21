<template>
    <div class="language-selector" v-if="$eventHub.translatable">
        <div class="btn-group btn-group-sm" role="group" data-toggle="buttons">
            <label v-for="locale in $eventHub.locales" :class="'btn btn-primary ' + ($eventHub.locale == locale ? 'active' : '')" @click="setLocale(locale)" :key="locale">
                <input type="radio" name="i18n_selector" :checked="$eventHub.locale == locale"> {{ locale | uppercase }}
            </label>
        </div>
    </div>
</template>

<script>
module.exports = {
    methods: {
        setLocale: function(locale) {
            this.$eventHub.locale = locale;
        },
    },
};
</script>