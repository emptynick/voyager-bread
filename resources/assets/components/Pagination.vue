<template>
    <ul class="pagination">
        <li v-if="page != 1">
            <a href="#" @click.prevent="openPage(1)">&laquo;</a>
            </li>
        <li v-if="page != 1">
            <a href="#" @click.prevent="openPage(previousPage)">&lt;</a>
        </li>
        <li v-for="p in pages" v-bind:class="[p == page ? 'active' : '']" :key="'pagination_'+p">
            <a href="#" @click.prevent="openPage(p)">{{ p }}</a>
        </li>
        <li v-if="page != pages">
            <a href="#" @click.prevent="openPage(pages)">&raquo;</a>
            </li>
        <li v-if="page != pages">
            <a href="#" @click.prevent="openPage(nextPage)">&gt;</a>
        </li>
    </ul>
</template>

<script>
module.exports = {
    props: ['pages', 'page', 'callback', 'left', 'right'],
    methods: {
        openPage: function (page) {
            if (this.callback) {
                this.callback(page);
            }
        }
    },
    computed: {
        previousPage: function () {
            var p = this.page -1;
            if (p < 1) {
                return 1;
            }

            return p;
        },
        nextPage: function () {
            var p = this.page + 1;
            if (p > this.pages) {
                return this.pages;
            }

            return p;
        }
    }
};
</script>