<template>
  <div v-if="work.Current" class="logoDiv">
    <img class="logoIcon" src="/static/img/rcr.png" />
  </div>
</template>

<script>
export default {
  name: "logoFloat",
  props: ["work"],
  methods: {
    onResize() {
      var visible = this.work.Current !== null;
      if (visible) {
        this.updateWork();
      }
    },
    updateWork() {
      var visible = this.work.Current !== null;
      var logo = document.getElementById("logoFloatIcon");
      if (visible) {
        if (window.SegMap) {
          window.SegMap.TriggerResize();
        }
      } else {
        this.work.Current = null;
        if (window.SegMap) {
          window.SegMap.SaveRoute.RemoveWork();
          window.SegMap.TriggerResize();
        }
      }
    }
  }
};
</script>

<style scoped>
.logoDiv {
  opacity: 75%;
  height: 5.5rem;
  overflow: hidden;
  bottom: 2.5rem;
  right: 46px;
  min-height: 40px;
  z-index: 1;
  position: absolute;
  background: seashell;
  border-radius: 21px;
  padding: 0.25em;
}
.logoIcon {
  width: auto;
  height: 100%;
}
</style>

