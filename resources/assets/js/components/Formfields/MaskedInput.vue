<template>
    <div v-if="view == 'browse'">
        {{ $parent.getValue() || getTranslation(options.value || '') }}
    </div>
    <div v-else-if="view == 'read'">
        <p>
            {{ $parent.getValue() || getTranslation(options.value || '') }}
        </p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'">
        <the-mask
            v-bind:value="$parent.getValue() || getTranslation(options.value || '')"
            v-on:input="$parent.setValue($event)"
            class="form-control"
            :disabled="view == 'mockup'"
            :mask="options.mask || ''"
            :type="options.type || 'text'"
            :masked="false"
            :placeholder="getTranslation(options.placeholder || '')"
        ></the-mask>
    </div>
    <div v-else-if="view == 'options'">
        <div class="form-group">
            <div class="form-group">
                <label>Mask</label>
                <input type="text" v-model="options.mask" class="form-control" placeholder="Mask">
            </div>
            <label>Type</label>
            <select class="form-control" v-model="options.type">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="tel">Telephone Number</option>
                <option value="email">E-Mail</option>
                <option value="url">URL</option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['view', 'layoutType', 'options', 'base']
};
</script>