<template>
    <ul class="">
        <li class="nav-item dropdown">
            <span @click.stop="toggleSideBar()" class="nav-link dropdown-toggle">

            </span>
        </li>
    </ul>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                state: false,
            }
        },
        mounted() {

        },
        computed: {

        },
        methods: {
            toggleSideBar() {
                EventBus.$emit('toggle-sidebar', this.state);
                this.state = !this.state;
            },
        }
    }
</script>

<style type="scss" scoped>
    ul  {
        list-style: none;
    }
    .dropdown-toggle {
        cursor: pointer;
    }
</style>
