<template>
  <div class="avatar-thumbnail-mini"
       :class="className">
    <div v-for="(part, index) in avatarParts"
         :key="index"
         :style="`background-image: url(/storage/avatars/components/${part}/${gender}/${avatar[part]}.png)`"
         :class="`avatar-thumbnail-mini__part ${part}`"/>
  </div>
</template>

<script>
  import {mapState, mapActions} from 'vuex';
  import ChatStore from 'Mixins/ChatStore';

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
        return this.avatar.gender === 'm' ? 'man' : 'woman';
      },
    },

    mounted() {

    },

    methods: {

    },
  }
</script>
