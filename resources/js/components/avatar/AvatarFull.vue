<template>
  <div class="avatar-full"
       :class="{'original-size': originalSize}">
    <div v-for="(part, index) in avatarParts"
         :key="index"
         :style="`background-image: url(/storage/avatars/components/${part}/${gender}/${avatar[part]}.png)`"
         :class="`avatar-full__part ${part}`"/>
  </div>
</template>

<script>
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
       * Whether to display the original dimentions or not.
       * This is both used in Alias form and Avatar form.
       */
      originalSize: {
        type: Boolean,
        default: false,
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
          'pants',
          'shoes',
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
  }
</script>
