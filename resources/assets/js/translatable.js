var Translatable = {
    data: function() {
        return {

        }
    },
    mounted: function() {
        var vm = this;
        if (this.translatable) {
            this.translatable.forEach(function(attr) {
                vm.$watch("options." + attr, function(key) {
                    this.$bus.$emit('updateItemsHeight')
                    if (!key.includes(" ") && key != "") {
                        vm.$bus.$emit("requestTranslation", key, attr);
                    }
                    vm[attr] = key;
                    vm.$parent[attr] = key;
                });

                vm[attr] = vm.options[attr];
                if (vm[attr] !== undefined && !vm[attr].includes(" ") && vm[attr] != "") {
                    vm.$bus.$emit("requestTranslation", vm[attr], attr);
                }
            });

            this.$bus.$on('translationReceived', (key, data, property) => {
                if (this.options[property] && this.options[property] == key) {
                    this[property] = data;
                    this.$parent[property] = data;
                }
            });
        }
    },
};
