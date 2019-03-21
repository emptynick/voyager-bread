<template>
    <div>
        <table class="table">
            <thead style="color: white">
                <th>Rule</th>
                <th>Message</th>
                <th></th>
            </thead>
            <tbody>
                <tr v-for="(rule, key) in value" :key="key">
                    <td>
                        <input type="text" class="form-control" v-model="value[key].rule" placeholder="Rule">
                    </td>
                    <td>
                        <language-input classes="form-control" v-model="value[key].message" placeholder="Message" />
                    </td>
                    <td><button class="btn btn-danger" @click="deleteRule(key)">Delete</button></td>
                </tr>
            </tbody>
        </table>
        <button class="btn btn-success" @click="addRule()">Add Rule</button>
    </div>
</template>

<script>
module.exports = {
    props: [
        'value',
    ],
    methods: {
        addRule: function () {
            this.value.push({
                'rule': '',
                'message': {}
            });
        },
        deleteRule: function (key) {
            this.value.splice(key, 1);
        }
    },
};
</script>