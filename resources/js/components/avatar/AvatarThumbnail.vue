<template>
  <div class="avatar-thumbnail"
       :class="className">
    <div v-for="(part, index) in avatarParts"
         :key="index"
         :style="`background-image: url(/storage/avatars/components/${part}/${gender}/${avatar[part]}.png)`"
         :class="`avatar-thumbnail__part ${part}`"/>
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
          'shirts',
          'faces',
          'hair',
          'coats',
          'head_accessories',
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
