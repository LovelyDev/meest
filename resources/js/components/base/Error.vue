<template>
  <div class="error">
    <md-dialog :md-active.sync="showError"
               class="error">
      <div class="error__header">
        <i class='error__icon info'/>
      </div>

      <div class="error__body">
        <h5 class="error__title">{{title}}</h5>
        <ul class="error__items">
          <li v-for="(errorMsg, index) in errorMessages"
              class="error__item">{{ errorMsg }}</li>
        </ul>
      </div>

      <div class="error__footer">
        <div class="error__footer-inner-wrapper">
          <button type="button"
                  @click.prevent="setErrorMessages({show: false})"
                  class="error__close btn--primary">
            Close
          </button>
        </div>
      </div>
    </md-dialog>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';

  export default {
    mixins: [ChatStore],

    props: {
      /**
       * Error title.
       */
      title: {
        type: String,
        default: '',
      },
    },

    data() {
      return {
      };
    },

    computed: {
    },

    mounted() {
    },

    methods: {

    },
  };
</script>
