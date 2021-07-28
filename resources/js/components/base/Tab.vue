<template>
  <section v-show="isActive"
           :aria-hidden="!isActive"
           class="tabs-component-panel"
           :id="computedId"
           role="tabpanel"
  >
    <slot/>
  </section>
</template>

<script>
  export default {
    props: {
      id: {default: null},
      name: {required: true},
      prefix: {default: ''},
      suffix: {default: ''},
      isDisabled: {default: false},
    },

    data: () => ({
      isActive: false,
      isVisible: true,
      roomId: 0,
    }),

    computed: {
      header() {
        return this.prefix + this.name + this.suffix;
      },

      computedId() {
        return this.id ? this.id : this.name.toLowerCase().replace(/ /g, '-');
      },

      hash() {
        if (this.isDisabled) {
          return '#';
        }

        return '#' + this.computedId;
      },
    },

    // methods: {
    //   destroy() {
    //     this.$destroy();
    //   }
    // },

    mounted() {
      this.roomId = this.id;
    },
  };
</script>
