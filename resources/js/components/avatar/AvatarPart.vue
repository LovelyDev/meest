<template>
  <div class="avatar-part">
    <div :style="`background-image: url(${path})`"
         :class="`avatar-part__item ${className} ${itemKey}`"/>
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
       * The avatar's key.
       */
      itemKey: {
        type: String,
        default: ''
      },
      /**
       * The avtar part path.
       */
      path: {
        type: String,
        default: ''
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
          'hair'
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
