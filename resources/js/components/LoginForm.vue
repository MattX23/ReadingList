<template>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
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
                        <button class="btn btn-success" @click="submitForm">{{ buttonText }}</button>
                        <div class="text-center input-type-selector">
                            {{ accountStatusText }} have an account? <a href="javascript:void(0);" @click="switchType">{{ switchText }}</a>
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
    export default {
        data() {
            return {
                accountStatusText: "Don't",
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
                    .catch((error) => {
                        console.log(error);
                    });
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
                if (this.user.username.length < 1) {
                    this.errors.username = 'Please enter your username';
                }
            },
            checkPasswordConfirmation() {
                if (this.user.password &&
                    this.user.passwordConfirm.length &&
                    this.user.passwordConfirm  < 1) {
                    this.errors.passwordConfirm = 'You must confirm your password';
                } else if (this.user.passwordConfirm !== this.user.password) {
                    this.errors.passwordConfirm = 'Your passwords do not match';
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
            hasNoErrors() {
                let result = true;
                for (let i in this.errors) {
                    if (this.errors[i] !== '') {
                        result = false;
                        break;
                    }
                }
                return result;
            },
            showCorrectForm() {
                const str = window.location.href;
                const n = str.lastIndexOf('/');
                const result = str.substring(n + 1);
                if (result === REGISTER.toLowerCase()) {
                    this.register = true;
                }
            },
            switchType() {
                if (!this.register) {
                    this.register = true;
                    this.accountStatusText = 'Already';
                    this.passwordPlaceHolder = 'Choose a password';
                    window.history.pushState('register', 'Title', '/register');
                } else {
                    this.register = false;
                    this.accountStatusText = "Don't";
                    this.passwordPlaceHolder = 'Enter your password';
                    window.history.pushState('login', 'Title', '/login');
                }
            },
            submitForm() {
                this.checkEmptyEmail();
                this.checkEmptyPassword();
                this.checkEmptyUsername();
                if (this.hasNoErrors()) {
                    let route = '/api/register';

                    let data = {
                        email: this.user.email,
                        password: this.user.password,
                    };

                    if (!this.register) {
                        route = 'api/login';
                        data.username = this.user.username;
                        data.password_confirmation = this.user.passwordConfirm;
                    }

                    axios.post(route, data)
                        .then(function (response) {
                            document.querySelector('meta[name="api-token"]').setAttribute("content", response.data);
                            location.href = '/home';
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
        }
    }
</script>

<style type="text/css">
    .card-footer {
        background: white;
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
        border-bottom: 1px solid slategray;
        border-radius: 0px;
    }
</style>
