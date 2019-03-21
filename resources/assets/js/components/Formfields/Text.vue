<template>
    <div v-if="view == 'browse'">{{ $parent.getValue().substring(0, options.length || 200) || getTranslation(options.value || '') }}</div>
    <div v-else-if="view == 'read'">
        <p>{{ $parent.getValue() }}</p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'">
        <input type="text" class="form-control"
                v-bind:value="$parent.getValue() || getTranslation(options.value || '')"
                v-on:input="$parent.setValue($event.target.value)"
                :placeholder="getTranslation(options.placeholder || '')"
                :disabled="view == 'mockup'">
    </div>
    <div v-else-if="view == 'options'">
        <div v-if="layoutType == 'list'">
            <div class="form-group">
                <label>Display length</label>
                <input class="form-control" placeholder="Display length" v-model="options.length" type="number" />
            </div>
            <div class="form-group">
                <label>Default value</label>
                <language-input classes="form-control" placeholder="Default Value" v-model="options.value" />
            </div>
        </div>
        <div v-else>

        </div>
    </div>
</template>

<script>
export default {
    props: ['view', 'layoutType', 'options']
};
</script>