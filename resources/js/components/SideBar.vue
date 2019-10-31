<template>
    <div v-show="showMenu" class="side-bar">
<!--        <div class="text-right toggle-container"><span class="toggle-sidebar">X</span></div>-->
        <div class="menu-item">New List</div>
        <div class="menu-item">Archives</div>
        <div class="menu-item">Logout</div>

<!--        <a class="dropdown-item" href="{{ route('logout') }}"-->
<!--           onclick="event.preventDefault();-->
<!--                                                     document.getElementById('logout-form').submit();">-->
<!--            {{ __('Logout') }}-->
<!--        </a>-->

<!--        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">-->
<!--            @csrf-->
<!--        </form>-->
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showMenu: false,
            }
        },
        mounted() {

        },
        created() {
            EventBus.$on('toggle-sidebar', (state) => {
                !state ? (this.showMenu = true) : (this.showMenu = false);
            });
        },
        computed: {

        },
        methods: {

        }
    }
</script>

<style type="scss" scoped>
    .menu-item {
        font-size: 1.15rem;
        text-align: center;
        border-top: 1px solid silver;
        padding-top: 0.25rem;
        line-height: 3.5rem;
    }
    .menu-item:hover {
        background: silver;
        color: white;
    }
    .side-bar {
        width: 400px;
        background: white;
        position: fixed;
        right: 0;
        top: 55px;
    }
    .toggle-container {
        height: 20px;
        margin-bottom: 15px;
    }
    .toggle-sidebar {
        float: right;
        margin-right: 25px;
    }
</style>
