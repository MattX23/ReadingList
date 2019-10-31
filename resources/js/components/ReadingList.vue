<template>
    <div class="reading-list">
        <div class="card">
            <div class="card-header text-center">
                <h3>Animals</h3>
            </div>
            <div class="card-body">
                <div class="row link-content">
                    <div class="col-2 img-block">
                        img
                    </div>
                    <div class="col-10">
                        <div>
                            LINK HEADER
                        </div>
                        <div>
                            Link content
                        </div>
                    </div>
                    <div class="col-12 footer">
                        footer
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {

            }
        },
        mounted() {

        },
        computed: {

        },
        methods: {

        }
    }
</script>

<style type="scss" scoped>
    .card {
        border: none;
    }
    .card-header {
        height: 50px;
    }
    .footer {
        border: 1px solid silver;
    }
    .img-block {
        border: 1px solid silver;
    }
    .link-content {
        min-height: 100px;
    }
    .reading-list {
        margin-bottom: 15px;
    }
</style>
