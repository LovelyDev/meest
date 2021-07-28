<template>
  <div class="ldialog-form">
    <b-modal :id="id"
             :title="title"
             :hide-header-close="true"
             :centered="true"
             modal-class="ldialog-form__modal ldialog-form"
             content-class="ldialog-form__content"
             header-class="ldialog-form__header"
             body-class="ldialog-form__body"
             footer-class="ldialog-form__footer"
             :size="size">


      <slot>
      </slot>

      <!-- Display footer-->
      <template v-slot:modal-footer>
        <button type="button"
                class="btn--transparent ldialog-form__cancel"
                @click="close">Cancel
        </button>

        <button type="button"
                class="btn--secondary ldialog-form__create"
                @click="confirm">Create
        </button>
      </template>
    </b-modal>

    <!-- Show error dialog -->
    <l-dialog id="modal-ldialog-form-error"
              :icon="errorIcon"
              type="extra"/>

    <!--Show confirm dialog-->
    <l-dialog :id="dialogConfirm"
              icon="user-plus"
              @close="confirmed = $event"
              type="confirm">
      {{ confirmMessage }}
    </l-dialog>
  </div>
</template>

<script>
  import {mapGetters, mapState} from "vuex";
  import get from "lodash/get";
  import forEach from "lodash/forEach";
  import first from "lodash/first";

  export default {
    props: {
      /**
       * The modal id.
       */
      id: {
        type: String,
        default: ''
      },
      /**
       * The header title.
       */
      title: {
        type: String,
        default: 'Notification'
      },
      /**
       * The size of the dialog box.
       */
      size: {
        type: String,
        default: 'md'
      },
      /**
       * The endpoint to hit for CRUD.
       */
      endpoint: {
        type: String,
        default: ''
      },
      /**
       * The data to pass to the endpoint.
       */
      data: {
        type: Object,
        default: {}
      },
      /**
       * The confirmation message to be displayed before calling the endpoint.
       */
      confirmMessage: {
        type: String,
        default: ''
      },
      /**
       * The no value message.
       */
      noValueMessage: {
        type: String,
        default: ''
      },
      /**
       * Determines if form has value.
       */
      hasValue: {
        type: Boolean,
        default: false
      }
    },

    data() {
      return {
        dialogConfirm: 'modal-ldialog-confirm',
        confirmed: false,
        errorIcon: 'user-circle-o'
      }
    },

    computed: {
      ...mapState('chat', [
        'errorMessages',
        'token'
      ]),
      ...mapGetters('chat', [
        'hasError'
      ]),
    },

    methods: {
      confirm() {
        if (!this.hasValue) {
          this.$store.commit('chat/setErrorMessages', [this.noValueMessage]);
          return;
        }

        this.$bvModal.show(this.dialogConfirm);
      },
      /**
       * Calls the create endpoint.
       */
      create() {
        this.$axios({
          method: 'post',
          url: this.endpoint,
          data: this.data,
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.errorIcon = 'user-circle-o';
            this.$store.commit('chat/setErrorMessages', ['Username successfully added to your account.']);
            this.$emit('close', 'success');
            this.close();
          })
          .catch((response) => {
            let messages = [];
            const errors = get(response, 'response.data.errors', 'An error has occured!');

            forEach(errors, (error) => {
              messages.push(first(error));
            });

            this.errorIcon = 'user-times';
            this.$store.commit('chat/setErrorMessages', messages);
          })
      },
      /**
       * Closes the dialog.
       */
      close() {
        this.$bvModal.hide(this.id);
        this.$emit('close', 'failed');
      }
    },

    watch: {
      // Displays the error dialog.
      hasError(value) {
        if (value) {
          this.$bvModal.show('modal-ldialog-form-error');
        }
      },
      // Displays the error dialog.
      confirmed(value) {
        if (value) {
          this.create();
          this.confirmed = false;
        }
      }
    }
  }
</script>
