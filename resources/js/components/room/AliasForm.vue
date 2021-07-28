<template>
  <div class="alias-form">
    <l-dialog-select :id="ldialogName"
                     title="Choose a username"
                     sidebar-title="User Names"
                     size="xl"
                     @close="closeAlias()">

      <template v-slot:ldialog-select-left>
        <div class="alias-form__title">
          User Names
        </div>

        <div class="alias-form__alias-items-wrapper"
             :class="{'full-height': !hasDisabledAliases}">
          <!--List of enabled aliases-->
          <ul class="alias-form__alias-items radio--buttons"
              :class="{'full-height': !hasDisabledAliases}">
            <li v-for="alias in sortAliases(0)"
                class="alias-form__alias-item"
                :class="{'active': alias.id === selectedAliasId}">
              <input type="radio"
                     :id="alias.id"
                     :value="alias.id"
                     :name="selectedAliasId"
                     v-model="selectedAliasId">

              <label :for="alias.id">
                <avatar-thumbnail :avatar="alias"/>
                <span>{{ alias.alias }}</span>
              </label>
            </li>
          </ul>

          <!--List of disabled aliases-->
          <template v-if="!isGuest() && hasDisabledAliases">
            <div class="alias-form__disabled-account-title">
              Disabled Accounts
            </div>

            <ul class="alias-form__alias-items radio--buttons disabled">
              <li v-for="alias in sortAliases(1)"
                  class="alias-form__alias-item disabled">
                <label :for="alias.id"
                       class="disabled">
                  <avatar-thumbnail :avatar="alias"
                                    class-name="disabled"/>
                  <span class="disabled">{{ alias.alias }}</span>
                </label>
              </li>
            </ul>
          </template>
        </div>

        <div class="alias-form__create-wrapper">
          <button type="button"
                  @click.prevent="showAliasCreateForm()"
                  :disabled="noAliasCreate"
                  class="alias-form__btn-create btn--secondary">
            Create Username
          </button>
        </div>
      </template>

      <!--Right content-->
      <div class="alias-form__avatar-preview-wrapper">
        <!--Display chat hours-->
        <span v-if="!isGuest()"
              class="alias-form__chat-hours">User's chat hours:
          <strong>{{ aliasChatHours }} hrs</strong>
        </span>

        <avatar-full :avatar="alias"/>

        <div class="alias-form__name-plate">{{ aliasName }}</div>
      </div>

      <div class="alias-form__footer">
        <button type="button"
                :disabled="selectedAliasIsDisabled"
                @click.prevent="showAvatarCreateForm()"
                class="alias-form__change-avatar btn--primary btn--shadow">
          Change Avatar
        </button>
        <button type="button"
                @click.prevent="closeAlias(true)"
                class="alias-form__select btn--primary btn--shadow">
          Select
        </button>
      </div>

    </l-dialog-select>

    <!--Display create alias form-->
    <l-dialog-form id="model-alias-create"
                   title="Create Username"
                   endpoint="api/alias"
                   :confirm-message="`Are you sure you want to create the user name ${newAliasName}?`"
                   no-value-message="Please enter a username to continue"
                   :has-value="hasValue"
                   :data="{alias: newAliasName,creator_alias_id: selectedAliasId}"
                   @close="reloadList($event)">

      <input type="text"
             ref="aliasInput"
             v-model="newAliasName"
             class="input--text"
             minlength="1"
             maxlength="14"
             name="guess"
             placeholder="Type new username"
             @keydown="keyInputHandler">
      </input>
    </l-dialog-form>

    <!-- Display the Avatar create form-->
    <avatar-form :alias="alias"
                 @avatar-form-close="closeAvatarForm($event)"/>
  </div>
</template>

<script>
  import {mapGetters, mapState} from "vuex";
  import ChatStore from 'Mixins/ChatStore';
  import get from 'lodash/get';
  import filter from 'lodash/filter';
  import orderBy from 'lodash/orderBy';
  import head from 'lodash/head';
  import find from 'lodash/find';
  import isEmpty from 'lodash/isEmpty';
  import AvatarForm from "Components/room/AvatarForm";
  import LDialogSelect from 'Tools/LDialogSelect';
  import LDialog from "Tools/LDialog";
  import LDialogForm from "Tools/LDialogForm";
  import AvatarThumbnail from "../avatar/AvatarThumbnail";

  export default {
    components: {
      AvatarThumbnail,
      LDialog,
      LDialogSelect,
      LDialogForm,
      AvatarForm
    },

    mixins: [ChatStore],

    data() {
      return {
        newAliasName: '',
        selectedAliasId: 0,
        ldialogName: 'modal-alias-select',
        ldialogConfirmName: 'modal-alias-confirm'
      }
    },

    computed: {
      ...mapState('chat', [
        'errorMessages',
        'showAliasSelect'
      ]),
      ...mapGetters('chat', [
        'hasError'
      ]),
      /**
       * Returns the selected alias object.
       */
      alias() {
        const alias = find(this.userInfo.aliases, ['id', this.selectedAliasId]);

        return !isEmpty(alias) ? alias : {};
      },
      /**
       * Returns the name of the selected alias.
       */
      aliasName() {
        return !isEmpty(this.alias) ? this.alias.alias : '';
      },
      /**
       * Returns the name of the selected alias.
       */
      aliasChatHours() {
        return !isEmpty(this.alias) ? this.alias.hours : '';
      },
      /**
       * Determines if alias can create another alias.
       */
      noAliasCreate() {
        // When chat hours < 24 hrs.
        // When alias has already created an alias.
        return this.aliasChatHours < 24 || this.alias.alias_child_id > 0;
      },
      /**
       * Determines if the selected alias is enabled or disabled.
       */
      selectedAliasIsDisabled() {
        return this.alias.disabled < 1 ? false : true;
      },
      /**
       * Determines if the list has disabled aliases.
       */
      hasDisabledAliases() {
        const alias = find(this.userInfo.aliases, ['disabled', 1]);
        return !isEmpty(alias) ? true : false;
      },
      /**
       * Determine if alias has value or not.
       */
      hasValue() {
        return !isEmpty(this.newAliasName);
      }
    },

    mounted() {
      // Set the first alias as the selected default username.
      this.$nextTick(() => {
        const defaultAlias = head(this.sortAliases(0));
        this.selectedAliasId = defaultAlias.id;
      });
    },

    methods: {
      /**
       * User selected an alias-form.
       * @Todo: process if graphic or classic.
       */
      closeAlias(value) {
        this.$store.commit('chat/setShowAliasSelect', false);
        this.$emit('alias-form-close', {
          userHasSelected: value,
          aliasId: this.selectedAliasId,
          aliasName: find(this.userInfo.aliases, ['id', this.selectedAliasId]).alias,
          userId: this.userInfo.id,
        });
      },
      /**
       * Closes the alias form and pass the new alias configuration to chat.
       */
      closeAvatarForm(alias) {
        this.$store.commit('chat/setShowCreateAvatar', false);
        this.$emit('alias-form-close', alias);
      },
      /**
       * Display the Alias create form.
       */
      showAliasCreateForm() {
        this.$bvModal.show('model-alias-create');

        // We set the focus to the message input.
        setTimeout(() => {
          this.$refs.aliasInput.focus();
        }, 1000);
      },
      /**
       * Display the Avatar create form.
       */
      showAvatarCreateForm() {
        this.$store.commit('chat/setShowAliasSelect', false);
        this.$store.commit('chat/setShowCreateAvatar', true);
        this.$bvModal.show('modal-avatar-select');
      },
      /**
       * Restrict special character input.
       *
       * @param event
       */
      keyInputHandler(event) {
        const regex = /[^A-Z0-9\s]/gi;
        const matches = regex.exec(event.key);

        if (matches) {
          event.preventDefault();
        }
      },
      /**
       * Reloads the alias list.
       * @param {boolean} success - Whether the creation of the new alias is successful.
       */
      reloadList(success) {
        this.newAliasName = '';
        this.reloadUserInfo();
      },
      /**
       * Returns the list of ordered enabled/disabled aliases.
       *
       * @param {boolean} disabled - Whether enabled or disabled alias.
       * @returns {array} - The list of ordered aliases.
       */
      sortAliases(disabled) {
        const filtered = filter(this.userInfo.aliases, ['disabled', disabled]);
        return orderBy(filtered, ['alias'], ['asc']);
      },
    },

    watch: {
      // Displays or hides the dialog.
      showAliasSelect(value) {
        if (value) {
          this.$bvModal.show(this.ldialogName);
          return;
        }

        this.$bvModal.hide(this.ldialogName);
      },

      // Displays or hides the avatar create dialog.
      showCreateAvatar(value) {
        if (value) {
          this.$bvModal.show('modal-avatar-select');
          return;
        }

        this.$bvModal.hide('modal-avatar-select');
      },

      // Displays the error dialog.
      hasError(value) {
        if (value) {
          this.$bvModal.show('dialog-error');
        }
      }
    }
  }
</script>
