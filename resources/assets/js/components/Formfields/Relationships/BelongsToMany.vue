<template>
    <div v-if="view == 'browse'">Browse</div>
    <div v-else-if="view == 'read'">
        <p>Read</p>
    </div>
    <div v-else-if="view == 'edit' || view == 'add' || view == 'mockup'">
        Edit/Add/Mockup
    </div>
    <div v-else-if="view == 'options'">
        <div v-if="layoutType == 'list'">
            
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