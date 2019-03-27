<template>
    <div v-if="view == 'browse'">
        {{ $parent.getValue() || getTranslation(options.value || '') }}
    </div>
    <div v-else-if="view == 'read'">
        <p>
            {{ $parent.getValue() || getTranslation(options.value || '') }}
        </p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'" style="text-align:center">
        <swatches :disabled="view == 'mockup'"
            :shapes="options.shape || 'circles'"
            :inline="options.inline"
            :colors="options.palette || 'basic'"
            v-bind:value="$parent.getValue() || getTranslation(options.value || '')"
            v-on:input="$parent.setValue($event.target.value)"
            />
        
    </div>
    <div v-else-if="view == 'options'">
        <div class="checkbox">
            <label><input type="checkbox" :value="true" v-model="options.inline"> Inline</label>
        </div>
        <div class="form-group">
            <label>Palette</label>
            <select class="form-control" v-model="options.palette">
                <option value="basic">Basic Colors</option>
                <option value="text-basic">Basic Colors for Text</option>
                <option value="text-advanced">Advanced Colors for Text</option>
                <option value="material-basic">Basic Material Colors</option>
                <option value="material-light">Light Material Colors</option>
                <option value="material-dark">Dark Material Colors</option>
            </select>
        </div>
        <div class="form-group">
            <label>Shape</label>
            <select class="form-control" v-model="options.shape">
                <option value="circles">Circles</option>
                <option value="squares">Squares</option>
            </select>
        </div>
    </div>
</template>

<script>
import Swatches from 'vue-swatches';
import "vue-swatches/dist/vue-swatches.min.css";

export default {
    components: { Swatches },
    props: ['view', 'layoutType', 'options', 'base'],
    created: function () {
        this.base.placeholder = false;
        this.base.default_value = false;
    }
};
</script>