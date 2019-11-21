require('./bootstrap');

window.Vue = require('vue');

Vue.component('alert', require('./components/FlashMessage.vue').default);
Vue.component('archive-modal', require('./components/modals/ArchiveModal.vue').default);
Vue.component('confirmation-modal', require('./components/modals/ConfirmationModal.vue').default);
Vue.component('home-page', require('./components/HomePage.vue').default);
Vue.component('login-form', require('./components/LoginForm.vue').default);
Vue.component('reading-link', require('./components/Link.vue').default);
Vue.component('reading-list', require('./components/ReadingList.vue').default);
Vue.component('side-bar', require('./components/partials/SideBar.vue').default);
Vue.component('single-input-modal', require('./components/modals/SingleInputModal.vue').default);
Vue.component('toggler', require('./components/partials/NavBarToggler.vue').default);

const app = new Vue({
    el: '#app',
});
