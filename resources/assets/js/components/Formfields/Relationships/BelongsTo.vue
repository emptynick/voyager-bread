<template>
    <div v-if="view == 'browse'">Browse</div>
    <div v-else-if="view == 'read'">
        <p>Read</p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'">
        Edit/Add/Mockup
    </div>
    <div v-else-if="view == 'options'">
        <div class="form-group">
            <label>Relationship</label>
            <select class="form-control" v-model="options.relationship">
                <option v-for="relationship in base.getRelationships('BelongsTo')" :key="relationship.name">
                    {{ relationship.name }}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>List</label>
            <select class="form-control" v-model="options.list">
                <option v-for="layout in base.getRelationshipLayouts(options.relationship)">
                    
                </option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['view', 'layoutType', 'options', 'relationships'],
    data: function () {
        return {
            base: null,
        };
    },
    created: function () {
        this.base = this.$parent.$parent;
    }
};
</script>