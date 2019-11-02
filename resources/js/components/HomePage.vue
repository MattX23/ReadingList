<template>
    <div class="container-fluid">
        <div class="row">
            <div v-for="readingList in readingLists" class="col-xl-4 col-md-6">
                <reading-list
                :name="readingList.name"
                :id="readingList.id"></reading-list>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                readingLists: {},
            }
        },
        mounted() {
            axios.get('/api/lists/get')
            .then((response) => {
                this.readingLists = response.data.readingLists;
            })
        },
        computed: {

        },
        methods: {

        }
    }
</script>

<style type="scss" scoped>

</style>
