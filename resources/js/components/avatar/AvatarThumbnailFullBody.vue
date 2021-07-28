<template>
  <div class="avatar-thumbnail-full-body"
       :class="className">
    <div v-for="(part, index) in avatarParts"
         class="avatar-thumbnail-full-body__inner-wrapper">
      <div :key="index"
           :style="`background-image: url(/storage/avatars/components/${part}/${gender}/${avatar[part]}.png)`"
           :class="`avatar-thumbnail-full-body__part ${part}`"/>
    </div>
  </div>
</template>

<script>
  import {mapState, mapActions} from 'vuex';
  import ChatStore from 'Mixins/ChatStore';
  import get from 'lodash/get';
  import head from 'lodash/head';
  import filter from 'lodash/filter';
  import find from 'lodash/find';
  import isEmpty from 'lodash/isEmpty';

  export default {

    mixins: [ChatStore],

    props: {
      /**
       * The avatar object.
       */
      avatar: {
        type: Object,
        default: () => {
        }
      },
      /**
       * class name.
       */
      className: {
        type: String,
        default: ''
      }
    },

    data() {
      return {
        selectedAliasId: 0,
        avatarParts: [
          'bodies',
          'hair',
          'shirts',
          'faces',
          'coats',
          'pants',
          'shoes',
          'accessories',
          'head_accessories',
          'specials',
        ]
      }
    },

    computed: {
      /**
       * Returns the gender of the user.
       */
      gender() {
        return this.userInfo.gender === 'm' ? 'man' : 'woman';
      },
    },

    mounted() {

    },

    methods: {

    },
  }
</script>
