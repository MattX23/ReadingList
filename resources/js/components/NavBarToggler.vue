<template>
    <ul class="">
        <li @click="toggleSideBar" class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

            </a>
        </li>
    </ul>
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
            toggleSideBar() {
                console.log("Toggle time");
            },
        }
    }
</script>

<style type="scss" scoped>
    ul  {
        list-style: none;
    }
</style>
