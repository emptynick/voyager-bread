<template>
    <div v-if="view == 'browse'">
        {{ getTranslation(options.prefix || '') }}
        {{ number_format(($parent.getValue() || getTranslation(options.value || '')), (options.decimals || 0), (options.dec_point || ','), (options.thousands_sep || '.')) }}
        {{ getTranslation(options.suffix || '') }}
    </div>
    <div v-else-if="view == 'read'">
        <p>
            {{ getTranslation(options.prefix || '') }}
            {{ number_format(($parent.getValue() || getTranslation(options.value || '')), (options.decimals || 0), (options.dec_point || ','), (options.thousands_sep || '.')) }}
            {{ getTranslation(options.suffix || '') }}
        </p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'">
        <input type="number" class="form-control"
                v-bind:value="$parent.getValue() || getTranslation(options.value || '')"
                v-on:input="$parent.setValue($event.target.value)"
                :placeholder="getTranslation(options.placeholder || '')"
                :disabled="view == 'mockup'"
                :min="options.min || 0"
                :max="options.max || Number.MAX_SAFE_INTEGER"
                :step="options.step || 1">
    </div>
    <div v-else-if="view == 'options'">
        <div class="form-group">
            <label>Prefix</label>
            <language-input classes="form-control" placeholder="Prefix" v-model="options.prefix" />
        </div>
        <div class="form-group">
            <label>Suffix</label>
            <language-input classes="form-control" placeholder="Suffix" v-model="options.suffix" />
        </div>
        <div class="form-group">
            <label>Min</label>
            <input type="number" class="form-control" v-model="options.min">
        </div>
        <div class="form-group">
            <label>Max</label>
            <input type="number" class="form-control" v-model="options.max">
        </div>
        <div class="form-group">
            <label>Step</label>
            <input type="number" class="form-control" v-model="options.step">
        </div>
        <div class="form-group">
            <label>Decimals</label>
            <input type="number" class="form-control" v-model="options.decimals" min="0" max="100">
        </div>
        <div class="form-group">
            <label>Decimal separator</label>
            <input type="text" class="form-control" v-model="options.dec_point">
        </div>
        <div class="form-group">
            <label>Thousands separator</label>
            <input type="text" class="form-control" v-model="options.thousands_sep">
        </div>
    </div>
</template>

<script>
export default {
    props: ['view', 'layoutType', 'options'],
    computed: {

    }
};
</script>