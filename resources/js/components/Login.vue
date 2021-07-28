<template>
  <div class="login">
    <div class="login__inner-wrapper">
      <div :class="{ 'not-centered': loginWindow}"
           class="login__left">

        <div v-if="loginWindow > 0"
             role="button"
             @click="loginWindow = 0"
             class="login__back">
        </div>

        <!--  Welcome choice    -->
        <div v-if="loginWindow === 0"
             class="login__welcome-wrapper">
          <h5 class="login__welcome">Welcome to {{ settings.site_name }}</h5>

          <div class="login__buttons">
            <button type="button"
                    @click="resetLogInTypeData()"
                    class="btn--secondary">
              I'm New
            </button>

            <button type="button"
                    @click="resetLogInTypeData(2)"
                    class="btn--transparent">
              I already have an account
            </button>
          </div>

          <div class="login__activate-wrapper">
            <a href="activate-account" class="login__activate-account">
              Activate my account
            </a>
          </div>
        </div>

        <!-- Sign in as a guess -->
        <div :key=1 v-if="loginWindow === 1"
             class="login__guess">

          <div class="login__user-image">
            <loading v-if="loading"
                     :animation-duration="1000"
                     :size="80"
                     color="#ff1d5e"/>
            <img src="/storage/user-logo.png" class="login__user-logo">
            <h4>{{ signInAsGuest }}</h4>
          </div>

          <div class="login__name">
            <input type="text"
                   v-model="guessName"
                   class="input--text"
                   minlength="1"
                   maxlength="14"
                   name="guess"
                   placeholder="Guest"
                   @keydown="keyInputHandler">
            </input>
          </div>

          <ul class="login__gender radio--buttons">
            <li class="login__gender-male">
              <input type="radio"
                     id="male"
                     value="m"
                     name="gender"
                     v-model="gender">
              <label for="male">
                Male
              </label>
              <div class="check">
                <div class="inside"></div>
              </div>
            </li>

            <li class="login__gender-female">
              <input type="radio"
                     id="female"
                     value="f"
                     name="gender"
                     v-model="gender">
              <label for="female">
                Female
              </label>
              <div class="check">
                <div class="inside"></div>
              </div>
            </li>
          </ul>

          <div class="login__sign-in">
            <button type="submit"
                    @click.prevent="login"
                    class="btn--secondary">
              Sign In
            </button>
          </div>

          <div class="login__footer">
            <a href=""
               class="login__create-account"
               @click.prevent="loginWindow = 2">
              Create an account
            </a>
          </div>
        </div>

        <!-- Sign in as a member -->
        <div v-if="loginWindow === 2"
             class="login__member">

          <div class="login__user-image">
            <loading v-if="loading"
                     :animation-duration="1000"
                     :size="80"
                     color="#ff1d5e"/>

            <img src="/storage/user-logo.png" class="login__user-logo">
            <h4> {{ signInAsMember }} </h4>
          </div>

          <div class="login__email">
            <input type="email"
                   v-model="email"
                   name="email"
                   class="input--text"
                   required
                   placeholder="Email">
            </input>
          </div>

          <div class="login__password">
            <input type="password"
                   v-model="password"
                   name="password"
                   minlength="5"
                   class="input--text"
                   required
                   placeholder="Password">
            </input>
          </div>

          <div class="login__sign-in">
            <button type="submit"
                    @click.prevent="login"
                    class="btn--secondary">
              Sign In
            </button>
          </div>

          <div class="login__footer">
            <a href=""
               class="login__create-account">
              Reset my password
            </a>
          </div>

        </div>
      </div>

      <div :style="`background: url(${imagePath}) no-repeat`"
           class="login__right">
        <div class="login__right-inner-wrapper">
          <div class="login__brand-wrapper">
            <img src="/storage/logo.png" class="login__logo">
            <h1 class="login__app-name">{{ settings.site_name }}</h1>
            <div>{{ settings.site_slogan }}</div>
          </div>

          <div class="login__language-wrapper">
            <label for="language">Select Language:</label>
            <v-select name="language"
                      :reduce="language => language.code"
                      label="language"
                      :options="languages">
            </v-select>
          </div>
        </div>
      </div>
    </div>

    <!-- Show error dialog-->
    <l-dialog id="dialog-error"/>

  </div>
</template>

<script>
  import Settings from 'Mixins/Settings';
  import get from 'lodash/get';
  import LDialog from "./tools/LDialog";
  import { mapState, mapGetters, mapMutations } from "vuex";

  export default {
    components: {LDialog},
    mixins: [Settings],

    props: {
      /**
       * An array of advert images
       */
      images: {
        type: Array,
        default: () => []
      },
    },

    data() {
      return {
        loading: false,
        signInAsGuest: 'Sign in as guest',
        signInAsMember: 'Sign in as a member',
        imagePath: `storage/${this.images[0]}`,
        imageIndex: 0,
        accessToken: '',
        loginWindow: 0,
        isMember: false,
        language: 'en',
        languages: [
          {code: 'en', language: 'Engish'},
          {code: 'fr', language: 'French'},
          {code: 'ge', language: 'German'},
        ],
        guessName: '',
        gender: 'm',
        email: '',
        password: '',
      }
    },

    computed: {
      ...mapState('chat', [
        'errorMessages'
      ]),
      ...mapGetters('chat', [
        'hasError'
      ]),
    },

    mounted() {
      setInterval(() => {
        this.imagePath = 'storage/' + this.images[this.imageIndex];

        this.imageIndex = this.imageIndex + 1;
        if (this.imageIndex > this.images.length -1) {
          this.imageIndex = 0;
        }

      }, 10000);
    },

    methods: {
      /**
       * Validate input.
       */
      validateGuessName() {
        const regex = /[^A-Z0-9\S]/gi;
        const matches = regex.exec(this.guessName);

        return matches;
      },
      /**
       * login
       */
      login() {
        // We check if the entered guess name is valid.
        if(this.validateGuessName()) {
          this.$store.commit('chat/setErrorMessages', [`Invalid characters detected, please only enter letters and numbers. No space allowed`]);
          return;
        }

        this.loading = true;
        this.$axios.post(`/api/auth/login`, {
          email: this.email,
          password: this.password,
          gender: this.gender,
          guess: this.guessName,
          remember_me: true,
        })
          .then((response) => {
            this.accessToken = get(response, 'data', []);

            if(this.loginWindow === 1) {
              this.signInAsGuest = `Welcome Guest_${this.$options.filters.capitalize(this.accessToken.user_name)}`;
            } else {
              this.signInAsMember = `Welcome ${this.$options.filters.capitalize(this.accessToken.user_name)}`;
            }

            setTimeout(() => {
              window.location.href = `/?token=${this.accessToken.access_token}&csrf=${this.accessToken.csrf}`;
            }, 3000);

          })
          .catch((error) => {
            const message = get(error, 'response.data.message', 'An error has occured!');
            this.$store.commit('chat/setErrorMessages', [message]);

            this.loading = false;
          });
      },
      /**
       * Reset the param being passed depending on the login type.
       *
       * @param loginType
       */
      resetLogInTypeData(loginType = 1) {
        this.loginWindow = loginType;

        // Guess
        if (loginType === 1) {
          this.email = '';
          this.password = '';

          return;
        }

        this.guessName = '';
      },
      /**
       * Restrict special character input.
       *
       * @param event
       */
      keyInputHandler(event) {
        const regex = /[^A-Z0-9\S]/gi;
        const matches = regex.exec(event.key);

        if (matches) {
          event.preventDefault();
        }
      }
    },

    watch: {
      // Displays the error dialog.
      hasError(value) {
        if(value) {
          this.$bvModal.show('dialog-error');
        }
      }
    }
  }
</script>
<style lang="scss">
  /**
   * Tab style
   */
  .el-tabs {
    &__header {
      margin: 0;
    }
  }
</style>
