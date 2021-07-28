<template>
  <div class="ldialog">
    <b-modal :id="id"
             :title="title"
             :hide-header-close="false"
             :no-close-on-backdrop="true"
             :no-close-on-esc="true"
             :centered="true"
             :modal-class="`ldialog__type-${type}`"
             content-class="ldialog__content"
             :header-class="`ldialog__header type-${type}`"
             :body-class="`ldialog__body type-${type}`"
             :footer-class="`ldialog__footer type-${type}`"
             :size="size">

      <!-- Display header-->
      <template v-slot:modal-header>
        <i :class="`ldialog__icon ${icon}`"/>
        <span class="ldialog__title">{{ title }}</span>
      </template>

      <slot>
        <!-- Display message -->
        <template v-if="type === 'message' || type === 'extra'"
                  class="ldialog__messages">
          <template v-for="(message, index) in errorMessages">
            <p :key="index"
               class="ldialog__message">
              {{ message }}
            </p>
          </template>

        </template>
      </slot>

      <!-- Display footer-->
      <template v-slot:modal-footer>

        <button v-if="type === 'message' || type === 'extra'"
                type="button"
                class="btn--secondary ldialog__ok"
                @click="close()">Ok
        </button>

        <button v-if="type === 'confirm'"
                type="button"
                class="btn--primary ldialog__no"
                @click="close()">No
        </button>

        <button v-if="type === 'confirm'"
                type="button"
                class="btn--secondary ldialog__yes"
                @click="yes()">yes
        </button>

      </template>

    </b-modal>
  </div>
</template>

<script>
  import { mapGetters, mapState} from "vuex";

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
       * The type of dialog to display.
       */
      type: {
        type: String,
        default: 'message'
      },
      /**
       * The header icon.
       */
      icon: {
        type: String,
        default: 'info'
      },
      /**
       * The header title.
       */
      title: {
        type: String,
        default: ''
      },
      /**
       * Determines if the header title is centered.
       */
      centeredTitle: {
        type: Boolean,
        default: false,
      },
      /**
       * The size of the dialog box.
       */
      size: {
        type: String,
        default: 'md'
      }
    },

    data() {
      return {}
    },

    computed: {
      ...mapState('chat', [
        'errorMessages'
      ]),
      ...mapGetters('chat', [
        'hasError'
      ]),
    },

    methods: {
      /**
       * Closes the modal and emit selection.
       *
       * @param value
       */
      close() {
        this.$bvModal.hide(this.id);
        this.$store.commit('chat/setErrorMessages', []);
        this.$emit('close');
      },
      /**
       * Close the model and emit yes.
       */
      yes() {
        this.$bvModal.hide(this.id);
        this.$store.commit('chat/setErrorMessages', []);
        this.$emit('close',true);
      }
    },
  }
</script>
