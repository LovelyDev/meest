<template>
  <div class="ldialog-select">
    <b-modal :id="id"
             :title="title"
             :hide-header-close="false"
             :centered="true"
             modal-class="ldialog-select"
             content-class="ldialog-select__content"
             header-class="ldialog-select__header"
             body-class="ldialog-select__body"
             footer-class="ldialog-select__footer"
             :no-close-on-esc="true"
             :no-close-on-backdrop="true"
             :hide-footer="true"
             :size="size">

      <!-- Display header-->
      <template v-slot:modal-header>
        <h3 class="ldialog-select__title">
          {{ title }}
        </h3>

        <a href='#'
           class="ldialog-select__close"
           @click.prevent="close">
          <i class='ldialog-select__close-icon'/>
        </a>
      </template>

      <!--Display main content-->
      <div class="ldialog-select__main-content">
        <div class="ldialog-select__left">
          <slot class="ldialog-select__left"
                name="ldialog-select-left"/>
        </div>

        <div class="ldialog-select__right">
          <slot></slot>
        </div>
      </div>

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
       * The modal title.
       */
      title: {
        type: String,
        default: 'Title'
      },
      /**
       * The sidebar title.
       */
      sidebarTitle: {
        type: String,
        default: 'Sidebar title'
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
        this.$emit('close');
        this.$bvModal.hide(this.id);
      }
    },
  }
</script>
