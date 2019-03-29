<template>
    <ul class="pagination">
        <li v-if="!firstPageSelected()" :class="[firstPageSelected() ? 'disabled' : '']">
            <a @click="prevPage()" @keyup.enter="prevPage()" :tabindex="firstPageSelected() ? -1 : 0"
                v-html="prevText"></a>
        </li>

        <li v-for="(page, key) in allPages" :class="[page.selected ? 'active' : '', page.disabled ? 'disabled' : '']"
            :key="'pagination_'+key">
            <a v-if="page.breakView" tabindex="0">
                <slot name="breakViewContent">{{ breakViewText }}</slot>
            </a>
            <a v-else-if="page.disabled" tabindex="0">{{ page.content }}</a>
            <a v-else @click="handlePageSelected(page.index + 1)" @keyup.enter="handlePageSelected(page.index + 1)"
                tabindex="0">{{ page.content }}</a>
        </li>

        <li v-if="!lastPageSelected()" :class="[lastPageSelected() ? 'disabled' : '']">
            <a @click="nextPage()" @keyup.enter="nextPage()" :tabindex="lastPageSelected() ? -1 : 0"
                v-html="nextText"></a>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        value: {
            type: Number
        },
        pages: {
            type: Number,
            required: true
        },
        pageRange: {
            type: Number,
            default: 3
        },
        marginPages: {
            type: Number,
            default: 1
        },
        prevText: {
            type: String,
            default: "Prev"
        },
        nextText: {
            type: String,
            default: "Next"
        },
        breakViewText: {
            type: String,
            default: "…"
        }
    },
    beforeUpdate() {},
    computed: {
        selected: {
            get: function () {
                return this.value || this.innerValue;
            },
            set: function (newValue) {
                this.innerValue = newValue;
            }
        },
        allPages: function () {
            let items = {};
            if (this.pages <= this.pageRange) {
                for (let index = 0; index < this.pages; index++) {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === this.selected - 1
                    };
                    items[index] = page;
                }
            } else {
                const halfPageRange = Math.floor(this.pageRange / 2);
                let setPageItem = index => {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === this.selected - 1
                    };
                    items[index] = page;
                };
                let setBreakView = index => {
                    let breakView = {
                        disabled: true,
                        breakView: true
                    };
                    items[index] = breakView;
                };
                // 1st - loop thru low end of margin pages
                for (let i = 0; i < this.marginPages; i++) {
                    setPageItem(i);
                }
                // 2nd - loop thru selected range
                let selectedRangeLow = 0;
                if (this.selected - halfPageRange > 0) {
                    selectedRangeLow = this.selected - 1 - halfPageRange;
                }
                let selectedRangeHigh = selectedRangeLow + this.pageRange - 1;
                if (selectedRangeHigh >= this.pages) {
                    selectedRangeHigh = this.pages - 1;
                    selectedRangeLow = selectedRangeHigh - this.pageRange + 1;
                }
                for (let i = selectedRangeLow; i <= selectedRangeHigh && i <= this.pages - 1; i++) {
                    setPageItem(i);
                }
                if (selectedRangeLow > this.marginPages) {
                    setBreakView(selectedRangeLow - 1);
                }
                if (selectedRangeHigh + 1 < this.pages - this.marginPages) {
                    setBreakView(selectedRangeHigh + 1);
                }
                for (let i = this.pages - 1; i >= this.pages - this.marginPages; i--) {
                    setPageItem(i);
                }
            }
            return items;
        }
    },
    data() {
        return {
            innerValue: 1
        };
    },
    methods: {
        handlePageSelected(selected) {
            if (this.selected === selected) return;
            this.innerValue = selected;
            this.$emit("input", selected);
        },
        prevPage() {
            if (this.selected <= 1) return;
            this.handlePageSelected(this.selected - 1);
        },
        nextPage() {
            if (this.selected >= this.pages) return;
            this.handlePageSelected(this.selected + 1);
        },
        firstPageSelected() {
            return this.selected === 1;
        },
        lastPageSelected() {
            return this.selected === this.pages || this.pages === 0;
        },
        selectFirstPage() {
            if (this.selected <= 1) return;
            this.handlePageSelected(1);
        },
        selectLastPage() {
            if (this.selected >= this.pages) return;
            this.handlePageSelected(this.pages);
        }
    }
};
</script>
<style scoped>
a {
    cursor: pointer;
}
</style>