<template>
  <div class="interface">
    <l-dialog-select :id="ldialogName"
                     title="Choose an interface"
                     sidebar-title="Interface"
                     size="xl"
                     @close="closeInterface()">

      <template v-slot:ldialog-select-left>
        <div class="interface__left-wrapper">
          <div class="interface__title">
            Interface
          </div>

          <div class="interface__type">
            <ul class="interface__type-ul radio--buttons">
              <li class="interface__type-classic"
                  :class="{'active': interfaceType === 'classic'}">
                <input type="radio"
                       id="classic"
                       value="classic"
                       name="interfaceType"
                       v-model="interfaceType">

                <label for="classic">
                  <img src="/storage/interface/interface-classic.png"
                       class="interface__type-logo interface__classic-logo">
                </label>
                <div class="check">
                  <div class="inside">Classic Interface</div>
                </div>
              </li>

              <li class="interface__type-graphic"
                  :class="{'active': interfaceType === 'graphic'}">
                <input type="radio"
                       id="graphic"
                       value="graphic"
                       name="interfaceType"
                       v-model="interfaceType">

                <label for="graphic">
                  <img src="/storage/interface/interface-graphic.png"
                       class="interface__type-logo interface__graphic-logo">
                </label>
                <div class="check">
                  <div class="inside">Graphic Interface</div>
                </div>
              </li>
            </ul>
          </div>

          <!--Display remember checkbox-->
          <div v-if="!isGuest()"
               class="interface__remember">
            <div class="lcheckbox">
              <input type="checkbox"
                     id="remember"
                     v-model="remember"
                     class="option-input lcheckbox__checkbox"/>

              <label for="remember" class="lcheckbox__label">
                Remember my selection
              </label>
            </div>
          </div>
        </div>
      </template>

      <!--Right content-->
      <div class="interface__type-logo-wrapper">
        <img :src="getInterfaceLogoPath"
             class="interface__type-logo-big">

        <div class="interface__type-text">{{ getInterfaceText }}</div>
      </div>

      <div class="interface__footer">
        <button type="button"
                @click.prevent="selectInterface"
                class="interface__submit btn--primary btn--shadow">
          Select
        </button>
      </div>
    </l-dialog-select>
  </div>
</template>

<script>
  import ChatStore from 'Mixins/ChatStore';
  import LDialogSelect from 'Tools/LDialogSelect';
  import LDialog from "Tools/LDialog";
  import {mapGetters, mapState} from "vuex";

  export default {
    mixins: [ChatStore],

    components: {
      LDialog,
      LDialogSelect
    },

    data() {
      return {
        interfaceType: 'classic',
        remember: false,
        ldialogName: 'modal-interface-select'
      }
    },

    computed: {
      ...mapState('chat', [
        'errorMessages',
        'showInterface'
      ]),
      ...mapGetters('chat', [
        'hasError'
      ]),
      /**
       * Returns the interface logo.
       */
      getInterfaceLogoPath() {
        return this.interfaceType === 'classic' ? 'storage/interface/interface-classic.png' :
          'storage/interface/interface-graphic.png';
      },
      /**
       * Returns the interface type text.
       */
      getInterfaceText() {
        return this.interfaceType === 'classic' ?
          'The classic design is simple but stylish and user friendly interface.' :
          'The graphic design is avatar based interface.';
      },
    },

    methods: {
      /**
       * Close the interface window.
       */
      closeInterface() {
        this.$store.commit('chat/setShowInterface', false);
      },
      /**
       * User selected an interface.
       * @Todo: process if graphic or classic.
       */
      selectInterface() {
        this.closeInterface();
        this.$emit('interface-close', this.interfaceType);
      }
    },

    watch: {
      // Displays or hides the dialog.
      showInterface(value) {
        if (value) {
          this.$bvModal.show(this.ldialogName);
          return;
        }

        this.$bvModal.hide(this.ldialogName);
      }
    }
  }
</script>
