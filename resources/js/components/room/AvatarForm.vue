<template>
  <div class="avatar-form">

    <l-dialog-select :id="ldialogName"
                     title="Design your avatar"
                     sidebar-title="User Names"
                     size="xl"
                     @close="closeForm()">

      <template v-slot:ldialog-select-left
                v-if="avatarComponents.length > 0">
        <!-- Display avatar parts category navigation -->
        <div v-if="avatarComponents.length"
             :class="{'center': !hasNavArrows}"
             class="avatar-form__title">
          <i v-if="hasNavArrows"
             class='arrow icon arrow-left'
             :class="{'disabled': !hasPrev}"
             @click="previous()"/>
          <span>{{ avatarComponents[selectedAvatarComponent]['label']}}</span>
          <i v-if="hasNavArrows"
             class='arrow icon arrow-right'
             :class="{'disabled': !hasNext}"
             @click="next()"/>
        </div>

        <!-- Display avatar parts category items -->
        <div class="avatar-form__part-items-wrapper">
          <ul class="avatar-form__part-items"
              :class="avatarComponents[selectedAvatarComponent].key">

            <!-- Display category items-->
            <template v-if="selectedAvatarComponent < 8">
              <li v-for="(imagePath, imageKey) in sortedCategoryItems[selectedPartItemsGroup]"
                  class="avatar-form__part-item"
                  :class="{'first-group': selectedPartItemsGroup === 0}"
                  @click="avatar[avatarComponents[selectedAvatarComponent].key] = imageKey">

                <div class="avatar-form__part-item-wrapper">
                  <avatar-part :item-key="imageKey"
                               :path="imagePath"
                               :class-name="avatarComponents[selectedAvatarComponent].key"/>
                </div>
              </li>
            </template>

            <!-- Display prebuilt avatar items-->
            <template v-if="avatarComponents.length > 0 && selectedAvatarComponent === 8">
              <li v-for="(avatarDefault, index) in avatarDefaults"
                  class="avatar-form__part-item">

                <div class="avatar-form__part-item-wrapper"
                     @click="loadSelectedPrebuildAvatar(index)">

                  <avatar-thumbnail-full-body :item-key="index"
                                              :avatar="avatarDefault"
                                              class-name=""/>
                </div>
              </li>
            </template>

          </ul>
        </div>
      </template>

      <div v-if="avatarComponents.length > 0"
           class="avatar-form__avatar-preview-wrapper">
        <ul class="avatar-form__circle-container">
          <li v-for="(item, index) in avatarComponents"
              class="part"
              :class="{'active': selectedAvatarComponent == index}">

            <div
              :style="`background-image: url(/storage/avatars/components/defaults/${avatarComponents[index].key}.png)`"
              :class="`${avatarComponents[index].key} image`"
              @click="selectedAvatarComponent = index; selectedPartItemsGroup =0"/>

          </li>

          <li class="part triangle"
              :class="`${avatarComponents[selectedAvatarComponent]['key']}`">
          </li>

          <li class="full">
            <div class="full__wrapper">
              <avatar-full :avatar="avatar" :original-size="true"/>
            </div>
          </li>
        </ul>
      </div>

      <div class="avatar-form__footer">
        <button type="button"
                @click.prevent="save(true)"
                class="alias-form__select btn--primary btn--shadow">
          Save
        </button>
      </div>
    </l-dialog-select>
  </div>
</template>

<script>
  import {mapState, mapActions} from 'vuex';
  import ChatStore from 'Mixins/ChatStore';
  import get from 'lodash/get';
  import values from 'lodash/values';
  import map from 'lodash/map';
  import forEach from 'lodash/forEach';
  import find from 'lodash/find';
  import isEmpty from 'lodash/isEmpty';
  import AvatarPart from "../avatar/AvatarPart";
  import AvatarThumbnail from "../avatar/AvatarThumbnail";
  import AvatarThumbnailFullBody from "../avatar/AvatarThumbnailFullBody";

  const path = require('path');

  export default {
    components: {
      AvatarThumbnailFullBody,
      AvatarThumbnail,
      AvatarPart
    },

    mixins: [ChatStore],

    props: {
      alias: {
        type: Object,
        default: () => {
        }
      }
    },

    data() {
      return {
        newAliasName: '',
        selectedAliasId: 0,
        ldialogName: 'modal-avatar-select',
        selectedAvatarComponent: 8,
        selectedPartItemsGroup: 0,
        avatar: {
          bodies: '001',
          hair: '001',
          faces: '001',
          shirts: '001',
          coats: '',
          pants: '001',
          shoes: '001',
          accessories: '',
          head_accessories: '',
          specials: ''
        },
        selectedPrebuildAvatar: 0
      }
    },

    computed: {
      ...mapState('avatar', [
        'avatarDefaults'
      ]),
      /**
       * Returns the name of the selected avatar.
       */
      avatarName() {
        const avatar = find(this.userInfo.avatares, ['id', this.selectedAliasId]);

        return !isEmpty(avatar) ? avatar.avatar : '';
      },
      /**
       * Returns the gender of the user.
       */
      gender() {
        return this.userInfo.gender === 'm' ? 'man' : 'woman';
      },
      /**
       * Determines if the navigation arrow should be displayed.
       */
      hasNavArrows() {
        if (this.selectedAvatarComponent === 8) {
          return this.avatarDefaults.length > 28;
        }

        return Object.keys(this.avatarComponents[this.selectedAvatarComponent][this.gender]).length > 28;
      },
      /**
       * Returns the all category items grouped in 24.
       */
      sortedCategoryItems() {
        const items = values(this.avatarComponents[this.selectedAvatarComponent][this.gender]);
        const itemsCount = items.length - 1;
        let itemGroups = [];
        let count = 0;
        let temp = {};

        forEach(items, (item, index) => {
          let key = path.basename(item, '.png');

          temp[key] = item;
          if ((((index + 1) % 24) === 0 && index > 0) || index === itemsCount) {
            itemGroups.push(temp);
            temp = {};
            count++;
          }
        });

        return itemGroups;
      },
      /**
       * Returns true if there is a next group
       */
      hasNext() {
        return this.sortedCategoryItems[this.selectedPartItemsGroup + 1] != undefined;
      },
      /**
       * Returns true if there is a prev group
       */
      hasPrev() {
        return this.sortedCategoryItems[this.selectedPartItemsGroup - 1] != undefined;
      },
    },

    methods: {
      ...mapActions('avatar', [
        'getPrebuiltAvatars'
      ]),
      /**
       * User selected an avatar-form.
       * @Todo: process if graphic or classic.
       */
      closeForm() {
        // const interfaceType = this.remember ? this.avatar-formType : '';
        this.selectedAvatarComponent = 8;
        this.selectedPartItemsGroup = 0;
        this.$emit('avatar-form-close');
      },
      // Save the avatar configuration.
      save(value) {
        this.$axios({
          method: 'put',
          url: `/api/alias/${this.alias.id}`,
          data: this.avatar,
          headers: {
            Authorization: `Bearer ${this.token}`,
          }
        })
          .then((response) => {
            this.selectedAvatarComponent = 8;
            this.selectedPartItemsGroup = 0;

            // We set the selected aliasInfo in Vuex.
            this.$store.commit('chat/setAliasInfo', this.avatar);

            this.$emit('avatar-form-close', {
              userHasSelected: value,
              aliasId: this.alias.id,
              aliasName: this.alias.alias,
              userId: this.userInfo.id,
              avatar: this.avatar
            });

            this.reloadUserInfo();
          })
          .catch((response) => {
            console.log(response.data);
          })
      },
      /**
       * Navigate to the next component.
       */
      next() {
        let next = this.selectedPartItemsGroup + 1;

        if (!this.sortedCategoryItems[next]) {
          next = 0;
        }
        this.selectedPartItemsGroup = next;
      },
      /**
       * Navigate to the previous component.
       */
      previous() {
        let next = this.selectedPartItemsGroup - 1;

        if (!this.sortedCategoryItems[next]) {
          next = 0;
        }

        this.selectedPartItemsGroup = next;
      },
      /**
       * Load the selected avatar default to the local placeholder.
       * @param index
       */
      loadSelectedPrebuildAvatar(index) {
        this.avatar.bodies = this.avatarDefaults[index].bodies;
        this.avatar.coats = this.avatarDefaults[index].coats;
        this.avatar.faces = this.avatarDefaults[index].faces;
        this.avatar.hair = this.avatarDefaults[index].hair;
        this.avatar.head_accessories = this.avatarDefaults[index].head_accessories;
        this.avatar.pants = this.avatarDefaults[index].pants;
        this.avatar.shirts = this.avatarDefaults[index].shirts;
        this.avatar.shoes = this.avatarDefaults[index].shoes;
        this.avatar.specials = this.avatarDefaults[index].specials;
        this.avatar.accessories = this.avatarDefaults[index].accessories;
      }
    },

    watch: {
      alias(value) {
        this.avatar = value;
      }
    }
  }
</script>
