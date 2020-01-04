<template>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body" @keydown.enter="submitForm">
                        <div class="form-group">
                            <input type="email"
                                   v-model="user.email"
                                   @blur="checkEmail"
                                   @keyup="clearError('email')"
                                   class="form-control login-input"
                                   placeholder="Enter your email address"
                                   required>
                            <small v-if="errors.email" class="text-danger">
                                {{ errors.email }}
                            </small>
                        </div>
                        <div class="form-group" v-show="register">
                            <input type="text"
                                   v-model="user.username"
                                   @keyup="clearError('username')"
                                   class="form-control login-input"
                                   placeholder="Enter a username"
                                   :required="register">
                            <small v-if="errors.username" class="text-danger">
                                {{ errors.username }}
                            </small>
                        </div>
                        <div class="form-group">
                            <input type="password"
                                   v-model="user.password"
                                   @blur="checkPassword"
                                   @keyup="clearError('password')"
                                   class="form-control login-input"
                                   :placeholder="passwordPlaceHolder"
                                   required
                                   autocomplete="new-password">
                            <small v-if="errors.password" class="text-danger">
                                {{ errors.password }}
                            </small>
                        </div>
                        <div class="form-group" v-show="register">
                            <input type="password"
                                   v-model="user.passwordConfirm"
                                   @blur="checkPasswordConfirmation"
                                   @keyup="clearError('passwordConfirm')"
                                   class="form-control login-input"
                                   placeholder="Confirm your password"
                                   :required="register"
                                   autocomplete="new-password">
                            <small v-if="errors.passwordConfirm && !errors.password" class="text-danger">
                                {{ errors.passwordConfirm }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success" @click="submitForm">{{ buttonText }}</button>
                        <div class="text-center input-type-selector">
                            {{ accountStatusText }} have an account? <a href="javascript:void(0);" @click="switchType">{{ switchText }}</a>
                        </div>
                        <div class="text-center input-type-selector">
                            <a :href="passwordResetLink"><small>Forgot your password?</small></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const SIGN_IN = 'Sign in';
    const REGISTER = 'Register';
    const LOGIN_ROUTE = '/api/login';
    const REGISTER_ROUTE = '/api/register';

    export default {
        props: ['passwordResetLink'],
        data() {
            return {
                errors: {
                    email: '',
                    password: '',
                    passwordConfirm: '',
                    username: '',
                },
                passwordPlaceHolder: 'Enter your password',
                register: false,
                user: {
                    email: '',
                    password: '',
                    passwordConfirm: '',
                    username: '',
                }
            }
        },
        mounted() {
            this.showCorrectForm();
        },
        computed: {
            accountStatusText() {
                if (!this.register) {
                    return "Don't";
                } else {
                    return "Already";
                }
            },
            buttonText() {
                if (this.register === false) {
                    return SIGN_IN;
                } else {
                    return REGISTER;
                }
            },
            switchText() {
                if (this.register === false) {
                    return REGISTER;
                } else {
                    return SIGN_IN;
                }
            }
        },
        methods: {
            checkEmail() {
                if (this.register && this.user.email) {
                    axios.post('/api/check-email', {
                        email: this.user.email
                    })
                    .then((response)  => {
                        this.errors.email = response.data;
                    })
                }
            },
            checkPassword() {
                if (this.user.password.length && this.user.password.length < 8) {
                    this.errors.password = 'Your password is too short';
                }
            },
            checkEmptyEmail() {
                if (this.user.email.length < 1) {
                    this.errors.email = 'Please enter your email address';
                }
            },
            checkEmptyPassword() {
                if (this.user.password.length < 1) {
                    this.errors.password = 'Please enter your password';
                }
            },
            checkEmptyUsername() {
                if (this.user.username.length < 1 && this.register) {
                    this.errors.username = 'Please enter your username';
                }
            },
            checkPasswordConfirmation() {
                if (this.register) {
                    if (
                        this.user.password &&
                        this.user.passwordConfirm.length < 1
                    ) {
                        this.errors.passwordConfirm = 'You must confirm your password';
                    } else if (this.user.passwordConfirm !== this.user.password) {
                        this.errors.passwordConfirm = 'Your passwords do not match';
                    }
                }
            },
            clearAllErrors() {
                this.errors = {
                    email: '',
                    password: '',
                    passwordConfirm: '',
                    username: '',
                }
            },
            clearError(input) {
                if (input === 'email') {
                    this.errors.email = '';
                }

                if (input === 'password') {
                    this.errors.password = '';
                }

                if (input === 'passwordConfirm' && this.user.passwordConfirm === this.user.password) {
                    this.errors.passwordConfirm = '';
                }

                if (input === 'username') {
                    this.errors.username = '';
                }
            },
            hasErrors() {
                let result = false;
                for (let i in this.errors) {
                    if (this.errors[i] !== '') {
                        result = true;
                        break;
                    }
                }
                return result;
            },
            showCorrectForm() {
                const url = window.location.href;
                const n = url.lastIndexOf('/');
                const result = url.substring(n + 1);
                if (result === REGISTER.toLowerCase()) {
                    this.register = true;
                }
            },
            switchType() {
                this.clearAllErrors();
                if (!this.register) {
                    this.register = true;
                    this.passwordPlaceHolder = 'Choose a password';
                    window.history.pushState('register', 'Title', '/register');
                } else {
                    this.register = false;
                    this.passwordPlaceHolder = 'Enter your password';
                    window.history.pushState('login', 'Title', '/login');
                }
            },
            submitForm() {
                this.clearAllErrors();
                this.checkEmptyEmail();
                this.checkEmptyPassword();
                this.checkEmptyUsername();
                this.checkPasswordConfirmation();

                if (!this.hasErrors()) {
                    let route = LOGIN_ROUTE;

                    let data = {
                        email: this.user.email,
                        password: this.user.password,
                    };

                    if (this.register) {
                        route = REGISTER_ROUTE;
                        data.username = this.user.username;
                        data.password_confirmation = this.user.passwordConfirm;
                    }

                    axios.post(route, data)
                    .then(() => {
                        location.href = '/home';
                    })
                    .catch((error) => {
                        if (error.response.status === 403) location.href = '/email/verify';

                        const errors = error.response.data.errors;
                        if (errors.email) {
                            this.errors.email = errors.email[0];
                        }

                        if (errors.username) {
                            this.errors.username = errors.username[0];
                        }

                        if (errors.password) {
                            this.errors.password = errors.password[0];
                        }
                    });
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../sass/variables';

    .card-footer {
        background: $white;
    }
    .input-type-selector {
        margin-top: 10px;
    }
    .login-container {
        margin-top: 10%;
    }
    .login-input {
        height: 50px;
        border: 0px;
        border-bottom: 1px solid $slategray;
        border-radius: 0px;
    }
</style>
